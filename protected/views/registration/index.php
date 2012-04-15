<?php
$this->breadcrumbs=array(
	'Register',
	'Begin',
);

$this->menu = array();
?>

<h1>Register on <?php echo Yii::app()->name; ?></h1>

<div class="form">

<p>I agree to abide by the terms of the <a href="http://www.kde.org/code-of-conduct">KDE Code of Conduct</a></p>

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'registrations-form',
	'enableAjaxValidation' => false,
)); ?>

	<div class="row">
		<?php echo CHtml::checkbox('confirmAcceptance') ?>
		I accept the above conditions
	</div>

	<div class="row buttons">
	<?php echo CHtml::submitButton('Continue Registration', array('name' => 'continue', 'class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>