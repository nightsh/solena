<?php
$this->breadcrumbs = array(
	'Groups' => array('index'),
	$model->description => array('view', 'cn' => $model->cn),
	'Remove Member',
);

$this->menu = $this->generateMenu($model);
?>

<h1>Remove Member From Group <?php echo CHtml::encode($model->cn); ?>
<small><?php echo CHtml::encode($model->description); ?></small></h1>

<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'group-form',
	'enableAjaxValidation' => false,
)); ?>

	<div class="alert">
		<h4 class="alert-heading">Warning!</h4>
		The member '<?php echo CHtml::encode($member->cn); ?>' will be removed from the group '<?php echo CHtml::encode($model->description); ?>' if you proceed.
	</div>

	<div class="row-fluid buttons">
		<?php echo CHtml::submitButton('Confirm Removal', array('name' => 'confirmRemoval', 'class' => 'btn btn-primary')); ?>
	</div>

	<?php echo CHtml::hiddenField('selectedPerson[0]', $member->dn); ?>

<?php $this->endWidget(); ?>

</div>