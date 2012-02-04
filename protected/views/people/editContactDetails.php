<?php
$this->breadcrumbs = array(
	'People' => array('index'),
	$model->cn => array('view', 'uid' => $model->uid),
	'Edit Contact Details',
);

$this->menu = array(
	array('label' => 'People List', 'url' => array('index')),
	array('label' => 'View Profile', 'url' => array('view', 'uid' => $model->uid)),
	array('label' => 'Edit Profile', 'url' => array('editProfile', 'uid' => $model->uid)),
	array('label' => 'Change Avatar', 'url' => array('editAvatar', 'uid' => $model->uid)),
	array('label' => 'Manage SSH Keys', 'url' => array('editKeys', 'uid' => $model->uid)),
	array('label' => 'Change Password', 'url' => array('changePassword', 'uid' => $model->uid)),
	array('label' => 'Toggle Account Lock', 'url' => array('toggleLock', 'uid' => $model->uid)),
	array('label' => 'Move Entry', 'url' => array('move', 'uid' => $model->uid)),
	array('label' => 'Delete Person', 'url' => array('delete', 'uid' => $model->uid)),
);
?>

<h1>Edit contact details for <?php echo $model->cn; ?></h1>

<div class="form">

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

	<?php $this->widget('application.widgets.MultiEdit', array(
		'model' => $model,
		'attribute' => 'emailAddresses',
		'editorHtmlOptions' => array('size' => 60),
	)); ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Update Contact Details'); ?>
	</div>

<?php $this->endWidget(); ?>

</div>
