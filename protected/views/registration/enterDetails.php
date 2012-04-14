<?php
$this->breadcrumbs=array(
	'Register',
	'Enter details',
);

$this->menu = array();
?>

<h1>Register on <?php echo Yii::app()->name; ?></h1>

<div class="form well">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'registrations-form',
	'enableAjaxValidation' => false,
	'htmlOptions' => array('class' => 'alert alert-error'),
)); ?>

	<p class="alert alert-info">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'givenName'); ?>
		<?php echo $form->textField($model, 'givenName', array('size' => 50, 'maxlength' => 50)); ?>
		<?php echo $form->error($model, 'givenName', array('class' => 'alert alert-error')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'sn'); ?>
		<?php echo $form->textField($model, 'sn', array('size' => 50, 'maxlength' => 50)); ?>
		<?php echo $form->error($model, 'sn', array('class' => 'alert alert-error')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'mail'); ?>
		<?php echo $form->textField($model, 'mail', array('size' => 50, 'maxlength' => 50)); ?>
		<?php echo $form->error($model, 'mail', array('class' => 'alert alert-error')); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Register account', array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>