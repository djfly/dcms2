<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
/**
 * @var yii\web\View $this
 */

$this->title = Yii::t('app', 'Backend Access');
$this->params['breadcrumbs'][] = Yii::t('app', 'Backend Access');
?>
<div class="access-form">
	<h1><?= Html::encode($this->title) ?></h1>
	<?php $form = ActiveForm::begin(['id' => 'form-siteinfo']); ?>
		<?= $form->field($model, 'ipAccess')->textArea(['rows' => 3]) ?>
		<div class="form-group">
			<?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary', 'name' => 'siteinfo-button']) ?>
		</div>
		<small><?= Yii::t('app', 'Above is a whitelist, example:<br>127.0.0.1 <br>113.139.20. * <br>Leave blank for all accessible IP<br><p class="text-info">Current ip: {ip}</p>', ['ip' => Yii::$app->getRequest()->getUserIP()]) ?></small>
	<?php ActiveForm::end(); ?>
</div>

