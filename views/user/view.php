<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\Alert;

?>
<?= Alert::widget() ?>
<div class="row user-view">
    <div class="col-md-2">
        <h4 class="text-right">个人中心</h4>
        <ul class="side-menu list-unstyled"><li>资料修改</li></ul>
    </div>
    <div class="col-md-7">
        <?php $form = ActiveForm::begin([
            'options'=>['enctype'=>'multipart/form-data']
        ]); ?>
        <?php if ($model->avatar) :?>
        <img src="<?=$model->avatar?>" width="100" height="100">
        <?php else : ?>
        <img src="<?= Yii::$app->homeUrl?>images/default.jpg" width="100" height="100">
        <?php endif ?>
        <?= $form->field($userform, 'avatar')->fileInput() ?>
        <?= $form->field($userform, 'username')->textInput() ?>
        <?= $form->field($userform, 'email')->textInput() ?>
        <?= $form->field($userform, 'password')->passwordInput() ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <div class="col-md-3">
        
    </div>
</div>
