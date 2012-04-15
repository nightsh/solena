<?php
$this->breadcrumbs = array(
	'People' => array('index'),
	$model->cn,
);

$this->menu = $this->generateMenu($model);
?>

<h1><?php echo CHtml::encode($model->cn); ?></h1>
<hr/>
<div class="span8">
<?php $this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'attributes' => array(
		'uid',
		'dateOfBirth',
		'gender:gender',
		'timezoneName:timezone:Current time',
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
</div>
<div class="span2">
<?php echo CHtml::image( CHtml::normalizeUrl(array('viewAvatar', 'uid' => $model->uid)), '',array('class' => 'thumbnail pull-right') ); ?>
</div>

<div class="span10">
<h3>Contact information</h3>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'attributes' => array(
		'homePostalAddress:ntext',
		'homePhone',
		'labeledURI:url',
		'ircNick',
		'jabberID',
		'emailAddresses:email:Email addresses',
		array(
			'name' => 'evMail',
			'type' => 'email',
			'visible' => isset($model->evMail),
		),
	),
	'htmlOptions' => array('class' => 'table table-bordered table-striped'),
)); ?>
</div>