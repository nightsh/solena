<?php
$this->breadcrumbs = array(
	'Registrations',
);

$this->menu = array();
?>

<h1>Currently Pending Registrations</h1>

<?php $this->widget('application.components.NeverGridView', array(
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
