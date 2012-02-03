<?php
$this->breadcrumbs = array(
	'People' => array('index'),
	$model->cn,
);

$this->menu = array(
	array('label' => 'People List', 'url' => array('index')),
	array('label' => 'Edit Profile', 'url' => array('editProfile', 'uid' => $model->uid)),
	array('label' => 'Edit Contact Details', 'url' => array('editContactDetails', 'uid' => $model->uid)),
	array('label' => 'Change Avatar', 'url' => array('editAvatar', 'uid' => $model->uid)),
	array('label' => 'Manage SSH Keys', 'url' => array('editKeys', 'uid' => $model->uid)),
	array('label' => 'Change Password', 'url' => array('changePassword', 'uid' => $model->uid)),
	array('label' => 'Toggle Account Lock', 'url' => array('toggleLock', 'uid' => $model->uid)),
	array('label' => 'Move Entry', 'url' => array('move', 'uid' => $model->uid)),
	array('label' => 'Delete Person', 'url' => array('delete', 'uid' => $model->uid)),
);
?>

<h1><?php echo $model->cn; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'attributes' => array(
		'uid',
		'personalTitle',
		'academicTitle',
		'dateOfBirth',
		'gender',
		'timezone',
	),
)); ?>
<br />

<h2>Contact information</h2>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'attributes' => array(
		'homePostalAddress:ntext',
		'homePhone',
		'labeledURI:url',
		'ircNick',
		'jabberId',
		'emailAddresses:email',
	),
)); ?>