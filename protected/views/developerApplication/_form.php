<div class="form well">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'developer-application-form',
	'enableAjaxValidation' => false,
	'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

	<p class="alert alert-info">Fields with <span class="required">*</span> are required.</p>

	<p>
		Please fill in the below fields accurately to enable KDE Sysadmin to process your application quickly and accurately.<br />
		Whilst a supporter is not required, it is recommended as it speeds up the process of approving a application.<br /><br />
		Be aware that involvement in the KDE Community is required before you can apply for a developer account.<br />
		Requests without sufficient public or verifiable involvement will be declined, unless there are exceptional circumstances.<br />
	</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'supporter_uid'); ?>
		<div>
			If you have a supporter, enter their name, username or email address and select them from the drop down.
		</div>
		<?php $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'model' => $model,
			'attribute' => 'supporter_uid',
			'source' => $this->createUrl('developerApplication/supporterAutocomplete'),
			'options' => array(
				'showAnim' => 'fold',
			),
			'htmlOptions' => array('size' => 60, 'maxlength' => 64),
		)); ?>
		<?php echo $form->error($model, 'supporter_uid', array('class' => 'alert alert-error')); ?>
	</div>

	<?php if(Yii::app()->user->checkAccess('sysadmins')) { ?>
		<div class="row">
			<?php echo $form->labelEx($model, 'status'); ?>
			<?php echo $form->dropDownList($model, 'status', $model->validStatus(), array('empty'=> 'Not set')); ?>
			<?php echo $form->error($model, 'status', array('class' => 'alert alert-error')); ?>
		</div>
	<?php } ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'special_reason'); ?>
		<div>
			Only confirmed Google Summer of Code students with KDE qualify as participants.
		</div>
		<?php echo $form->dropDownList($model, 'special_reason', $model->validSpecialReason(), array('empty'=> 'Not set')); ?>
		<?php echo $form->error($model, 'special_reason', array('class' => 'alert alert-error')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'justification'); ?>
		<div>
			Please explain the reason for your application.
			Keep it short and to the point, and indicate which teams/people you have worked with.
		</div>
		<?php echo $form->textArea($model, 'justification', array('rows' => 6, 'cols' => 50)); ?>
		<?php echo $form->error($model, 'justification', array('class' => 'alert alert-error')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'evidence_links'); ?>
		<div>
			Please provide links to mailing list postings, review requests or other publicly accessible material which indicates your involvement with the KDE Community.
		</div>
		<?php echo $form->textArea($model, 'evidence_links', array('rows' => 6, 'cols' => 50)); ?>
		<?php echo $form->error($model, 'evidence_links', array('class' => 'alert alert-error')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'ssh_key'); ?>
		<div>
			Please provide a single RSA, DSA or ECDSA key which you will use to access infrastructure once your application is approved.
		</div>
		<?php echo $form->fileField($model, 'ssh_key'); ?>
		<?php echo $form->error($model, 'ssh_key', array('class' => 'alert alert-error')); ?>
	</div>

	<div class="row buttons">
	<?php echo CHtml::submitButton($model->isNewRecord ? 'Submit application' : 'Update', array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->