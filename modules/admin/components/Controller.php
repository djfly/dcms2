<?php

namespace app\modules\admin\components;

use Yii;
use yii\web\ForbiddenHttpException;
use app\components\Common;

class Controller extends \yii\web\Controller
{
	public $layout = 'column2';
	public $allowedIPs;

	public function beforeAction($action) 
	{
		if (parent::beforeAction($action)) {
            if(!in_array(Yii::$app->user->identity->username, Yii::$app->params['admin']) || !$this->checkAccess()){
                    throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to access this page.'));
            }

            if (!Common::getLanguage()) {
                preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
                Common::setLanguage($matches[1]);
                Yii::$app->language=$matches[1]; 
            }else{
               Yii::$app->language=Common::getLanguage(); 
            }
            
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return boolean whether the module can be accessed by the current user
     */
    protected function checkAccess()
    {
    	$ipAccess = Yii::$app->config->get('ipAccess');
    	if (!empty($ipAccess)) {
			$this->allowedIPs=explode("\r\n",$ipAccess);
            $ip = Yii::$app->getRequest()->getUserIP();
            foreach ($this->allowedIPs as $filter) {
                if ($filter === '*' || $filter === $ip || (($pos = strpos($filter, '*')) !== false && !strncmp($ip, $filter, $pos))) {
                    return true;
                }
            }
		}else{
            return true;
        }

        return false;
    }
}
