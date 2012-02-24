<?php
$this->breadcrumbs = array('Groups');
$this->menu = array( array('label' => 'Create Group', 'url' => array('create'), 'visible' => Yii::app()->user->checkAccess('sysadmins') ) );
?>

<h1>Groups</h1>
<p>All searches performed below will be done as contains filters. A maximum of 150 entries will be browsable at any one time</p>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'group-grid',
	'dataProvider' => $dataProvider,
	'filter' => $model,
	'columns' => array(
		'cn',
		'description',
		array(
			'class' => 'CButtonColumn',
			'template' => '{view}',
			'viewButtonUrl' => 'Yii::app()->createUrl("/groups/view", array("cn" => $data->cn))',
		),
	),
)); ?>