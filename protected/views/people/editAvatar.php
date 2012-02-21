<?php
$this->breadcrumbs = array(
	'People' => array('index'),
	$model->cn => array('view', 'uid' => $model->uid),
	'Change Avatar',
);

$this->menu = $this->generateMenu($model);
?>

<h1>Change Avatar of <?php echo $model->cn; ?></h1>

<div class="form">

<?php
foreach(Yii::app()->user->getFlashes() as $key => $message) {
	echo CHtml::tag('div', array('class' => 'flash-' . $key), CHtml::encode($message));
}
?>

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'person-form',
	'enableAjaxValidation' => false,
	'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

	<div class="row">
		<?php echo CHtml::image( CHtml::normalizeUrl(array('viewAvatar', 'uid' => $model->uid)) ); ?>
	</div>

	<?php if( isset($model->jpegPhoto) && !$model->jpegPhoto instanceof CUploadedFile ) { ?>
	<div class="row">
		<?php echo CHtml::submitButton('Clear avatar', array('name' => 'clearAvatar')); ?>
	</div>
	<?php } ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'jpegPhoto'); ?>
		<?php echo $form->fileField($model, 'jpegPhoto'); ?>
		<?php echo $form->error($model, 'jpegPhoto'); ?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton('Upload', array('name' => 'uploadAvatar')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>