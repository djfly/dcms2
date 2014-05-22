<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\web\JqueryAsset;
use app\widgets\CategoryWidget;

/**
 * @var yii\web\View $this
 */
$this->title = $model->seo_title?$model->seo_title:$model->title;
$this->registerMetaTag(['name' => 'keywords', 'content' => $model->seo_keywords?$model->seo_keywords:$model->tags]);
$this->registerMetaTag(['name' => 'description', 'content' => $model->seo_description?$model->seo_description:$model->MakeSummary]);
$this->registerJsFile('js/jquery.cookie-1.4.1.min.js', [JqueryAsset::className()]);
$this->registerJs('
jQuery(".add-like").on("click", function (e) {
    if ($.cookie("post-'.$model->id.'")!=1) {
        $.get("'.Url::to(["post/like", "id" => $model->id]).'");
        $.cookie("post-'.$model->id.'", "1");
        $(this).children("span").css("color","red");
    }
    return false; 
});
jQuery(document).ready(function () {
    if ($.cookie("post-'.$model->id.'")) {
        $(".add-like").children("span").css("color","red");
    }
});
');
?>
<div class="row post-view">
    <div class="col-md-2">
        <?= CategoryWidget::widget() ?>
    </div>
    <div class="col-md-7">
        <div class="content">
            <h2 class="title"><?= Html::encode($model->title) ?></h2>
            <p class="info"><?= Html::encode($model->writer?$model->writer:"") ?>&nbsp;&nbsp;&nbsp;&nbsp;<?= $model->source?$model->SourceUrl?Html::a($model->source,$model->SourceUrl):$model->source:"" ?>&nbsp;&nbsp;&nbsp;&nbsp;时间：<?= date("Y-m-d H:i:s", $model->published_at) ?> </p>
            <?= $model->content ?>
            <p><?= $model->tags?"标签：":"" ?><?= implode(', ', $model->tagLinks); ?></p>
        </div>
        <div class="text-center like"><a href="" class="add-like" title="我喜欢"><span class="glyphicon glyphicon-heart"></span></a></div>
        <?php if ($pages->totalCount>1): ?>
        <?= LinkPager::widget(['pagination' => $pages]) ?>
        <?php endif ?>
        <?php if ($model->AboutRead): ?>
        <div class="about-read">
            <ul class="list-inline">
                <?php foreach ($model->AboutRead as $key => $value): ?>
                <li><a href="<?= Url::to(['post/view', 'id' => $value['id']]) ?>"><img src="<?= $value['thumbnail'] ?>" alt="<?= Html::encode($value['title']) ?>" style="max-width:120px;"></a>
                    <p><a href="<?= Url::to(['post/view', 'id' => $value['id']]) ?>"><?= Html::encode($value['title']) ?></a></p>
                </li>  
                <?php endforeach ?>
            </ul>   
        </div>
        <?php endif ?>
        <div class="comments">
            <?php if (!$model->disallow_comment): ?>
            <?= $this->render("_comment", ['id' => $model->id, 'parent_id' => 0]) ?>       
            <?php endif ?>
            <div class="comment-list">
            <?= $this->render("_comments", ['id' => $model->id]) ?></div> 
        </div>
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
