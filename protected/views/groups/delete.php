<?php
$this->breadcrumbs = array(
	'Groups' => array('index'),
	$model->description => array('view', 'cn' => $model->cn),
	'Delete',
);

$this->menu = $this->generateMenu($model);
?>

<h1>Delete group <?php echo CHtml::encode($model->description); ?></h1>

<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'group-form',
	'enableAjaxValidation' => false,
)); ?>

	<div class="alert">
		<h4 class="alert-heading">Warning!</h4>
		The group '<?php echo CHtml::encode($model->cn); ?>' will be deleted if you proceed.
	</div>
	<div class="well">
		<div class="row">
			<?php echo CHtml::checkbox('confirmDeletion') ?>
			Confirm group deletion
		</div>

		<div class="row buttons">
			<?php echo CHtml::submitButton('Delete group', array('name' => 'deleteGroup', 'class' => 'btn btn-primary')); ?>
		</div>
	</div>

<?php $this->endWidget(); ?>

</div>