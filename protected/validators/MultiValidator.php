<?php

class MultiValidator extends CValidator
{
	public $allowEmpty = true;
	public $validator = null;
	public $params = array();

	protected function validateAttribute($object,$attribute)
	{
		$data = $object->$attribute;
		// Make sure we are not empty if we are not allowed to be....
		if( $this->allowEmpty && $this->isEmpty($data) ) {
			return;
		}
		// Make sure we have a valid configuration....
		if( is_null($this->validator) ) {
			$this->addError($object, $attribute, 'Validator configuration invalid - validator class not specified');
			return;
		}
		// Make sure we are actually trying to validate an array....
		if( !is_array($data) ) {
			$this->addError($object, $attribute, 'Attribute being validated is not an array');
			return;
		}

		// Create the model reflector
		$reflector = new ModelReflector;
		$reflector->model = $object;
		// Create the validator instance, and configure it
		$localValidator = CValidator::createValidator($this->validator, $object, $attribute, $this->params);

		// Start validating
		foreach( $data as $key => $value ) {
			$reflector->$attribute = $value;
			$reflector->keyNumber = $key;
			$localValidator->validate($reflector);
		}
	}

	public function __set($name,$value)
	{
		$this->params[$name] = $value;
	}
};

class ModelReflector
{
	public $keyNumber;
	public $model;

	public function addError($attribute, $message)
	{
		$attrName = sprintf("%s[%s]", $attribute, $this->keyNumber);
		$this->model->addError($attrName, $message);
	}

	public function __call($method, $parameters)
	{
		if( method_exists($this->model, $method) ) {
			return call_user_func_array(array($this->model, $method), $parameters);
		}
		parent::__call($method, $parameters);
	}
};