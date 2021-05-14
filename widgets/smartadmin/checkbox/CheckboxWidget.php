<?php
declare(strict_types = 1);

namespace app\widgets\smartadmin\checkbox;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\InputWidget as YiiInputWidget;

/**
 * Class CheckboxWidget
 * @package app\widgets\smartadmin\checkbox
 */
class CheckboxWidget extends YiiInputWidget
{
	/**
	 * @var array HTML атрибуты для отрисовки контейнера чекбокса
	 */
	public array $wrapperOptions = ['class' => 'custom-control custom-checkbox'];
	/**
	 * @var array HTML атрибуты для отрисовки input'а
	 */
	public array $checkboxOptions = ['class' => 'custom-control-input', 'label' => false];
	/**
	 * @var array HTML атрибуты для отрисовки label'а
	 */
	public array $labelOptions = ['class' => 'custom-control-label'];

	public function init(): void
	{
		parent::init();

		if ($this->field) {
			$this->field->options      = ['class' => 'form-group text-left'];
			$this->field->template     = Html::tag(
				'div',
				"{input}\n{label}\n{hint}\n{error}",
				$this->wrapperOptions
			);
			$this->field->labelOptions = $this->labelOptions;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function run(): string
	{
		return $this->renderInputHtml('checkbox');
	}

	/**
	 * @inheritdoc
	 */
	protected function renderInputHtml($type): string
	{
		if ($this->hasModel() && $this->field) {
			return Html::activeCheckbox($this->model, $this->attribute, $this->checkboxOptions);
		}

		$render = Html::beginTag('div', $this->wrapperOptions);

		if ($this->hasModel()) {
			$label = Html::activeLabel(
				$this->model,
				$this->attribute,
				$this->labelOptions
			);
		} else {
			//null, т.к. логика отрисовки лейбла для обычного чекбокса отличается
			$this->checkboxOptions['label'] = null;

			$label = Html::label(
				$this->options['label'],
				$this->options['id'],
				$this->labelOptions
			);
		}

		$this->options = ArrayHelper::merge($this->options, $this->checkboxOptions);
		$render .= parent::renderInputHtml($type) . $label;

		$render .= Html::endTag('div');

		return $render;
	}
}