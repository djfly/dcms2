<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
/**
 * @var yii\web\View $this
 */

$this->title = Yii::t('app', 'Site Info');
$this->params['breadcrumbs'][] = Yii::t('app', 'Site Info');
$siteinfo=Yii::$app->config->get("siteInfo");
if (isset($siteinfo["closed"]) && $siteinfo["closed"]==1) {
	$this->registerJs('jQuery(".field-infoform-message").hide();');	
}
$this->registerJs('
jQuery("input[name=\"InfoForm[closed]\"][checked]").val(); 
jQuery(document).on("click", "input[name=\"InfoForm[closed]\"]",function(){ if (jQuery("input[name=\"InfoForm[closed]\"]:checked").val()==1) {jQuery(".field-infoform-message").hide();}if (jQuery("input[name=\"InfoForm[closed]\"]:checked").val()==0) {jQuery(".field-infoform-message").show();}
});
');
?>
<div class="info-form">
	<h1><?= Html::encode($this->title) ?></h1>
	<?php $form = ActiveForm::begin(['id' => 'form-siteinfo']); ?>
		<?= $form->field($model, 'siteName') ?>
		<?= $form->field($model, 'siteUrl') ?>
		<?= $form->field($model, 'siteTitle') ?>
		<?= $form->field($model, 'siteKeywords') ?>
		<?= $form->field($model, 'siteDescription')->textArea(['rows' => 3]) ?>
		<?= $form->field($model, 'adminEmail') ?>
		<?= $form->field($model, 'siteCopyright')->textArea(['rows' => 3]) ?>
		<?= $form->field($model, 'statCode')->textArea(['rows' => 3]) ?>
		<?= $form->field($model, 'closed')->inline()->radioList([1 => Yii::t('app', 'open'), 0 => Yii::t('app', 'close')]) ?>
		<?= $form->field($model, 'message')->textArea(['rows' => 3]) ?>
		<div class="form-group">
			<?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary', 'name' => 'siteinfo-button']) ?>
		</div>
	<?php ActiveForm::end(); ?>
</div>

