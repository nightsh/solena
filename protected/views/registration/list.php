<?php
$this->breadcrumbs = array(
	'Registrations',
);

$this->menu = array();
?>

<h1>Currently pending registrations</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'registrations-grid',
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
