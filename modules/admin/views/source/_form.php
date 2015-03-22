<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/**
 * @var yii\web\View $this
 * @var app\models\Source $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="source-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => 255]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
