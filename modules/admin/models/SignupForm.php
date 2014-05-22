<?php
namespace app\modules\admin\models;

use Yii;
use yii\base\Model;

/**
 * Signup form
 */
class SignupForm extends Model
{
	public $allowSignup=1;
	public $message;
	public $holdUser;
	public $signupVerifyWay=1;

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['allowSignup', 'boolean'],
			[['message', 'holdUser'], 'string'],
			['signupVerifyWay', 'integer']
		];
	}

	public function attributeLabels()
	{
		return [
			'allowSignup' => Yii::t('app', 'Allow Signup'),
			'message' => Yii::t('app', 'Closed Signup display message'),
			'signupVerifyWay' => Yii::t('app', 'Signup Verify Way'),
			'holdUser' => Yii::t('app', 'Hold User Name'),
		];
	}

}
