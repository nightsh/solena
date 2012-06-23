<?php
	$mailer->subject = sprintf('Developer account application approval by %s', $model->applicant->cn);
	$guidelines_path = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'mail' . DIRECTORY_SEPARATOR . 'guidelines.txt';
	$mailer->attach(Swift_Attachment::fromPath($guidelines_path));
	if( $model->special_reason == DeveloperApplication::ReasonGsoc ) {
		$mailer->cc = array('kde-soc-mentor-owner@kde.org', Yii::app()->params['adminEmail']);
	} else {
		$mailer->cc = array(Yii::app()->params['adminEmail']);
	}
?>
Hello <?php echo $model->supporter->cn; ?>,

This is an automatically generated message.
<?php echo $model->applicant->cn; ?> has indicated that you have encouraged him/her to apply for a developer account.
Please confirm your support of this request by replying to this email. Their application will not be accepted until you respond.

Guidelines how to evaluate their application is attached to this mail.

<?php if( $model->special_reason == DeveloperApplication::ReasonGsoc ) { ?>
They have also indicated that they are a confirmed Google Summer of Code participant.
<?php } ?>

They provided the following justification for their developer account application:
<?php echo trim($model->justification); ?>


They also provided the following supporting evidence for their justification:
<?php echo trim($model->evidence_links); ?>


If the application is accepted, the username which will be associated with this account will be '<?php echo $model->uid; ?>'.
If you want to monitor their commits, you should adjust your commit filter.

Regards,
<?php echo Yii::app()->name ?> site administrator.
