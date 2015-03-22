<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\widgets\Alert;

/**
 * @var \yii\web\View $this
 * @var string $content
 */
AppAsset::register($this);
$siteInfo = Yii::$app->config->get('siteInfo');
$nav = \app\models\Nav::findOne(1);
$descendants = $nav->children()->all();
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
    <div class="wrap bs-docs-main">
        <?php
            NavBar::begin([
                'brandLabel' => $siteInfo['siteName']?$siteInfo['siteName']:'DCMS',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'bs-docs-nav',
                ],
            ]);
            foreach ($descendants as $key => $value) {
                $menuItems[] = ['label' => $value->name, 'url' => $value->url, 'linkOptions' => ['target' => $value->target?'_blank':'_self']];
            }
            if (Yii::$app->user->isGuest) {
                $menuItems2[] = ['label' => 'Signup', 'url' => ['/site/signup']];
                $menuItems2[] = ['label' => 'Login', 'url' => ['/site/login']];
            } else {
                $menuItems2[] = [
                    'label' => '个人中心',
                    'url' => ['user/view', 'id' => Yii::$app->user->identity->id],
                ];
                $menuItems2[] = [
                    'label' => 'Logout (' . Html::encode(Yii::$app->user->identity->username) . ')',
                    'url' => ['/site/logout'],
                    'linkOptions' => ['data-method' => 'post']
                ];
            }
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav'],
                'encodeLabels' => false,
                'items' => $menuItems,
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => $menuItems2,
            ]);
            NavBar::end();
        ?>

        <div class="container main">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
        </div>

        <footer class="bs-docs-footer">
            <div class="container">
            <p><?= $siteInfo['siteCopyright'] ?></p>
            <p>Powered by <a href="http://www.cmsboom.com/" target="_blank">DCMS</a></p>
            </div>
        </footer>

    </div>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>