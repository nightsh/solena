<?php
$this->breadcrumbs = array('People');
$this->menu = array( array('label' => 'Create Person', 'url' => array('create')) );
?>

<h1>People</h1>
<p>All searches performed below will be done as contains filters. A maximum of 150 entries will be browsable at any one time</p>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=> 'person-grid',
	'dataProvider'=> $dataProvider,
	'filter'=> $model,
	'columns'=> array(
		'uid',
		'cn',
		'mail',
	),
)); ?>