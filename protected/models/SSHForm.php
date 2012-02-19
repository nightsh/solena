<?php

class SSHForm extends CFormModel
{
	public $newKey;
	public $existingKeys;
	static private $regex = '/^(ssh-(?:dss|rsa)|ecdsa-sha2-nistp(?:256|384|521))\s+([a-zA-Z0-9+\/.=]+)\s*([[:print:]]*)$/';

	public function rules()
	{
		return array(
			// The submitted SSH Key must be valid
			array('newKey', 'application.validators.SSHKeyValidator', 'existingKeys' => $this->existingKeys),
		);
	}

	protected function beforeValidate()
	{
		// Trim newly uploaded keys to remove newlines if they have them - causes a validation error
		if( isset($this->newKey) ) {
			$this->newKey = trim($this->newKey);
		}
		// Call the parent
		return parent::beforeValidate();
	}

	static public function splitKey($key, $id = 0)
	{
		// Make sure we have a possibly valid key first...
		$matches = array();
		if( !preg_match(self::$regex, $key, $matches) ) {
			return false;
		}
		// Generate the fingerprint and return the data
		$type = $matches[1];
		$comment = empty($matches[3]) ? 'No comment' : $matches[3];
		$fingerprint = preg_replace('/(..)/', '\1:', md5($matches[2]));
		$fingerprint = rtrim($fingerprint, ':');
		return array('id' => $id, 'type' => $type, 'fingerprint' => $fingerprint, 'comment' => $comment);
	}
}
