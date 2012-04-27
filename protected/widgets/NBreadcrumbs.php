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

	public function init() {
		parent::init();
	}


	public function run() {
		if(empty($this->links))
		{
			echo CHtml::openTag($this->tagName,$this->htmlOptions)."\n";
			echo CHtml::openTag("li\n");
			echo CHtml::openTag('a href="'.Yii::app()->homeUrl.'"');
			echo CHtml::tag('i', array( 'class' => 'icon-home icon-white'), ' ', true);
			echo "Home";
			echo CHtml::closeTag('a');
			echo CHtml::closeTag('li');
			echo CHtml::closeTag($this->tagName);
			return;
		}
		echo CHtml::openTag($this->tagName,$this->htmlOptions)."\n";
		$links=array();
		if($this->homeLink===null)
			$links[]='<li>'.CHtml::tag('i', array( 'class' => 'icon-home icon-white'), ' ', true).CHtml::link(Yii::t('zii','Home'),Yii::app()->homeUrl).'</li>';
		else if($this->homeLink!==false)
			$links[]='<li>'.$this->homeLink.'<li class="divider-vertical"></li></li>';
		foreach($this->links as $label=>$url)
		{
			if(is_string($label) || is_array($url))
				$links[]=CHtml::link($this->encodeLabel ? '<li><li class="divider-vertical"></li>'.CHtml::encode($label).'</li>' :'<li>'. $label, $url.'</li>');
			else
				$links[]=($this->encodeLabel ? '<li><li class="divider-vertical"></li>'.CHtml::encode($url).'</li>' : '<li>'.$url.'</li>');
		}
		echo implode($links);
		echo CHtml::closeTag($this->tagName);
	}

}

?>
