<?php

/**
 * PHP file based authorization manager for Yii which dynamically assigns the roles a user has to them instead of storing them
 */
class PhpAuthManager extends CPhpAuthManager
{
	public function init()
	{
		// Change the authorization config file default to protected/config/auth.php
		if( $this->authFile === null ) {
			$this->authFile = Yii::getPathOfAlias('application.config.auth').'.php';
		}

		parent::init();

		// Assign the roles to the current user, if we have one...
		if( !Yii::app()->user->isGuest ) {
			foreach(Yii::app()->user->roles as $role) {
				$this->assign($role, Yii::app()->user->id);
			}
		}
	}

	protected function checkAttributeWritable($attribute, $model)
	{
		return false;
	}
}

?>