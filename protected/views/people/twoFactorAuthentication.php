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

	<p class="alert alert-info">
		KDE Identity supports two-factor authentication to protect your Identity account against unauthorized access. Please store a copy of the security grid for future login attempts by printing or saving a copy of it.<br /><br />
		Two factor authentication is a method where you protect your login with an additional step. KDE has chosen to do this based on a grid-challenge. The idea is that you combine 'something that you know', with 'something you got'.  The first one is your password, the second is the grid shown below. The grid used by each person is individual and unique.<br /><br />
		When you try to login into identity.kde.org, we will ask for your username and password as usual, and after that we will ask for a coordinate from the grid below, for example D10. You look in the grid, select column D and row 10. Then you enter the 4 characters you find there.<br /><br />
		The advantage of all this is that if your password is compromised, people cannot login to KDE Identity using your account, because they don't have access to your grid. On the other hand, if someone gets to your grid, they can not access identity.kde.org, because they don't have your password.
	</p>
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