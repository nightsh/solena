<?php

/**
 * Custom Formatter to handle the transformation of Array's into appropriately formatted results
 */
class Formatter extends CFormatter
{
	public $arraySeperator = "<br />";

	public function format($value, $type)
	{
		// Check to see if it is an array....
		if( !is_array($value) ) {
			return parent::format($value, $type);
		}
		
		// Iterate over and process each one....
		$results = array();
		foreach( $value as $entry ) {
			$results[] = parent::format($entry, $type);
		}
		return implode($this->arraySeperator, $results);
	}
};

?>