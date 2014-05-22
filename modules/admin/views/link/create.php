<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var common\models\Link $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
  'modelClass' => 'Link',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Links'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="link-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
