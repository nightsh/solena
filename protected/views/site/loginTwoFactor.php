<?php
$this->pageTitle=Yii::app()->name . ' - Two-Factor Authentication';
$this->breadcrumbs=array(
	'Two-Factor Authentication',
);
?>

<h1>Two-Factor Authentication</h1>

<p>Your KDE Identity account is currently protected with Two-Factor Authentication, please complete it to login.</p>

<div class="form well">
<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'loginTwoFactor-form',
	'enableClientValidation' => false,
)); ?>

	<p>Please enter the token from position '<?php echo CHtml::encode($gridPosition); ?>'.</p>
	<div class="row-fluid">
		<?php echo $form->labelEx($model, 'token'); ?>
		<?php echo $form->textField($model, 'token'); ?>
		<?php echo $form->error($model, 'token'); ?>
	</div>

	<div class="row-fluid">
		<?php echo $form->labelEx($model, 'password'); ?>
		<?php echo $form->passwordField($model, 'password'); ?>
		<?php echo $form->error($model, 'password'); ?>
	</div>

	<div class="row-fluid">
		<?php echo $form->error($model, 'authentication'); ?>
	</div>

	<div class="row-fluid buttons">
		<?php echo CHtml::submitButton('Complete Login', array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->
