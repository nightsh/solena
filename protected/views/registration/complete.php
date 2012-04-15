<?php
$this->breadcrumbs=array(
	'Register',
	'Completed',
);

$this->menu = array();
?>

<h1>Registration Completed on <?php echo Yii::app()->name; ?></h1>

<p class="well">
  Your registration has now been completed <?php echo CHtml::encode($model->cn); ?>.
  The username for your account is '<?php echo CHtml::encode($model->uid); ?>'.
  <br /><br />
  To begin using your account, please <?php echo CHtml::link('login', array('site/login')); ?>.
</p>