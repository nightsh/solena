<?php
$this->breadcrumbs = array(
	'Groups' => array('index'),
	'Create',
);
?>

<h1>Create Group</h1>

<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'group-form',
	'enableAjaxValidation' => false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'cn'); ?>
		<?php echo $form->textField($model, 'cn', array('size' => 60, 'maxlength' => 128)); ?>
		<?php echo $form->error($model, 'cn'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'description'); ?>
		<?php echo $form->textField($model, 'description', array('size' => 60, 'maxlength' => 128)); ?>
		<?php echo $form->error($model, 'description'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Create'); ?>
	</div>

<?php $this->endWidget(); ?>

</div>