<?php

class OrganisationalUnit extends SLdapModel
{
	protected $_requiredObjectClasses = array('organizationalUnit');

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function uniqueAttributes()
	{
		return array('ou');
	}

	public function defaultAttributes()
	{
		return array('objectClass' => array('organizationalUnit') );
	}
}

?>