<?php
$this->breadcrumbs=array(
	'Register',
	'Confirmation sent',
);

$this->menu = array();
?>

<h1>Registration accepted on <?php echo Yii::app()->name; ?></h1>

<p>
  Your registration has been accepted <?php echo CHtml::encode($model->name); ?>.
  <br /><br />
  A confirmation email has now been sent to the email address you provided,
  however you will not be able to access your account until it is activated.
</p>