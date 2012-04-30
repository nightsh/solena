<?php
$this->pageTitle=Yii::app()->name . ' - Login';
$this->breadcrumbs=array(
	'Login',
);
?>

<h1>Login</h1>

<p>Please fill out the following form with your login credentials:</p>

<div class="form well">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>
	<p>Not registered yet? Go to the <a href="<?php Yii::app()->controller->createUrl('/registration/index');?>">Registration Page</a></p>
	<p class="alert alert-info">Fields with <span class="required">*</span> are required.</p>

	<div class="row-fluid">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username'); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

	<div class="row-fluid">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password'); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>

	<div class="row-fluid buttons">
		<?php echo CHtml::submitButton('Login', array('class' => 'btn btn-primary')); ?>
		<?php echo CHtml::link('Reset Password', array('passwordReset')); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->
