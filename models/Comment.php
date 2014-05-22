<?php

namespace app\models;

use Yii;
use app\models\Post;
use app\models\User;
use app\models\Rom;

/**
 * This is the model class for table "tbl_comment".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $type
 * @property string $content
 * @property string $score
 * @property integer $status
 * @property integer $create_time
 * @property string $author
 * @property string $email
 * @property string $url
 * @property string $ip
 * @property integer $up
 * @property integer $down
 * @property integer $post_id
 * @property integer $user_id 
 *
 * @property Post $post
 */
class Comment extends \yii\db\ActiveRecord
{
    const STATUS_PENDING=0;
    const STATUS_APPROVED=1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%comment}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'required'],
            [['parent_id', 'post_id', 'up', 'down', 'type', 'status'], 'integer'],
            [['content'], 'string'],
            [['author', 'email'], 'string', 'max' => 128],
            [['url'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'parent_id' => Yii::t('app', 'Parent ID'),
            'type' => Yii::t('app', 'Type'),
            'content' => Yii::t('app', 'Content'),
            'status' => Yii::t('app', 'Status'),
            'create_time' => Yii::t('app', 'Create Time'),
            'author' => Yii::t('app', 'Author'),
            'email' => Yii::t('app', 'Email'),
            'url' => Yii::t('app', 'Url'),
            'ip' => Yii::t('app', 'Ip'),
            'down' => Yii::t('app', 'Down'),
            'up' => Yii::t('app', 'Up'),
            'post_id' => Yii::t('app', 'Post ID'),
            'user_id' => Yii::t('app', 'User ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(Post::className(), ['id' => 'post_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert))
        {
            if($this->isNewRecord){
                $this->create_time=time(); 
                $this->ip=Yii::$app->getRequest()->getUserIP();
                if (!Yii::$app->user->isGuest) {
                    $this->user_id=Yii::$app->user->identity->id;
                }
            }
            return true;
        }
        else
            return false;
    }
}
