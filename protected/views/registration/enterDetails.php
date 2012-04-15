<?php
$this->breadcrumbs=array(
	'Register',
	'Enter Details',
);

$this->menu = array();
?>

<h1>Register on <?php echo Yii::app()->name; ?></h1>

<div class="form well">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'registrations-form',
	'enableAjaxValidation' => false,
)); ?>

	<p class="alert alert-info">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'givenName'); ?>
		<?php echo $form->textField($model, 'givenName', array('size' => 50, 'maxlength' => 50)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'sn'); ?>
		<?php echo $form->textField($model, 'sn', array('size' => 50, 'maxlength' => 50)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'mail'); ?>
		<?php echo $form->textField($model, 'mail', array('size' => 50, 'maxlength' => 50)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Register Account', array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>