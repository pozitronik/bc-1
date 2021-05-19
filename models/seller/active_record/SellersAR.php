<?php
declare(strict_types = 1);

namespace app\models\seller\active_record;

use app\models\core\prototypes\ActiveRecordTrait;
use app\models\store\active_record\relations\RelStoresToSellers;
use app\models\store\Stores;
use pozitronik\helpers\DateHelper;
use Throwable;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "sellers".
 *
 * @property int $id
 * @property string $name Имя продавца
 * @property string $create_date Дата регистрации
 * @property int $deleted
 *
 * @property RelStoresToSellers[] $relatedStoresToSellers Связь к промежуточной таблице к продавцам
 * @property Stores[] $stores Магазины продавца
 */
class SellersAR extends ActiveRecord {
	use ActiveRecordTrait;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'sellers';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['name'], 'required'],
			[['create_date'], 'safe'],
			[['create_date'], 'default', 'value' => DateHelper::lcDate()],
			[['deleted'], 'integer'],
			[['name'], 'string', 'max' => 255],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'name' => 'Имя продавца',
			'create_date' => 'Дата регистрации',
			'stores' => 'Магазины',
			'deleted' => 'Deleted',
		];
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedStoresToSellers():ActiveQuery {
		return $this->hasMany(RelStoresToSellers::class, ['seller_id' => 'id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getStores():ActiveQuery {
		return $this->hasMany(Stores::class, ['id' => 'store_id'])->via('relatedStoresToSellers');
	}

	/**
	 * @param mixed $stores
	 * @throws Throwable
	 */
	public function setStores($stores):void {
		RelStoresToSellers::linkModels($stores, $this, true);/* Соединение идёт "наоборот", добавляем ключ backLink */
	}
}