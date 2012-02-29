<?php

class UniqueAttributeValidator extends CValidator
{
	protected function validateAttribute($object, $attribute)
	{
		// Make sure we are dealing with a SLdapModel class here....
		if( !$object instanceof SLdapModel ) {
			$this->addError($object, $attribute, "Invalid validator configuration - this model cannot use this validator!");
		}
		// Generate a filter and perform a search
		$filter = Net_LDAP2_Filter::create($attribute, 'equals', $object->$attribute);
		$results = $object::model()->findByFilter($filter);
		// If we got no results - we are definitely unique....
		if( $results->count() == 0 ) {
			return;
		}
		// Now we check the DN names to see if they match....
		$entry = array_shift($results->entries());
		if( $entry->dn != $object->dn || $object->isNewObject ) {
			$this->addError($object, $attribute, "{attribute} is already in use.");
		}
	}
};