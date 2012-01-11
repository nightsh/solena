<?php

class SLdapSort extends CSort
{
	/**
	 * Instance of LDAP Model which will be used to provide attributes and names
	 */
	public $ldapModel = null;

	/**
	 * Constructor.
	 */
	public function __construct($ldapModel = null)
	{
		$this->ldapModel = $ldapModel;
	}

	/**
	 * @see CSort::resolveAttribute
	 */
	public function resolveAttribute($attribute)
	{
		if( $this->attributes !== array() ) {
			return parent::resolveAttribute($attribute);
		} else if( $this->ldapModel !== null && $this->ldapModel->hasAttribute($attribute) ) {
			return $attribute;
		}
		return false;
	}

	/**
	 * @see CSort::resolveLabel
	 */
	public function resolveLabel($attribute)
	{
		$definition = $this->resolveAttribute($attribute);
		if( is_string($definition) && $this->ldapModel !== null ) {
			return $this->ldapModel->getAttributeLabel($definition);
		}
		return parent::resolveLabel($attribute);
	}
}

?>