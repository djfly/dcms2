<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_tag".
 *
 * @property integer $id
 * @property string $name
 * @property integer $frequency
 */
class Tag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tag}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['frequency'], 'integer'],
            [['name'], 'string', 'max' => 128]
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
            'frequency' => Yii::t('app', 'Frequency'),
        ];
    }

    /**
     * Returns tag names and their corresponding weights.
     * Only the tags with the top weights will be returned.
     * @param integer the maximum number of tags that should be returned
     * @return array weights indexed by tag names.
     */
    public function findTagWeights($limit=20)
    {
        $models=self::find()->orderBy(['frequency' => SORT_DESC])->limit($limit)->all();

        $total=0;
        foreach($models as $model)
        {
            $total+=$model->frequency;
        }

        $tags=[];
        if($total>0)
        {
            foreach($models as $model)
                $tags[$model->name]=8+(int)(16*$model->frequency/($total+10));
            ksort($tags);
        }
        return $tags;
    }

    /**
     * Suggests a list of existing tags matching the specified keyword.
     * @param string the keyword to be matched
     * @param integer maximum number of tags to be returned
     * @return array list of matching tag names
     */
    public function suggestTags($keyword,$limit=20)
    {
        $tags=self::find()->where(['like', 'name', $keyword])->orderBy(['frequency' => 'SORT_DESC', 'Name' => SORT_ASC])->limit($limit)->all();
        
        $names=[];
        foreach($tags as $tag)
        {
            $names[]=$tag->name;
        }
            
        return $names;
    }

    public static function string2array($tags)
    {
        return preg_split('/\s*,\s*/',trim($tags),-1,PREG_SPLIT_NO_EMPTY);
    }

    public static function array2string($tags)
    {
        return implode(', ',$tags);
    }

    public function updateFrequency($oldTags, $newTags)
    {
        $oldTags=self::string2array($oldTags);
        $newTags=self::string2array($newTags);
        $this->addTags(array_values(array_diff($newTags,$oldTags)));
        $this->removeTags(array_values(array_diff($oldTags,$newTags)));
    }

    public function addTags($tags)
    {
        self::updateAllCounters(['frequency' => 1],['in', 'name', $tags]);
        foreach($tags as $name)
        {
            if(!self::findOne(['name' => $name]))
            {
                $tag=new Tag;
                $tag->name=$name;
                $tag->frequency=1;
                $tag->save();
            }
        }
    }

    public function removeTags($tags)
    {
        if(empty($tags)){
            return;
        }
        self::updateAllCounters(['frequency' => -1],['in', 'name', $tags]);
        self::deleteAll('frequency<=0');
    }
}
