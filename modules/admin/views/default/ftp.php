<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
/**
 * @var yii\web\View $this
 */

$this->title = Yii::t('app', 'Ftp');
$this->params['breadcrumbs'][] = Yii::t('app', 'Ftp');
?>
<div class="user-form">
	<h1><?= Html::encode($this->title) ?></h1>
	<?php $form = ActiveForm::begin(); ?>
	<?= $form->field($model, 'ssl')->inline()->radioList([1 => Yii::t('app', 'yes'), 0 => Yii::t('app', 'no')]) ?>
	<?= $form->field($model, 'host')->textInput() ?>
	<?= $form->field($model, 'port')->textInput() ?>
	<?= $form->field($model, 'username')->textInput() ?>
	<?= $form->field($model, 'password')->textInput() ?>
	<?= $form->field($model, 'pasv')->inline()->radioList([1 => Yii::t('app', 'yes'), 0 => Yii::t('app', 'no')]) ?>
	<?= $form->field($model, 'outTime')->textInput() ?>
	<div class="form-group">
		<?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
	</div>
	<?php ActiveForm::end(); ?>
</div>

