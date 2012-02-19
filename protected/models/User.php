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
			// Searching (used on index)
			array('uid, cn, mail, secondaryMail', 'safe', 'on'=>'search'),
			// Shared validations...
			array('uid, givenName, sn', 'required', 'on' => 'editProfile, create'),
			array('uid, givenName, sn', 'length', 'min' => 2, 'max' => 64, 'on' => 'editProfile, create'),
			array('mail', 'email', 'on' => 'editContactDetails, create'),
			// Profile Editing
			array('uid', 'unsafe', 'on' => 'editProfile'), // The username can never be mass-assigned
			array('dateOfBirth', 'date', 'format' => 'dd/MM/yyyy', 'on' => 'editProfile'),
			array('gender', 'in', 'range' => array_keys($this->validGenders()), 'on' => 'editProfile'),
			array('timezoneName', 'in', 'range' => array_keys($this->validTimezones()), 'on' => 'editProfile'),
			// Contact Details editing
			array('homePostalAddress, homePhone, labeledURI, ircNick, jabberID', 'safe', 'on' => 'editContactDetails'),
			// SSH Key management
			array('sshPublicKey', 'application.validators.SSHKeyValidator', 'on' => 'editKeys'),
			// Avatar changing - 3MB max upload limit, file must be a jpeg/gif/png image
			array('jpegPhoto', 'file', 'on' => 'editAvatar', 'types' => 'jpg, jpeg, gif, png', 'maxSize' => 1024 * 1024 * 3, 'allowEmpty' => true),
			// Password validation - to ensure only Salted-SHA1 passwords are saved to protect outselves
			array('userPassword', 'match', 'pattern' => '/\{SSHA\}.+/'),
			// User creation
			array('mail', 'required', 'on' => 'create'),
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
			'homePostalAddress' => 'Physical address',
			'ircNick' => 'IRC Nickname',
			'jabberID' => 'Jabber ID',
			'jpegPhoto' => 'Avatar upload',
			'timezoneName' => 'Timezone',
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
		$timezoneNames = array();
		foreach( DateTimeZone::listIdentifiers() as $tzName ) {
			$timezoneNames[$tzName] = $tzName;
		}
		return $timezoneNames;
	}

	protected function beforeSave()
	{
		// Update the CN if we need to - it is only needed for the editProfile and create scenarios
		if( $this->scenario == 'editProfile' || $this->scenario == 'create' ) {
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
		
		// Have we set a timezoneName? If so, clear timezone as it is deprecated now
		if( isset($this->timezoneName) && isset($this->timezone) ) {
			$this->removeAttribute('timezone');
		}
		
		// Call our parent now
		return parent::beforeSave();
	}
}

?>