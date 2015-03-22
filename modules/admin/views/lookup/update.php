<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var common\models\Lookup $model
 */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
  'modelClass' => 'Lookup',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Lookups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="lookup-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
