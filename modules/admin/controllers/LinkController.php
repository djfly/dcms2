<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Link;
use app\models\search\LinkSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use app\components\Common;
use app\modules\admin\components\Controller;

/**
 * LinkController implements the CRUD actions for Link model.
 */
class LinkController extends Controller
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
                        'actions' => ['index', 'view', 'create', 'update', 'delete'],
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

    public function actions()
    {
        return [
            'toggle' => [
                'class'=>'app\components\ToggleAction',
                'modelName' => 'app\models\Link',
            ]
        ];
    }

    /**
     * Lists all Link models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LinkSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single Link model.
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
     * Creates a new Link model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Link;

        if ($model->load(Yii::$app->request->post())) {
            $image=UploadedFile::getInstance($model,'logo');
            if(!empty($image->name)){
                $dir=BASE_PATH.'/upload/link/';
                if(!is_dir($dir)) {
                    @mkdir(dirname($dir), 0777);
                    @mkdir($dir, 0777);
                    touch($dir.'/index.html');
                }
                $name=date('His').strtolower(Common::random(16)).strrchr($image->name,'.');
                $image->saveAs($dir.$name);
                $model->logo='upload/link/'.$name;
            }
            if($model->save()){
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Link model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $image=UploadedFile::getInstance($model,'logo');
            if(!empty($image->name)){
                $dir=BASE_PATH.'/upload/link/';
                if(!is_dir($dir)) {
                    @mkdir(dirname($dir), 0777);
                    @mkdir($dir, 0777);
                    touch($dir.'/index.html');
                }
                $name=date('His').strtolower(Common::random(16)).strrchr($image->name,'.');
                $image->saveAs($dir.$name);
                $model->logo='upload/link/'.$name;
            }
            if($model->save()){
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Link model.
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
     * Finds the Link model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Link the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Link::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
