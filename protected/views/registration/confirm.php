<?php
$this->breadcrumbs=array(
	'Register',
	'Completion',
);

$this->menu = array();
?>

<h1>Complete Registration on <?php echo Yii::app()->name; ?></h1>

<div class="form well">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'registrations-form',
	'enableAjaxValidation' => false,
)); ?>

	<p class="alert alert-info">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'uid'); ?>
		<?php echo $form->dropDownList($model, 'uid', $model->validUsernames(), array('empty'=> 'Not selected')); ?>
		<?php echo $form->error($model, 'uid'); ?>
	</div>

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
		<?php echo CHtml::submitButton('Register Account', array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>