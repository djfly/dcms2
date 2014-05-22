<?php
namespace app\components;

use Yii;
use yii\web\HttpException;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

class ToggleAction extends \yii\base\Action
{
	/**
	 * @var string the name of the model we are going to toggle values to
	 */
	public $modelName;

	/**
	 * @var bool whether to throw an exception if we cannot find a model requested by the id
	 */
	public $exceptionOnNullModel = true;

	/**
	 * @var mixed the route to redirect the call after updating attribute
	 */
	public $redirectRoute;

	/**
	 * @var int|string the value to update the model to [yes|no] standard toggle options, but you can toggle any value.
	 */
	public $yesValue = 1;

	/**
	 * @var int|string the value to update the model to [yes|no]
	 */
	public $noValue = 0;

	/**
	 * @var mixed the response to return to an AJAX call when the attribute was successfully saved.
	 */
	public $ajaxResponseOnSuccess = 1;

	/**
	 * @var mixed the response to return to an AJAX call when failed to update the attribute.
	 */
	public $ajaxResponseOnFailed = 0;


	/**
	 * Widgets run function
	 *
	 * @param integer $id
	 * @param string $attribute
	 *
	 * @throws CHttpException
	 */
	public function run($id, $attribute)
	{
		if (Yii::$app->getRequest()->isPost) {
			$model = $this->loadModel($id);
			$model->$attribute = ($model->$attribute == $this->noValue) ? $this->yesValue : $this->noValue;
			$success = $model->save(false, [$attribute]);

			if (Yii::$app->getRequest()->isAjax) {
				echo $success ? $this->ajaxResponseOnSuccess : $this->ajaxResponseOnFailed;
				exit(0);
			}
			if ($this->redirectRoute !== null) {
				return $this->getController()->redirect($this->redirectRoute);
			}
		} else {
			throw new HttpException(Yii::t('app', 'Invalid request'));
		}
	}

	/**
	 * Loads the requested data model.
	 *
	 * @param integer $id the model ID
	 *
	 * @return ActiveRecord the model instance.
	 * @throws HttpException if the model cannot be found
	 */
	protected function loadModel($id)
	{
		$model= new $this->modelName;
		$model = $model::findOne($id);

		if (!$model)
			throw new HttpException(404, 'Unable to find the requested object.');

		return $model;
	}
}
