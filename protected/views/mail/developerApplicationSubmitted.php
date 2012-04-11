<?php
	$mailer->subject = 'Developer account application submitted successfully on ' . Yii::app()->name;
?>
Hello <?php echo $model->applicant->cn; ?>,

Your developer account application was submitted successfully. You should recieve a response within the next few days.
You may view your application at: <?php echo Yii::app()->controller->createAbsoluteUrl('/developerApplication/view', array('id' => $model->id)); ?>


Thanks,
<?php echo Yii::app()->name; ?> site administrators.