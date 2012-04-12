<?php

class Group extends SLdapModel
{
	const LowestGroupId = 1000;
	const HighestGroupId = 4294967296; // Generated with 'pow(2,32)' - php doesn't allow that though
	const NobodyGroupId = 65534;

	protected $_requiredObjectClasses = array('groupOfNames');

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function uniqueAttributes()
	{
		return array('cn');
	}

	public function defaultAttributes()
	{
		return array('objectClass' => array('top', 'groupOfNames', 'posixGroup') );
	}

	public function multivaluedAttributes()
	{
		return array('member', 'memberUid');
	}

	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			// Searching (used on index)
			array('cn, description', 'safe', 'on' => 'search'),
			// Shared validations...
			array('cn, description', 'required', 'on' => 'edit, create'),
			array('cn, description', 'length', 'min' => 2, 'max' => 64, 'on' => 'edit, create'),
			array('cn', 'match', 'pattern' => '/^([a-z]|-)+$/', 'on' => 'edit, create'),
			array('cn', 'UniqueAttributeValidator', 'on' => 'edit, create'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'cn' => 'Group name',
			'gidNumber' => 'Group ID Number',
		);
	}

	public function getMembers()
	{
		$filter = Net_LDAP2_Filter::create('groupMember', 'equals', $this->cn);
		return User::model()->findByFilter($filter);
	}

	public function addMember($user)
	{
		// Make sure we have a SLdapModel - User instance...
		if( !$user instanceof User ) {
			return false;
		}
		
		// Make sure the user is not already a member - no need to add them twice....
		if( in_array($user->uid, (array) $this->memberUid) ) {
			return false;
		}
		
		// Add them into the group - and update their own attributes to indicate they are a member of this group
		$this->addAttribute('memberUid', $user->uid);
		$this->addAttribute('member', $user->dn);
		$user->addAttribute('groupMember', $this->cn);
		
		// If the group is the ev-members group, then we do some very special handling
		if( $this->cn == 'ev-members' ) {
			$user->addAttribute('objectClass', 'kdeEvMember');
			$user->addAttribute('memberStatus', 'Active');
			$user->addAttribute('evMail', $user->mail);
		}
	}

	public function removeMember($user)
	{
		// Make sure we have a SLdapModel - User instance....
		if( !$user instanceof User ) {
			return false;
		}
		
		// Make sure the user is a member of the group - we cannot remove a person who is not a member of the group from the group
		if( !in_array($user->uid, (array) $this->memberUid) ) {
			return false;
		}
		
		// Remove them from the group - and update their attribute to indicate their removal from the group
		$this->removeAttribute('memberUid', $user->uid);
		$this->removeAttribute('member', $user->dn);
		$user->removeAttribute('groupMember', $this->cn);
		
		// If the group is the ev-members group, then we do some very special handling
		if( $this->cn == 'ev-members' ) {
			$user->removeAttribute('memberStatus');
			$user->removeAttribute('evMail');
			$user->removeAttribute('objectClass', 'kdeEvMember');
		}
	}

	public function updateExistingMember($user)
	{
		// Make sure the old user is a member of the group - we cannot update a existing member if they are not a member
		$originalUid = $user->getAttribute('uid', true);
		if( !in_array($originalUid, $this->memberUid) ) {
			return false;
		}

		// Remove the old user data
		$this->removeAttribute('memberUid', $originalUid);
		$this->removeAttribute('member', $user->originalDn);

		// Add in the new user data
		$this->addAttribute('memberUid', $user->uid);
		$this->addAttribute('member', $user->dn);
	}

	protected function beforeSave()
	{
		// Make sure we have a gidNumber - as we have to have one....
		if( !isset($this->gidNumber) ) {
			// Get a list of groups which have valid group id numbers
			$filter = Net_LDAP2_Filter::create('gidNumber', 'present');
			$groups = $this->findByFilter($filter, array('gidNumber'));
			
			// Turn the list of groups into a list of used group id numbers
			$used_gids = array( self::NobodyGroupId ); // We reserve these group id numbers to prevent system clashes
			foreach( $groups as $group ) {
				$used_gids[] = $group->gidNumber;
			}
			
			// Find a free group id
			for( $gid = self::LowestGroupId; $gid++; $gid < self::HighestGroupId ) {
				if( !in_array($gid, $used_gids) ) {
					$this->gidNumber = $gid;
					break;
				}
			}
			
			// Final safety check
			if( !isset($this->gidNumber) ) {
				$this->addError('system', 'Failed to allocate an unused Group ID Number');
				return false;
			}
		}
		
		// Do we need to invoke a workaround? (RFC2307 requires the 'member' attribute to be present - we need to allow empty groups)
		if( empty($this->memberUid) ) {
			$this->member = $this->dn;
		}
		
		// Do we need to cleanup the workaround for RFC2307 compliance?
		if( !empty($this->memberUid) && in_array($this->dn, $this->member) ) {
			$this->removeAttribute('member', $this->dn);
		}
		
		// Call our parent now
		return parent::beforeSave();
	}

	protected function afterMove()
	{
		// We need to update group members with the new name of the group
		$originalName = $this->getAttribute('cn', true);
		$filter = Net_LDAP2_Filter::create('groupMember', 'equals', $originalName);
		$currentMembers = User::model()->findByFilter($filter);
		foreach( $currentMembers as $member ) {
			$member->removeAttribute('groupMember', $originalName);
			$member->addAttribute('groupMember', $this->cn);
			$member->save();
		}

		// Call our parent now
		return parent::afterMove();
	}

	protected function afterDelete()
	{
		// We need to remove our members from our group now....
		foreach( $this->members as $member ) {
			$this->removeMember($member);
			$member->save();
		}

		// Call our parent now
		return parent::afterDelete();
	}
};

?>