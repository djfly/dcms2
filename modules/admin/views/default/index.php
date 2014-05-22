<?php
/**
 * @var yii\web\View $this
 */
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Post;
use app\models\Comment;
use app\models\Category;
use app\models\Page;
use app\models\User;

$this->title = 'Overview - DCMS2.0 - backend';
?>

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title"><?= Yii::t('app', 'Overview') ?></h3>
  </div>
  <div class="panel-body">
    文章数量：<?= Post::find()->count()?>  评论数量：<?= Comment::find()->count()?> 分类数量：<?= Category::find()->count()?> 单页数量：<?= Page::find()->count()?> 用户：<?= User::find()->count()?>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">最新评论！</h3>
  </div>
  <div class="panel-body">
  <ul class="list-unstyled">
  	<?php foreach ($comments as $key => $value): ?>
  	<li><a href="<?= Url::to(['comment/view', 'id' => $value->id]) ?>"><?= Html::encode($value->content) ?></a></li>
  	<?php endforeach ?>
  </ul>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">system</h3>
  </div>
  <div class="panel-body">
    
    powerby: DCMS v2.0 <br>
	author: ff <br>
	official: <a href="http://www.cmsboom.com/" target="_blank">website</a><br>
  </div>
</div>

