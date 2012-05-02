<?php
$this->breadcrumbs = array(
	'People' => array('index'),
	$model->cn => array('view', 'uid' => $model->uid),
	'Edit Contact Details',
);

$this->menu = $this->generateMenu($model);
?>

<h1>Edit Contact Details for <?php echo CHtml::encode($model->cn); ?></h1>

<div class="form well">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'person-form',
	'enableAjaxValidation' => false,
)); ?>

	<p class="alert alert-info">Please ensure you save your contact details after adding details.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php $this->widget('application.widgets.MultiEdit', array(
		'model' => $model,
		'attribute' => 'homePhone',
		'editorHtmlOptions' => array('size' => 60),
	)); ?>

	<?php $this->widget('application.widgets.MultiEdit', array(
		'model' => $model,
		'attribute' => 'homePostalAddress',
		'editorType' => 'textarea',
		'editorHtmlOptions' => array('cols' => 60, 'rows' => 3),
	)); ?>

	<?php $this->widget('application.widgets.MultiEdit', array(
		'model' => $model,
		'attribute' => 'labeledURI',
		'editorHtmlOptions' => array('size' => 60),
	)); ?>

	<?php $this->widget('application.widgets.MultiEdit', array(
		'model' => $model,
		'attribute' => 'ircNick',
		'editorHtmlOptions' => array('size' => 60),
	)); ?>

	<?php $this->widget('application.widgets.MultiEdit', array(
		'model' => $model,
		'attribute' => 'jabberID',
		'editorHtmlOptions' => array('size' => 60),
	)); ?>

	<div class="row-fluid buttons">
		<?php echo CHtml::submitButton('Update Contact Details', array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>
