<?php
$this->breadcrumbs = array(
	'Groups' => array('index'),
	$model->description => array('view', 'cn' => $model->cn),
	'Add Member',
);

$this->menu = $this->generateMenu($model);
?>

<h1>Add Member to Group <?php echo CHtml::encode($model->cn); ?>
<small><?php echo CHtml::encode($model->description); ?></small></h1>
<p>All searches performed below will be done as contains filters. A maximum of 150 entries will be browsable at any one time</p>
<hr/>
<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'person-grid-form',
	'enableAjaxValidation' => false,
)); ?>

	<?php $this->widget('application.components.NeverGridView', array(
		'id' => 'person-grid',
		'dataProvider' => $dataProvider,
		'filter' => $dataProvider->model,
		'columns' => array(
			array(
				'class' => 'CCheckBoxColumn',
				'id' => 'selectedPerson',
			),
			'uid',
			'cn',
			'mail',
		),
	)); ?>
	<?php
		if( $dataProvider->itemCount > 0 ) {
			echo CHtml::submitButton('Add Member', array('name' => 'addMember', 'class' => 'btn btn-primary'));
		}
	?>

<?php $this->endWidget(); ?>

</div>