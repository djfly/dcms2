<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Lookup;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\models\search\PostSearch $searchModel
 */

$this->title = Yii::t('app', 'Posts');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-index">
    
    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create {modelClass}', [
  'modelClass' => 'Post',
]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'category_id',
            'title',
            // 'url:url',
            // 'thumbnail',
            [
                'attribute'=>'type',
                'value' => function ($data) {
                    return Lookup::item("{{post}}type",$data->type);
                },
                'filter' => Lookup::items("{{post}}type"),
            ],
            // 'summary',
            // 'source',
            // 'writer',
            // 'content:ntext',
            // 'tags',
            // 'seo_title',
            // 'seo_keywords',
            // 'seo_description',
            'published_at:date',
            'views',
            'likes',
            'comment_count',
            // 'disallow_comment',
            [
                'attribute'=>'status',
                'value' => function ($data) {
                    return Lookup::item("{{post}}status",$data->status);
                },
                'filter' => Lookup::items("{{post}}status"),
            ],
            // 'created_by',
            // 'updated_by',
            // 'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
