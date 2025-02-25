<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var Model $model
 */

use pozitronik\widgets\BadgeWidget;
use yii\base\Model;
use yii\bootstrap4\Modal;
use yii\web\JsExpression;
use yii\web\View;
use yii\bootstrap4\ActiveForm;

$modelName = $model->formName();
?>
<?php Modal::begin([
	'id' => "{$modelName}-modal-create-new",
	'size' => Modal::SIZE_LARGE,
	'title' => BadgeWidget::widget([
		'items' => $model,
		'subItem' => 'id'
	]),
	'footer' => $this->render('../subviews/editPanelFooter', [
		'model' => $model,
		'form' => "{$modelName}-modal-create"
	]),//post button outside the form
	'options' => [
		'tabindex' => false, // important for Select2 to work properly
		'class' => 'modal-dialog-large'
	]
]); ?>
<?php $form = ActiveForm::begin(
	[
		'id' => "{$modelName}-modal-create",
		'enableAjaxValidation' => true,
		'options' => [
			"onsubmit" => new JsExpression("formSubmitAjax(event)")
		]
	])
?>
<?= $this->render('../subviews/editPanelBody', compact('model', 'form')) ?>
<?php ActiveForm::end(); ?>
<?php Modal::end(); ?>