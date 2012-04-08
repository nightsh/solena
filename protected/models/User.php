<?php

require_once('StringCleaner.php');

class User extends SLdapModel
{
	// Constants defining the types of lock
	const AccountUnlocked = "Unlocked";
	const AccountTemporaryLocked = "Temporarily locked";
	const AccountPermanentLocked = "Permanently locked";
	const InfinitelyLocked = "000001010000Z"; // Magic value meaning "infinitely locked by an administrator"

	protected $_requiredObjectClasses = array('kdeAccount');
	public $sshKeysAdded = array();
	public $currentPassword = '';
	public $newPassword = '';
	public $confirmNewPassword = '';

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
		return array('homePostalAddress', 'homePhone', 'labeledURI', 'ircNick', 'jabberID', 'sshPublicKey', 'groupMember');
	}

	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			// Searching (used on index)
			array('uid, cn, mail, secondaryMail', 'safe', 'on' => 'search'),
			// Shared validations...
			array('uid, givenName, sn', 'required', 'on' => 'editProfile, create, register'),
			array('uid, givenName, sn', 'length', 'min' => 2, 'max' => 64, 'on' => 'editProfile, create, register'),
			array('uid', 'match', 'pattern' => '/^([a-z]|-)+$/', 'on' => 'editProfile, create, register'),
			array('uid', 'UniqueAttributeValidator', 'on' => 'editProfile, create, register'),
			array('mail', 'email', 'on' => 'editContactDetails, create, register'),
			// Profile Editing
			array('uid', 'unsafe', 'on' => 'editProfile'), // The username can never be mass-assigned
			array('dateOfBirth', 'date', 'format' => 'dd/MM/yyyy', 'on' => 'editProfile'),
			array('gender', 'in', 'range' => array_keys($this->validGenders()), 'on' => 'editProfile'),
			array('timezoneName', 'in', 'range' => array_keys($this->validTimezones()), 'on' => 'editProfile'),
			// KDE e.V membership detail editing
			array('memberStatus', 'unsafe', 'on' => 'editProfile'),
			array('memberStatus', 'in', 'range' => array_keys($this->validMemberStatus()), 'on' => 'editProfile'),
			array('evMail', 'in', 'range' => array_keys($this->validEmailAddresses()), 'on' => 'editProfile'),
			// Contact Details editing
			array('mail, secondaryMail', 'unsafe', 'on' => 'editContactDetails'),
			array('jabberID', 'MultiValidator', 'validator' => 'email', 'on' => 'editContactDetails'),
			array('secondaryMail', 'MultiValidator', 'validator' => 'email',  'on' => 'editContactDetails'),
			array('labeledURI', 'MultiValidator', 'validator' => 'url', 'defaultScheme' => 'http', 'on' => 'editContactDetails'),
			array('homePostalAddress, homePhone, ircNick', 'MultiValidator', 'validator' => 'length',  'min' => 4, 'on' => 'editContactDetails'),
			// SSH Key management
			array('sshPublicKey', 'application.validators.SSHKeyValidator', 'on' => 'editKeys'),
			array('sshKeysAdded', 'application.validators.SSHKeyValidator', 'existingKeys' => (array) $this->getAttribute("sshPublicKey"), 'on' => 'editKeys'),
			// Avatar changing - 3MB max upload limit, file must be a jpeg/gif/png image
			array('jpegPhoto', 'file', 'types' => 'jpg, jpeg, gif, png', 'maxSize' => 1024 * 1024 * 3, 'allowEmpty' => true, 'on' => 'editAvatar'),
			// Password validation - to ensure only Salted-SHA1 passwords are saved to protect outselves
			array('userPassword', 'unsafe', 'on' => 'changePassword, register'), // The direct value for the password can never be mass-assigned
			array('userPassword', 'match', 'pattern' => '/\{SSHA\}.+/', 'on' => 'changePassword, register'),
			array('currentPassword', 'verifyPassword', 'on' => 'changePassword'),
			array('newPassword', 'length', 'min' => 6, 'on' => 'changePassword, register'),
			array('newPassword, confirmNewPassword', 'required', 'on' => 'changePassword, register'),
			array('newPassword', 'compare', 'compareAttribute' => 'confirmNewPassword', 'on' => 'changePassword, register'),
			// User creation
			array('mail', 'required', 'on' => 'create'),
			// User registration
			array('givenName, sn, mail', 'unsafe', 'on' => 'register'), // Do not massive assign these, as they are previously known
			array('uid', 'in', 'range' => array_keys($this->validUsernames()), 'on' => 'register'),
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
			'memberStatus' => 'e.V Membership Status',
			'evMail' => 'Email address for e.V matters',
		);
	}

	/**
	 * Returns Group instances for the groups this user is a member of
	 */
	public function getGroups()
	{
		$filter = Net_LDAP2_Filter::create('memberUid', 'equals', $this->uid);
		return Group::model()->findByFilter($filter);
	}

	public function getEmailAddresses()
	{
		return array_merge( array($this->mail), (array) $this->secondaryMail );
	}

	/**
	 * Provides detailed information about the email addresses held by this user
	 * Specifies the primary, secondary and pending addresses - and includes this information
	 */
	public function getEmailAddressData()
	{
		// Start the list of email data, starting with the primary address....
		$emailData = array( array('id' => 0, 'mail' => $this->mail, 'type' => 'primary') );

		// Now add secondary addresses
		foreach( (array) $this->secondaryMail as $address ) {
			$emailData[] = array('id' => count($emailData), 'mail' => $address, 'type' => 'secondary');
		}

		// Finally add any pending addresses...
		$pendingAddresses = Token::model()->findAllByAttributes( array('uid' => $this->uid, 'type' => Token::TypeVerifyAddress) );
		foreach( $pendingAddresses as $pending ) {
			$emailData[] = array('id' => count($emailData), 'mail' => $pending->mail, 'type' => 'pending');
		}

		return $emailData;
	}

	/**
	 * Sets the primary email address for the user.
	 * The given address must already be set as a secondary address
	 */
	public function setPrimaryEmailAddress($address)
	{
		if( !in_array($address, $this->secondaryMail) ) {
			return false;
		}
		$this->addAttribute("secondaryMail", $this->mail);
		$this->removeAttribute("secondaryMail", $address);
		$this->replaceAttribute("mail", $address);
		return true;
	}

	/**
	 * Removes the given email address from the user entry.
	 * The given address must be either a secondary or pending address.
	 * This applies immediately, and will save all pending changes!
	 */
	public function removeEmailAddress($address)
	{
		// We search for a possibly pending address first, and if found will delete that
		$pending = Token::model()->findByAttributes( array('uid' => $this->uid, 'type' => Token::TypeVerifyAddress, 'mail' => $address) );
		if( $pending instanceof CActiveRecord ) {
			return $pending->delete();
		// No pending address found, so maybe it is a secondary address...
		} else if( in_array($address, $this->secondaryMail) ) {
			$this->removeAttribute("secondaryMail", $address);
			return $this->save();
		}
		// Neither was found, invalid request so we fail it
		return false;
	}

	/**
	 * Converts the unpresentable SSH Keys into a presentable format.
	 * Includes the key type, fingerprint and it's comment if it has one
	 */
	public function getProcessedSshKeys()
	{
		$keyData = array();
		foreach( (array) $this->getAttribute("sshPublicKey") as $id => $key) {
			$keyData[] = SSHKeyValidator::splitKey($key, $id);
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
	 * Stages the uploaded SSH Keys for saving to the model
	 * They will not be available until the model has been validated and saved
	 * Does not perform the saving procedure itself
	 */
	public function addSSHKeys($uploadedKeys)
	{
		// Retrieve the content of the file, and then stage the keys for addition so they can be validated first
		$keys = file_get_contents($uploadedKeys->tempName);
		$keys = explode("\n", trim($keys));
		$this->sshKeysAdded = array_merge($keys, $this->sshKeysAdded);
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

	public function validMemberStatus()
	{
		return array('Active' => 'Active', 'Extraordinary' => 'Extraordinary', 'Supporting' => 'Supporting');
	}

	public function validEmailAddresses()
	{
		$addresses = array();
		foreach( $this->emailAddresses as $email ) {
			$addresses[$email] = $email;
		}
		return $addresses;
	}

	public function validUsernames()
	{
		// Prepare
		$prospects = array();
		$firstname = cleanString($this->givenName);
		$lastname  = cleanString($this->sn);

		// Build a prospective list of usernames
		$prospects[] = substr($firstname, 0, 1) . $lastname; // jsmith
		$prospects[] = $firstname . substr($lastname, 0, 1); // johns
		$prospects[] = $lastname; // smith
		$prospects[] = $firstname . $lastname; // johnsmith
		$prospects[] = $lastname . $firstname; // smithjohn

		// Actualise the prospective addresses by ensuring they are not already in use
		$usernames = array();
		foreach($prospects as $prospect) {
			$filter = Net_LDAP2_Filter::create('uid', 'equals', $prospect);
			$results = User::model()->findFirstByFilter( $filter, array('uid') );
			if( is_null($results) && strlen($prospect) > 4) {
				$usernames[$prospect] = $prospect;
			}
		}

		return $usernames;
	}

	public function verifyPassword($attribute, $params)
	{
		// We only do this verification if they are changing their own password, or if we do not have a User instance yet
		if( $this->isNewObject || Yii::app()->user->dn != $this->dn ) {
			return true;
		}
		// Now we check their password...
		$state = User::getLdapConnection()->reauthenticate($this->dn, $this->$attribute);
		if( !$state ) {
			$this->addError($attribute, 'Password is not correct');
		}
		return $state;
	}

	protected function beforeSave()
	{
		// Update the CN if we need to - it is only needed for the editProfile and create scenarios
		if( $this->scenario == 'editProfile' || $this->scenario == 'create' || $this->scenario == 'register' ) {
			// The validators will prevent this code from being reached if givenName/sn are null - so no need to check
			$this->cn = sprintf("%s %s", $this->givenName, $this->sn);
		}
		
		// Have we got a changed password?
		if( $this->newPassword != '' ) {
			$this->changePassword($this->newPassword);
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
		
		// Have we got newly uploaded SSH Keys to add?
		if( !empty($this->sshKeysAdded) ) {
			if( !$this->hasObjectClass("ldapPublicKey") ) {
				$this->addAttribute("objectClass", "ldapPublicKey");
			}
			$this->addAttribute("sshPublicKey", $this->sshKeysAdded);
		}
		// Do we no longer have any SSH Keys?
		if( $this->hasObjectClass("ldapPublicKey") && empty($this->sshPublicKey) && $this->scenario == 'editKeys') {
			$this->removeAttribute("objectClass", "ldapPublicKey");
		}
		
		// Call our parent now
		return parent::beforeSave();
	}

	protected function afterSave()
	{
		// If the password has been changed, then make sure the user session is updated if needed
		if( $this->newPassword != '' && Yii::app()->user->dn == $this->dn ) {
			User::getLdapConnection()->reauthenticate($this->dn, $this->newPassword);
			User::getLdapConnection()->retainCredentials();
		}

		// Call our parent now
		return parent::afterSave();
	}

	protected function afterCreate()
	{
		// Is the user a member of any groups? If they are not, add them to the default group
		if( $this->groups->count() == 0 ) {
			$filter = Net_LDAP2_Filter::create('cn', 'equals', Yii::app()->params['defaultGroup']);
			$entry = Group::model()->findFirstByFilter($filter);
			if( $entry instanceof Group ) {
				$entry->addMember($this);
				$this->save();
				$entry->save();
			}
		}

		// Call our parent now
		return parent::afterCreate();
	}

	protected function afterMove()
	{
		// If the current user is changing their dn we have to update the saved dn otherwise future transactions will break....
		if( Yii::app()->user->dn == $this->originalDn ) {
			User::getLdapConnection()->updateRetainedDn( $this->dn );
			Yii::app()->user->setId( $this->uid );
		}

		// If they are a member of any groups - we need to update the groups with their new dn / uid
		$filter = Net_LDAP2_Filter::create('memberUid', 'equals', $this->getAttribute('uid', true));
		$currentGroups = Group::model()->findByFilter($filter);
		foreach( $currentGroups as $group ) {
			$group->updateExistingMember($this);
			$group->save();
		}

		// Call our parent now
		return parent::afterMove();
	}

	protected function beforeDelete()
	{
		// Make sure we are not trying to delete ourselves, as that is strictly prohibited
		if( Yii::app()->user->dn == $this->dn ) {
			$this->addError("dn", "People cannot delete themselves - suicide not permitted");
		}

		// Call our parent now
		return parent::beforeDelete();
	}

	protected function afterDelete()
	{
		// Remove us from any groups we were a member of prior to deletion
		foreach( $this->groups as $group ) {
			$group->removeMember($this);
			$group->save();
		}

		// Call our parent now
		return parent::afterDelete();
	}
}

?>