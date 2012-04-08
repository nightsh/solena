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
			array('mail', 'email'),
			array('mail', 'length', 'max' => 64),
			array('mail', 'unique', 'on' => 'verify, register'), // Database check
			array('mail', 'validateUniqueEmail', 'on' => 'verify, register'), // LDAP check
			// Verify address validations
			array('uid, mail', 'required', 'on' => 'verify'),
			// Registration validations
			array('mail, givenName, sn', 'required', 'on' => 'register'),
			array('givenName, sn', 'length', 'max' => 64, 'on' => 'register'),
			// Password reset validations
			array('mail, uid', 'required', 'on' => 'reset'),
			array('mail', 'validateEmailMatches', 'on' => 'reset'),
			array('uid', 'validateUsernameExists', 'on' => 'reset'),
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

	public function validateUsernameExists($attribute, $params)
	{
		// Create the filter and perform the search
		$filter = Net_LDAP2_Filter::create('uid', 'equals', $this->$attribute);
		$result = User::model()->findByFilter($filter);
		// If we do not have one result exactly then either it does not exist, or more than one user has this username...
		if( $result->count() != 1 ) {
			$this->addError("uid", "Username does not exist.");
		}
	}

	public function validateEmailMatches($attribute, $params)
	{
		// Build the email filters
		$mailFilters = array();
		$mailFilters[] = Net_LDAP2_Filter::create('mail', 'equals', $this->$attribute);
		$mailFilters[] = Net_LDAP2_Filter::create('secondaryMail', 'equals', $this->$attribute);
		// Combine the mail filters with a username filter
		$filters = array();
		$filters[] = Net_LDAP2_Filter::combine('or', $mailFilters);
		$filters[] = Net_LDAP2_Filter::create('uid', 'equals', $this->uid);
		// Create the final filter, and perform the search
		$filter = Net_LDAP2_Filter::combine('and', $filters);
		$result = User::model()->findByFilter($filter);
		if( $result->count() != 1 ) {
			$this->addError("mail", "Email address does not match those known for the given username");
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