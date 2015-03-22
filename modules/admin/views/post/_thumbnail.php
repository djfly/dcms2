<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use app\models\ThumbnailForm;
use newerton\jcrop\jCropAsset;
use yii\web\jQueryAsset;
$thumbnail = new ThumbnailForm;
jCropAsset::register($this);
$this->registerJs('
function imageList(){
    jQuery("#image-list").empty();  
    $.getJSON("'. Url::toRoute(['upload-ajax', 'id' => $model->id]) .'", function(json){  
        $(json).each(function(i, item) {
            $("<li></li>").html("<a href=\"#\"><img src=\""+item["path"]+"\" width=\"190\" class=\"img-thumbnail\"></a>").appendTo("#image-list");
        });
    });  
};
jQuery("#choose-thumbnail").on("show.bs.modal", function (e) {
    imageList(); 
});
var jcrop_api;


function showCoords(c)
{
  $("#thumbnailform-x1").val(c.x);
  $("#thumbnailform-y1").val(c.y);
  $("#thumbnailform-x2").val(c.x2);
  $("#thumbnailform-y2").val(c.y2);
  $("#thumbnailform-w").val(c.w);
  $("#thumbnailform-h").val(c.h);
};

function clearCoords()
{
  $("#coords input").val("");
};

function initJcrop()
{

  $("#target").Jcrop({
    onChange:   showCoords,
    onSelect:   showCoords,
    onRelease:  clearCoords
  },function(){

    jcrop_api = this;

  });

};

initJcrop();

$("#coords").on("change","input",function(e){
  var x1 = $("#thumbnailform-x1").val(),
      y1 = $("#thumbnailform-y1").val(),
      w = $("#thumbnailform-w").val(),
      h = $("#thumbnailform-h").val();
  jcrop_api.setSelect([x1,y1,w,h]);
});

jQuery(document).on("click", "#image-list > li > a",function(){ jcrop_api.setImage(jQuery(this).children("img").attr("src"));jQuery("#thumbnailform-image").attr("src",jQuery(this).children("img").attr("src"));jQuery("#target").attr("src",jQuery(this).children("img").attr("src"));return false; });
jQuery("#original").click(function(){ jQuery("#post-thumbnail").val(jQuery("#target").attr("src"));jQuery("#thumbnail").attr("src",jQuery("#target").attr("src"));jQuery("#choose-thumbnail").modal("hide"); });

KindEditor.ready(function(K) {

  var editor = K.editor({
    fileManagerJson : "'.Url::toRoute('post/filemanager').'",
    langType : "'.Yii::$app->language.'",
    "uploadJson" : "'.Url::toRoute('create-img-ajax').'"
  });

  K("#filemanager").click(function() {
    editor.loadPlugin("filemanager", function() {
      editor.plugin.filemanagerDialog({
        viewType : "VIEW",
        dirName : "image",
        clickFn : function(url, title) {
          K("#url").val(url);
          jcrop_api.setImage(url);
          K("#target").attr("src",url);
          editor.hideDialog();
        }
      });
    });
  });

  K("#image3").click(function() {
    editor.loadPlugin("image", function() {
      editor.plugin.imageDialog({
        showRemote : false,
        imageUrl : K("#url").val(),
        clickFn : function(url, title, width, height, border, align) {
          K("#url").val(url);
          jcrop_api.setImage(url);
          K("#target").attr("src",url);
          editor.hideDialog();
        }
      });
    });
  });
});
');
?>
<!-- Modal -->
<div class="modal fade" id="choose-thumbnail" tabindex="-1" role="dialog" aria-labelledby="thumbnailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="thumbnailModalLabel">Thumbnail</h4>
      </div>
      <div class="modal-body">
        <img src="<?= $model->thumbnail ?>" id="target" alt="[Jcrop Example]" />
        <?php $form = ActiveForm::begin([
          'options' => ['id' => 'coords'],
          'action' => Url::toRoute('crop-ajax'),
          'beforeSubmit' => new \yii\web\JsExpression('function(form) {
            jQuery("#image-submit").button("loading");
            jQuery("#thumbnailform-image").val(jQuery("#target").attr("src"));
            if ($("#thumbnailform-w").val().length == 0) {
              alert("Please select a crop region then press submit.");
              jQuery("#image-submit").button("reset");
              return false;
            }
            jQuery.ajax({
                url: "'. Url::toRoute('crop-ajax') .'",
                type: "POST",
                dataType: "json",
                data: form.serialize(),
                success: function(response) {
                    if (response) {
                        jQuery("#post-thumbnail").val(response);jQuery("#thumbnail").attr("src",response);jQuery("#choose-thumbnail").modal("hide");
                    }
                    jQuery("#image-submit").button("reset");
                    // $("#choose-thumbnail").modal("hide");
                    return false;
                },
                error: function(response) {
                    jQuery(".alert-danger").show();
                    jQuery(".alert-danger").fadeOut(4000);
                    jQuery("#image-submit").button("reset");
                    return false;
                }
            });return false;
          }')
        ]); ?>
        <div class="form-group">
            <div class="row">
                <div class="col-md-2">
                    <?= $form->field($thumbnail, 'w')->textInput(['maxlength' => 255]) ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($thumbnail, 'h')->textInput(['maxlength' => 255]) ?>
                </div>
            </div>
        </div>
        
        <?= Html::activeHiddenInput($thumbnail, 'image') ?>
        <?= Html::activeHiddenInput($thumbnail, 'x1') ?>
        <?= Html::activeHiddenInput($thumbnail, 'y1') ?>
        <?= Html::activeHiddenInput($thumbnail, 'x2') ?>
        <?= Html::activeHiddenInput($thumbnail, 'y2') ?>
        <div class="form-group">
          <?= Html::submitButton(Yii::t('app', 'Crop Image'), ['class' => 'btn btn-success', 'data-loading-text'=>"submitting...", 'id' => 'image-submit']) ?>
          <button id="original" type="button" class="btn btn-primary">use original image</button>
        </div>
      <?php ActiveForm::end(); ?>
      <hr><h5>Choose existing images or brower server or upload </h5>
        <input type="text" id="url" value=""  readonly="readonly"/>
        <input type="button" id="image3" value="<?= Yii::t('app', 'Upload') ?>" /> 
        <input type="button" id="filemanager" value="<?= Yii::t('app', 'Browse')?>" />
        <br><br>
        <ul id="image-list" class="list-inline">
          
        </ul>
      <div class="alert alert-dismissable alert-success" style="display:none">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <strong>Success!</strong> Your thumbnail add is successfully.</div>
      </div>
      <div class="alert alert-dismissable alert-danger" style="display:none">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <strong>Error!</strong> Your thumbnail add is failed, Try again.</div>
      </div>
      
    </div>
  </div>
</div>