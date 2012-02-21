<?php
$this->breadcrumbs = array(
	'People' => array('index'),
	$model->cn => array('view', 'uid' => $model->uid),
	'Move (change parent organisational unit)',
);

$this->menu = $this->generateMenu($model);
?>

<h1>Change parent organisational unit of <?php echo $model->cn; ?></h1>

<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'person-form',
	'enableAjaxValidation' => false,
)); ?>

	<p class="note">This will change the Parent Organisation Unit for <?php echo $model->cn; ?> which may affect LDAP based logins</p>

	<div class="row">
		<?php echo $form->labelEx($model, 'parentDn'); ?>
		<?php echo $form->dropDownList($model, 'parentDn', CHtml::listData(OrganisationalUnit::model()->findByFilter(null), 'dn', 'dn'), array('empty'=> 'Select Parent Unit')); ?>
		<?php echo $form->error($model, 'parentDn'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Move account'); ?>
	</div>

<?php $this->endWidget(); ?>

</div>