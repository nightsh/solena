<?php
$this->breadcrumbs = array(
	'People' => array('index'),
	'Create',
);
?>

<h1>Create Person</h1>

<div class="form well">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'person-form',
	'enableAjaxValidation' => false,
)); ?>

	<p class="alert alert-info">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row-fluid-fluid">
		<?php echo $form->labelEx($model, 'uid'); ?>
		<?php echo $form->textField($model, 'uid', array('size' => 60, 'maxlength' => 128)); ?>
	</div>

	<div class="row-fluid">
		<?php echo $form->labelEx($model, 'givenName'); ?>
		<?php echo $form->textField($model, 'givenName', array('size' => 60, 'maxlength' => 128)); ?>
	</div>

	<div class="row-fluid">
		<?php echo $form->labelEx($model, 'sn'); ?>
		<?php echo $form->textField($model, 'sn', array('size' => 60, 'maxlength' => 128)); ?>
	</div>

	<div class="row-fluid">
		<?php echo $form->labelEx($model, 'mail'); ?>
		<?php echo $form->textField($model, 'mail', array('size' => 60, 'maxlength' => 128)); ?>
	</div>

	<div class="row-fluid">
		<?php echo $form->labelEx($model, 'parentDn'); ?>
		<?php echo $form->dropDownList($model, 'parentDn', CHtml::listData(OrganisationalUnit::model()->findByFilter(null), 'dn', 'dn'), array('empty'=> 'Select Parent Unit')); ?>
	</div>

	<div class="row-fluid buttons">
		<?php echo CHtml::submitButton('Create', array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>