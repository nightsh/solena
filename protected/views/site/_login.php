<div class="form well">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id' => 'login-form',
	'action' => array('/site/login'),
	'enableClientValidation' => false,
)); ?>

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

	<div class="row-fluid">
		<?php echo $form->error($model, 'authentication'); ?>
	</div>

	<div class="row-fluid buttons">
		<?php echo CHtml::submitButton('Login', array('class' => 'btn btn-primary')); ?>
		<?php echo CHtml::link('Reset Password', array('passwordReset')); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->