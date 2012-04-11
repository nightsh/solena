<?php
	$mailer->subject = sprintf('Developer account application by %s on %s', $model->applicant->cn, Yii::app()->name);
?>
Hi site administrator,

<?php echo $model->applicant->cn; ?> has filed a developer account application on <?php echo Yii::app()->name; ?>.
Their username is '<?php echo $model->uid; ?>' and their email address is '<?php echo $model->applicant->mail; ?>'.
Their profile can be found at <?php echo Yii::app()->controller->createAbsoluteUrl('/people/view', array('uid' => $model->uid)); ?>


<?php if( $model->special_reason == DeveloperApplication::ReasonGsoc ) { ?>
They have indicated that they are a confirmed Google Summer of Code participant.
<?php } ?>

<?php if( $model->supporter instanceof User ) { ?>
They have specified <?php echo $model->supporter->cn; ?> as their supporter, whose profile may be found at:
<?php echo Yii::app()->controller->createAbsoluteUrl('/people/view', array('uid' => $model->supporter_uid)); ?>
<?php } ?>


They provided the following justification for their developer account application:
<?php echo trim($model->justification); ?>


They also provided the following supporting evidence for their justification:
<?php echo trim($model->evidence_links); ?>


To confirm the developer registration, run 'sync <?php echo $model->uid; ?>'

Once you have confirmed their developer registration, please send them the following text:

Your account has now been converted to a developer account.
The username for SVN is "<?php echo $model->uid; ?>". Please find instructions attached.

Regards,
<?php echo Yii::app()->name ?>.