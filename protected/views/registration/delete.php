<?php
$this->breadcrumbs=array(
	'Registrations' => array('list'),
	$model->name => array('view', 'id' => $model->id),
	'Delete',
);

$this->menu = array(
	array('label' => 'View Registration', 'url' => array('view', 'id' => $model->id)),
	array('label' => 'Update Registration', 'url'=> array('update', 'id' => $model->id)),
);
?>

<h1>Delete Registration of <?php echo CHtml::encode($model->name); ?></h1>

<div class="alert alert-warning">
	<h4 class="alert-heading">Warning!</h4>
	The registration of '<?php echo CHtml::encode($model->name); ?>' will be deleted if you proceed.
</div>
<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'registrations-form',
	'enableAjaxValidation' => false,
)); ?>

	<div class="well">
		<p>
			<?php echo CHtml::checkbox('confirmDeletion') ?>
			Confirm registration deletion
		</p>

		<div class="row-fluid buttons">
			<?php echo CHtml::submitButton('Delete Registration', array('name' => 'deleteAccount', 'class' => 'btn btn-primary')); ?>
		</div>
	</div>
	
<?php $this->endWidget(); ?>

</div>