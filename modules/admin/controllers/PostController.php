<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Post;
use app\models\Tag;
use app\models\Comment;
use app\models\search\PostSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\UploadedFile;
use app\components\Common;
use app\modules\admin\components\Controller;

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
                        'actions' => ['error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'upload-ajax', 'filemanager', 'create-img-ajax', 'suggest-tags', 'thumbnail-delete'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return in_array(Yii::$app->user->identity->username, Yii::$app->params['admin']);
                        }
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if ($action->id=="create-img-ajax" || $action->id=="filemanager" || $action->id=="crop-ajax") {
            $this->enableCsrfValidation=false;
        }
        return parent::beforeAction($action);
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

    public function actionThumbnailDelete($id)
    {
        $model = $this->findModel($id);
        $model->thumbnail="";
        if ($model->save()) {
            echo "1";
        }else{
            echo "0";
        }
        Yii::$app->end();
    }

    public function actionUploadAjax($id)
    {
        if (Yii::$app->request->isAjax) {
            $upload=Upload::find()->where(['post_id' => $id])->asArray()->all();
            echo Json::encode($upload);
        } else {
            echo Json::encode("0");
        }
        Yii::$app->end();
    }

    public function actionCreateImgAjax()
    {
        if(!empty($_FILES)){
            $imageType = array('.gif', '.jpg', '.jpeg', '.png');
            if (!in_array(strrchr(strtolower($_FILES['imgFile']['name']),'.'), $imageType)) {
                echo Json::encode(['error' => 1, 'message' => Yii::t('app', "that's not an image, only allow '.gif', '.jpg', '.jpeg', '.png'")]);
                Yii::$app->end();
            }
            $dir=BASE_PATH.'/upload/post/'.date('Ym').'/';
            if(!is_dir($dir)) {
                @mkdir(dirname($dir), 0777);
                @mkdir($dir, 0777);
                touch($dir.'/index.html');
            }
            $name=date('His').strtolower(Common::random(16)).strrchr($_FILES['imgFile']['name'],'.');
            $tmp_name = $_FILES['imgFile']['tmp_name'];
            move_uploaded_file($tmp_name, $dir.$name);
            $url=Yii::$app->homeUrl.'upload/post/'.date('Ym').'/'.$name;
            $name=$_FILES['imgFile']['name'];
            $size=$_FILES['imgFile']['size'];
            echo Json::encode(['error' => 0, 'url' => $url]);
        } else {
            echo Json::encode(['error' => 1, 'message' => Yii::t('app', "upload error")]);
        }
            
        Yii::$app->end();
    }

    public function actionFilemanager()
    {
        $php_path = BASE_PATH;
        $php_url = Yii::$app->homeUrl;
        $root_path = $php_path . '/upload/post/';
        $root_url = $php_url . '/upload/post/';
        $ext_arr = array('gif', 'jpg', 'jpeg', 'png');
        $dir_name = '';
        // $dir_name = empty($_GET['dir']) ? '' : trim($_GET['dir']);
        if (!in_array($dir_name, array('', 'image', 'flash', 'media', 'file'))) {
            echo "Invalid Directory name.";
            exit;
        }
        if ($dir_name !== '') {
            $root_path .= $dir_name . "/";
            $root_url .= $dir_name . "/";
            if (!file_exists($root_path)) {
                mkdir($root_path);
            }
        }

        if (empty($_GET['path'])) {
            $current_path = realpath($root_path) . '/';
            $current_url = $root_url;
            $current_dir_path = '';
            $moveup_dir_path = '';
        } else {
            $current_path = realpath($root_path) . '/' . $_GET['path'];
            $current_url = $root_url . $_GET['path'];
            $current_dir_path = $_GET['path'];
            $moveup_dir_path = preg_replace('/(.*?)[^\/]+\/$/', '$1', $current_dir_path);
        }
        //echo realpath($root_path);
        
        $order = empty($_GET['order']) ? 'name' : strtolower($_GET['order']);

        
        if (preg_match('/\.\./', $current_path)) {
            echo 'Access is not allowed.';
            exit;
        }
        
        if (!preg_match('/\/$/', $current_path)) {
            echo 'Parameter is not valid.';
            exit;
        }
        
        if (!file_exists($current_path) || !is_dir($current_path)) {
            echo 'Directory does not exist.';
            exit;
        }

        $file_list = array();
        if ($handle = opendir($current_path)) {
            $i = 0;
            while (false !== ($filename = readdir($handle))) {
                if ($filename{0} == '.') continue;
                $file = $current_path . $filename;
                if (is_dir($file)) {
                    $file_list[$i]['is_dir'] = true; 
                    $file_list[$i]['has_file'] = (count(scandir($file)) > 2); 
                    $file_list[$i]['filesize'] = 0; 
                    $file_list[$i]['is_photo'] = false; 
                    $file_list[$i]['filetype'] = ''; 
                } else {
                    $file_list[$i]['is_dir'] = false;
                    $file_list[$i]['has_file'] = false;
                    $file_list[$i]['filesize'] = filesize($file);
                    $file_list[$i]['dir_path'] = '';
                    $file_ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    $file_list[$i]['is_photo'] = in_array($file_ext, $ext_arr);
                    $file_list[$i]['filetype'] = $file_ext;
                }
                $file_list[$i]['filename'] = $filename; 
                $file_list[$i]['datetime'] = date('Y-m-d H:i:s', filemtime($file)); 
                $i++;
            }
            closedir($handle);
        }
        
        usort($file_list, [$this,'cmp_func']);

        $result = array();
        
        $result['moveup_dir_path'] = $moveup_dir_path;
        
        $result['current_dir_path'] = $current_dir_path;
        
        $result['current_url'] = $current_url;
        
        $result['total_count'] = count($file_list);
        
        $result['file_list'] = $file_list;

        //输出JSON字符串
        header('Content-type: application/json; charset=UTF-8');
        echo Json::encode($result);
    }

    protected static function cmp_func($a, $b) 
    {
        global $order;
        if ($a['is_dir'] && !$b['is_dir']) {
            return -1;
        } else if (!$a['is_dir'] && $b['is_dir']) {
            return 1;
        } else {
            if ($order == 'size') {
                if ($a['filesize'] > $b['filesize']) {
                    return 1;
                } else if ($a['filesize'] < $b['filesize']) {
                    return -1;
                } else {
                    return 0;
                }
            } else if ($order == 'type') {
                return strcmp($a['filetype'], $b['filetype']);
            } else {
                return strcmp($a['filename'], $b['filename']);
            }
        }
    }

    /**
     * Lists all Post models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PostSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single Post model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
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
     * Deletes an existing Post model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
