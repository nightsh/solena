<?php
$this->breadcrumbs = array('Groups');
$this->menu = array( array('label' => 'Create Group', 'url' => array('create'), 'visible' => Yii::app()->user->checkAccess('sysadmins') ) );
?>

<h1>Groups</h1>
<p>All members can be part of one or more groups which have certain permissions, here you can see which members are part of a certain group.</p>
<p>Shown below are both full and partial matches against the entered filter text.</p>
<hr/>
<?php $this->widget('application.components.NeverGridView', array(
	'id' => 'group-grid',
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