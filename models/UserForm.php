<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class UserForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $avatar;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username'], 'required'],
            [['email'], 'email'],
            [['password'], 'string', 'max' => 255],
            ['avatar', 'file', 'skipOnEmpty' => true, 'types'=>'jpg, gif, png', 'maxSize'=>2097152, 'tooBig' => Yii::t('app','{file} files can not exceed 2MB. Please upload a small bit of the file.')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'Email'),
            'username' => Yii::t('app', 'Username'),
            'avatar' => Yii::t('app', 'Avatar'),
        ];
    }
}
