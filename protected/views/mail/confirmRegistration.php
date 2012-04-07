<?php
	$mailer->subject = 'Account activation on ' . Yii::app()->name;
?>
Hello <?php echo $model->name; ?>,

In order to activate your new account please follow the link below.
You will not be able to begin using your account on <?php echo Yii::app()->name; ?> until you have activated your account.

If you did not request this, please inform the site administrator by replying to this email.

<?php echo Yii::app()->controller->createAbsoluteUrl('/registration/confirm', array('id' => $model->id, 'token' => $model->token)); ?>


Thanks,
<?php echo Yii::app()->name; ?> site administrators.