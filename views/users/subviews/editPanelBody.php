<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var PermissionsCollections $model
 * @var ActiveForm $form
 */

use app\models\sys\permissions\active_record\Permissions;
use app\models\sys\permissions\active_record\PermissionsCollections;
use dosamigos\multiselect\MultiSelectListBox;
use kartik\form\ActiveForm;
use pozitronik\helpers\ArrayHelper;
use yii\web\View;

$this->registerCss(".ms-container {width:100%}");

?>

<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'username')->textInput() ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'comment')->textarea() ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'relatedPermissions')->widget(MultiSelectListBox::class, [
			'options' => [
				'multiple' => true,
			],
			'data' => ArrayHelper::map(Permissions::find()->all(), 'id', 'name'),
		]) ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?= $form->field($model, 'relatedPermissionsCollections')->widget(MultiSelectListBox::class, [
			'options' => [
				'multiple' => true,
			],
			'data' => ArrayHelper::map(PermissionsCollections::find()->all(), 'id', 'name'),
		]) ?>
	</div>
</div>

