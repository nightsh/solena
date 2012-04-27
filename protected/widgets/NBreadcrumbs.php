<?php

Yii::import('zii.widgets.CBreadcrumbs');

/***********************************************************
 * Meant to overwrite default Breadcrumbs and output a list 
 * instead of pure links
 **********************************************************/

class NBreadcrumbs extends CBreadcrumbs {

	public $tagName='ul';

	public $htmlOptions=array('class'=>'nav');

	public $separator='';

	public $icon;

	public function init() {
		parent::init();
	}


	public function run()
	{
		if(empty($this->links))
		{
			echo $this->homeLink;
			return;
		}
		echo CHtml::openTag($this->tagName,$this->htmlOptions)."\n";
		$links=array();
		if($this->homeLink===null)
			$links[]=CHtml::link(Yii::t('zii','Home'),Yii::app()->homeUrl);
		else if($this->homeLink!==false)
			$links[]=$this->homeLink;
		foreach($this->links as $label=>$url)
		{
			if(is_string($label) || is_array($url))
				$links[]=CHtml::link($this->encodeLabel ? CHtml::encode($label) : $label, $url);
			else
				$links[]='<span>'.($this->encodeLabel ? CHtml::encode($url) : $url).'</span>';
			}
			echo implode($this->separator,$links);
			echo CHtml::closeTag($this->tagName);
        }

}

?>
