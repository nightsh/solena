<?php
	$mailer->subject = 'Email address verification for ' . Yii::app()->name;
?>
Hello <?php echo $model->cn; ?>,

In order to confirm your control of the email address <?php echo $entry->mail; ?> please follow the link below.
You will not be able to use this email address with <?php echo Yii::app()->name; ?> until you have validated the address.

If you did not request this, please inform the site administrator by replying to this email.

<?php echo Yii::app()->controller->createAbsoluteUrl('/people/verifyEmail', array('uid' => $model->uid, 'token' => $entry->token)); ?>


Thanks,
<?php echo Yii::app()->name; ?> site administrators