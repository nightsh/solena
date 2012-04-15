<?php
$this->breadcrumbs = array('Groups');
$this->menu = array( array('label' => 'Create Group', 'url' => array('create'), 'visible' => Yii::app()->user->checkAccess('sysadmins') ) );
?>

<h1>Groups</h1>
<p>Shown below are both full and partial matches against the entered filter text. A maximum of 150 results can be shown at any one time.</p>
<hr/>
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
	'pager' => array(
		'class' => 'CLinkPager',
		'htmlOptions' => array('class' => 'pagination'),
	),
	'pagerCssClass' => '',
)); ?>