<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\widgets\CategoryWidget;

/**
 * @var yii\web\View $this
 */
$siteInfo = Yii::$app->config->get('siteInfo');
$this->title = $siteInfo['siteTitle'];
$this->registerMetaTag(['name' => 'keywords', 'content' => $siteInfo['siteKeywords']], 'meta-keywords');
$this->registerMetaTag(['name' => 'description', 'content' => $siteInfo['siteDescription']], 'meta-description');
?>
<div class="row site-index">
    <div class="col-md-2">
        <?= CategoryWidget::widget() ?>
    </div>
    <div class="col-md-7">
        <?php if ($headline): ?>
        <div class="headline">
            <h3><a href="<?= Url::to(['post/view', 'id' => $headline->id]) ?>"><?= Html::encode($headline->title) ?></a></h3>
            <p><?= Html::encode($headline->MakeSummary) ?></p>
            <a href="<?= Url::to(['post/view', 'id' => $headline->id]) ?>"><img src="<?= $headline->thumbnail ?>" alt="<?= Html::encode($headline->title) ?>" style="width:100%;"></a>
        </div>
        <?php endif ?>
        <?php if ($recommend[0]): ?>
        <div class="recommend clearfix">
            <div class="pull-left" style="width:48%">
                <h4><a href="<?= Url::to(['post/view', 'id' => $recommend[0]['id']]) ?>"><?= Html::encode($recommend[0]['title']) ?></a></h4>
                <a href="<?= Url::to(['post/view', 'id' => $recommend[0]['id']]) ?>"><img src="<?= $recommend[0]['thumbnail'] ?>" alt="<?= Html::encode($recommend[0]['title']) ?>" style="width:280px;"></a>
                <p><?= Html::encode($recommend[0]->MakeSummary) ?></p>
            </div>
            <?php if ($recommend[1]): ?>
            <div class="pull-right" style="width:48%">
                <h4><a href="<?= Url::to(['post/view', 'id' => $recommend[1]['id']]) ?>"><?= Html::encode($recommend[1]['title']) ?></a></h4>
                <a href="<?= Url::to(['post/view', 'id' => $recommend[1]['id']]) ?>"><img src="<?= $recommend[1]['thumbnail'] ?>" alt="<?= Html::encode($recommend[1]['title']) ?>" style="width:280px;"></a>
                <p><?= Html::encode($recommend[1]->MakeSummary) ?></p>
            </div>
            <?php endif ?>
            <?php endif ?>
        </div>
        <h2 class="lastest">latest news</h2>
        <ul class="media-list">
        <?php foreach ($posts as $key => $value): ?>
          <li class="media">
            <div class="info clearfix">
                <div class="topic pull-left">
                <span><a href="<?= $value->category?Url::to(['post/index', 'id' => $value->category->id]):"未分类" ?>"><?= $value->category?$value->category->name:"未分类" ?></a> / <?= Html::encode($value->tags) ?>
                </span>
                </div>
                <div class="postmeta pull-right"><a href="<?= Url::to(['user/view', 'username' => $value->writer]) ?>" class="" target="_blank"><?= Html::encode($value->writer) ?></a> • <span class="timeago" title="<?= date("Y-m-d H:i:s", $value->published_at) ?>"><?= $value->MakeTime ?></span> </div>
            </div>
            <a class="pull-left" href="<?= Url::to(['post/view', 'id' => $value->id]) ?>">
              <img class="media-object" src="<?= $value->thumbnail ?>" alt="<?= $value->title ?>" style="width:180px;height:120px;">
            </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="<?= Url::to(['post/view', 'id' => $value->id]) ?>"><?= Html::encode($value->title) ?></a></h4>
              <p><?= Html::encode($value->MakeSummary) ?></p>
            </div>
          </li>
        <?php endforeach ?>
        </ul>
        <?= LinkPager::widget(['pagination' => $pages, 'prevPageLabel' => '&larr; 上一页', 'nextPageLabel' => '下一页 &rarr;', 'prevPageCssClass' => 'previous', 'options' => ['class' => 'pager'], 'maxButtonCount' => 0]) ?> 
    </div>
    <div class="col-md-3">
        <div class="side-right">
            <?= \app\widgets\RecommendArticles::widget(['max' => 10]) ?>
            <br>
            <?= \app\widgets\HotArticles::widget(['max' => 10]) ?>
            <br>
            <h4>Tags</h4>
            <?= \app\widgets\TagCloud::widget(['max'=>Yii::$app->params['tagCloudCount']]) ?>
            <br>
            <?= \app\widgets\LinkWidget::widget(['max' => 10]) ?> 
        </div>
    </div>
</div>
