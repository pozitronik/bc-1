<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var Users $model
 */

use app\models\sys\users\Users;
use yii\bootstrap\Modal;
use yii\web\View;
use yii\widgets\ActiveForm;

?>
<?php Modal::begin([
	'id' => "{$model->formName()}-modal-create-new",
	'size' => Modal::SIZE_LARGE,
	'header' => 'Новый пользователь',
	'footer' => $this->render('../subviews/editPanelFooter', [
		'model' => $model,
		'form' => "{$model->formName()}-modal-create"
	]),//post button outside the form
	'clientOptions' => [
		'backdrop' => true
	],
	'options' => [
		'class' => 'modal-dialog-large',
	]
]); ?>
<?php $form = ActiveForm::begin(['id' => "{$model->formName()}-modal-create"]) ?>
<?= $this->render('../subviews/editPanelBody', compact('model', 'form')) ?>
<?php ActiveForm::end(); ?>
<?php Modal::end(); ?>