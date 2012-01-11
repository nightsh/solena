<?php

class User extends SLdapModel
{
	protected $_requiredObjectClasses = array('kdeAccount');

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function uniqueAttributes()
	{
		return array('uid');
	}

	public function defaultAttributes()
	{
		return array('objectClass' => array('top','person','organizationalPerson','inetOrgPerson','kdeAccount') );
	}

	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('uid, cn, mail, sn, givenName', 'required'),
			array('uid, cn, mail, sn, givenName', 'length', 'max'=>128),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('uid, cn, mail', 'safe', 'on'=>'search'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'uid' => 'Username',
			'sn' => 'Last name',
			'givenName' => 'First name',
			'cn' => 'Full name',
			'mail' => 'Email address',
		);
	}
}

?>