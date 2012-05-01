<?php

Yii::import('zii.widgets.grid.CGridView');

class NeverGridView extends CGridView
{
	public $itemsCssClass = 'table table-bordered table-striped table-condensed';
	public $pagerCssClass = 'pagination pagination-centered';
	public $pager = array('class' => 'CLinkPager', 'htmlOptions' => array('class' => ''), 'header' => '');
	public $afterAjaxUpdate = 'solenaAfterAjax';

	public function registerClientScript()
	{
		parent::registerClientScript();

		$id = $this->getId();
		$cs = Yii::app()->getClientScript();
		$cs->registerScriptFile('js/solena-gridview.js',CClientScript::POS_END);
		$cs->registerScript(__CLASS__.'#sgv#'.$id,"setupSearchTimeout(jQuery('#$id'), '{$this->filterCssClass}');");
	}
};
