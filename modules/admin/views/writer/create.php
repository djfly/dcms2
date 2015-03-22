<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var app\models\Writer $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
  'modelClass' => 'Writer',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Writers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="writer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
