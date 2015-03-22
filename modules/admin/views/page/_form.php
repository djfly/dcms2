<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var app\models\Page $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="page-form">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs">
      <li class="active"><a href="#home" data-toggle="tab"><?= Yii::t('app', 'Page') ?></a></li>
      <li><a href="#seo" data-toggle="tab"><?= Yii::t('app', 'SEO') ?></a></li>
    </ul>
    <br>

    <!-- Tab panes -->
    <div class="tab-content">
        <div class="tab-pane active" id="home">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>
    <?=\djfly\kindeditor\KindEditor::widget([
        'id' => 'page-content',
        'model' => $model,
        'attribute' => 'content',
        'items' => [
            'langType' => Yii::$app->language=="zh-CN"?"zh_CN":Yii::$app->language,
            'height' => '350px',
            'themeType' => 'simple',
            'pagebreakHtml' => Yii::$app->params['pagebreakHtml'],
            'allowImageUpload' => true,
            'allowFileManager' => true,
            'uploadJson' => Url::toRoute('create-img-ajax'),
            'fileManagerJson' => Url::toRoute('post/filemanager'),
            
        ],
    ])?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>
    </div>
    <div class="tab-pane" id="seo">
    <?= $form->field($model, 'seo_title')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'seo_keywords')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'seo_description')->textArea(['rows' => 5]) ?>
    </div>

    

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
