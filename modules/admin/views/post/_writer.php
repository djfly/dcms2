<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use app\models\Writer;
$writer = new Writer;
$this->registerJs('
function writerList(){
    jQuery("#writer-list").empty();  
    $.getJSON("'. Url::toRoute('writer/all-ajax') .'", function(json){  
        $(json).each(function(i, item) {
            $("<li></li>").html("<a href=\"#\" class=\"\">"+item["name"]+"</a>").appendTo("#writer-list");
        });
    });  
};
jQuery("#choose-writer").on("show.bs.modal", function (e) {
    writerList(); 
});
jQuery(document).on("click", "#writer-list > li > a",function(){ jQuery("#post-writer").prop("value", jQuery(this).text());jQuery("#writer-list > li > a").removeClass("label label-primary");jQuery(this).addClass("label label-primary");jQuery("#choose-writer").modal("hide");return false; });
');
?>
<!-- Modal -->
<div class="modal fade" id="choose-writer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Choose Writer</h4>
      </div>
      <div class="modal-body">
        <ul id="writer-list" class="list-inline">
          
        </ul>
        <?php $form = ActiveForm::begin([
          'action' => Url::toRoute('writer/create-ajax'),
          'beforeSubmit' => new \yii\web\JsExpression('function(form) {
            jQuery("#writer-submit").button("loading");
            jQuery.ajax({
                url: "'. Url::toRoute('writer/create-ajax') .'",
                type: "POST",
                dataType: "json",
                data: form.serialize(),
                success: function(response) {
                    if (response) {
                        jQuery("#post-writer").prop("value", jQuery("#writer-name").prop("value"));
                        writerList(); 
                        jQuery("#writer-name").prop("value", "");
                        jQuery("#writer-url").prop("value", "");
                        jQuery(".alert-success").show();
                        jQuery(".alert-success").fadeOut(4000);
                    }
                    jQuery("#writer-submit").button("reset");
                    return false;
                },
                error: function(response) {
                    jQuery(".alert-danger").show();
                    jQuery(".alert-danger").fadeOut(4000);
                    jQuery("#writer-submit").button("reset");
                    return false;
                }
            });return false;
          }')
        ]); ?>

        <?= $form->field($writer, 'name')->textInput(['maxlength' => 255]) ?>
        <div class="form-group">
          <?= Html::submitButton($writer->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $writer->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'data-loading-text'=>"submitting...", 'id' => 'writer-submit']) ?>
        </div>
      <?php ActiveForm::end(); ?>
      <div class="alert alert-dismissable alert-success" style="display:none">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <strong>Success!</strong> Your writer add is successfully.</div>
      </div>
      <div class="alert alert-dismissable alert-danger" style="display:none">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <strong>Error!</strong> Your writer add is failed, Try again.</div>
      </div>
      
    </div>
  </div>
</div>