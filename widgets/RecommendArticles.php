<?php

namespace app\widgets;
use app\models\Post;
use yii\helpers\Url;
use yii\helpers\Html;

class RecommendArticles extends \yii\bootstrap\Widget
{
	public $max=10;
	public function init()
	{
		parent::init();
		$hots = Post::find()->where('status=1 AND type=3')->orderBy(['views' => SORT_DESC])->limit($this->max)->all();

		echo '<h4>推荐文章</h4>
            <ul class="media-list clearfix">';
		foreach($hots as $key=>$value)
		{
			$thumbnail=Html::a('<img class="media-object" src="'.$value->thumbnail.'" alt="'.$value->title.'" style="max-width:120px;">', ['post/view','id'=>$value->id], ['class' => 'pull-left']);
			$link=Html::a(Html::encode($value->title), ['post/view','id'=>$value->id], ['class' => 'media-heading']);
			$link=Html::tag('div', $link, ['class' => 'media-body']);
			echo Html::tag('li',$thumbnail.$link, ['class' => 'media']);
		}
		echo '</ul>';
	}
}