<?php
	$mailer->subject = 'Account registered on ' . Yii::app()->name . ' by ' . $model->cn;
?>
Hello site administrator,

<?php echo $model->cn; ?> has successfully registered on <?php echo Yii::app()->name; ?>.
Their username is '<?php echo $model->uid; ?>'.
You can access their profile by following the link below.

<?php echo Yii::app()->controller->createAbsoluteUrl('/people/view', array('uid' => $model->uid)); ?>


Regards,
<?php echo Yii::app()->name; ?>.