<?php
$this->breadcrumbs = array(
	'People' => array('index'),
	$model->cn => array('view', 'uid' => $model->uid),
	'Change Password',
);

$this->menu = $this->generateMenu($model);
?>

<h1>Change Password for <?php echo CHtml::encode($model->cn); ?></h1>

<div class="form well">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'person-form',
	'enableAjaxValidation' => false,
)); ?>

	<p class="alert alert-info">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php if( Yii::app()->user->dn == $model->dn ) { ?>
		<div class="row">
			<?php echo $form->labelEx($model, 'currentPassword'); ?>
			<?php echo $form->passwordField($model, 'currentPassword', array('size' => 60, 'maxlength' => 128)); ?>
		</div>
	<?php } ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'newPassword'); ?>
		<?php echo $form->passwordField($model, 'newPassword', array('size' => 60, 'maxlength' => 128)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'confirmNewPassword'); ?>
		<?php echo $form->passwordField($model, 'confirmNewPassword', array('size' => 60, 'maxlength' => 128)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Change Password', array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>