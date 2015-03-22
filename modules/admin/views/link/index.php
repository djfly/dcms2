<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\models\search\LinkSearch $searchModel
 */

$this->title = Yii::t('app', 'Links');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="link-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create {modelClass}', [
  'modelClass' => 'Link',
]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); 
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'name',
            'url:url',
            'logo',
            'position',
            'target',
            [
                'class' => 'yii\grid\DataColumn',
                'attribute'=>'type',
                'filter' => [0=>Yii::t('app', 'Text'), 1=>Yii::t('app', 'Picture')],
            ],
            [
                'class' => 'yii\grid\DataColumn',
                'attribute'=>'visible',
                'filter' => [1=>Yii::t('app', 'Available') ,0=>Yii::t('app', 'Unavailable')],
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ]
    ]); 
    Pjax::end();?>
</div>
