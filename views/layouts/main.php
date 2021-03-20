<?php
declare(strict_types = 1);

/**
 * @var View $this
 * @var string $content
 */

use app\assets\AppAsset;
use app\models\sys\users\CurrentUser;
use app\widgets\search\SearchWidget;
use pozitronik\helpers\Utils;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

AppAsset::register($this);
?>
<!DOCTYPE html>
<?php $this->beginPage(); ?>
<html lang="<?= Yii::$app->language ?>">
<head>
	<meta charset="<?= Yii::$app->charset ?>"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="commit" content="<?= Utils::LastCommit() ?>">
	<?= Html::csrfMetaTags() ?>
	<title><?= $this->title ?></title>
	<?php $this->head(); ?>
</head>
<body>
<?php $this->beginBody(); ?>
<div class="navigation">
	<?php NavBar::begin([
		'renderInnerContainer' => false,
		'options' => [
			'class' => 'navbar'
		]
	]); ?>
	<?= Nav::widget([
		'items' => [
			[
				'label' => 'Домой',
				'url' => CurrentUser::homeUrl()
			],
			[
				'label' => 'Пользователи',
				'items' => [
					[
						'label' => 'Все',
						'url' => ['users/index']
					]
				],
			],
			SearchWidget::widget(),
			[
				'label' => CurrentUser::model()->username,
				'options' => [
					'class' => 'pull-right'
				],
				'items' => [
					'<li class="dropdown-header">'.CurrentUser::model()->comment.'</li>',
					[
						'label' => "Профиль",
						'url' => '#',
						'options' => [
							'onclick' => new JsExpression('alert("!@")')
						]
					],
					'<li class="divider"></li>',
					[
						'label' => 'Выход',
						'url' => Url::to(['site/logout']),
						'options' => [
							'class' => 'pull-right'
						]
					],
				],
			],

		],
		'options' => [
			'class' => 'nav-pills pull-left'
		]
	]) ?>
	<?php NavBar::end(); ?>
</div>
<div class="clearfix"></div>
<div class="boxed">
	<div id="content-container">
		<div id="page-content">
			<?= $content ?>
		</div>
	</div>
</div>

<?php $this->endBody(); ?>
</body>
<?php $this->endPage(); ?>
</html>