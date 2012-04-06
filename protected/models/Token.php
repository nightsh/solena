<?php

class Token extends CActiveRecord
{
	/**
	 * Types of Token which can exist
	 */
	const TypeVerifyAddress = 1;
	const TypeRegisterAccount = 2;
	const TypeResetPassword = 3;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Token the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tokens';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('token, type, mail, uid, givenName, sn', 'required'),
			array('type', 'numerical', 'integerOnly' => true),
			array('token', 'length', 'max' => 50),
			array('mail, uid, givenName, sn', 'length', 'max' => 255),
		);
	}

	protected function beforeValidate()
	{
		// If we do not yet have token, generate one
		if( !isset($this->token) ) {
			$key = sprintf('%08x%08x%08x%08x',mt_rand(),mt_rand(),mt_rand(),mt_rand());
			$this->token = sha1($key);
		}

		// Call our parent
		return parent::beforeValidate();
	}
}