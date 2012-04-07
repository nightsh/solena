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
		return array(
			// Operational validations
			array('token, type', 'unsafe'),
			array('token, type', 'required'),
			array('token', 'length', 'max' => 50),
			array('type', 'numerical', 'integerOnly' => true),
			// Data validations
			array('uid', 'length', 'max' => 64, 'on' => 'verify, reset'),
			array('mail', 'email', 'on' => 'verify, register'),
			array('mail', 'unique', 'on' => 'verify, register'), // Database check
			array('mail', 'validateUniqueEmail', 'on' => 'verify, register'), // LDAP check
			array('mail', 'length', 'max' => 64, 'on' => 'verify, register'),
			// Verify address validations
			array('uid, mail', 'required', 'on' => 'verify'),
			// Registration validations
			array('mail, givenName, sn', 'required', 'on' => 'register'),
			array('givenName, sn', 'length', 'max' => 64, 'on' => 'register'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'uid' => 'Username',
			'sn' => 'Last name',
			'givenName' => 'First name',
			'mail' => 'Email address',
		);
	}

	public function getName()
	{
		return sprintf('%s %s', $this->givenName, $this->sn);
	}

	public function validateUniqueEmail($attribute, $params)
	{
		// Build the filter for the LDAP check
		$filters = array();
		$filters[] = Net_LDAP2_Filter::create('mail', 'equals', $this->$attribute);
		$filters[] = Net_LDAP2_Filter::create('secondaryMail', 'equals', $this->$attribute);
		$filter = Net_LDAP2_Filter::combine('or', $filters);

		// Check against LDAP
		$result = User::model()->findByFilter($filter);
		if( $result->count() > 0 ) {
			$this->addError("mail", "Email address is already in use.");
		}
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