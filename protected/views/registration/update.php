<?php
$this->breadcrumbs=array(
	'Registrations' => array('list'),
	$model->name => array('view', 'id' => $model->id),
	'Update',
);

$this->menu = array(
	array('label' => 'View Registration', 'url' => array('view', 'id' => $model->id)),
	array('label' => 'Delete Registration', 'url'=> array('delete', 'id' => $model->id)),
);
?>

<h1>Update Registration of <?php echo CHtml::encode($model->name); ?></h1>
<hr/>
<div class="form well">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id' => 'registrations-form',
	'enableAjaxValidation' => false,
)); ?>

	<p class="alert alert-info">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row-fluid">
		<?php echo $form->labelEx($model, 'givenName'); ?>
		<?php echo $form->textField($model, 'givenName', array('size' => 50,'maxlength' => 50)); ?>
	</div>

	<div class="row-fluid">
		<?php echo $form->labelEx($model, 'sn'); ?>
		<?php echo $form->textField($model, 'sn', array('size' => 50, 'maxlength' => 50)); ?>
	</div>

	<div class="row-fluid">
		<?php echo $form->labelEx($model, 'mail'); ?>
		<?php echo $form->textField($model, 'mail', array('size' => 50, 'maxlength' => 50)); ?>
	</div>

	<div class="row-fluid buttons">
		<?php echo CHtml::submitButton('Update Registration', array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>