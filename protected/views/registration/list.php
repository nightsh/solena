<?php
$this->breadcrumbs = array(
	'Registrations',
);

$this->menu = array();
?>

<h1>Currently Pending Registrations</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'registrations-grid',
	'itemsCssClass' => 'table table-bordered table-striped',
	'dataProvider' => $dataProvider,
	'filter' => $model,
	'columns' => array(
		'givenName',
		'sn',
		'mail',
		array(
			'class'=>'CButtonColumn',
			'buttons' => array(
				'delete' => array('click' => ''),
			),
		),
	),
)); ?>
