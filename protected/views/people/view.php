<?php
$this->breadcrumbs = array(
	'People' => array('index'),
	$model->cn,
);

$this->menu = $this->generateMenu($model);
?>
<div class="row-fluid">
	<h1><?php echo CHtml::encode($model->cn); ?></h1>
	<hr/>
	<div class="span10">
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
	</div>
	<div class="span2">
		<?php echo CHtml::image( CHtml::normalizeUrl(array('viewAvatar', 'uid' => $model->uid)), '',array('class' => 'thumbnail pull-right') ); ?>
	</div>

	<div class="row">
		<h3>Contact Information</h3>

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
			'htmlOptions' => array('class' => 'table table-bordered table-striped'),
		)); ?>
	</div>

	<div class="row">
		<h3>Member of Groups</h3>

		<?php $this->widget('application.components.NeverGridView', array(
			'id'=> 'group-grid',
			'dataProvider'=> $groupData,
			'template' => '{items}{pager}',
			'columns'=> array(
				array(
					'class' => 'LinkDataColumn',
					'name' => 'cn',
					'header' => 'Group Name',
					'urlExpression' => 'Yii::app()->createUrl("/groups/view", array("cn" => $data->cn))',
				),
				'description:text:Description',
			),
		)); ?>
	</div>
</div>
