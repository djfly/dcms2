<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Comment;

/**
 * @var yii\web\View $this
 * @var app\models\Comment $model
 * @var yii\widgets\ActiveForm $form
 */
$model = new Comment;
?>

<div class="comment-form">

    <?php $form = ActiveForm::begin([
            'action' => Url::toRoute(['post/comment-ajax', 'id' => $id]),
            'beforeSubmit' => new \yii\web\JsExpression('function(form) {
            jQuery(".comment-submit").button("loading");
            jQuery.ajax({
                url: "'. Url::toRoute(['post/comment-ajax', 'id' => $id]) .'",
                type: "POST",
                dataType: "json",
                data: form.serialize(),
                success: function(response) {
                    if (response!=0) {
                        $(".comment-list .media-list").prepend(response);
                    }else{
                       alert("提交失败"); 
                    }
                    jQuery(".comment-submit").button("reset");
                    return false;
                },
                error: function(response) {
                    jQuery(".comment-submit").button("reset");
                    return false;
                }
            });return false;
          }')
        ]); ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6])->label(false)?>

    <div class="form-group text-right">
        <?= Html::submitButton('&nbsp;&nbsp;&nbsp;&nbsp;发表&nbsp;&nbsp;&nbsp;&nbsp;', ['class' => 'btn btn-default comment-submit', 'data-loading-text'=>"submitting..."]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
