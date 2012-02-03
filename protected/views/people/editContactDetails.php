<?php
$this->breadcrumbs = array(
	'People' => array('index'),
	$model->cn => array('view', 'uid' => $model->uid),
	'Edit Contact Details',
);

$this->menu = array(
	array('label' => 'People List', 'url' => array('index')),
	array('label' => 'View Profile', 'url' => array('view', 'uid' => $model->uid)),
	array('label' => 'Edit Profile', 'url' => array('editProfile', 'uid' => $model->uid)),
	array('label' => 'Change Avatar', 'url' => array('editAvatar', 'uid' => $model->uid)),
	array('label' => 'Manage SSH Keys', 'url' => array('editKeys', 'uid' => $model->uid)),
	array('label' => 'Change Password', 'url' => array('changePassword', 'uid' => $model->uid)),
	array('label' => 'Toggle Account Lock', 'url' => array('toggleLock', 'uid' => $model->uid)),
	array('label' => 'Move Entry', 'url' => array('move', 'uid' => $model->uid)),
	array('label' => 'Delete Person', 'url' => array('delete', 'uid' => $model->uid)),
);
?>

<h1>Edit contact details for <?php echo $model->cn; ?></h1>