<?php
$this->breadcrumbs = array(
	'Groups' => array('index'),
	$model->description => array('view', 'cn' => $model->cn),
	'Add Member',
);

$this->menu = $this->generateMenu($model);
?>

<h1>Add member to group <?php echo CHtml::encode($model->description); ?></h1>
<p>All searches performed below will be done as contains filters. A maximum of 150 entries will be browsable at any one time</p>
<hr/>
<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'person-form',
	'enableAjaxValidation' => false,
)); ?>

	<?php
		$template = '{summary}{items}';
		if( $dataProvider->itemCount > 0 ) {
			$template .= CHtml::submitButton('Add Member', array('name' => 'addMember', 'style' => 'float:left', 'class' => 'btn btn-primary'));
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
			),
			'uid',
			'cn',
			'mail',
		),
	)); ?>

<?php $this->endWidget(); ?>

</div>