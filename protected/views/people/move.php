<?php
$this->breadcrumbs = array(
	'People' => array('index'),
	$model->cn => array('view', 'uid' => $model->uid),
	'Move (Change Parent Organisational Unit)',
);

$this->menu = $this->generateMenu($model);
?>

<h1>Change Parent Organisational Unit of <?php echo CHtml::encode($model->cn); ?></h1>

<div class="form well">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'person-form',
	'enableAjaxValidation' => false,
)); ?>

	<p class="alert alert-info">This will change the Parent Organisational Unit for <?php echo CHtml::encode($model->cn); ?> which may affect LDAP based logins.</p>
	<?php echo $form->errorSummary($model); ?>

	<div class="row-fluid">
		<?php echo $form->labelEx($model, 'parentDn'); ?>
		<?php echo $form->dropDownList($model, 'parentDn', CHtml::listData(OrganisationalUnit::model()->findByFilter(null), 'dn', 'dn'), array('empty'=> 'Select Parent Unit')); ?>
	</div>

	<div class="row-fluid buttons">
		<?php echo CHtml::submitButton('Move Account', array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>