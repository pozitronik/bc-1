<?php
declare(strict_types = 1);

namespace app\modules\active_hints\controllers;

use app\models\core\prototypes\ActiveRecordTrait;
use app\modules\active_hints\models\ActiveStorage;
use app\modules\active_hints\models\ActiveStorageSearch;
use app\modules\active_hints\widgets\active_hints\ActiveHints;
use kartik\grid\EditableColumnAction;
use pozitronik\core\traits\ControllerTrait;
use pozitronik\helpers\ArrayHelper;
use pozitronik\sys_exceptions\models\LoggedException;
use Throwable;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class DefaultController
 */
class DefaultController extends Controller {
	use ControllerTrait;

	public function actions():array {
		return ArrayHelper::merge(parent::actions(), [
			/**
			 * Можно назначить один экшен на все поля, которым не требуется специализированный обработчик,
			 * данные всё равно грузятся так, будто постится полная форма.
			 * @see \kartik\grid\EditableColumnAction::validateEditable()
			 */
			'editDefault' => [
				'class' => EditableColumnAction::class,
				'modelClass' => ActiveStorageSearch::class,
				'showModelErrors' => true,
			],
			'editHint' => [
				'class' => EditableColumnAction::class,
				'modelClass' => ActiveStorageSearch::class,
				'showModelErrors' => true,
				'outputValue' => function(ActiveHints $model, string $attribute, int $key, int $index) {
					return '';
				},
			]
		]);
	}

	/**
	 * @return string
	 */
	public function actionIndex() {
		$params = Yii::$app->request->queryParams;
		$searchModel = new ActiveStorageSearch();
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $searchModel->search($params),
		]);
	}

	/**
	 * @param int $id
	 * @return string
	 * @throws Throwable
	 */
	public function actionView(int $id):string {
		if (null === $model = ActiveStorage::findOne($id)) {
			throw new LoggedException(new NotFoundHttpException());
		}
		if (Yii::$app->request->isAjax) {
			return $this->renderAjax('modal/view', [
				'model' => $model
			]);
		}
		return $this->render('view', [
			'model' => $model
		]);
	}

	/**
	 * @param int $id
	 * @return string|Response
	 * @throws Throwable
	 */
	public function actionEdit(int $id) {
		if (null === $model = ActiveStorage::findOne($id)) {
			throw new LoggedException(new NotFoundHttpException());
		}

		/** @var ActiveRecordTrait $model */
		if (Yii::$app->request->post('ajax')) {/* запрос на ajax-валидацию формы */
			return $this->asJson($model->validateModelFromPost());
		}
		$errors = [];
		$posting = $model->updateModelFromPost($errors);

		if (true === $posting) {/* Модель была успешно прогружена */
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

	/**
	 * @return string|Response
	 * @throws Throwable
	 */
	public function actionCreate() {
		$model = new ActiveStorage();
		$errors = [];
		$posting = $model->createModelFromPost($errors);/* switch тут нельзя использовать из-за его нестрогости */
		if (true === $posting) {/* Модель была успешно прогружена */
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
	 * @param int $id
	 * @return Response
	 * @throws Throwable
	 */
	public function actionDelete(int $id):Response {
		if (null === $model = ActiveStorage::findOne($id)) {
			throw new LoggedException(new NotFoundHttpException());
		}
		/** @noinspection PhpUndefinedMethodInspection */
		$model->safeDelete();
		return $this->redirect('index');
	}

}
