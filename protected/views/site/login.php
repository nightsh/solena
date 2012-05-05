<?php
$this->pageTitle=Yii::app()->name . ' - Login';
$this->breadcrumbs=array(
	'Login',
);
?>

<h1>Login</h1>

<p>Not registered yet? You can begin registration <?php echo CHtml::link('here', array('/registration/index')); ?>.</p>

<?php $this->renderPartial('_login', array('model' => $model)); ?>
