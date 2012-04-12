<?php
$this->breadcrumbs = array(
	'Developer Applications',
);

$this->menu = array();
?>

<h1>Developer Applications</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'developer-applications-grid',
	'itemsCssClass' => 'table table-bordered table-striped',
	'dataProvider' => $dataProvider,
	'filter' => $model,
	'columns' => array(
		array(
			'class' => 'LinkDataColumn',
			'name' => 'uid',
			'urlExpression' => 'Yii::app()->createUrl("/developerApplication/view", array("id" => $data->id))',
		),
		array(
			'class' => 'LinkDataColumn',
			'name' => 'supporter_uid',
			'urlExpression' => 'Yii::app()->createUrl("/people/view", array("uid" => $data->supporter_uid))',
		),
		array(
			'class' => 'CDataColumn',
			'name' => 'status',
			'value' => '$data->statusName;',
			'filter' => $model->validStatus(),
		),
		array(
			'class' => 'CDataColumn',
			'name' => 'special_reason',
			'value' => '$data->specialReasonName;',
			'filter' => $model->validSpecialReason(),
		),
	),
)); ?>
