<?php
$this->breadcrumbs = array(
	'Groups' => array('index'),
	$model->description => array('view', 'cn' => $model->cn),
	'Remove Member',
);

$this->menu = $this->generateMenu($model);
?>

<h1>Remove member from group <?php echo CHtml::encode($model->description); ?></h1>

<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'group-form',
	'enableAjaxValidation' => false,
)); ?>

	<div class="row">
		The member '<?php echo CHtml::encode($member->cn); ?>' will be removed from the group '<?php echo CHtml::encode($model->description); ?>' if you proceed.
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Confirm removal', array('name' => 'confirmRemoval')); ?>
	</div>

	<?php echo CHtml::hiddenField('selectedPerson[0]', $member->dn); ?>

<?php $this->endWidget(); ?>

</div>