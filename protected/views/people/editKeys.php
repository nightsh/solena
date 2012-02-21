<?php
$this->breadcrumbs = array(
	'People' => array('index'),
	$model->cn => array('view', 'uid' => $model->uid),
	'Manage SSH Keys',
);

$this->menu = $this->generateMenu($model);

Yii::app()->clientScript->registerScript("disableCheckAll", "$('input.select-on-check-all').hide();", CClientScript::POS_LOAD);
?>

<h1>Manage SSH Keys of <?php echo $model->cn; ?></h1>

<?php
foreach(Yii::app()->user->getFlashes() as $key => $message) {
	echo CHtml::tag('div', array('class' => 'flash-' . $key), CHtml::encode($message));
}
?>

<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'person-form',
	'enableAjaxValidation' => false,
)); ?>

	<div class="row">
		<?php
			$template = '{items}';
			if( $dataProvider->itemCount > 0 ) {
				$template .= CHtml::submitButton('Remove selected keys', array('name' => 'removeKeys'));
			}
		?>
		<?php $this->widget('zii.widgets.grid.CGridView', array(
			'id' => 'sshkey-grid',
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
		<?php echo $form->textArea($sshForm, 'newKey', array('cols' => 80, 'rows' => 6)); ?>
		<?php echo $form->error($sshForm, 'newKey'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Add key', array('name' => 'addKeys')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>
