<?php

namespace app\controllers;

use Yii;
use app\models\Post;
use app\models\Tag;
use app\models\Comment;
use app\models\Category;
use app\models\search\PostSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\Common;
use app\components\Controller;
use yii\data\Pagination;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\data\ActiveDataProvider;

/**
 * PostController implements the CRUD actions for Post model.
 */
class PostController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'error', 'like', 'comment-up', 'comment-down', 'comment-page'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['create', 'update', 'upload-ajax', 'filemanager', 'create-img-ajax', 'suggest-tags', 'comment-ajax'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'autocomplete' => [
                'class' => 'app\components\AutocompleteAction',
                'tableName' => Tag::tableName(),
                'field' => 'name'
            ]
        ];
    }

    public function beforeAction($action)
    {
        if ($action->id=="create-img-ajax" || $action->id=="filemanager" || $action->id=="crop-ajax") {
            $this->enableCsrfValidation=false;
        }
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $queryParams = Yii::$app->request->getQueryParams();
        $query = Post::find()->where(['status' => 1]);
        $category="";
        if (isset($queryParams['id'])) {
            $category=Category::findOne($queryParams['id']);
            $query = $query->andWhere(['category_id' => $queryParams['id']]);
        }
        if (isset($queryParams['tag'])) {
            $query = $query->andWhere(['like', 'tags', $queryParams['tag']]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>['defaultOrder' => 
                ['created_at' => SORT_DESC]
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'category' => $category
        ]);
    }

    public function actionCommentAjax($id)
    {
        $model = new Comment;

        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->params['commentNeedApproval']) {
                $model->type=0;
            }else{
                $model->type=1;
            }
            $model->status=1;
            $model->post_id=$id;
            if ($model->save()) {
                if (isset($model->user)) {
                    $username=$model->user->username;
                    $avatar=$model->user->avatar;
                    $url=Url::to(['user/view', 'id' => $model->user_id]);
                }else{
                    $username=$model->author?$model->author:"游客";
                    $avatar=Yii::$app->homeUrl."upload/avatar/default.png";
                    $url="javascript:;";
                }
                if ($model->parent_id) {
                    $li='<li class="media">';
                    $endli='</li>';
                }else{
                    $li='';
                    $endli='';
                }
                echo Json::encode($li.'<div class="media"><a class="pull-left" href="'.$url.'"> <img class="media-object img-circle" alt="'.Html::encode($username).'" src="'.$avatar.'" style="width: 48px; height: 48px;"> </a> <div class="media-body"> <h4 class="media-heading"><a href="'.$url.'">'.Html::encode($username).'</a> • <span title="'.date("Y-m-d H:i:s", $model->create_time).'">'.Common::formatTime($model->create_time).'</span></h4> <p>'.Html::encode($model->content).'</p><div class="ops"><a href="" class="comment-up" data-id="'.$model->id.'"><i class="glyphicon glyphicon-thumbs-up"></i> (<span>0</span>)</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="" class="comment-down" data-id="'.$model->id.'"><i class="glyphicon glyphicon-thumbs-down"></i> (<span>0</span>)</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="" class="comment-reply" data-id="'.$model->id.'" data-postid="'.$id.'" title="回复"><i class="glyphicon glyphicon-share-alt"></i></a></div>'.$endli);
            }else{
                echo "0";
            }
        }else{
            echo "0";
        }
        Yii::$app->end();
    }

    public function actionCommentPage($id)
    {
        return $this->renderPartial("_comments", ['id' => $id]);
    }

    public function actionLike($id)
    {
        if(Post::updateAllCounters(['likes' => 1],['id' => $id])){
            echo "1";
        }else{
            echo "0";
        }
        Yii::$app->end();
    }

    public function actionCommentUp($id)
    {
        if(Comment::updateAllCounters(['up' => 1],['id' => $id])){
            echo "1";
        }else{
            echo "0";
        }
        Yii::$app->end();
    }

    public function actionCommentDown($id)
    {
        if(Comment::updateAllCounters(['down' => 1],['id' => $id])){
            echo "1";
        }else{
            echo "0";
        }
        Yii::$app->end();
    }

    /**
     * Displays a single Post model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        if (!empty($model->url)) {
            return $this->redirect($model->url);
        }
        $model->updateCounters(['views' => 1]);
        $content=explode(Yii::$app->params['pagebreakHtml'], $model->content);
        $count=count($content);
        $pages = new Pagination(['totalCount' => $count, 'pageSize' => 1]);
        $model->content=$content[$pages->page];
        return $this->render('view', [
            'model' => $model,
            'pages' => $pages
        ]);
    }

    /**
     * Creates a new Post model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Post;
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Post model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Suggests tags based on the current user input.
     * This is called via AJAX when the user is entering the tags input.
     */
    public function actionSuggestTags()
    {
        if(isset($_GET['term']) && ($keyword = trim($_GET['term'])) !== '')
        {
            $model = new Tag;
            $tags = $model->suggestTags($keyword);
            if($tags !== array()){
                echo Json::encode($tags);
            }
        }
    }

    /**
     * Finds the Post model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Post the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Post::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
