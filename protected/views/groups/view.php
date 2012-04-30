<?php
$this->breadcrumbs = array(
	'Groups' => array('index'),
	$model->cn,
);

$this->menu = $this->generateMenu($model);
?>

<h1>Members of Group <?php echo CHtml::encode($model->cn); ?>
<small><?php echo CHtml::encode($model->description); ?></small></h1>
<p>Shown below are both full and partial matches against the entered filter text. A maximum of 500 results can be shown at any one time.</p>

<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'person-form',
	'action' => array('removeMember', 'cn' => $model->cn),
	'enableAjaxValidation' => false,
)); ?>
<hr/>

	<?php $this->widget('application.components.NeverGridView', array(
		'id' => 'person-grid',
		'dataProvider' => $dataProvider,
		'filter' => $dataProvider->model,
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
	<?php
		if( Yii::app()->user->checkAccess('manageGroup', array('group' => $model)) && $dataProvider->itemCount > 0 ) {
			echo CHtml::submitButton('Remove Member', array('name' => 'removeMember', 'style' => 'float:left', 'class' => 'btn btn-primary'));
		}
	?>

<?php $this->endWidget(); ?>

</div>