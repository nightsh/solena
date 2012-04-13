<?php
$this->breadcrumbs = array(
	'People' => array('index'),
	$model->cn => array('view', 'uid' => $model->uid),
	'Manage SSH Keys',
);

$this->menu = $this->generateMenu($model);

Yii::app()->clientScript->registerScript("disableCheckAll", "$('input.select-on-check-all').hide();", CClientScript::POS_LOAD);
?>

<h1>Manage SSH Keys of <?php echo CHtml::encode($model->cn); ?></h1>

<div class="form well">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'person-form',
	'enableAjaxValidation' => false,
	'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

	<div class="row">
		<?php
			$template = '{items}';
			if( $dataProvider->itemCount > 0 ) {
				$template .= CHtml::submitButton('Remove selected keys', array('name' => 'removeKeys', 'class' => 'btn'));
			}
		?>
		<?php $this->widget('zii.widgets.grid.CGridView', array(
			'id' => 'sshkey-grid',
			'itemsCssClass' => 'table table-bordered table-striped',
			'selectableRows' => 2,
			'dataProvider' => $dataProvider,
			'template' => $template,
			'columns'=> array(
				array(
					'class' => 'CCheckBoxColumn',
					'id' => 'selectedKeys'
				),
				'type:text:Type',
				'fingerprint:text:Fingerprint',
				'comment:text:Comment',
			),
		)); ?>
	</div>

	<hr />
	<h3>Add new SSH Key</h3>
	<div class="row">
		<?php echo $form->fileField($model, 'sshKeysAdded'); ?>
		<?php echo $form->error($model, 'sshKeysAdded'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Upload keys', array('name' => 'uploadKeys', 'class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>
