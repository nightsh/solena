<?php
$this->breadcrumbs = array(
	'Developer Applications',
	'Apply',
);

$this->menu = array();
?>

<h1>Apply for a developer account</h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>