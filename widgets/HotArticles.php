<?php

namespace app\widgets;
use app\models\Post;
use yii\helpers\Url;
use yii\helpers\Html;

class HotArticles extends \yii\bootstrap\Widget
{
	public $max=10;
	public function init()
	{
		parent::init();
		$hots = Post::find()->where('status=1')->orderBy(['views' => SORT_DESC])->limit($this->max)->all();
		echo '<h4>热门文章</h4>
            <table class="hot-list">';
		foreach($hots as $key=>$value)
		{
			$i=$key+1;
			$link=Html::a(Html::encode($value->title), ['post/view','id'=>$value->id]);
			echo Html::tag('tr', '<td><i>'.$i.'.&nbsp;</i></td><td>'.$link."</td>");
		}
		echo '</table>';
	}
}