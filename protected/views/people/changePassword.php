<?php
$this->breadcrumbs = array(
	'People' => array('index'),
	$model->cn => array('view', 'uid' => $model->uid),
	'Change Password',
);

$this->menu = array(
	array('label' => 'People List', 'url' => array('index')),
	array('label' => 'View Profile', 'url' => array('view', 'uid' => $model->uid)),
	array('label' => 'Edit Profile', 'url' => array('editProfile', 'uid' => $model->uid)),
	array('label' => 'Edit Contact Details', 'url' => array('editContactDetails', 'uid' => $model->uid)),
	array('label' => 'Change Avatar', 'url' => array('editAvatar', 'uid' => $model->uid)),
	array('label' => 'Manage SSH Keys', 'url' => array('editKeys', 'uid' => $model->uid)),
	array('label' => 'Toggle Account Lock', 'url' => array('toggleLock', 'uid' => $model->uid)),
	array('label' => 'Move Entry', 'url' => array('move', 'uid' => $model->uid)),
	array('label' => 'Delete Person', 'url' => array('delete', 'uid' => $model->uid)),
);
?>

<h1>Change Password for <?php echo $model->cn; ?></h1>

<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'person-form',
	'enableAjaxValidation' => false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($passwordForm); ?>

	<?php if( Yii::app()->user->dn == $model->dn ) { ?>
		<div class="row">
			<?php echo $form->labelEx($passwordForm, 'currentPassword'); ?>
			<?php echo $form->passwordField($passwordForm, 'currentPassword', array('size' => 60, 'maxlength' => 128)); ?>
			<?php echo $form->error($passwordForm, 'currentPassword'); ?>
		</div>
	<?php } ?>

	<div class="row">
		<?php echo $form->labelEx($passwordForm, 'newPassword'); ?>
		<?php echo $form->passwordField($passwordForm, 'newPassword', array('size' => 60, 'maxlength' => 128)); ?>
		<?php echo $form->error($passwordForm, 'newPassword'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($passwordForm, 'confirmNewPassword'); ?>
		<?php echo $form->passwordField($passwordForm, 'confirmNewPassword', array('size' => 60, 'maxlength' => 128)); ?>
		<?php echo $form->error($passwordForm, 'confirmNewPassword'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Change Password'); ?>
	</div>

<?php $this->endWidget(); ?>

</div>