<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use app\models\Source;
$source = new Source;
$this->registerJs('
function sourceList(){
    jQuery("#source-list").empty();  
    $.getJSON("'. Url::toRoute('source/all-ajax') .'", function(json){  
        $(json).each(function(i, item) {
            $("<li></li>").html("<a href=\"#\" class=\"\">"+item["name"]+"</a>").appendTo("#source-list");
        });
    });  
};
jQuery("#choose-source").on("show.bs.modal", function (e) {
    sourceList(); 
});
jQuery(document).on("click", "#source-list > li > a",function(){ jQuery("#post-source").prop("value", jQuery(this).text());jQuery("#source-list > li > a").removeClass("label label-primary");jQuery(this).addClass("label label-primary");jQuery("#choose-source").modal("hide");return false; });
');
?>
<!-- Modal -->
<div class="modal fade" id="choose-source" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Choose Source</h4>
      </div>
      <div class="modal-body">
        <ul id="source-list" class="list-inline">
          
        </ul>
        <?php $form = ActiveForm::begin([
          'action' => Url::toRoute('source/create-ajax'),
          'beforeSubmit' => new \yii\web\JsExpression('function(form) {
            jQuery("#source-submit").button("loading");
            jQuery.ajax({
                url: "'. Url::toRoute('source/create-ajax') .'",
                type: "POST",
                dataType: "json",
                data: form.serialize(),
                success: function(response) {
                    if (response) {
                        jQuery("#post-source").prop("value", jQuery("#source-name").prop("value"));
                        sourceList(); 
                        jQuery("#source-name").prop("value", "");
                        jQuery("#source-url").prop("value", "");
                        jQuery(".alert-success").show();
                        jQuery(".alert-success").fadeOut(4000);
                    }
                    jQuery("#source-submit").button("reset");
                    return false;
                },
                error: function(response) {
                    jQuery(".alert-danger").show();
                    jQuery(".alert-danger").fadeOut(4000);
                    jQuery("#source-submit").button("reset");
                    return false;
                }
            });return false;
          }')
        ]); ?>

        <?= $form->field($source, 'name')->textInput(['maxlength' => 255]) ?>

        <?= $form->field($source, 'url')->textInput(['maxlength' => 255]) ?>
        <div class="form-group">
          <?= Html::submitButton($source->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $source->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'data-loading-text'=>"submitting...", 'id' => 'source-submit']) ?>
        </div>
      <?php ActiveForm::end(); ?>
      <div class="alert alert-dismissable alert-success" style="display:none">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <strong>Success!</strong> Your source add is successfully.</div>
      </div>
      <div class="alert alert-dismissable alert-danger" style="display:none">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <strong>Error!</strong> Your source add is failed, Try again.</div>
      </div>
      
    </div>
  </div>
</div>