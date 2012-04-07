<?php
$this->breadcrumbs=array(
	'Registrations' => array('list'),
	$model->name,
);

$this->menu = array(
	array('label' => 'Update registration', 'url' => array('update', 'id' => $model->id)),
	array('label' => 'Delete registration', 'url'=> array('delete', 'id' => $model->id)),
);
?>

<?php
foreach(Yii::app()->user->getFlashes() as $key => $message) {
	echo CHtml::tag('div', array('class' => 'flash-' . $key), CHtml::encode($message));
}
?>

<h1>View registration of <?php echo $model->name; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'attributes' => array(
		'givenName',
		'sn',
		'mail',
	),
)); ?>

<br />

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'registrations-form',
	'enableAjaxValidation' => false,
)); ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Resend confirmation', array('name' => 'resendConfirmation')); ?>
	</div>

<?php $this->endWidget(); ?>
