<?php
namespace app\modules\admin\models;

use Yii;
use yii\base\Model;

/**
 * Info form
 */
class InfoForm extends Model
{
	public $siteName;
	public $siteUrl;
	public $siteTitle;
	public $siteKeywords;
	public $siteDescription;
	public $adminEmail;
	public $siteCopyright;
	public $statCode;
	public $closed;
	public $message;

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['closed', 'boolean'],
			['adminEmail', 'email'],
			[['siteName', 'siteUrl', 'siteTitle', 'siteKeywords', 'siteDescription', 'adminEmail', 'siteCopyright',' statCode', 'message'], 'string']
		];
	}

	public function attributeLabels()
	{
		return [
			'siteName' => Yii::t('app', 'Site name'),
			'siteUrl' => Yii::t('app', 'Site url'),
			'siteTitle' => Yii::t('app', 'Site title'),
			'siteKeywords' => Yii::t('app', 'Site keywords'),
			'siteDescription' => Yii::t('app', 'Site description'),
			'adminEmail' => Yii::t('app', 'Admin email'),
			'siteCopyright' => Yii::t('app', 'Site copyright'),
			'statCode' => Yii::t('app', 'Site code'),
			'closed' => Yii::t('app', 'Site closed'),
			'message' => Yii::t('app', 'Site closed display message'),
		];
	}

}
