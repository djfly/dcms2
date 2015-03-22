<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
/**
 * @var yii\web\View $this
 */

$this->title = Yii::t('app', 'Slides');
$this->params['breadcrumbs'][] = Yii::t('app', 'Slides');
?>
<div class="user-form">
	<h1><?= Html::encode($this->title) ?></h1>
	<?php $form = ActiveForm::begin(); ?>
		<?= Html::activeHiddenInput($model, 'links[]') ?>
		<div class="form-group">
        <div class="row">
            <div class="col-md-5">
                <label for="slidesform-links">图片</label>
                <p class="help-block"></p>
            </div>
            <div class="col-md-5">
                <label for="slidesform-links">网址</label>
                <p class="help-block"></p>
            </div>
            <div class="col-md-2">
                <label for="slidesform-links">标题</label>
                <p class="help-block"></p>
            </div>
        </div>
        <?php //var_dump(Yii::$app->config->get("slides"));exit(); ?>
        <?php if (!empty($model->links)): ?>
        <?php foreach ($model->links as $key => $value) :?>
        <div class="row">
            <div class="col-md-5">
                <input type="text" class="form-control" name="SlidesForm[links][image][]" value="<?= $value['image'] ?>">
                <p class="help-block"></p>
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control" name="SlidesForm[links][url][]" value="<?= $value['url'] ?>">
                <p class="help-block"></p>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="SlidesForm[links][alt][]" value="<?= $value['alt'] ?>">
                <p class="help-block"></p>
            </div>
        </div>
        <?php endforeach ?>
        <?php endif ?>
        <div class="row">
            <div class="col-md-5">
                <input type="text" class="form-control" name="SlidesForm[links][image][]">
                <p class="help-block"></p>
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control" name="SlidesForm[links][url][]">
                <p class="help-block"></p>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="SlidesForm[links][alt][]">
                <p class="help-block"></p>
            </div>
        </div>
        <a href="#" id="add-slides">添加 <span class="glyphicon glyphicon-plus"></span></a>
        <br>
    </div>
		<div class="form-group">
			<?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
		</div>
	<?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJs('
jQuery(document).on("click", "#add-slides",function(){ jQuery(this).before(\'<div class="row"> <div class="col-md-5"> <input type="text" class="form-control" name="SlidesForm[links][image][]"> <p class="help-block"></p> </div> <div class="col-md-5"> <input type="text" class="form-control" name="SlidesForm[links][url][]"> <p class="help-block"></p> </div> <div class="col-md-2"> <input type="text" class="form-control" name="SlidesForm[links][alt][]"> <p class="help-block"></p> </div> </div>\');return false; }); 
');

