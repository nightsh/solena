<?php
$this->breadcrumbs = array(
	'Groups' => array('index'),
	$model->description,
);

$this->menu = $this->generateMenu($model);
?>

<h1>Members of group <?php echo $model->description; ?></h1>
<p>All searches performed below will be done as contains filters. A maximum of 500 entries will be browsable at any one time</p>

<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'person-form',
	'action' => array('removeMember', 'cn' => $model->cn),
	'enableAjaxValidation' => false,
)); ?>

	<?php
		$template = '{summary}{items}';
		if( Yii::app()->user->checkAccess('manageGroup') && $dataProvider->itemCount > 0 ) {
			$template .= CHtml::submitButton('Remove member', array('name' => 'removeMember', 'style' => 'float:left'));
		}
		$template .= '{pager}';
	?>
	<?php $this->widget('zii.widgets.grid.CGridView', array(
		'id' => 'person-grid',
		'dataProvider' => $dataProvider,
		'filter' => $dataProvider->model,
		'template' => $template,
		'columns' => array(
			array(
				'class' => 'CCheckBoxColumn',
				'id' => 'selectedPerson',
				'visible' => Yii::app()->user->checkAccess('manageGroup'),
			),
			'uid',
			'cn',
			'mail',
			array(
				'class' => 'CButtonColumn',
				'template' => '{view}',
				'viewButtonUrl' => 'Yii::app()->createUrl("/people/view", array("uid" => $data->uid))',
			),
		),
	)); ?>

<?php $this->endWidget(); ?>

</div>