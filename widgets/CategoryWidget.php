<?php

namespace app\widgets;
use Yii;
use app\models\Category;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Menu;

class CategoryWidget extends \yii\bootstrap\Widget
{
	public function init()
	{
		parent::init();
		$category = Category::findOne(83);
		$descendants = $category->children()->all();
		$menuItems=[];
		foreach($descendants as $key=>$value)
		{
			$menuItems[] = ['label' => $value->name, 'url' => ['post/index', 'id' => $value->id]];
		}
		$menuItems[] = ['label' => '未分类', 'url' => ['post/index', 'id' => 0]];
		echo '<h4 class="text-right">分类</h4>';
		echo Menu::widget([
			'options' => ['class' => 'side-menu list-unstyled'],
			'items' => $menuItems,
			'encodeLabels' => false
		]);
	}
}