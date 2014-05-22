<?php

namespace app\widgets;
use app\models\Tag;
use yii\helpers\Url;
use yii\helpers\Html;

class TagCloud extends \yii\bootstrap\Widget
{
	public $title='Tags';
	public $max=20;

	public function init()
	{
		parent::init();
		$tag=new Tag;
		$tags=$tag->findTagWeights($this->max);

		foreach($tags as $tag=>$weight)
		{
			$link=Html::a(Html::encode($tag), ['post/index','tag'=>$tag]);
			echo Html::tag('span', $link, [
				'class'=>'tag',
				'style'=>"font-size:{$weight}pt",
			])."\n";
		}
	}
}