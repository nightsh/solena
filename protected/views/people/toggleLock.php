<?php
$this->breadcrumbs = array(
	'People' => array('index'),
	$model->cn => array('view', 'uid' => $model->uid),
	'Lock/Unlock account',
);

$this->menu = array(
	array('label' => 'People List', 'url' => array('index')),
	array('label' => 'View Profile', 'url' => array('view', 'uid' => $model->uid)),
	array('label' => 'Edit Profile', 'url' => array('editProfile', 'uid' => $model->uid)),
	array('label' => 'Edit Contact Details', 'url' => array('editContactDetails', 'uid' => $model->uid)),
	array('label' => 'Change Avatar', 'url' => array('editAvatar', 'uid' => $model->uid)),
	array('label' => 'Manage SSH Keys', 'url' => array('editKeys', 'uid' => $model->uid)),
	array('label' => 'Change Password', 'url' => array('changePassword', 'uid' => $model->uid)),
	array('label' => 'Move Entry', 'url' => array('move', 'uid' => $model->uid)),
	array('label' => 'Delete Person', 'url' => array('delete', 'uid' => $model->uid)),
);
?>

<h1>Lock/Unlock account of <?php echo $model->cn; ?></h1>

<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'person-form',
	'enableAjaxValidation' => false,
)); ?>

	<p class="note">Temporary locks are performed automatically when too many invalid password attempts are made, and will expire automatically</p>
	<p>Account Status: <?php echo $model->accountStatus; ?></p>

	<div class="row buttons">
		<?php 
			if( $model->accountStatus != User::AccountUnlocked ) {
				echo CHtml::submitButton('Unlock account', array('name' => 'unlockAccount'));
			}
			if( $model->accountStatus != User::AccountPermanentLocked ) {
				echo CHtml::submitButton('Lock account infinitely', array('name' => 'lockAccount'));
			}
		?>
	</div>

<?php $this->endWidget(); ?>

</div>