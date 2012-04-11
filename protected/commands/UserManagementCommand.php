<?php

class UserManagementCommand extends CConsoleCommand
{
	private $currentUser;
	private $developerGroup;
	private $disabledDeveloperGroup;

	public function actionDisableDeveloper($username)
	{
		// Load the data, then verify that they are a developer
		$this->loadData($username);
		if( !in_array($this->developerGroup->cn, $this->currentUser->groupMember) ) {
			throw new CException('The given user is not a member of the developer group, and so cannot be disabled');
		}

		// Change their group membership now
		$this->developerGroup->removeMember( $this->currentUser );
		$this->disabledDeveloperGroup->addMember( $this->currentUser );
		// Save the changes
		$successful = $this->developerGroup->save() && $this->disabledDeveloperGroup->save() && $this->currentUser->save();
		if( !$successful ) {
			throw new CException('The changes to the group membership could not be saved');
		}
	}

	public function actionEnableDeveloper($username)
	{
		// Load the data, then verify that they are a developer
		$this->loadData($username);
		if( !in_array($this->disabledDeveloperGroup->cn, $this->currentUser->groupMember) ) {
			throw new CException('The given user is not a member of the disabled developer group, and so cannot be enabled');
		}

		// Change their group membership now
		$this->disabledDeveloperGroup->removeMember( $this->currentUser );
		$this->developerGroup->addMember( $this->currentUser );
		// Save the changes
		$successful = $this->disabledDeveloperGroup->save() &&  $this->developerGroup->save() && $this->currentUser->save();
		if( !$successful ) {
			throw new CException('The changes to the group membership could not be saved');
		}
	}

	private function loadData($username)
	{
		// First we retrieve the developer to disable
		$filter = Net_LDAP2_Filter::create('uid', 'equals', $username);
		$this->currentUser = User::model()->findFirstByFilter($filter);

		// Now we get the developer group
		$filter = Net_LDAP2_Filter::create('cn', 'equals', Yii::app()->params['developerGroup']);
		$this->developerGroup = Group::model()->findFirstByFilter( $filter );

		// Finally we get the disabled developer group
		$filter = Net_LDAP2_Filter::create('cn', 'equals', Yii::app()->params['disabledDeveloperGroup']);
		$this->disabledDeveloperGroup = Group::model()->findFirstByFilter( $filter );

		// Now we validate everything to make sure we can alter this
		if( !$this->currentUser instanceof User || !$this->developerGroup instanceof Group || !$this->disabledDeveloperGroup instanceof Group ) {
			throw new CException('Either the user specified could not be found, or the groups have not been configured');
		}
	}
};

?>