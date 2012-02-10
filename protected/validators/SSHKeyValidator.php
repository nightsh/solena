<?php

class SSHKeyValidator extends CValidator
{
	public $existingKeys = array();

	protected function validateAttribute($object, $attribute)
	{
		// If we have any existing keys, make sure we add them to the list of known fingerprints
		$knownFingerprints = array();
		foreach( $this->existingKeys as $key ) {
			$split = SSHForm::splitKey($key);
			$knownFingerprints[] = $split['fingerprint'];
		}

		// Start the validation
		$value = (array) $object->$attribute;
		foreach( $value as $key ) {
			// Make sure the key is valid....
			$split = SSHForm::splitKey($key);
			if( !$split ) {
				$this->addError($object, $attribute, "The given SSH key is not a valid DSS, RSA or ECDSA key");
				continue;
			}
			// Make sure we haven't already got that key....
			if( in_array($split['fingerprint'], $knownFingerprints) ) {
				$this->addError($object, $attribute, "The given SSH Key is already present");
				continue;
			}
			$knownFingerprints[] = $split['fingerprint'];
		}
	}


};