<?php

/**
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends CFormModel
{
	public $username;
	public $password;
	public $token;

	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('username, password', 'required'),
			// password needs to be authenticated
			array('password', 'authenticate'),
			// token is needed for two-factor authentication
			array('token', 'length', 'min' => 4, 'max' => 4, 'on' => 'twoFactor'),
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute, $params)
	{
		if( !$this->hasErrors() ) {
			// Authenticate the user if necessary
			$this->_identity = new UserIdentity($this->username, $this->password, $this->token);
			$this->_identity->authenticate();
			if( $this->_identity->errorCode === UserIdentity::ERROR_USERNAME_INVALID || $this->_identity->errorCode === UserIdentity::ERROR_PASSWORD_INVALID ) {
				$this->addError('authentication', 'Incorrect username or password.');
			}
			if( $this->scenario == 'twoFactor' && $this->_identity->errorCode === UserIdentity::ERROR_TWOFACTOR_INVALID ) {
				$this->addError('authentication', 'Incorrect two-factor token.');
			}
		}
	}

	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login()
	{
		if( $this->_identity === null ) {
			$this->_identity = new UserIdentity($this->username, $this->password, $this->token);
			$this->_identity->authenticate();
		}
		if( $this->_identity->errorCode === UserIdentity::ERROR_NONE ) {
			Yii::app()->user->login($this->_identity, 0);
			return true;
		} else {
			return false;
		}
	}
}
