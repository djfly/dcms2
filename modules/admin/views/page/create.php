<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var app\models\Page $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
  'modelClass' => 'Page',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
