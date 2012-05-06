<?php
$this->breadcrumbs = array(
	'People' => array('index'),
	$model->cn => array('view', 'uid' => $model->uid),
	'Manage Email Addresses',
);

$this->menu = $this->generateMenu($model);
?>

<h1>Manage Email Addresses for <?php echo CHtml::encode($model->cn); ?></h1>

<div class="form well">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'emailAddress-form',
	'enableAjaxValidation' => false,
)); ?>

	<p class="alert alert-info">The primary email address cannot be removed as all accounts must have a email address associated with them.</p>

	<?php $this->widget('application.components.NeverGridView', array(
		'id' => 'emailAddress-grid',
		'dataProvider' => $emailDataProvider,
		'template' => '{items}{pager}',
		'columns'=> array(
			'mail:email:Email Address',
			array(
				'type' => 'raw',
				'value'=> 'EmailHelper::showVerification($data);',
				'htmlOptions' => array('style' => 'width: 150px;'),
			),
			array(
				'type' => 'raw',
				'value'=> 'EmailHelper::showDelete($data);',
				'htmlOptions' => array('style' => 'width: 150px;'),
			),
		),
	)); ?>

<?php $this->endWidget(); ?>

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'emailAddress-form',
	'enableAjaxValidation' => false,
)); ?>

	<?php echo $form->labelEx($token, 'mail', array('label' => 'Add Email Address')); ?>
	<?php echo $form->textField($token, 'mail', array('size' => 60, 'maxlength' => 128)); ?>
	<?php echo $form->error($token, 'mail'); ?>

	<?php echo CHtml::hiddenField('action', 'add'); ?>
	<?php echo CHtml::submitButton('Add', array('class' => 'btn btn-primary')); ?>

<?php $this->endWidget(); ?>

</div>