<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\helpers\Html;
use app\models\User;
use app\models\UserForm;
use yii\web\UploadedFile;
use app\components\Common;

class UserController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['view'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->identity->id == Yii::$app->request->getQueryParam('id') ;
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post','get'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);

        $userform = new UserForm;
        $userform->username=$model->username;
        $userform->email=$model->email;
        if ($userform->load(Yii::$app->request->post()) && $userform->validate()) {
            $types=['.gif', '.jpg', '.png'];
            $image=UploadedFile::getInstance($userform,'avatar');
            
            if(!empty($image->name) && in_array(strrchr(strtolower($image->name),'.'), $types)){
                $dir=BASE_PATH.'/upload/avatar/';
                if(!is_dir($dir)) {
                    @mkdir($dir, 0777);
                    touch($dir.'/index.html');
                }
                $name=date('His').strtolower(Common::random(16)).strrchr($image->name,'.');
                $image->saveAs($dir.$name);
                $model->avatar=Yii::$app->homeUrl.'upload/avatar/'.$name;
            }

            if (!empty($userform->username)) {
                $model->username=$userform->username;
            }
            if (!empty($userform->password)) {
                $model->password=$userform->password;
            }
            if (!empty($userform->email)) {
                $model->email=$userform->email;
            }
            if ($model->save()) {
                Yii::$app->getSession()->setFlash('success', '保存成功.');
            }else{
                Yii::$app->getSession()->setFlash('danger', '保存失败.');
            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('view', [
                'model' => $model,
                'userform' => $userform
            ]);
        }
    }

    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
