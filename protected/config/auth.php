<?php

return array(
	// User groups which can have permissions assigned to them
	'sysadmins' => array(
		'type' => CAuthItem::TYPE_ROLE,
		'description' => 'Is a KDE Sysadmin',
		'bizRule' => '',
		'data' => '',
		'children' => array('changeUserDetails', 'changeUserSshKeys', 'changeUserAvatar', 'changeUserPassword', 'changeUserUsername'),
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
		'description' => 'Is a member of the KDE e.V',
		'bizRule' => '',
		'data' => '',
	),

	'users' => array(
		'type' => CAuthItem::TYPE_ROLE,
		'description' => 'Is a KDE Identity user',
		'bizRule' => '',
		'data' => '',
		'children' => array('selfChangeUserData'),
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
		'bizRule' => '',
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
);

?>