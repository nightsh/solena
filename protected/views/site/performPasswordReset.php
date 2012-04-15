<?php
$this->breadcrumbs = array(
	'Reset Password',
);

$this->menu = array();
?>

<h1>Reset password</h1>

<p>Please fill out the details below to allow your account password to be reset:

<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'passwordReset-form',
	'enableAjaxValidation' => false,
)); ?>

	<p class="alert alert-info">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'newPassword'); ?>
		<?php echo $form->passwordField($model, 'newPassword', array('size' => 60, 'maxlength' => 128)); ?>
		<?php echo $form->error($model, 'newPassword'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'confirmNewPassword'); ?>
		<?php echo $form->passwordField($model, 'confirmNewPassword', array('size' => 60, 'maxlength' => 128)); ?>
		<?php echo $form->error($model, 'confirmNewPassword'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Change password'); ?>
	</div>

<?php $this->endWidget(); ?>

</div>