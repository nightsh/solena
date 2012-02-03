<?php
$this->breadcrumbs = array(
	'People' => array('index'),
	'Create',
);

$this->menu = array(
	array('label' => 'People List', 'url' => array('index')),
);
?>

<h1>Create Person</h1>

<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'person-form',
	'enableAjaxValidation' => false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'uid'); ?>
		<?php echo $form->textField($model, 'uid', array('size' => 60, 'maxlength' => 128)); ?>
		<?php echo $form->error($model, 'uid'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'givenName'); ?>
		<?php echo $form->textField($model, 'givenName', array('size' => 60, 'maxlength' => 128)); ?>
		<?php echo $form->error($model, 'givenName'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'sn'); ?>
		<?php echo $form->textField($model, 'sn', array('size' => 60, 'maxlength' => 128)); ?>
		<?php echo $form->error($model, 'sn'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'mail'); ?>
		<?php echo $form->textField($model, 'mail', array('size' => 60, 'maxlength' => 128)); ?>
		<?php echo $form->error($model, 'mail'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'parentDn'); ?>
		<?php echo $form->dropDownList($model, 'parentDn', CHtml::listData(OrganisationalUnit::model()->findByFilter(null), 'dn', 'dn'), array('empty'=> 'Select Parent Unit')); ?>
		<?php echo $form->error($model, 'parentDn'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Create'); ?>
	</div>

<?php $this->endWidget(); ?>

</div>