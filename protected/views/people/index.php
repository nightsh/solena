<?php
$this->breadcrumbs = array('People');
$this->menu = array( array('label' => 'Create Person', 'url' => array('create'), 'visible' => Yii::app()->user->checkAccess('sysadmins') ) );
?>

<h1>People</h1>
<p>Shown below are both full and partial matches against the entered filter text. A maximum of 150 results can be shown at any one time.</p>
<hr/>
<?php $this->widget('application.components.NeverGridView', array(
	'id'=> 'person-grid',
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