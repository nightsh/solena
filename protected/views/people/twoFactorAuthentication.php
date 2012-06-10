<?php
$this->breadcrumbs = array(
	'People' => array('index'),
	$model->cn => array('view', 'uid' => $model->uid),
	'Two-Factor Authentication',
);

$this->menu = $this->generateMenu($model);
?>

<h1>Enable/Disable Two-Factor Authentication for <?php echo CHtml::encode($model->cn); ?></h1>

<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'person-form',
	'enableAjaxValidation' => false,
)); ?>

	<p class="alert alert-info">KDE Identity supports two-factor authentication to protect your Identity account against unauthorized access. Please store a copy of the below security grid for future login attempts.</p>
	<p><?php echo Yii::app()->tokenGrid->xhtmlGrid; ?></p>

	<div class="row-fluid buttons">
		<?php 
			if( isset($model->twoFactorAuthentication) ) {
				echo CHtml::submitButton('Disable Two-Factor Authentication', array('name' => 'disableTwoFactor', 'class' => 'btn btn-primary'));
			} else {
				echo CHtml::submitButton('Enable Two-Factor Authentication', array('name' => 'enableTwoFactor', 'class' => 'btn btn-primary'));
			}
		?>
	</div>

<?php $this->endWidget(); ?>

</div>