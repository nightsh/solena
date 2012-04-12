<?php
$this->breadcrumbs = array(
	'People' => array('index'),
	$model->cn,
);

$this->menu = $this->generateMenu($model);
?>

<h1><?php echo $model->cn; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'attributes' => array(
		'uid',
		'dateOfBirth',
		'gender:gender',
		'timezoneName:timezone:Current Time',
		array(
			'name' => 'timezone',
			'visible' => isset($model->timezone),
		),
		array(
			'name' => 'memberStatus',
			'visible' => isset($model->memberStatus),
		),
	),
	'htmlOptions' => array('class' => 'table table-bordered table-striped'),
)); ?>
<br />

<h2>Contact information</h2>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'attributes' => array(
		'homePostalAddress:ntext',
		'homePhone',
		'labeledURI:url',
		'ircNick',
		'jabberID',
		'emailAddresses:email',
		array(
			'name' => 'evMail',
			'type' => 'email',
			'visible' => isset($model->evMail),
		),
	),
)); ?>