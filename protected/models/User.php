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

	public function multivaluedAttributes()
	{
		return array('homePostalAddress', 'homePhone', 'labeledURI', 'ircNick', 'jabberID', 'emailAddresses');
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
			array('gender', 'in', 'range' => array_keys($this->validGenders())),
			array('timezone', 'in', 'range' => array_keys($this->validTimezones())),
			// Searching (used on index)
			array('uid, cn, mail, secondaryMail', 'safe', 'on'=>'search'),
			// Profile Editing
			array('givenName, sn', 'required', 'on' => 'editProfile'),
			array('personalTitle, academicTitle, dateOfBirth, gender, timezone', 'safe', 'on' => 'editProfile'),
			// Contact Details editing
			array('homePostalAddress, homePhone, labeledURI, ircNick, jabberID', 'safe', 'on' => 'editContactDetails'),
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
			'jabberID' => 'Jabber ID',
		);
	}

	public function getEmailAddresses()
	{
		return array_merge( array($this->mail), (array) $this->secondaryMail );
	}

	public function validGenders()
	{
		return array('F' => 'Female', 'M' => 'Male', 'O' => 'Other');
	}

	public function validTimezones()
	{
		return array("-1200" => "GMT - 12"   , "-1100" => "GMT - 11"  , "-1030" => "GMT - 10:30", "-1000" => "GMT - 10"  , "-0930" => "GMT - 9:30" , "-0900" => "GMT - 9",
		             "-0830" => "GMT - 8:30" , "-0800" => "GMT - 8"   , "-0700" => "GMT - 7"    , "-0600" => "GMT - 6"   , "-0500" => "GMT - 5"    , "-0430" => "GMT - 4:30",
		             "-0400" => "GMT - 4"    , "-0330" => "GMT - 3:30", "-0300" => "GMT - 3"    , "-0230" => "GMT - 2:30", "-0200" => "GMT - 2"    , "-1000" => "GMT - 1",
		             "-0044" => "GMT - 0:44" , "-0025" => "GMT - 0:25", "+0000" => "GMT"        , "+0020" => "GMT + 0:20", "+0030" => "GMT + 0:30" , "+0100" => "GMT + 1",
		             "+0124" => "GMT + 1:24" , "+0200" => "GMT + 2"   , "+0230" => "GMT + 2:30" , "+0300" => "GMT + 3"   , "+0330" => "GMT + 3:30" , "+0400" => "GMT + 4",
		             "+0430" => "GMT + 4:30" , "+0451" => "GMT + 4:51", "+0500" => "GMT + 5"    , "+0530" => "GMT + 5:30", "+0540" => "GMT + 5:40" , "+0545" => "GMT + 5:45",
		             "+0600" => "GMT + 6"    , "+0630" => "GMT + 6:30", "+0700" => "GMT + 7"    , "+0720" => "GMT + 7:20", "+0730" => "GMT + 7:30" , "+0800" => "GMT + 8",
		             "+0830" => "GMT + 8:30" , "+0845" => "GMT + 8:45", "+0900" => "GMT + 9"    , "+0930" => "GMT + 9:30", "+0945" => "GMT + 9:45" , "+1000" => "GMT + 10",
		             "+1030" => "GMT + 10:30", "+1100" => "GMT + 11"  , "+1130" => "GMT + 11:30", "+1200" => "GMT + 12"  , "+1245" => "GMT + 12:45", "+1300" => "GMT + 13",
		             "+1345" => "GMT + 13:45", "+1400" => "GMT + 14");
	}

	protected function beforeSave()
	{
		// Are both givenName and sn available to us?
		if( is_null($this->givenName) || is_null($this->sn) ) {
			return false;
		}
		// Update the attribute "cn" based on the value of givenName and sn
		$givenName = $this->givenName;
		$sn = $this->sn;
		$this->cn = "$givenName $sn";
		
		// Call our parent now
		return parent::beforeSave();
	}
}

?>