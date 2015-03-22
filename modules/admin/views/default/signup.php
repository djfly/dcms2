<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
/**
 * @var yii\web\View $this
 */

$this->title = Yii::t('app', 'Signup');
$this->params['breadcrumbs'][] = Yii::t('app', 'Signup');
$signup=Yii::$app->config->get("signup");
if (isset($signup["allowSignup"]) && $signup["allowSignup"]==1) {
	$this->registerJs('jQuery(".field-signupform-message").hide();');	
}
$this->registerJs('
jQuery("input[name=\"SignupForm[allowSignup]\"][checked]").val(); 
jQuery(document).on("click", "input[name=\"SignupForm[allowSignup]\"]",function(){ if (jQuery("input[name=\"SignupForm[allowSignup]\"]:checked").val()==1) {jQuery(".field-signupform-message").hide();}if (jQuery("input[name=\"SignupForm[allowSignup]\"]:checked").val()==0) {jQuery(".field-signupform-message").show();}
});
');
?>
<div class="user-form">
	<h1><?= Html::encode($this->title) ?></h1>
	<?php $form = ActiveForm::begin(); ?>
		<?= $form->field($model, 'allowSignup')->inline()->radioList([1 => Yii::t('app', 'open'), 0 => Yii::t('app', 'close')]) ?>
		<?= $form->field($model, 'message')->textArea(['rows' => 3]) ?>
		<?= $form->field($model, 'holdUser')->textArea(['rows' => 3]) ?>
		<?= $form->field($model, 'signupVerifyWay')->radioList([1 => Yii::t('app', 'Normal'), 2 => Yii::t('app', 'Approve'), 3 => Yii::t('app', 'Verify By Email')]) ?>
		<div class="form-group">
			<?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
		</div>
	<?php ActiveForm::end(); ?>
</div>

