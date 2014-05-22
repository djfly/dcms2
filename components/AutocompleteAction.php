<?php
namespace app\components;

use yii\base\Action;
use yii\helpers\Json;
use yii\base\InvalidConfigException;

class AutocompleteAction extends Action
{
    public $tableName;

    public $field;

    public $clientIdGetParamName = 'query';

    public $searchPrefix = '';

    public $searchSuffix = '%';

    public function init()
    {
        if($this->tableName === null) {
            throw new  InvalidConfigException(get_class($this) . '::$tableName must be defined.');
        }
        if($this->field === null) {
            throw new  InvalidConfigException(get_class($this) . '::$field must be defined.');
        }
        parent::init();
    }

    public function run()
    {
        $value = $this->searchPrefix . $_GET[$this->clientIdGetParamName] . $this->searchSuffix;
        $rows = \Yii::$app->db
            ->createCommand("SELECT {$this->field} AS value FROM {$this->tableName} WHERE {$this->field} LIKE :field ORDER BY {$this->field}")
            ->bindValues([':field' => $value])
            ->queryAll();

        echo Json::encode($rows);
    }
}