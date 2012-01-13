<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	private $fullName;

	/**
	 * Authenticates a user.
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		// Perform a search to see if the provided username exists, and to retrieve it's DN....
		$attributes = array('uid', 'cn', 'groupMember');
		$filter = Net_LDAP2_Filter::create('uid', 'equals', $this->username);
		$entry = User::model()->findFirstByFilter($filter, $attributes);
		
		// Is the entry valid?
		if( is_null($entry) ) {
			$this->errorCode = self::ERROR_USERNAME_INVALID;
			return false;
		}
		
		// Try to bind as the entry we got (ie. the username is valid - now check the password)
		$dn = $entry->getDn();
		$valid = User::getLdapConnection()->reauthenticate($dn, $this->password);
		if( !$valid ) {
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
			return false;
		}
		
		// Set the user state up now - and return true
		$this->errorCode = self::ERROR_NONE;
		$this->fullName = $entry->cn;
		$this->username = $entry->uid;
		return true;
	}

	public function getName()
	{
		return $this->fullName;
	}
}