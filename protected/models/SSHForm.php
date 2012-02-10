<?php

class SSHForm extends CFormModel
{
	public $newKey;
	public $existingKeys;
	static private $regex = '/^(ssh-(dss|rsa)|ecdsa-sha2-nistp(256|384|521))\s+([a-zA-Z0-9+\/.=]+)\s+([[:print:]]+)$/';

	public function rules()
	{
		return array(
			// The submitted SSH Key must be valid
			array('newKey', 'application.validators.SSHKeyValidator', 'existingKeys' => $this->existingKeys),
		);
	}

	static public function splitKey($key, $id = 0)
	{
		// Make sure we have a possibly valid key first...
		if( !preg_match(self::$regex, $key) ) {
			return false;
		}
		// Generate the fingerprint and return the data
		list($type, $data, $comment) = preg_split('/\s/', $key);
		$fingerprint = preg_replace('/(..)/', '\1:', md5($data));
		$fingerprint = rtrim($fingerprint, ':');
		return array('id' => $id, 'type' => $type, 'fingerprint' => $fingerprint, 'comment' => $comment);
	}
}
