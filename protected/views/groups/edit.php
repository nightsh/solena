<?php
$this->breadcrumbs = array(
	'Groups' => array('index'),
	$model->description => array('view', 'cn' => $model->cn),
	'Edit',
);

$this->menu = $this->generateMenu($model);
?>

<h1>Edit Information of Group <?php echo CHtml::encode($model->description); ?></h1>

<div class="form well">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'group-form',
	'enableAjaxValidation' => false,
)); ?>

	<p class="alert alert-info">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row-fluid">
		<?php echo $form->labelEx($model, 'cn'); ?>
		<?php echo $form->textField($model, 'cn', array('size' => 60, 'maxlength' => 128)); ?>
	</div>

	<div class="row-fluid">
		<?php echo $form->labelEx($model, 'description'); ?>
		<?php echo $form->textField($model, 'description', array('size' => 60, 'maxlength' => 128)); ?>
	</div>

	<div class="row-fluid buttons">
		<?php echo CHtml::submitButton('Update Group', array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>