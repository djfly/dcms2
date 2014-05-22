<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use app\models\Category;

/**
 * @var yii\web\View $this
 * @var app\models\Category $model
 * @var yii\widgets\ActiveForm $form
 */

$category = new Category;
$Categories = $category->roots()->all();
$level = 0;

$items[0] = Yii::t('app', 'Please select the parent node');
foreach ($Categories as $key => $value){
	
    $items[$value->attributes['id']]=$value->attributes['name'];
    $children = $value->descendants()->all();
    foreach ($children as $child){
        $string = '  ';
        $string .= str_repeat('│  ', $child->level - $level - 1);
        if ($child->isLeaf() && !$child->next()->one()) {
            $string .= '└';
        } else {

            $string .= '├';
        }
        $string .= '─' . $child->name;
        $items[$child->id]=$string;
    }
}

if (!$model->isNewRecord) {
    $parent = $model->parent()->one();
}

?>

<div class="category-form">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs">
      <li class="active"><a href="#home" data-toggle="tab"><?= Yii::t('app', 'Category') ?></a></li>
      <li><a href="#seo" data-toggle="tab"><?= Yii::t('app', 'SEO') ?></a></li>
    </ul>
    <br>

    <!-- Tab panes -->
    <div class="tab-content">
        <div class="tab-pane active" id="home">
    <?php $form = ActiveForm::begin([
        'options'=>['enctype'=>'multipart/form-data']
    ]); ?>
	
	<?php if (!$model->isNewRecord && isset($parent)) : ?>
	<?php $model->parent=$parent->id;?>
    <?= $form->field($model, 'parent')->dropDownList($items) ?>
	<?php else:?>
    <?= $form->field($model, 'parent')->dropDownList($items) ?>
	<?php endif?>
    
    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'summary')->textArea(['rows' => 6]) ?>
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
