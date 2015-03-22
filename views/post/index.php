<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use app\widgets\CategoryWidget;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\models\search\PageSearch $searchModel
 */
if ($category) {
$this->title = $category->seo_title?$category->seo_title:$category->name;
$this->registerMetaTag(['name' => 'keywords', 'content' => $category->seo_keywords?$category->seo_keywords:""]);
$this->registerMetaTag(['name' => 'description', 'content' => $category->seo_description?$category->seo_description:$category->summary]);
}
?>
<div class="row post-list">
    <div class="col-md-2">
        <?= CategoryWidget::widget() ?>
    </div>
    <div class="col-md-7">
        <div class="content">
            <?php if ($category): ?>
            <h2 class="title"><?= Html::encode($category->name) ?></h2> 
            <?php endif ?>
            <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'itemOptions' => ['class' => 'item'],
                'itemView' => function ($model, $key, $index, $widget) {
                    return Html::a(Html::encode($model->title), ['view', 'id' => $model->id]);
                },
            ]) ?>
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
