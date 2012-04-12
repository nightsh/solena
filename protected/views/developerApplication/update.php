<?php
$this->breadcrumbs = array(
	'Developer Applications' => array('list'),
	$model->id => array('view', 'id' => $model->id),
	'Update',
);

$this->menu = array(
	array('label' => 'List Applications', 'url' => array('list')),
	array('label'=> 'View Application', 'url' => array('view', 'id' => $model->id)),
);
?>

<h1>Update Developer Application by <?php echo CHtml::encode($model->uid); ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>