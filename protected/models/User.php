<?php

class User extends SLdapModel
{
	// Constants defining the types of lock
	const AccountUnlocked = "Unlocked";
	const AccountTemporaryLocked = "Temporarily locked";
	const AccountPermanentLocked = "Permanently locked";
	const InfinitelyLocked = "000001010000Z"; // Magic value meaning "infinitely locked by an administrator"

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
		return array('homePostalAddress', 'homePhone', 'labeledURI', 'ircNick', 'jabberID', 'sshPublicKey');
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
			// SSH Key management
			array('sshPublicKey', 'application.validators.SSHKeyValidator', 'on' => 'editKeys'),
			// Avatar changing - 3MB max upload limit, file must be a jpeg/gif/png image
			array('jpegPhoto', 'file', 'on' => 'editAvatar', 'types' => 'jpg, gif, png', 'maxSize' => 1024 * 1024 * 3, 'allowEmpty' => true),
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
			'jpegPhoto' => 'Avatar upload',
		);
	}

	public function getEmailAddresses()
	{
		return array_merge( array($this->mail), (array) $this->secondaryMail );
	}

	/**
	 * Converts the unpresentable SSH Keys into a presentable format.
	 * Includes the key type, fingerprint and it's comment if it has one
	 */
	public function getProcessedSshKeys()
	{
		$keyData = array();
		foreach( (array) $this->getAttribute("sshPublicKey") as $id => $key) {
			$keyData[] = SSHForm::splitKey($key, $id);
		}
		return $keyData;
	}

	/**
	 * Retrieves the status of the account - unlocked, temporarily locked and indefinitely locked
	 */
	public function getAccountStatus()
	{
		if( !isset($this->pwdAccountLockedTime) ) {
			return User::AccountUnlocked;
		} else if( $this->pwdAccountLockedTime == User::InfinitelyLocked ) {
			return User::AccountPermanentLocked;
		}
		return User::AccountTemporaryLocked;
	}

	/**
	 * Hashes the given password and sets it to the user model, in preperation for saving it
	 * Does not perform the saving procedure itself
	 */
	public function changePassword($newPassword)
	{
		// Generate a new Salt
		$salt = substr(pack("h*", md5(mt_rand())), 0, 8);
		$salt = substr(sha1($salt.$newPassword, true), 0, 4);
		// Hash the password, prepending the {SSHA} indicator that LDAP relies on to identify it as a Salted-SHA1 password
		$hashedPassword = "{SSHA}".base64_encode(sha1($newPassword.$salt, true).$salt);
		// Write the password
		$this->userPassword = $hashedPassword;
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
		// Update the CN if we need to - it is only needed for the editProfile scenario
		if( $this->scenario == 'editProfile' ) {
			// The validators will prevent this code from being reached if givenName/sn are null - so no need to check
			$this->cn = sprintf("%s %s", $this->givenName, $this->sn);
		}
		
		// Do we have a newly uploaded photo?
		if( $this->jpegPhoto instanceof CUploadedFile ) {
			// Create an Imagick instance and read the image in to commence processing it
			$im = new Imagick();
			try {
				$im->readImage($this->jpegPhoto->tempName);
			} catch( Exception $e ) {
				$this->addError("jpegPhoto", "Invalid or corrupted image uploaded");
				return false;
			}
			// gif/png images which use Alpha channels are distorted without this
			$im->setImageOpacity(1.0);
			// Resize the image to 147x200 at best fit, with 0.5 sharpness
			$im->resizeImage(147, 200, Imagick::FILTER_UNDEFINED, 0.5, TRUE);
			// Save the JPEG image into LDAP
			$im->setImageFormat('jpeg'); 
			$this->jpegPhoto = $im->getImageBlob();
		}
		
		// Call our parent now
		return parent::beforeSave();
	}
}

?>