<?php
$this->breadcrumbs=array(
	'Registrations' => array('list'),
	$model->name => array('view', 'id' => $model->id),
	'Delete',
);

$this->menu = array(
	array('label' => 'View registration', 'url' => array('view', 'id' => $model->id)),
	array('label' => 'Update registration', 'url'=> array('update', 'id' => $model->id)),
);
?>

<h1>Delete registration of <?php echo CHtml::encode($model->name); ?></h1>

<div class="form well">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'registrations-form',
	'enableAjaxValidation' => false,
)); ?>

	<div class="row">
		The registration of '<?php echo CHtml::encode($model->name); ?>' will be deleted if you proceed.
	</div>

	<div class="row">
		<?php echo CHtml::checkbox('confirmDeletion') ?>
		Confirm registration deletion
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Delete registration', array('name' => 'deleteAccount', 'class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>