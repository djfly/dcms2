<?php

namespace app\components;

use Yii;
use yii\web\ForbiddenHttpException;
use app\components\Common;

class Controller extends \yii\web\Controller
{
	public function beforeAction($action) 
	{
		if (parent::beforeAction($action)) {
            
            $siteInfo = Yii::$app->config->get('siteInfo');
            $route=Yii::$app->controller->id.'/'.$action->id;
            if(isset($siteInfo) &&!$siteInfo['closed'] && $this->isAdmin() && $route!=='site/error' && $route!=='site/login' && $route!=='site/logout'){
                    throw new ForbiddenHttpException(Yii::t('app', $siteInfo['message']?$siteInfo['message']:'Website was temporarily in closed.'));
            }
            return true;
        } else {
            return false;
        }
    }

    public function isAdmin()
    {
        if (!Yii::$app->user->isGuest) {
            return !in_array(Yii::$app->user->identity->username, Yii::$app->params['admin']);
        }else{
            return false;
        }
    }
}
