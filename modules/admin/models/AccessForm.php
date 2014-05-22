<?php
namespace app\modules\admin\models;

use Yii;
use yii\base\Model;

/**
 * Access Form
 */
class AccessForm extends Model
{
	public $ipAccess;

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['ipAccess'], 'default','value'=>'']
		];
	}

	public function attributeLabels()
	{
		return [
			'ipAccess' => Yii::t('app', 'Allow IP List to access the backend')
		];
	}

	public static function allowIp($ip,$ipFilters)
	{
		if(empty($ipFilters)){
			return true;
		}
			
		foreach($ipFilters as $filter)
		{
			if($filter==='*' || $filter===$ip || (($pos=strpos($filter,'*'))!==false && !strncmp($ip,$filter,$pos))){
				return true;
			}
		}
		return false;
	}
}
