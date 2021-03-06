<?php

/**
 * WebUser represents the persistent state for a Web application user.
 */
class WebUser extends CWebUser
{
	/**
	 * Cached copy of the user model which we use
	 */
	private $_model;

	/**
	 * Return the list of groups the user is in as their account roles
	 * A user is a member of no groups if they cannot be found
	 */
	public function getRoles()
	{
		$user = $this->getModel();
		if( !$user ) {
			return array();
		}
		$groupMember = $user->groupMember;
		return (array) $groupMember;
	}

	/**
	 * Returns the DN of the currently logged in user
	 * Returns null if nobody is logged in
	 */
	public function getDn()
	{
		if( $this->isGuest ) {
			return null;
		}
		return $this->getModel()->dn;
	}

	/**
	 * Find the user's entry in LDAP
	 */
	private function getModel()
	{
		if( !$this->isGuest && $this->_model === null ) {
			$filter = Net_LDAP2_Filter::create('uid', 'equals', $this->id);
			$this->_model = User::model()->findFirstByFilter($filter, array('groupMember'));
		}
		return $this->_model;
	}

	protected function afterLogin($fromCookie)
	{
		User::getLdapConnection()->retainCredentials();
	}

	protected function afterLogout()
	{
		User::getLdapConnection()->discardCredentials();
	}
}

?>