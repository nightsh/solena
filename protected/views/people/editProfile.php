<?php
$this->breadcrumbs = array(
	'People' => array('index'),
	$model->cn => array('view', 'uid' => $model->uid),
	'Edit Profile',
);

$this->menu = $this->generateMenu($model);
?>

<h1>Edit profile of <?php echo CHtml::encode($model->cn); ?></h1>

<div class="form well">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'person-form',
	'enableAjaxValidation' => false,
)); ?>

	<p class="alert alert-info">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php if( Yii::app()->user->checkAccess('changeUserUsername', array('user' => $model)) ) { ?>
		<div class="row">
			<?php echo $form->labelEx($model, 'uid'); ?>
			<?php echo $form->textField($model, 'uid', array('size' => 60, 'maxlength' => 128)); ?>
			<?php echo $form->error($model, 'uid'); ?>
		</div>
	<?php } ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'givenName'); ?>
		<?php echo $form->textField($model, 'givenName', array('size' => 60, 'maxlength' => 128)); ?>
		<?php echo $form->error($model, 'givenName'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'sn'); ?>
		<?php echo $form->textField($model, 'sn', array('size' => 60, 'maxlength' => 128)); ?>
		<?php echo $form->error($model, 'sn'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'dateOfBirth'); ?>
		<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
			'model' => $model,
			'attribute' => 'dateOfBirth',
			'options' => array(
				'showAnim' => 'fold',
				'dateFormat' => 'dd/mm/yy',
			),
			'htmlOptions' => array('size' => 60),
		)); ?>
		<?php echo $form->error($model, 'dateOfBirth'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'gender'); ?>
		<?php echo $form->dropDownList($model, 'gender', $model->validGenders(), array('empty'=> 'Not set')); ?>
		<?php echo $form->error($model, 'gender'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'timezoneName'); ?>
		<?php echo $form->dropDownList($model, 'timezoneName', $model->validTimezones(), array('empty'=> 'Not set')); ?>
		<?php echo $form->error($model, 'timezoneName'); ?>
	</div>

	<?php if( Yii::app()->user->checkAccess('manageEvMembershipData', array('user' => $model)) ) { ?>
		<div class="row">
			<?php echo $form->labelEx($model, 'memberStatus'); ?>
			<?php echo $form->dropDownList($model, 'memberStatus', $model->validMemberStatus(), array('empty'=> 'Not set')); ?>
			<?php echo $form->error($model, 'memberStatus'); ?>
		</div>
	<?php } ?>

	<?php if( Yii::app()->user->checkAccess('changeUserEvDetails', array('user' => $model)) ) { ?>
		<div class="row">
			<?php echo $form->labelEx($model, 'evMail'); ?>
			<?php echo $form->dropDownList($model, 'evMail', $model->validEmailAddresses(), array('empty'=> 'Not set')); ?>
			<?php echo $form->error($model, 'evMail'); ?>
		</div>
	<?php } ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Update Profile', array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>