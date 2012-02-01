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
			// General Validation
			array('givenName, sn, personalTitle, academicTitle', 'length', 'min' => 2, 'max' => 64),
			array('mail, secondaryMail', 'email'),
			array('dateOfBirth', 'date', 'format' => 'dd/MM/yyyy'),
			array('labeledURI', 'url'),
			array('uid', 'required'),
			// Searching (used on index)
			array('uid, cn, mail, secondaryMail', 'safe', 'on'=>'search'),
			// Profile Editing
			array('givenName, sn', 'required', 'on' => 'editProfile'),
			array('personalTitle, academicTitle, dateOfBirth, gender, timezone', 'safe', 'on' => 'editProfile'),
			// User creation
			array('uid, givenName, sn, mail', 'required', 'on' => 'create')
		);
	}

	public function attributeLabels()
	{
		return array(
			'parentDn' => 'Parent Organisational Unit',
			'uid' => 'Username',
			'sn' => 'Last name',
			'givenName' => 'First name',
			'cn' => 'Full name',
			'mail' => 'Email address',
			'labeledURI' => 'Website',
			'ircNick' => 'IRC Nickname',
			'jabberId' => 'Jabber ID',
		);
	}

	public function getEmailAddresses()
	{
		return array_merge( array($this->mail), (array) $this->secondaryMail );
	}
}

?>