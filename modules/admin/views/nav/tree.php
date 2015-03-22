<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Nav;
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\models\search\CategorySearch $searchModel
 */

$this->title = Yii::t('app', 'Navs Tree');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="nav-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Nav'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php
        $category = new Nav;
        $roots=$category->roots()->all();
        foreach ($roots as $key => $root) {
            $categories = Nav::find()->where(['root' => $root->id])->orderBy('lft')->all();
            $level = 0;
            foreach ($categories as $n => $category)
            {
                if ($category->level == $level) {
                    echo Html::endTag('li') . "\n";
                } elseif ($category->level > $level) {
                    echo Html::beginTag('ul') . "\n";
                } else {
                    echo Html::endTag('li') . "\n";

                    for ($i = $level - $category->level; $i; $i--) {
                        echo Html::endTag('ul') . "\n";
                        echo Html::endTag('li') . "\n";
                    }
                }

                echo Html::beginTag('li');
                echo Html::encode($category->name).'&nbsp;<span class="text-muted">(';
                echo Html::encode($category->id).')</span>&nbsp;&nbsp;';
                echo Html::a('<span class="glyphicon glyphicon-arrow-up"></span>', ['move', 'id' => $category->id, 'updown' => 'up']).'&nbsp;&nbsp;';
                echo Html::a('<span class="glyphicon glyphicon-arrow-down"></span>', ['move', 'id' => $category->id, 'updown' => 'down']).'&nbsp;&nbsp;';
                echo Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['view', 'id' => $category->id]).'&nbsp;&nbsp;';
                echo Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['update', 'id' => $category->id]).'&nbsp;&nbsp;';
                echo Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete', 'id' => $category->id], [
                        'title' => Yii::t('app', 'Delete'),
                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]);
                $level = $category->level;
            }

            for ($i = $level; $i; $i--) {
                echo Html::endTag('li') . "\n";
                echo Html::endTag('ul') . "\n";
            }
        }
    ?>
</div>
