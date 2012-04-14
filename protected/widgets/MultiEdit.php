<?php

/**
 * MultiEdit implements the editing of multiple-value fields in a specified model 
 */
class MultiEdit extends CWidget
{
	/**
	 * @var CModel the model that is being edited
	 */
	public $model = null;
	/**
	 * @var string the attribute that is will be displayed and available for editing
	 */
	public $attribute = null;
	/**
	 * @var array the html options which will be applied to the wrapping fieldset
	 */
	public $fieldsetHtmlOptions = array();
	/**
	 * @var array the html options which will be applied to the inner editing fields
	 */
	public $editorHtmlOptions = array();
	/**
	 * @var string the type of widget which should be used for editing. textarea and textfield are two supported values
	 */
	public $editorType = "TextField";

	/**
	 * Initialises the MultiEdit widget
	 */
	public function init()
	{
		if($this->model === null || $this->attribute === null) {
			throw new CException('Neither the "model" or "attribute" properties may be empty');
		}
	}

	/**
	 * Renders the Multiple value editor
	 */
	public function run()
	{
		// First we will setup some variables
		$deleteButton = CHtml::button('Delete', array('onclick' => 'js:multiEditRemoveInput( $(this) )', 'class' => 'btn'));
		$attribute = $this->attribute;
		$data = (array) $this->model->$attribute;
		
		// If we have no data - then add a single empty entry so that a editor is shown - even if empty
		if( empty($data) ) {
			$data = array(0 => '');
		}
		
		// Start the primary wrapper
		echo CHtml::openTag('fieldset', $this->fieldsetHtmlOptions) . "\n";
		echo CHtml::tag('legend', array(), CHtml::activeLabel($this->model, $this->attribute));
		
		// Build the editor wrapper
		echo CHtml::openTag('div');
		foreach( $data as $key => $entry ) {
			$indexedAttribute = $this->attribute . "[$key]";
			$errorState = CHtml::error($this->model, $indexedAttribute);
			if( strtolower($this->editorType) == 'textarea' ) {
				$content = CHtml::activeTextArea($this->model, $indexedAttribute, $this->editorHtmlOptions) . $deleteButton . $errorState;
			} else {
				$content = CHtml::activeTextField($this->model, $indexedAttribute, $this->editorHtmlOptions) . $deleteButton . $errorState;
			}
			echo CHtml::tag('div', array(), $content);
		}
		echo CHtml::closeTag('div');
		
		// Add the Add new entry item
		echo CHtml::button('Add', array('class' => 'btn', 'onclick' => 'js:multiEditAddInput( $(this) )'));
		echo CHtml::closeTag('fieldset');
		
		// Setup Javascript
		Yii::app()->clientScript->registerCoreScript('jquery');
		Yii::app()->clientScript->registerScriptFile("js/multiedit-widget.js");
	}
};

?>