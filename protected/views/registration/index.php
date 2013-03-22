<?php
$this->breadcrumbs=array(
	'Register',
	'Begin',
);

$this->menu = array();
?>

<h1>Register on <?php echo Yii::app()->name; ?></h1>

<?php if( SiteReferer::getReferer() != null ): ?>
	<p class="well">
		You have been redirected here from <?php echo CHtml::link(SiteReferer::getReferer(), SiteReferer::getReferer()); ?> for registering your account.
	</p>
<?php endif; ?>

<div class="form">

<p>I agree to abide by the terms of the <a href="http://www.kde.org/code-of-conduct">KDE Code of Conduct</a></p>

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'registrations-form',
	'enableAjaxValidation' => false,
)); ?>

	<div class="row-fluid">
		<?php echo CHtml::checkbox('confirmAcceptance') ?>
		I accept the above conditions
	</div>

	<div class="row-fluid buttons">
	<?php echo CHtml::submitButton('Continue Registration', array('name' => 'continue', 'class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>
