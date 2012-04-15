<?php
$this->breadcrumbs=array(
	'Password Reset',
	'Completed',
);

$this->menu = array();
?>

<h1>Password Reset Successful</h1>

<p>Your account password has been reset successfully, you may now <?php echo CHtml::link('login', array('site/login')); ?>.</p>