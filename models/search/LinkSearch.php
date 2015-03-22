<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Link;

/**
 * LinkSearch represents the model behind the search form about `app\models\Link`.
 */
class LinkSearch extends Link
{
    public function rules()
    {
        return [
            [['id', 'target', 'type', 'position', 'visible'], 'integer'],
            [['name', 'url', 'logo'], 'safe'],
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
            'target' => Yii::t('app', 'Target'),
            'type' => Yii::t('app', 'Type'),
            'position' => Yii::t('app', 'Position'),
            'visible' => Yii::t('app', 'Visible'),
        ];
    }

    public function search($params)
    {
        $query = Link::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $this->addCondition($query, 'id');
        $this->addCondition($query, 'name', true);
        $this->addCondition($query, 'url', true);
        $this->addCondition($query, 'logo', true);
        $this->addCondition($query, 'target');
        $this->addCondition($query, 'type');
        $this->addCondition($query, 'position');
        $this->addCondition($query, 'visible');
        return $dataProvider;
    }

    protected function addCondition($query, $attribute, $partialMatch = false)
    {
        if (($pos = strrpos($attribute, '.')) !== false) {
            $modelAttribute = substr($attribute, $pos + 1);
        } else {
            $modelAttribute = $attribute;
        }

        $value = $this->$modelAttribute;
        if (trim($value) === '') {
            return;
        }
        if ($partialMatch) {
            $query->andWhere(['like', $attribute, $value]);
        } else {
            $query->andWhere([$attribute => $value]);
        }
    }
}
