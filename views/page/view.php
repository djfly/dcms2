<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Menu;
/**
 * @var yii\web\View $this
 */
$this->title = $model->seo_title?$model->seo_title:$model->title;
$this->registerMetaTag(['name' => 'keywords', 'content' => $model->seo_keywords?$model->seo_keywords:""]);
$this->registerMetaTag(['name' => 'description', 'content' => $model->seo_description?$model->seo_description:""]);
?>
<div class="row post-view">
    <div class="col-md-2">
        <h4 class="text-right">页面</h4>
        <?= Menu::widget([
            'options' => ['class' => 'side-menu list-unstyled'],
            'items' => $menuItems,
            'encodeLabels' => false
        ]);?>
    </div>
    <div class="col-md-7">
        <div class="content">
            <h2 class="title"><?= Html::encode($model->title) ?></h2>
            <?= $model->content ?>
        </div>
    </div>
    <div class="col-md-3">
        <div class="side-right">
            <?= \app\widgets\RecommendArticles::widget(['max' => 10]) ?>
            <br>
            <?= \app\widgets\HotArticles::widget(['max' => 10]) ?>
            <br>
            <h4>Tags</h4>
            <?= \app\widgets\TagCloud::widget(['max'=>Yii::$app->params['tagCloudCount']]) ?>
            <br>
            <?= \app\widgets\LinkWidget::widget(['max' => 10]) ?> 
        </div>
    </div>
</div>
