<?php
$this->breadcrumbs = array(
	'People' => array('index'),
	$model->cn => array('view', 'uid' => $model->uid),
	'Change Avatar',
);

$this->menu = $this->generateMenu($model);
?>

<h1>Change Avatar of <?php echo CHtml::encode($model->cn); ?></h1>

<div class="form well">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'person-form',
	'enableAjaxValidation' => false,
	'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

	<div class="span7">
		<div class="row-fluid">
			<?php echo $form->labelEx($model, 'jpegPhoto'); ?>
			<?php echo $form->error($model, 'jpegPhoto'); ?>
			<?php echo $form->fileField($model, 'jpegPhoto'); ?>
		</div>
		<div class="row-fluid buttons">
			<?php echo CHtml::submitButton('Upload Avatar', array('name' => 'uploadAvatar', 'class' => 'btn btn-primary')); ?>
		</div>
	</div>
	<div class="span2">
		<?php echo CHtml::image( CHtml::normalizeUrl(array('viewAvatar', 'uid' => $model->uid)), '', array('class' => 'thumbnail') ); ?>
		<br />
		<?php if( isset($model->jpegPhoto) && !$model->jpegPhoto instanceof CUploadedFile ) { ?>
			<?php echo CHtml::submitButton('Clear Avatar', array('name' => 'clearAvatar', 'class' => 'btn')); ?>
		<?php } ?>
	</div>
	<div class="clearfix"></div>
<?php $this->endWidget(); ?>

</div>