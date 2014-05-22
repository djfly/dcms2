<?php
use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Comment;
use yii\data\Pagination;
use yii\widgets\LinkPager;
use app\components\Common;

$pageSize=5;
$pages = new Pagination(['totalCount' => Comment::find()->where(['post_id'=> $id])->andWhere('status=1 AND type=1')->count(), 'pageSize' => $pageSize, 'pageParam' => 'commentpage', 'pageSizeParam' => 'commentpagesizeparam']);
$comments=Comment::find()->where(['post_id'=> $id])->andWhere('status=1 AND type=1')->orderBy(['create_time' => SORT_DESC,])->all();

function renderItems($items,$parent,$id)
{  
	foreach ($items as $key => $value) {
		if ($parent->id==$value->parent_id) {
			if (isset($value->user)) {
				$username=$value->user->username;
				$avatar=$value->user->avatar;
				$url=Url::to(['user/view', 'id' => $value->user_id]);
			}else{
				$username=$value->author?$value->author:"游客";
				$avatar=Yii::$app->homeUrl."upload/avatar/default.png";
				$url="javascript:;";
			}
			echo '<div class="media"><a class="pull-left" href="'.$url.'"> <img class="media-object img-circle" alt="'.Html::encode($username).'" src="'.$avatar.'" style="width: 48px; height: 48px;"> </a> <div class="media-body"> <h4 class="media-heading"><a href="'.$url.'">'.Html::encode($username).'</a> • <span title="'.date("Y-m-d H:i:s", $value->create_time).'">'.Common::formatTime($value->create_time).'</span></h4> <p>'.Html::encode($value->content).'</p><div class="ops"><a href="" class="comment-up" data-id="'.$value->id.'"><i class="glyphicon glyphicon-thumbs-up"></i> (<span>'.$value->up.'</span>)</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="" class="comment-down" data-id="'.$value->id.'"><i class="glyphicon glyphicon-thumbs-down"></i> (<span>'.$value->down.'</span>)</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="" class="comment-reply" data-id="'.$value->id.'" data-postid="'.$id.'" title="回复"><i class="glyphicon glyphicon-share-alt"></i></a></div>';
			renderItems($items,$value,$id);
			echo "</div></div>";
		}
	 } 
}
function Comments($items,$pages,$id){

	foreach ($items as $key => $value) {
		
		if ($key>=$pages->offset && $key<$pages->offset+$pages->limit) {
			if ($value->parent_id==0) {
				if (isset($value->user)) {
					$username=$value->user->username;
					$avatar=$value->user->avatar;
					$url=Url::to(['user/view', 'id' => $value->user_id]);
				}else{
					$username=$value->author?$value->author:"游客";
					$avatar=Yii::$app->homeUrl."upload/avatar/default.png";
					$url="javascript:;";
				}
				echo '<li class="media"> <a class="pull-left" href="'.$url.'"> <img class="media-object img-circle" alt="'.Html::encode($username).'" src="'.$avatar.'" style="width: 48px; height: 48px;"> </a> <div class="media-body"> <h4 class="media-heading"><a href="'.$url.'">'.Html::encode($username).'</a> • <span title="'.date("Y-m-d H:i:s", $value->create_time).'">'.Common::formatTime($value->create_time).'</span></h4> <p>'.Html::encode($value->content).'</p><div class="ops"><a href="" class="comment-up" data-id="'.$value->id.'"><i class="glyphicon glyphicon-thumbs-up"></i> (<span>'.$value->up.'</span>)</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="" class="comment-down" data-id="'.$value->id.'"><i class="glyphicon glyphicon-thumbs-down"></i> (<span>'.$value->down.'</span>)</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="" class="comment-reply" data-id="'.$value->id.'" data-postid="'.$id.'" title="回复"><i class="glyphicon glyphicon-share-alt"></i></a></div>';
				renderItems($items,$value,$id);
				echo "</li>";
			}
		}
	}
} 
echo '<ul class="media-list">';
echo Comments($comments,$pages,$id);
echo '</ul>';
if ($pages->totalCount>$pages->pageSize){
	echo LinkPager::widget([
		'pagination' => $pages,
	]);
}
$this->registerJs('
jQuery(".comment-list").on("click", ".comment-up", function (e) {
	if ($.cookie("comment-"+$(this).data("id"))!=1) {
		$.get("'.Url::to(['post/comment-up']).'?id="+$(this).data("id"));
		$(this).children("span").text(parseInt($(this).children("span").text())+1);
		$.cookie("comment-"+$(this).data("id"), "1");
		$(this).parent("div").css("position", "relative");
		$(this).after("<span style=\"position:absolute;color:red;\">+1</span>").next().animate({bottom:"19px"}).fadeOut(900);
	}else{
		alert("你已经投过票了！");
	}
    return false; 
});
jQuery(".comment-list").on("click", ".comment-down", function (e) {
	if ($.cookie("comment-"+$(this).data("id"))!=1) {
		$.get("'.Url::to(['post/comment-down']).'?id="+$(this).data("id"));
		$(this).children("span").text(parseInt($(this).children("span").text())+1);
		$.cookie("comment-"+$(this).data("id"), "1");
		$(this).parent("div").css("position", "relative");
		$(this).after("<span style=\"position:absolute;color:red;\">+1</span>").next().animate({bottom:"19px"}).fadeOut(900);
	}
    return false; 
});
jQuery(".comment-list").on("click", ".comment-reply", function (e) {
	var parentIdHtml="<input type=\"hidden\" id=\"comment-parent_id\" name=\"Comment[parent_id]\" value=\""+$(this).data("id")+"\">";
	if ($(".comment-list form")) {
		$(".comment-list form").remove();
	}
	$(this).parent("div").after($(".comment-form").html());
	$(".comment-list form").append(parentIdHtml);
    return false; 
});
jQuery(".comment-list").on("click", ".pagination li > a", function (e) {
	$.get($(this).attr("href").replace("view","comment-page"), function(result){
		$(".comment-list").html(result);
	});
    return false; 
});
$(".comment-list").on("submit", ".media-list form", function (e) {
    var myform = $(this);
    jQuery(".comment-submit").button("loading");
    jQuery.ajax({
        url: "'. Url::toRoute(['post/comment-ajax', 'id' => $id]) .'",
        type: "POST",
        dataType: "json",
        data: myform.serialize(),
        success: function(response) {
            myform.before(response);
            myform.remove();
            jQuery(".comment-submit").button("reset");
            return false;
        },
        error: function(response) {
            jQuery(".comment-submit").button("reset");
            return false;
        }
    });
    return false;
});
');
?>
