<?php
declare(strict_types = 1);

namespace app\controllers;

use app\models\core\prototypes\DefaultController;
use app\models\seller\Sellers;
use app\models\seller\SellersSearch;
use pozitronik\sys_exceptions\models\LoggedException;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Class SellersController
 */
class SellersController extends DefaultController {

	public string $modelClass = Sellers::class;
	public string $modelSearchClass = SellersSearch::class;

	public const CREATE_SCENARIO = 'create';

	/**
	 * @inheritDoc
	 */
	public function getViewPath():string {
		return '@app/views/sellers';
	}

	/**
	 * @inheritDoc
	 */
	public function actionCreate() {
		/** @var Sellers $model */
		$model = $this->model;
		$model->scenario = self::CREATE_SCENARIO;
		if (Yii::$app->request->post('ajax')) {
			return $this->asJson($model->validateModelFromPost());
		}

		$posting = $model->createModelFromPost([], $errors);
		if (true === $posting) {
			$model->uploadAttribute('sellerDocs');
			$model->createAccess();
			return $this->redirect('index');
		}
		/* Пришёл постинг, но есть ошибки */
		if ((false === $posting) && Yii::$app->request->isAjax) {
			return $this->asJson($errors);
		}

		/* Постинга не было */
		return (Yii::$app->request->isAjax)
			?$this->renderAjax('modal/create', ['model' => $model])
			:$this->render('create', ['model' => $model]);
	}

	/**
	 * @inheritDoc
	 */
	public function actionEdit(int $id) {
		if (null === $model = $this->model::findOne($id)) {
			throw new LoggedException(new NotFoundHttpException());
		}

		if (Yii::$app->request->post('ajax')) {/* запрос на ajax-валидацию формы */
			return $this->asJson($model->validateModelFromPost());
		}

		/** @var Sellers $model */
		$posting = $model->updateModelFromPost([], $errors);

		if (true === $posting) {/* Модель была успешно прогружена */
			$model->uploadAttribute('sellerDocs');
			return $this->redirect('index');
		}
		/* Пришёл постинг, но есть ошибки */
		if ((false === $posting) && Yii::$app->request->isAjax) {
			return $this->asJson($errors);
		}

		/* Постинга не было */
		return (Yii::$app->request->isAjax)
			?$this->renderAjax('modal/edit', ['model' => $model])
			:$this->render('edit', ['model' => $model]);
	}

}