<?php
namespace app\modules\admin\controllers;

use Yii;
use yii\filters\AccessControl;
use app\models\LoginForm;
use app\models\Comment;
use app\modules\admin\models\InfoForm;
use app\modules\admin\models\AccessForm;
use app\modules\admin\models\SignupForm;
use app\modules\admin\models\EmailForm;
use app\modules\admin\models\FtpForm;
use app\modules\admin\models\ImageForm;
use app\modules\admin\models\SlidesForm;
use yii\filters\VerbFilter;
use app\components\Common;
use app\modules\admin\components\Controller;

/**
 * Default controller
 */
class DefaultController extends Controller
{
	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'actions' => ['login', 'error', 'locale'],
						'allow' => true,
					],
					[
						'actions' => ['logout', 'index', 'info', 'access', 'signup', 'email', 'ftp', 'image', 'slides'],
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
	 * @inheritdoc
	 */
	public function actions()
	{
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
		];
	}

	public function actionIndex()
	{
		$comments = Comment::find()->limit(10)->all();
		return $this->render('index', ['comments' => $comments]);
	}

	public function actionLocale($language)
	{
		Common::setLanguage($language);
		return $this->redirect(['index']);
	}

	public function actionInfo()
	{
		$model = new InfoForm();
		$model->attributes=Yii::$app->config->get("siteInfo");
		if ($model->load(Yii::$app->request->post())) {
			Yii::$app->config->set("siteInfo",$model->attributes);
			Yii::$app->getSession()->setFlash('success', Yii::t('app', 'save success!'));
			return $this->refresh();
		}

		return $this->render('info', [
			'model' => $model,
		]);
	}

	public function actionAccess()
	{
		$model = new AccessForm();
		$model->ipAccess=Yii::$app->config->get("ipAccess");
		if ($model->load(Yii::$app->request->post())) {
			$model->ipAccess=trim(preg_replace("/(\s*(\r\n|\n\r|\n|\r)\s*)/", "\r\n", $model->ipAccess));

	        //Detect whether yourself ip added to the list
    		if( !empty($model->ipAccess) && !AccessForm::allowIp(Yii::$app->getRequest()->getUserIP(),explode("\r\n",$model->ipAccess))){
					Yii::$app->getSession()->setFlash('warning', Yii::t('app', 'You must add yourself ip to the list!'));
					return $this->redirect(['access']);
    		}
        	Yii::$app->config->set("ipAccess",$model->ipAccess);
        	Yii::$app->getSession()->setFlash('success', Yii::t('app', 'save success!'));
			return $this->refresh();
		}
		return $this->render('access', [
			'model' => $model,
		]);
	}

	public function actionSignup()
	{
		$model = new SignupForm();
		$model->attributes=Yii::$app->config->get("signup");
		if ($model->load(Yii::$app->request->post())) {
			Yii::$app->config->set("signup",$model->attributes);
			Yii::$app->getSession()->setFlash('success', Yii::t('app', 'save success!'));
			return $this->refresh();
		}

		return $this->render('signup', [
			'model' => $model,
		]);
	}
}
