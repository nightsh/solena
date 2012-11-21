<?php
$this->breadcrumbs = array(
	'Developer Applications' => array('list'),
	$model->id,
);

$this->menu = array(
	array('label' => 'List Applications', 'url' => array('list'), 'visible' => Yii::app()->user->checkAccess('sysadmins')),
	array('label' => 'Update Application', 'url' => array('update', 'id' => $model->id), 'visible' => Yii::app()->user->checkAccess('sysadmins')),
);
?>

<h1>Developer Application by <?php echo CHtml::encode($model->applicant->cn); ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'attributes' => array(
		array(
			'type' => 'raw',
			'label' => 'Applicant',
			'value' => CHtml::link( $model->applicant->cn, array('people/view', 'uid' => $model->applicant->uid) ),
			'visible' => Yii::app()->user->checkAccess('sysadmins'),
		),
		array(
			'type' => 'text',
			'label' => 'Request Status',
			'value' => $model->statusName,
		),
		array(
			'type' => 'boolean',
			'name' => 'bugzilla_email',
		),
		array(
			'type' => 'raw',
			'label' => 'Supporter',
			'value' => empty($model->supporter_uid) ? 'None' : CHtml::link( $model->supporter->cn, array('people/view', 'uid' => $model->supporter->uid) ),
		),
		array(
			'type' => 'text',
			'label' => 'Special Reason',
			'value' => $model->specialReasonName,
		),
		'justification:ntext',
		'evidence_links:ntext',
	),
	'htmlOptions' => array('class' => 'table table-bordered table-striped')
)); ?>
