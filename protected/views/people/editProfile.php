<?php
$this->breadcrumbs = array(
	'People' => array('index'),
	$model->cn => array('view', 'uid' => $model->uid),
	'Edit Profile',
);

$this->menu = array(
	array('label' => 'People List', 'url' => array('index')),
	array('label' => 'View Profile', 'url' => array('view', 'uid' => $model->uid)),
	array('label' => 'Edit Contact Details', 'url' => array('editContactDetails', 'uid' => $model->uid)),
	array('label' => 'Change Avatar', 'url' => array('editAvatar', 'uid' => $model->uid)),
	array('label' => 'Manage SSH Keys', 'url' => array('editKeys', 'uid' => $model->uid)),
	array('label' => 'Change Password', 'url' => array('changePassword', 'uid' => $model->uid)),
	array('label' => 'Toggle Account Lock', 'url' => array('toggleLock', 'uid' => $model->uid)),
	array('label' => 'Move Entry', 'url' => array('move', 'uid' => $model->uid)),
	array('label' => 'Delete Person', 'url' => array('delete', 'uid' => $model->uid)),
);
?>

<h1>Edit profile of <?php echo $model->cn; ?></h1>

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
		<?php echo $form->labelEx($model, 'personalTitle'); ?>
		<?php echo $form->textField($model, 'personalTitle', array('size' => 60, 'maxlength' => 128)); ?>
		<?php echo $form->error($model, 'personalTitle'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'academicTitle'); ?>
		<?php echo $form->textField($model, 'academicTitle', array('size' => 60, 'maxlength' => 128)); ?>
		<?php echo $form->error($model, 'academicTitle'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'dateOfBirth'); ?>
		<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
			'model' => $model,
			'attribute' => 'dateOfBirth',
			'options' => array(
				'showAnim' => 'fold',
				'dateFormat' => 'dd/mm/yy',
			),
			'htmlOptions' => array('size' => 60),
		)); ?>
		<?php echo $form->error($model, 'dateOfBirth'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'gender'); ?>
		<?php echo $form->dropDownList($model, 'gender', array('F' => 'Female', 'M' => 'Male', 'O' => 'Other'), array('empty'=> 'Not disclosed')); ?>
		<?php echo $form->error($model, 'gender'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'timezone'); ?>
		<?php echo $form->textField($model, 'timezone', array('size' => 60, 'maxlength' => 128)); ?>
		<?php echo $form->error($model, 'timezone'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Update Profile'); ?>
	</div>

<?php $this->endWidget(); ?>

</div>