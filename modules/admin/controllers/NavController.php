<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Nav;
use app\models\search\NavSearch;
use app\modules\admin\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * NavController implements the CRUD actions for Nav model.
 */
class NavController extends Controller
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
                        'actions' => ['tree', 'index', 'view', 'create', 'update', 'move', 'delete'],
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

    /**
     * Lists all Nav models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NavSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionTree()
    {
        return $this->render('tree');
    }

    /**
     * Displays a single Nav model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Nav model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Nav;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->parent == 0){
                $model->saveNode();
            } else if ($model->parent){
                $root = $this->findModel($model->parent);
                $model->appendTo($root);
            }
            return $this->render('tree');
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Nav model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $parent = $model->parent()->One();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->saveNode();
            if ($model->parent == 0 && !$model->isRoot()){
                $model->moveAsRoot();
            } elseif ($model->parent != 0 && $model->parent != $parent->id){
                $root = $this->findModel($model->parent);
                $model->moveAsLast($root);
            }
            return $this->render('tree');
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionMove($id,$updown)
    {
        $model=$this->findModel($id);

        if($updown=="down") {
            $sibling=$model->next()->one();
            if (isset($sibling)) {
                $model->moveAfter($sibling);
                return $this->redirect(array('tree'));
            }
            return $this->redirect(array('tree'));
        }
        if($updown=="up"){
            $sibling=$model->prev()->one();
            if (isset($sibling)) {
                $model->moveBefore($sibling);
                return $this->redirect(array('tree'));
            }
            return $this->redirect(array('tree'));
        }
    }

    /**
     * Deletes an existing Nav model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->deleteNode();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Nav model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Nav the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Nav::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
