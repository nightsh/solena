<?php
$this->breadcrumbs = array(
	'Developer Applications',
	'Apply',
);

$this->menu = array();
?>

<h1>Apply for a Developer Account</h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>