<?php

/**
 * BehaviorLoader will automatically load the appropriate behaviors based on the application parameters
 * This allows external modules to load behaviors into the components which load this behaviors through the application config
 */
class BehaviourLoader extends CBehavior
{
	public function attach($owner)
	{
		parent::attach($owner);
		$behaviors = $this->behaviorsToHandle();
		foreach( $behaviors as $name => $config ) {
			$owner->attachBehavior( $name, $config );
		}
	}
	
	public function detach($owner)
	{
		$behaviors = $this->behaviorsToHandle();
		foreach( $behaviors as $name => $config ) {
			$owner->detachBehavior( $name, $config );
		}
		parent::detach($owner);
	}
	
	public setEnabled($value)
	{
		// If our status isn't changing, don't do the above and just skip the rest of the process
		if( $value == $this->getEnabled() ) {
			return;
		}
		// if setEnabled(true) - then call enableBehavior, otherwise call disableBehavior
		$change = $value ? 'enableBehavior' : 'disableBehavior';
		$behaviors = $this->behaviorsToHandle();
		foreach( $behaviors as $name ) {
			$this->getOwner()->$change($name);
		}
		parent::setEnabled($value);
	}
	
	private function behaviorsToHandle()
	{
		$className = get_class( $this->getOwner() );
		return Yii::app()->params['behaviorLoader'][$className];
	}
}

?>