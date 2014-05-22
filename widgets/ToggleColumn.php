<?php
namespace app\widgets;

use Yii;
use yii\base\Exception;
use yii\helpers\Json;
use yii\helpers\Html;

class ToggleColumn extends \yii\grid\DataColumn
{
	/**
	 * @var string the attribute name of the data model. Used for column sorting, filtering and to render the corresponding
	 * attribute value in each data cell. If {@link value} is specified it will be used to rendered the data cell instead of the attribute value.
	 * @see value
	 * @see sortable
	 */
	public $name;

	/**
	 * @var string the label for the toggle button. Defaults to "Check".
	 * Note that the label will not be HTML-encoded when rendering.
	 */
	public $checkedButtonLabel;

	/**
	 * @var string the label for the toggle button. Defaults to "Uncheck".
	 * Note that the label will not be HTML-encoded when rendering.
	 */
	public $uncheckedButtonLabel;

	/**
	 * @var string the label for the NULL value toggle button. Defaults to "Not Set".
	 * Note that the label will not be HTML-encoded when rendering.
	 */
	public $emptyButtonLabel;

	/**
	 * @var string the glyph icon toggle button "checked" state.
	 * You may set this property to be false to render a text link instead.
	 */
	public $checkedIcon = 'glyphicon glyphicon-ok-sign';

	/**
	 * @var string the glyph icon toggle button "unchecked" state.
	 * You may set this property to be false to render a text link instead.
	 */
	public $uncheckedIcon = 'glyphicon glyphicon-remove-sign';

	/**
	 * @var string the glyph icon toggle button "empty" state (example for null value)
	 */
	public $emptyIcon = 'glyphicon glyphicon-question-sign';

	/**
	 * @var string Name of the action to call and toggle values
	 * @see bootstrap.action.TbToggleAction for an easy way to use with your controller
	 */
	public $toggleAction = 'toggle';

	/**
	 * @var string a javascript function that will be invoked after the toggle ajax call.
	 *
	 * The function signature is <code>function(data)</code>
	 * <ul>
	 * <li><code>success</code> status of the ajax call, true if the ajax call was successful, false if the ajax call failed.
	 * <li><code>data</code> the data returned by the server in case of a successful call or XHR object in case of error.
	 * </ul>
	 * Note that if success is true it does not mean that the delete was successful, it only means that the ajax call was successful.
	 *
	 * Example:
	 * <pre>
	 *  array(
	 *     class'=>'TbToggleColumn',
	 *     'afterToggle'=>'function(success,data){ if (success) alert("Toggled successfuly"); }',
	 *  ),
	 * </pre>
	 */
	public $afterToggle;

	/**
	 * @var array the configuration for toggle button.
	 */
	protected $button = [];

	/**
	 * Initializes the column.
	 * This method registers necessary client script for the button column.
	 */
	public function init()
	{
		if ($this->name === null) {
			throw new Exception(Yii::t(
				'app',
				'"{attribute}" attribute cannot be empty.',
				['attribute' => "name"]
			));
		}

		$this->registerClientScript();
	}

	protected function renderHeaderCellContent()
    {
    	return parent::renderHeaderCellContent();
    }

    protected function renderFilterCellContent()
    {
    	return parent::renderFilterCellContent();
    }

	protected function registerClientScript()
	{

$function = "
function(e) {
	$.ajax({
	   type: 'POST',
	   url: $(this).attr('href'),
	   success: function(msg){
	     $.pjax.reload({container: '#' + $('#{$this->grid->id}').parent().attr('id'), timeout: 2000});
	   }
	});
	return false;
}";

		// $function = Json::encode($function);
		$class = $this->name . '_toggle';
		$this->grid->getView()->registerJs("jQuery(document).on('click','#{$this->grid->id} a.{$class}',$function);");
	}

	/**
	 * Renders the data cell content.
	 * This method renders the view, update and toggle buttons in the data cell.
	 *
	 * @param integer $row the row number (zero-based)
	 * @param mixed $data the data associated with the row
	 */
	protected function renderDataCellContent($model, $key, $index)
	{
		$n=$this->name;
		return Html::a('<span class="'.$this->getButtonIcon($model->$n).'"></span>', [$this->toggleAction, 'id'=>$key, 'attribute' => $this->name], [
					'title' => $this->getButtonLabel($model->$n),
                    'data-pjax' => '0',
                    'class' => $this->name . '_toggle'
                ]);
	}

	private function getButtonLabel($value)
	{
		return $value === null ? $this->emptyButtonLabel
			: ($value ? $this->checkedButtonLabel : $this->uncheckedButtonLabel);
	}

	private function getButtonIcon($value)
	{
		return $value === null ? $this->emptyIcon
			: ($value ? $this->checkedIcon : $this->uncheckedIcon);
	}
}
