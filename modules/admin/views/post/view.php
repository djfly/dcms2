<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Lookup;

/**
 * @var yii\web\View $this
 * @var app\models\Post $model
 */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Posts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'category_id',
            'title',
            [
                'label'=> Yii::t('app', 'Type'),
                'value'=>Lookup::item("{{post}}type",$model->type),
            ],
            'thumbnail',
            'url:url',
            'summary',
            'source',
            'writer',
            'content:ntext',
            'tags',
            'seo_title',
            'seo_keywords',
            'seo_description',
            'published_at',
            'views',
            'likes',
            'comment_count',
            'disallow_comment',
            'status',
            [
                'label'=> Yii::t('app', 'Status'),
                'value'=>Lookup::item("{{post}}status",$model->status),
            ],
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
