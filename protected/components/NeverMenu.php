<?php
Yii :: import('zii.widgets.CMenu');
class NeverMenu extends CMenu {
	// must set this to allow  parameter changes in CMenu widget call
	public $activateItemsOuter = true;

	public function run() {
		$this->renderMenu($this->items);
	}

}