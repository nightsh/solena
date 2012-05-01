<?php

return array(
	// User groups which can have permissions assigned to them
	'sysadmins' => array(
		'type' => CAuthItem::TYPE_ROLE,
		'description' => 'Is a KDE Sysadmin',
		'bizRule' => '',
		'data' => '',
		'children' => array('changeUserDetails', 'changeUserSshKeys', 'changeUserAvatar', 'changeUserPassword', 'changeUserUsername', 'manageEvMembershipData', 'manageGroup', 'manageDeveloperApplications'),
	),

	'web-admins' => array(
		'type' => CAuthItem::TYPE_ROLE,
		'description' => 'Is a KDE Website Administrator',
		'bizRule' => '',
		'data' => '',
	),

	'developers' => array(
		'type' => CAuthItem::TYPE_ROLE,
		'description' => 'Is a KDE Developer',
		'bizRule' => '',
		'data' => '',
		'children' => array('selfChangeUserData', 'selfChangeUserSshKeys'),
	),

	'disabled-developers' => array(
		'type' => CAuthItem::TYPE_ROLE,
		'description' => 'Is a inactive KDE Developer',
		'bizRule' => '',
		'data' => '',
		'children' => array('selfChangeUserData', 'selfChangeUserSshKeys'),
	),

	'ev-members' => array(
		'type' => CAuthItem::TYPE_ROLE,
		'description' => 'Is a member of the KDE e.V.',
		'bizRule' => '',
		'data' => '',
		'children' => array('selfChangeUserEvDetails'),
	),

	'ev-board' => array(
		'type' => CAuthItem::TYPE_ROLE,
		'description' => 'Is a member of the KDE e.V. Board',
		'bizRule' => '',
		'data' => '',
		'children' => array('manageEvMembership', 'manageEvMembershipData'),
	),

	'users' => array(
		'type' => CAuthItem::TYPE_ROLE,
		'description' => 'Is a KDE Identity user',
		'bizRule' => '',
		'data' => '',
		'children' => array('selfChangeUserData', 'selfViewDeveloperApplication'),
	),

	// Operations - we check the access to an operation on these
	'changeUserDetails' => array(
		'type' => CAuthItem::TYPE_OPERATION,
		'description' => 'Allowed to modify user profile and contact details',
		'bizRule' => '',
		'data' => '',
	),

	'changeUserSshKeys' => array(
		'type' => CAuthItem::TYPE_OPERATION,
		'description' => 'Allowed to modify user SSH keys',
		'bizRule' => 'return in_array("developers", $params["user"]->groupMember) || in_array("disabled-developers", $params["user"]->groupMember);',
		'data' => '',
	),

	'changeUserAvatar' => array(
		'type' => CAuthItem::TYPE_OPERATION,
		'description' => 'Allowed to change user avatars',
		'bizRule' => '',
		'data' => '',
	),

	'changeUserPassword' => array(
		'type' => CAuthItem::TYPE_OPERATION,
		'description' => 'Allowed to change user passwords',
		'bizRule' => '',
		'data' => '',
	),

	'changeUserUsername' => array(
		'type' => CAuthItem::TYPE_OPERATION,
		'description' => 'Allowed to change user usernames',
		'bizRule' => '',
		'data' => '',
	),

	'changeUserEvDetails' => array(
		'type' => CAuthItem::TYPE_OPERATION,
		'description' => 'Allowed to change user KDE e.V. membership related details, excluding type',
		'bizRule' => 'return in_array("ev-members", $params["user"]->groupMember);',
		'data' => '',
	),

	'manageGroup' => array(
		'type' => CAuthItem::TYPE_OPERATION,
		'description' => 'Allowed to manage a group, including the descriptions and members',
		'bizRule' => '',
		'data' => '',
	),

	'manageDeveloperApplications' => array(
		'type' => CAuthItem::TYPE_OPERATION,
		'description' => 'Allowed to view and manage developer applications',
		'bizRule' => '',
		'data' => '',
	),

	// Tasks - grant permission to conduct an operation
	'selfChangeUserData' => array(
		'type' => CAuthItem::TYPE_TASK,
		'description' => 'Users are allowed to change their own profile, contact details, avatar and password',
		'bizRule' => 'return Yii::app()->user->dn == $params["user"]->dn;',
		'data' => '',
		'children' => array('changeUserDetails', 'changeUserAvatar', 'changeUserPassword'),
	),

	'selfChangeUserSshKeys' => array(
		'type' => CAuthItem::TYPE_TASK,
		'description' => '(Disabled) Developers are allowed to change their SSH keys',
		'bizRule' => 'return Yii::app()->user->dn == $params["user"]->dn;',
		'data' => '',
		'children' => array('changeUserSshKeys'),
	),

	'selfChangeUserEvDetails' => array(
		'type' => CAuthItem::TYPE_TASK,
		'description' => 'e.V. Members are permitted to change their own membership details',
		'bizRule' => 'return Yii::app()->user->dn == $params["user"]->dn;',
		'data' => '',
		'children' => array('changeUserEvDetails'),
	),

	'selfViewDeveloperApplication' => array(
		'type' => CAuthItem::TYPE_TASK,
		'description' => 'Users are allowed to view their own developer application',
		'bizRule' => 'return Yii::app()->user->id == $params["application"]->uid;',
		'data' => '',
		'children' => array('manageDeveloperApplications'),
	),

	'manageEvMembership' => array(
		'type' => CAuthItem::TYPE_TASK,
		'description' => 'People permitted to manage the ev-members and ev-board groups',
		'bizRule' => 'return $params["group"]->cn == "ev-members" || $params["group"]->cn == "ev-board";',
		'data' => '',
		'children' => array('manageGroup'),
	),

	'manageEvMembershipData' => array(
		'type' => CAuthItem::TYPE_TASK,
		'description' => 'Allowed to access and change data on KDE e.V. members',
		'bizRule' => 'return in_array("ev-members", $params["user"]->groupMember);',
		'data' => '',
		'children' => array('changeUserEvDetails'),
	),
);

?>