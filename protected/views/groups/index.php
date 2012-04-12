<?php
$this->breadcrumbs = array('Groups');
$this->menu = array( array('label' => 'Create Group', 'url' => array('create'), 'visible' => Yii::app()->user->checkAccess('sysadmins') ) );
?>

<h1>Groups</h1>
<p>All searches performed below will be done as contains filters. A maximum of 150 entries will be browsable at any one time</p>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'group-grid',
	'itemsCssClass' => 'table table-bordered table-striped',
	'dataProvider' => $dataProvider,
	'filter' => $model,
	'columns' => array(
		array(
			'class' => 'LinkDataColumn',
			'name' => 'cn',
			'urlExpression' => 'Yii::app()->createUrl("/groups/view", array("cn" => $data->cn))',
		),
		'description',
	),
)); ?>