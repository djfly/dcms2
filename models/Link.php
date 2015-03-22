<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_link".
 *
 * @property integer $id
 * @property string $name
 * @property string $url
 * @property string $logo
 * @property integer $position
 * @property integer $target
 * @property integer $type
 * @property integer $visible
 */
class Link extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%link}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url', 'name'], 'required'],
            [['position', 'target', 'type', 'visible'], 'integer'],
            [['name', 'url'], 'string', 'max' => 255],
            ['logo', 'file', 'skipOnEmpty' => true, 'types'=>'jpg, gif, png', 'maxSize'=>2097152, 'tooBig' => Yii::t('app','{file} files can not exceed 2MB. Please upload a small bit of the file.')],
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'url' => Yii::t('app', 'Url'),
            'logo' => Yii::t('app', 'Logo'),
            'position' => Yii::t('app', 'Position'),
            'target' => Yii::t('app', 'Target'),
            'type' => Yii::t('app', 'Type'),
            'visible' => Yii::t('app', 'Visible'),
        ];
    }
}
