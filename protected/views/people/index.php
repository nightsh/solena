<?php
$this->breadcrumbs = array('People');
$this->menu = array( array('label' => 'Create Person', 'url' => array('create'), 'visible' => Yii::app()->user->checkAccess('sysadmins') ) );
?>

<h1>People</h1>
<p>All searches performed below will be done as contains filters. A maximum of 150 entries will be browsable at any one time</p>
<hr/>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=> 'person-grid',
	'itemsCssClass' => 'table table-bordered table-striped',
	'dataProvider'=> $dataProvider,
	'filter'=> $model,
	'columns'=> array(
		array(
			'class' => 'LinkDataColumn',
			'name' => 'uid',
			'urlExpression'=> 'Yii::app()->createUrl("/people/view", array("uid" => $data->uid))',
		),
		'cn',
		'mail',
	),
)); ?>