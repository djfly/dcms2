<?php

namespace app\widgets;
use app\models\Link;
use yii\helpers\Url;
use yii\helpers\Html;

class LinkWidget extends \yii\bootstrap\Widget
{
	public $max=10;
	public function init()
	{
		parent::init();
		$links = Link::find()->where('visible=1')->orderBy(['position' => SORT_DESC])->limit($this->max)->all();
		echo '<h4>友情链接</h4><ul class="list-unstyled">';
		foreach($links as $key=>$value)
		{
			$link=Html::a(Html::encode($value->name), $value->url);
			echo Html::tag('li', $link);
		}
		echo "</ul>";
	}
}