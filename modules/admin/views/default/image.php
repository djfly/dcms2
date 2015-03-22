<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
/**
 * @var yii\web\View $this
 */

$this->title = Yii::t('app', 'Image');
$this->params['breadcrumbs'][] = Yii::t('app', 'Image');
?>
<div class="user-form">
	<h1><?= Html::encode($this->title) ?></h1>
	<?php $form = ActiveForm::begin(); ?>
	<?= $form->field($model, 'watermark')->fileInput() ?>
	<?= $form->field($model, 'watermarkPosition')->inline()->radioList([1 => Yii::t('app', 'left top'), 2 => Yii::t('app', 'right top'), 3 => Yii::t('app', 'left bottom'), 4 => Yii::t('app', 'right bottom'), 5 => Yii::t('app', 'center')]) ?>
	<?= $form->field($model, 'thumbnailWidth')->textInput() ?>
	<?= $form->field($model, 'thumbnailheight')->textInput() ?>
	<?= $form->field($model, 'thumbQuality')->textInput() ?>
	<div class="form-group">
		<?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
	</div>
	<?php ActiveForm::end(); ?>
</div>

