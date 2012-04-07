<?php
$this->breadcrumbs=array(
	'Register',
	'Completed',
);

$this->menu = array();
?>

<h1>Registration completed on <?php echo Yii::app()->name; ?></h1>

<p>
  Your registration has now been completed <?php echo $model->cn; ?>.
  The username for your account is '<?php echo $model->uid; ?>'.
  <br /><br />
  To begin using your account, you may now <?php echo CHtml::link('login', array('site/login')); ?>
</p>