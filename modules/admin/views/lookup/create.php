<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var common\models\Lookup $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
  'modelClass' => 'Lookup',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Lookups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lookup-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
