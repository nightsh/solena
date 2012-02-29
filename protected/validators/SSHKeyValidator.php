<?php

class SSHKeyValidator extends CValidator
{
	public $existingKeys = array();
	static private $regex = '/^(ssh-(?:dss|rsa)|ecdsa-sha2-nistp(?:256|384|521))\s+([a-zA-Z0-9+\/.=]+)\s*([[:print:]]*)$/';

	protected function validateAttribute($object, $attribute)
	{
		// If we have any existing keys, make sure we add them to the list of known fingerprints
		$knownFingerprints = array();
		foreach( $this->existingKeys as $key ) {
			$split = SSHKeyValidator::splitKey($key);
			$knownFingerprints[] = $split['fingerprint'];
		}

		// Start the validation
		$value = isset($object->$attribute) ? $object->$attribute : array();
		foreach( $value as $key ) {
			// Make sure the key is valid....
			$split = SSHKeyValidator::splitKey($key);
			if( !$split ) {
				$this->addError($object, $attribute, "The uploaded SSH key is not a valid DSS, RSA or ECDSA key");
				continue;
			}
			// Make sure we haven't already got that key....
			if( in_array($split['fingerprint'], $knownFingerprints) ) {
				$this->addError($object, $attribute, "The uploaded SSH Key is already present");
				continue;
			}
			$knownFingerprints[] = $split['fingerprint'];
		}
	}

	static public function splitKey($key, $id = 0)
	{
		// Make sure we have a possibly valid key first...
		$matches = array();
		if( !preg_match(self::$regex, $key, $matches) ) {
			return false;
		}
		// Generate the fingerprint and return the data
		$comment = empty($matches[3]) ? 'No comment' : $matches[3];
		$fingerprint = preg_replace('/(..)/', '\1:', md5($matches[2]));
		$fingerprint = rtrim($fingerprint, ':');
		return array('id' => $id, 'type' => $matches[1], 'fingerprint' => $fingerprint, 'comment' => $comment);
	}
};