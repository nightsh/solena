<?php
$this->breadcrumbs = array(
	'Groups' => array('index'),
	'Create',
);
?>

<h1>Create Group</h1>

<div class="form well">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'group-form',
	'enableAjaxValidation' => false,
)); ?>

	<p class="alert alert-info">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model,'','',array('class' => 'alert alert-error')); ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'cn'); ?>
		<?php echo $form->textField($model, 'cn', array('size' => 60, 'maxlength' => 128)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'description'); ?>
		<?php echo $form->textField($model, 'description', array('size' => 60, 'maxlength' => 128)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Create', array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>