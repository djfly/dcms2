<?php
use app\modules\admin\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use app\components\Common;

/**
 * @var \yii\web\View $this
 * @var string $content
 */
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
	<meta charset="<?= Yii::$app->charset ?>"/>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= Html::encode($this->title) ?></title>
	<?php $this->head() ?>
</head>
<body>
	<?php $this->beginBody() ?>
	<div class="wrap">
		<?php
			NavBar::begin([
				'brandLabel' => '<span class="glyphicon glyphicon-glass"></span> DCMS <small class="text-muted">v2.0</small>',
				'brandUrl' => ['/admin/default/index'],
				'options' => [
					'class' => 'navbar-default',
				],
			]);
			
			$menuItems = [
				['label' => \Yii::t('app', 'Site Home'), 'url' => ['/site/index'], 'linkOptions' => ['target' => '_blank']],
				['label' => \Yii::t('app', 'Hello,welcome to use DCMS!'), 'url' => '#'],
			];
			if (Yii::$app->user->isGuest) {
				$menuItems[] = ['label' => \Yii::t('app', 'Login'), 'url' => ['/site/login']];
			} else {
				$menuItems[] = [
					'label' => \Yii::t('app', 'Logout').' (' . Yii::$app->user->identity->username . ')',
					'url' => ['/site/logout'],
					'linkOptions' => ['data-method' => 'post']
				];
			}
			if (Common::getLanguage()) {
				switch (Common::getLanguage()) {
					case 'en':
						$languageLable='<i class="ficon-flag-GB"></i> English';
						break;
					case 'zh-CN':
						$languageLable='<i class="ficon-flag-CN"></i> 简体中文';
						break;
					default:
						$languageLable='<i class="ficon-flag-GB"></i> English';
						break;
				}
				
			}
			$menuItems[] = [
				'label' => Common::getLanguage()?$languageLable:\Yii::t('app', 'choose language'), 
				'url' => '#', 
		        'items' => [
		        	['label' => '<i class="ficon-flag-GB"></i> English', 'url' => ['default/locale', 'language' => 'en']],
		        	['label' => '<i class="ficon-flag-CN"></i> 简体中文', 'url' => ['default/locale', 'language' => 'zh-CN']],
		    	]
		    ];
			echo Nav::widget([
				'options' => ['class' => 'navbar-nav navbar-right'],
				'items' => $menuItems,
				'encodeLabels' => false
			]);
			NavBar::end();
		?>
	
		<!-- <div class="container"> -->
		<?= $content ?>
		<!-- </div> -->
	</div>
	<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
