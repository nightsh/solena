<?php

class DeveloperApplication extends CActiveRecord
{
	// Constants defining the status types
	const StatusPending = 0;
	const StatusApproved = 1;
	const StatusRejected = 2;
	// Constants defining the types of special reason
	const ReasonNone = 0;
	const ReasonGsoc = 1;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'developer_applications';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			// These are all required to make a valid developer application
			array('status, uid, special_reason, justification, evidence_links, ssh_key', 'required'),
			// The system itself sets uid, and the status can only be changed by sysadmins
			array('uid, status', 'unsafe', 'on' => 'insert, update'),
			// Ensure that the fields hae the correct values in them
			array('status', 'in', 'range' => array_keys($this->validStatus())),
			array('special_reason', 'in', 'range' => array_keys($this->validSpecialReason())),
			// The supporting user must exist (the applicant is set internally)
			array('uid, supporter_uid', 'length', 'max' => 64),
			array('supporter_uid', 'validateUsernameExists'),
			// A minimum length on the justification is required
			array('justification', 'length', 'min' => 25),
			// We have to have a SSH key, but it is assigned by hand
			array('ssh_key', 'unsafe'),
			array('ssh_key', 'application.validators.SSHKeyValidator'),
			// Searchable attributes
			array('status, uid, supporter_uid, special_reason', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'status' => 'Application Status',
			'uid' => 'Applicant',
			'supporter_uid' => 'Supporter',
			'special_reason' => 'Special Reason',
			'justification' => 'Justification',
			'evidence_links' => 'Supporting Evidence',
			'ssh_key' => 'SSH Key',
		);
	}

	public function getApplicant()
	{
		$filter = Net_LDAP2_Filter::create('uid', 'equals', $this->uid);
		return User::model()->findFirstByFilter($filter);
	}

	public function getSupporter()
	{
		$filter = Net_LDAP2_Filter::create('uid', 'equals', $this->supporter_uid);
		return User::model()->findFirstByFilter($filter);
	}

	public function getStatusName()
	{
		$validStatus = $this->validStatus();
		return $validStatus[$this->status];
	}

	public function getSpecialReasonName()
	{
		$validSpecialReason = $this->validSpecialReason();
		return $validSpecialReason[$this->special_reason];
	}

	public function setUploadedSSHKey($uploadedFile)
	{
		// If we have a SSH Key upload, then that needs to be loaded across into the table
		if( $uploadedFile instanceof CUploadedFile && $uploadedFile->error === UPLOAD_ERR_OK ) {
			$this->ssh_key = file_get_contents($uploadedFile->tempName);
		}
	}

	public function validStatus()
	{
		return array(0 => 'Awaiting processing', 1 => 'Approved', 2 => 'Rejected');
	}

	public function validSpecialReason()
	{
		return array(0 => 'None', 1 => 'Google Summer of Code participant');
	}

	public function validateUsernameExists($attribute, $params)
	{
		// We do not require a supporter, so do not fail them if the supporter is blank
		if( $this->$attribute === '' ) {
			return;
		}

		// Create the filter
		$filters = array();
		$filters[] = Net_LDAP2_Filter::create('uid', 'equals', $this->$attribute);
		$filters[] = Net_LDAP2_Filter::create('groupMember', 'equals', Yii::app()->params['developerGroup']);
		$filter = Net_LDAP2_Filter::combine('and', $filters);

		// Perform the search - if we do not have one result exactly then either it does not exist, or more than one user has this username...
		$result = User::model()->findByFilter($filter);
		if( $result->count() != 1 ) {
			$this->addError($attribute, "Supporter username does not exist or is not a developer.");
		}
	}

	public function approveApplication()
	{
		// Get the applicant to start with
		$applicant = $this->applicant;
		// Now we get the default (user) group
		$filter = Net_LDAP2_Filter::create('cn', 'equals', Yii::app()->params['defaultGroup']);
		$defaultGroup = Group::model()->findFirstByFilter( $filter );
		// Finally we get the developer group
		$filter = Net_LDAP2_Filter::create('cn', 'equals', Yii::app()->params['developerGroup']);
		$developerGroup = Group::model()->findFirstByFilter( $filter );
		// Now we validate everything to make sure we can approve this
		if( !$applicant instanceof User || !$defaultGroup instanceof Group || !$developerGroup instanceof Group ) {
			return false;
		}

		// Commence approving the developer application - add the SSH keys, then change over the group membership
		$applicant->addSSHKeys( $this->ssh_key );
		$defaultGroup->removeMember( $applicant );
		$developerGroup->addMember( $applicant );
		// Save this state
		$successful = $defaultGroup->save() && $developerGroup->save() && $applicant->save();
		if( !$successful ) {
			return false;
		}

		// Now we mark the application as approved - their entries have been setup
		$this->status = DeveloperApplication::StatusApproved;
		return $this->save();
	}

	public function rejectApplication()
	{
		$this->status = DeveloperApplication::StatusRejected;
		return $this->save();
	}
}