<?php

namespace app\models;

use Yii;
use app\components\NestedSetBehavior;

/**
 * This is the model class for table "tbl_nav".
 *
 * @property string $id
 * @property string $root
 * @property string $lft
 * @property string $rgt
 * @property integer $level
 * @property string $name
 * @property string $url
 * @property integer $target
 */
class Nav extends \yii\db\ActiveRecord
{
    public $parent;

    public function behaviors()
    {
        return [
            [
                'class' => NestedSetBehavior::className(),
                'hasManyRoots' => true
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%nav}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['target', 'parent'], 'integer'],
            [['name'], 'required'],
            [['name', 'url'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'root' => Yii::t('app', 'Root'),
            'lft' => Yii::t('app', 'Lft'),
            'rgt' => Yii::t('app', 'Rgt'),
            'level' => Yii::t('app', 'Level'),
            'name' => Yii::t('app', 'Name'),
            'url' => Yii::t('app', 'Url'),
            'target' => Yii::t('app', 'Target'),
            'Parent' => Yii::t('app', 'Parent'),
        ];
    }
}
