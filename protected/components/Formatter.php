<?php

/**
 * Custom Formatter to handle the transformation of Array's into appropriately formatted results
 */
class Formatter extends CFormatter
{
	public $arraySeperator = "<br />";
	public $timezoneFormat = 'g:i a - F j, Y';

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
		// If it is multi-lined text, then we need to double up to get the needed effect
		if( $type == "ntext" ) {
			return implode($this->arraySeperator . $this->arraySeperator, $results);
		}
		return implode($this->arraySeperator, $results);
	}

	public function formatTimezone($value)
	{
		$timezone = new DateTimeZone($value);
		$currentTime = new DateTime("now", $timezone);
		return $currentTime->format($this->timezoneFormat);
	}

	public function formatGender($value)
	{
		$genderList = User::validGenders();
		return $this->formatText( $genderList[$value] );
	}
};

?>