<?php
declare(strict_types = 1);

namespace app\modules\fraud\components\behaviours;

use app\models\product\ProductOrder;
use app\modules\fraud\components\queue\ChangeFraudStepWithValidateJob;
use app\modules\fraud\components\validators\orders\simcard\HasDuplicateAbonentPassportData;
use app\modules\fraud\components\validators\orders\simcard\HasDecreaseTariffPlan;
use app\modules\fraud\components\validators\orders\simcard\HasActivityOnSimcard;
use app\modules\fraud\components\validators\orders\simcard\HasIncreaseBalance;
use app\modules\fraud\components\validators\orders\simcard\HasPaySubscriptionFeeAndHasntCalls;
use app\modules\fraud\components\validators\orders\simcard\IncomingCallFromOneDevice;
use app\modules\fraud\components\validators\orders\simcard\IncomingCallToOneNumber;
use app\modules\fraud\components\validators\orders\simcard\IsAbonentBlockByFraud;
use app\modules\fraud\components\validators\orders\simcard\IsActiveSimcardValidator;
use app\modules\fraud\models\FraudCheckStep;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use Yii;
use yii\db\AfterSaveEvent;
use yii\db\Exception;

/**
 * Class ProductOrderSimcardAsyncBehaviour
 * @package app\modules\fraud\components\behaviours
 */
class ProductOrderSimcardAsyncBehaviour extends Behavior {
	public array $validators = [
		IsActiveSimcardValidator::class,
		HasActivityOnSimcard::class,
		HasDecreaseTariffPlan::class,
		IncomingCallToOneNumber::class,
		IncomingCallFromOneDevice::class,
		HasDuplicateAbonentPassportData::class,
		HasIncreaseBalance::class,
		HasPaySubscriptionFeeAndHasntCalls::class,
		IsAbonentBlockByFraud::class
	];

	/**
	 * @inheritDoc
	 */
	public function events():array {
		return [
			ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
		];
	}

	/**
	 * @param AfterSaveEvent $event
	 * @throws Exception
	 */
	public function afterInsert(AfterSaveEvent $event):void {
		/**
		 * @var ActiveRecord $model
		 */
		$model = $event->sender;
		if (!($model instanceof ProductOrder && $model->isSimcard())) {
			return;
		}

		(new FraudCheckStep())->addNewSteps(array_map(static function($class) use ($event) {
			return FraudCheckStep::newStep($event->sender->id, get_class($event->sender), $class);
		}, $this->validators));

		foreach ($this->validators as $validatorClass) {
			Yii::$app->queue->push(new ChangeFraudStepWithValidateJob([
				'validatorClass' => $validatorClass,
				'entityId' => $event->sender->id
			]));
		}
	}
}