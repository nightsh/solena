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
			array(
				'name' => 'dateOfBirth',
				'visible' => isset($model->dateOfBirth),
			),
			array(
				'name' => 'gender',
				'type' => 'gender',
				'visible' => isset($model->gender),
			),
			array(
				'name' => 'timezoneName',
				'type' => 'timezone',
				'label' => 'Current Time',
				'visible' => isset($model->timezoneName),
			),
			array(
				'name' => 'timezone',
				'visible' => isset($model->timezone),
			),
			array(
				'name' => 'memberStatus',
				'visible' => isset($model->memberStatus),
			),
		),
		'htmlOptions' => array('class' => 'table table-bordered table-striped table-condensed fixed-cell'),
	)); ?>
	</div>
	<div class="span2">
		<?php echo CHtml::image( CHtml::normalizeUrl(array('viewAvatar', 'uid' => $model->uid)), '',array('class' => 'thumbnail pull-right') ); ?>
	</div>

	<div class="row-fluid">
		<div class="span12">
			<h3>Contact Information</h3>

			<?php $this->widget('zii.widgets.CDetailView', array(
				'data' => $model,
				'attributes' => array(
					array(
						'name' => 'homePostalAddress',
						'type' => 'ntext',
						'visible' => isset($model->homePostalAddress),
					),
					array(
						'name' => 'homePhone',
						'visible' => isset($model->homePhone),
					),
					array(
						'name' => 'labeledURI',
						'type' => 'url',
						'visible' => isset($model->labeledURI),
					),
					array(
						'name' => 'ircNick',
						'visible' => isset($model->ircNick),
					),
					array(
						'name' => 'jabberID',
						'visible' => isset($model->jabberID),
					),
					array(
						'name' => 'emailAddresses',
						'type' => 'email',
					),
					array(
						'name' => 'evMail',
						'type' => 'email',
						'visible' => isset($model->evMail),
					),
				),
				'htmlOptions' => array('class' => 'table table-bordered table-striped table-condensed fixed-cell'),
			)); ?>
		</div>
	</div>

	<div class="row-fluid">
		<div class="span12">
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
</div>
