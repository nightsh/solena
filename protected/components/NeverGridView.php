<?php

Yii::import('zii.widgets.grid.CGridView');

class NeverGridView extends CGridView
{
	public $itemsCssClass = 'table table-bordered table-striped table-condensed';
	public $pagerCssClass = 'pagination';
	public $pager = array('class' => 'CLinkPager', 'htmlOptions' => array('class' => ''), 'header' => '');
};
