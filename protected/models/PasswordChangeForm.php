<?php

class PasswordChangeForm extends CFormModel
{
	public $currentPassword;
	public $newPassword;
	public $confirmNewPassword;
	public $model;

	public function rules()
	{
		return array(
			// Ensure the current password is valid
			array('currentPassword', 'verifyPassword'),
			// The new password must always be present
			array('newPassword, confirmNewPassword', 'required'),
			// Ensure the new password matches with the confirmation of it
			array('newPassword', 'compare', 'compareAttribute' => 'confirmNewPassword'),
			// We want a minimum length for the new password
			array('newPassword', 'length', 'min' => 6),
		);
	}

	public function verifyPassword($attribute, $params)
	{
		// We only do this verification if they are changing their own password...
		if( Yii::app()->user->dn != $this->model->dn ) {
			return true;
		}
		// Now we check their password...
		$state = User::getLdapConnection()->reauthenticate($this->model->dn, $this->$attribute);
		if( !$state ) {
			$this->addError($attribute, 'Password is not correct');
		}
		return $state;
	}
}
