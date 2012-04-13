<?php
$this->breadcrumbs = array(
	'People' => array('index'),
	$model->cn => array('view', 'uid' => $model->uid),
	'Edit Contact Details',
);

$this->menu = $this->generateMenu($model);
?>

<h1>Edit contact details for <?php echo CHtml::encode($model->cn); ?></h1>

<div class="form well">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'person-form',
	'enableAjaxValidation' => false,
)); ?>

	<p class="note">Please ensure you save your contact details after adding details</p>

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

	<div class="row buttons">
		<?php echo CHtml::submitButton('Update Contact Details', array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

<hr />

<h3>Email addresses</h3>

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'emailAddress-form',
	'action' => array('changeEmail', 'uid' => $model->uid),
	'enableAjaxValidation' => false,
)); ?>

	<p class="note">Email address changes will take effect immediately, and do not require saving</p>

	<?php $this->widget('zii.widgets.grid.CGridView', array(
		'id' => 'emailAddress-grid',
		'itemsCssClass' => 'table table-bordered table-striped',
		'dataProvider' => $emailDataProvider,
		'template' => '{items}',
		'columns'=> array(
			'mail:email:Email address',
			array(
				'type' => 'raw',
				'value' => '($data["type"] == "secondary") ? CHtml::submitButton("Set as primary", array("submit" => "", "params" => array("action" => "primary", "mail" => $data["mail"])) ) : ""',
				'htmlOptions' => array('style' => 'width: 100px;'),
			),
			array(
				'type' => 'raw',
				'value'=> '($data["type"] == "pending") ? CHtml::submitButton("Resend verification", array("submit" => "", "class" => "btn" "params" => array("action" => "resend", "mail" => $data["mail"])) ) : ""',
				'htmlOptions' => array('style' => 'width: 100px;'),
			),
			array(
				'type' => 'raw',
				'value'=> '($data["type"] != "primary") ? CHtml::submitButton("Remove address", array("submit" => "", "class" => "btn", "params" => array("action" => "remove", "mail" => $data["mail"])) ) : ""',
				'htmlOptions' => array('style' => 'width: 100px;'),
			),
		),
	)); ?>

<?php $this->endWidget(); ?>

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'emailAddress-form',
	'action' => array('changeEmail', 'uid' => $model->uid),
	'enableAjaxValidation' => false,
)); ?>

	<?php echo CHtml::label('New email address', 'mail'); ?>
	<?php echo CHtml::textField('mail', ''); ?>
	<?php echo CHtml::submitButton('Add address', array("submit" => "", 'class' => 'btn btn-primary', "params" => array("action" => "add")) ); ?>

<?php $this->endWidget(); ?>

</div>
