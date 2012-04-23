<?php
/***********************************************************
 * Meant to overwrite default Breadcrumbs and output a list 
 * instead of pure links
 **********************************************************/

class NBreadcrumbs extends CBreadcrumbs {

	public $tagName='ul';

	public $htmlOptions=array('class'=>'breadcrumb');

	public $separator='';

	public function init() {
		parent::init();
	}


	public function run() {
		if(empty($this->links))
		{
			echo CHtml::openTag($this->tagName,$this->htmlOptions)."\n";
			echo CHtml::openTag("li\n");
			echo CHtml::openTag('a href="'.Yii::app()->homeUrl.'"');
			echo "Home";
			echo CHtml::closeTag("a");
			echo CHtml::closeTag("li");
			echo CHtml::closeTag($this->tagName);
			return;
		}
		echo CHtml::openTag($this->tagName,$this->htmlOptions)."\n";
		$links=array();
		if($this->homeLink===null)
			$links[]="<li>".CHtml::link(Yii::t('zii','Home'),Yii::app()->homeUrl)."<li>";
		else if($this->homeLink!==false)
			$links[]="<li>".$this->homeLink."<li>";
		foreach($this->links as $label=>$url)
		{
			if(is_string($label) || is_array($url))
				$links[]="<li>".CHtml::link($this->encodeLabel ? CHtml::encode($label) : $label, $url)."<li>";
			else
				$links[]="<li>".($this->encodeLabel ? CHtml::encode($url) : $url)."<li>";
		}
		echo implode($links);
		echo CHtml::closeTag($this->tagName);
	}

}

?>
