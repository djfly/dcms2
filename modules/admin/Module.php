<?php

namespace app\modules\admin;
use Yii;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\admin\controllers';
    public $allowedIPs;

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $route=Yii::$app->controller->id.'/'.$action->id;
        
        if (!$this->checkAccess() && $route!=='site/error' && $route!=='site/login' && $route!=='site/logout') {
            throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to access this page.'));
        } else { 
        	$this->checkAccess();
            return parent::beforeAction($action);
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
