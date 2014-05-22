<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var common\models\Link $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="link-form">

    <?php $form = ActiveForm::begin([
        'options'=>['enctype'=>'multipart/form-data']
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => 255]) ?>
    <?php 
    if (!$model->isNewRecord && !empty($model->logo)) {
        echo '<div class="form-group"><label class="control-label" for="link-logo">'.Yii::t('app', 'Preview').'</label>'.Html::img($model->logo).'</div>';
    }
    ?>
    <?= $form->field($model, 'logo')->fileInput() ?>

    <?= $form->field($model, 'target')->dropDownList(['1' => Yii::t('app', '_blank'), '0' => Yii::t('app', '_self')]) ?>

    <?= $form->field($model, 'type')->dropDownList(['0' => Yii::t('app', 'Text'), '1' => Yii::t('app', 'Picture')]) ?>
    <?php $model->position=0; ?>
    <?= $form->field($model, 'position')->textInput() ?>

    <?= $form->field($model, 'visible')->dropDownList(['1' => Yii::t('app', 'Available'), '0' => Yii::t('app', 'Unavailable')]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
