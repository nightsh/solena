<?php
$this->breadcrumbs = array(
	'Groups' => array('index'),
	$model->description,
);

$this->menu = $this->generateMenu($model);
?>

<h1>Members of Group <?php echo CHtml::encode($model->description); ?></h1>
<p>Shown below are both full and partial matches against the entered filter text. A maximum of 500 results can be shown at any one time.</p>

<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'person-form',
	'action' => array('removeMember', 'cn' => $model->cn),
	'enableAjaxValidation' => false,
)); ?>
<hr/>
	<?php
		$template = '{summary}{items}';
		if( Yii::app()->user->checkAccess('manageGroup', array('group' => $model)) && $dataProvider->itemCount > 0 ) {
			$template .= CHtml::submitButton('Remove Member', array('name' => 'removeMember', 'style' => 'float:left', 'class' => 'btn btn-primary'));
		}
		$template .= '{pager}';
	?>
	<?php $this->widget('zii.widgets.grid.CGridView', array(
		'id' => 'person-grid',
		'itemsCssClass' => 'table table-bordered table-striped',
		'dataProvider' => $dataProvider,
		'filter' => $dataProvider->model,
		'template' => $template,
		'columns' => array(
			array(
				'class' => 'CCheckBoxColumn',
				'id' => 'selectedPerson',
				'visible' => Yii::app()->user->checkAccess('manageGroup', array('group' => $model)),
			),
			array(
				'class' =>'LinkDataColumn',
				'name' => 'uid',
				'urlExpression' => 'Yii::app()->createUrl("/people/view", array("uid" => $data->uid))',
			),
			'cn',
			'mail'
		),
	)); ?>

<?php $this->endWidget(); ?>

</div>