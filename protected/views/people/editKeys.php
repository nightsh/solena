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

	<p class="alert alert-info">Please be aware that SSH Key changes must be synced to KDE infrastructure by a KDE Sysadmin in order to become active.</p>

	<div class="row-fluid">
		<?php $this->widget('application.components.NeverGridView', array(
			'id' => 'sshkey-grid',
			'selectableRows' => 2,
			'dataProvider' => $dataProvider,
			'template' => '{items}{pager}',
			'columns'=> array(
				array(
					'class' => 'CCheckBoxColumn',
					'id' => 'selectedKeys'
				),
				'type:text:Type',
				'fingerprint:text:Fingerprint',
				'comment:text:Comment',
			),
			'itemsCssClass' => 'table table-striped table-condensed',
		)); ?>
		<?php
			if( $dataProvider->itemCount > 0 ) {
				echo CHtml::submitButton('Remove Selected Keys', array('name' => 'removeKeys', 'class' => 'btn'));
			}
		?>
	</div>

	<hr />
	<h3>Add New SSH Key</h3>
	<div class="row-fluid">
		<?php echo $form->error($model, 'sshKeysAdded'); ?>
		<?php echo $form->fileField($model, 'sshKeysAdded'); ?>
	</div>

	<div class="row-fluid buttons">
		<?php echo CHtml::submitButton('Upload Keys', array('name' => 'uploadKeys', 'class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>
