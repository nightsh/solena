<?php
$this->breadcrumbs=array(
	'Registrations' => array('list'),
	$model->name,
);

$this->menu = array(
	array('label' => 'Update Registration', 'url' => array('update', 'id' => $model->id)),
	array('label' => 'Delete Registration', 'url'=> array('delete', 'id' => $model->id)),
);
?>

<h1>View Registration of <?php echo CHtml::encode($model->name); ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'attributes' => array(
		'givenName',
		'sn',
		'mail',
	),
	'htmlOptions' => array('class' => 'table table-bordered table-striped'),
)); ?>

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'registrations-form',
	'enableAjaxValidation' => false,
)); ?>

	<div class="row-fluid buttons">
		<?php echo CHtml::submitButton('Resend Confirmation', array('name' => 'resendConfirmation', 'class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>
