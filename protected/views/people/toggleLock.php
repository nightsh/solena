<?php
$this->breadcrumbs = array(
	'People' => array('index'),
	$model->cn => array('view', 'uid' => $model->uid),
	'Lock/Unlock account',
);

$this->menu = $this->generateMenu($model);
?>

<h1>Lock/Unlock account of <?php echo CHtml::encode($model->cn); ?></h1>

<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'person-form',
	'enableAjaxValidation' => false,
)); ?>

	<p class="note">Temporary locks are performed automatically when too many invalid password attempts are made, and will expire automatically</p>
	<p><strong>Account Status:</strong> <?php echo CHtml::encode($model->accountStatus); ?></p>

	<div class="row buttons">
		<?php 
			if( $model->accountStatus != User::AccountUnlocked ) {
				echo CHtml::submitButton('Unlock account', array('name' => 'unlockAccount', 'class' => 'btn btn-primary'));
			}
			if( $model->accountStatus != User::AccountPermanentLocked ) {
				echo CHtml::submitButton('Lock account infinitely', array('name' => 'lockAccount', 'class' => 'btn btn-primary'));
			}
		?>
	</div>

<?php $this->endWidget(); ?>

</div>