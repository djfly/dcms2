<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var app\models\search\CommentSearch $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="comment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'parent_id') ?>

    <?= $form->field($model, 'content') ?>

    <?= $form->field($model, 'status') ?>

    <?= $form->field($model, 'create_time') ?>

    <?php // echo $form->field($model, 'up') ?>

    <?php // echo $form->field($model, 'down') ?>

    <?php // echo $form->field($model, 'author') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'url') ?>

    <?php // echo $form->field($model, 'ip') ?>

    <?php // echo $form->field($model, 'post_id') ?>

    <?php // echo $form->field($model, 'user_id') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
