<?php

Yii::import('zii.widgets.CBreadcrumbs');

/**
 * Meant to overwrite default Breadcrumbs and output a list 
 * instead of pure links
 */
class NBreadcrumbs extends CBreadcrumbs {

	public $tagName = 'ul';

	public $htmlOptions = array('class'=>'nav');

	public $separator = '&nbsp;&raquo;&nbsp;';

	public function init() {
		parent::init();
	}


	public function run()
	{
		echo CHtml::openTag($this->tagName,$this->htmlOptions)."\n";
		$links=array();
		if($this->homeLink===null)
			$links[]=CHtml::link(Yii::t('zii','Home'),Yii::app()->homeUrl);
		else if($this->homeLink!==false)
			$links[]=$this->homeLink;
		foreach($this->links as $label=>$url)
		{
			if(is_string($label) || is_array($url))
				$links[]='<li>'.$this->separator.CHtml::link($this->encodeLabel ? CHtml::encode($label) : $label, $url).'</li>';
			else
				$links[]='<li>'.$this->separator.($this->encodeLabel ? CHtml::encode($url) : $url).'</li>';
		}
		echo implode($links);
		echo CHtml::closeTag($this->tagName);
	}

}

?>
