<?php
$this->breadcrumbs = array(
	'People' => array('index'),
	$model->cn => array('view', 'uid' => $model->uid),
	'Edit Profile',
);

$this->menu = $this->generateMenu($model);
?>

<h1>Edit Profile of <?php echo CHtml::encode($model->cn); ?></h1>

<div class="form well">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'person-form',
	'enableAjaxValidation' => false,
)); ?>

	<p class="alert alert-info">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php if( Yii::app()->user->checkAccess('changeUserUsername', array('user' => $model)) ) { ?>
		<div class="row-fluid">
			<?php echo $form->labelEx($model, 'uid'); ?>
			<?php echo $form->textField($model, 'uid', array('size' => 60, 'maxlength' => 128)); ?>
		</div>
	<?php } ?>

	<?php if( Yii::app()->user->checkAccess('changeUserDetails', array('user' => $model)) ) { ?>
		<div class="row-fluid">
			<?php echo $form->labelEx($model, 'givenName'); ?>
			<?php echo $form->textField($model, 'givenName', array('size' => 60, 'maxlength' => 128)); ?>
		</div>

		<div class="row-fluid">
			<?php echo $form->labelEx($model, 'sn'); ?>
			<?php echo $form->textField($model, 'sn', array('size' => 60, 'maxlength' => 128)); ?>
		</div>

		<div class="row-fluid">
			<?php echo $form->labelEx($model, 'dateOfBirth'); ?>
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
				'model' => $model,
				'attribute' => 'dateOfBirth',
				'options' => array(
					'showAnim' => 'fold',
					'dateFormat' => 'dd/mm/yy',
					'changeYear' => true,
					'yearRange' => 'c-120:c+0',
				),
				'htmlOptions' => array('size' => 60),
			)); ?>
		</div>

		<div class="row-fluid">
			<?php echo $form->labelEx($model, 'gender'); ?>
			<?php echo $form->dropDownList($model, 'gender', $model->validGenders(), array('empty'=> 'Not set')); ?>
		</div>

		<div class="row-fluid">
			<?php echo $form->labelEx($model, 'timezoneName'); ?>
			<?php echo $form->dropDownList($model, 'timezoneName', $model->preppedTimezones(), array('empty'=> 'Not set')); ?>
		</div>
	<?php } ?>

	<?php if( Yii::app()->user->checkAccess('manageEvMembershipData', array('user' => $model)) ) { ?>
		<div class="row-fluid">
			<?php echo $form->labelEx($model, 'memberStatus'); ?>
			<?php echo $form->dropDownList($model, 'memberStatus', $model->validMemberStatus()); ?>
		</div>
	<?php } ?>

	<?php if( Yii::app()->user->checkAccess('changeUserEvDetails', array('user' => $model)) ) { ?>
		<div class="row-fluid">
			<?php echo $form->labelEx($model, 'evMail'); ?>
			<?php echo $form->dropDownList($model, 'evMail', $model->validEmailAddresses()); ?>
		</div>
	<?php } ?>

	<div class="row-fluid buttons">
		<?php echo CHtml::submitButton('Update Profile', array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>
