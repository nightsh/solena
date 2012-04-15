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
		<?php echo $form->labelEx($model, 'uid'); ?>
		<?php echo $form->textField($model, 'uid'); ?>
		<?php echo $form->error($model, 'uid'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'mail'); ?>
		<?php echo $form->textField($model, 'mail'); ?>
		<?php echo $form->error($model, 'mail'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Request password reset'); ?>
	</div>

<?php $this->endWidget(); ?>

</div>