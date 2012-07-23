<?php

class GroupManagementCommand extends CConsoleCommand
{
	public function actionSyncEvActive()
	{
		// First we retrieve the ev-active group
		$filter = Net_LDAP2_Filter::create('cn', 'equals', 'ev-active');
		$evActiveGroup = Group::model()->findFirstByFilter( $filter );

		// Now we retrieve the the list of people who are currently active e.V. members
		$memberFilter = Net_LDAP2_Filter::create('groupMember', 'equals', 'ev-members');
		$activeFilter = Net_LDAP2_Filter::create('memberStatus', 'equals', 'Active');
		$filter = Net_LDAP2_Filter::combine('and', array($memberFilter, $activeFilter));
		$currentMembers = User::model()->findByFilter($filter);

		// Now we validate everything to make sure we can alter this
		if( !$evActiveGroup instanceof Group || !$currentMembers instanceof SLdapSearchResult ) {
			throw new CException('Failure to find the requirements to run');
		}

		// Rekey the list of currently active members
		$keyedMembers = array();
		foreach( $currentMembers as $member ) {
			$username = $member->uid;
			$keyedMembers[$username] = $member;
		}

		// Get a list of currently listed people
		$listedPeople = array();
		foreach( $evActiveGroup->members as $person ) {
			$username = $person->uid;
			$listedPeople[$username] = $person;
		}

		// Remove anyone who is no longer a valid member
		$toRemove = array_diff_key( $listedPeople, $keyedMembers );
		foreach( $toRemove as $username => $person ) {
			// Remove them
			$evActiveGroup->removeMember($person);
			$person->save();
		}

		// Add anyone who is not listed currently
		$toAdd = array_diff_key( $keyedMembers, $listedPeople );
		foreach( $toAdd as $username => $person ) {
			// Add them
			$evActiveGroup->addMember($person);
			if( !$person->save() ) {
				print "Exception!";
			}
		}

		// Save changes to the group
		$evActiveGroup->save();
	}
};

?>