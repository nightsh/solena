<?php
/**
Custom grid column which extends CDataColumn and add a 'urlExpression' feature to it so we can easily show DataColumns which are linked.
*/
class LinkDataColumn extends CDataColumn {
	protected $_urlExpression = false;

	public function setUrlExpression($urlExpression)
	{
		$this->_urlExpression = $urlExpression;
	}

	public function getUrlExpression()
	{
		return $this->_urlExpression;
	}

	public function renderDataCellContent($row, $data)
	{
		$url = $this->evaluateExpression($this->urlExpression,array('data'=>$data,'row'=>$row));
		echo CHtml::openTag('a', array('href' => $url));
		parent::renderDataCellContent($row, $data);
		echo CHtml::closeTag('a');
	}
}
?>