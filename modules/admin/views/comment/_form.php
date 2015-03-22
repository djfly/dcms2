<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var app\models\Comment $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="comment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'type')->dropDownList(['1' => Yii::t('app', 'Article'), '2' => Yii::t('app', 'Rom')]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'author')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => 255]) ?>
	
	<?= $form->field($model, 'up')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'down')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'status')->textInput(['maxlength' => 255]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
