<?php
$this->breadcrumbs = array(
	'People' => array('index'),
	$model->cn => array('view', 'uid' => $model->uid),
	'Delete',
);

$this->menu = $this->generateMenu($model);
?>

<h1>Delete account of <?php echo CHtml::encode($model->cn); ?></h1>

<div class="alert">
	<h4 class="alert-heading">Warning!</h4>
	The account of '<?php echo CHtml::encode($model->uid); ?>' will be deleted if you proceed.
</div>

<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'person-form',
	'enableAjaxValidation' => false,
)); ?>
	<div class="well">
		<p>
			<?php echo CHtml::checkbox('confirmDeletion') ?>
			Confirm account deletion
		</p>
		<div class="row buttons">
			<?php echo CHtml::submitButton('Delete account', array('name' => 'deleteAccount', 'class' => 'btn btn-primary')); ?>
		</div>
	</div>
	
<?php $this->endWidget(); ?>

</div>