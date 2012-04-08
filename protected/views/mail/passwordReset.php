<?php
	$mailer->subject = 'Password reset on ' . Yii::app()->name;
?>
Hello <?php echo $model->uid; ?>,

In order to reset the password for your account, please follow the link below.
If you did not request this, please inform the site administrator by replying to this email.

<?php echo Yii::app()->controller->createAbsoluteUrl('/site/performPasswordReset', array('uid' => $model->uid, 'token' => $model->token)); ?>


Thanks,
<?php echo Yii::app()->name; ?> site administrators.