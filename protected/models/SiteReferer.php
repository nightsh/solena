<?php

/**
 * Helper class for handling the referer in the registration process
 *
 */
class SiteReferer
{
	/**
	*
	* @var array whitelist for referers
	*/
	private $refererWhiteList = array();

	public function __construct()
	{
		$params = Yii::app()->getParams();
		if( is_array($params['refererWhiteList']) ) {
			$this->refererWhiteList = $params['refererWhiteList'];
		}
	}

	/**
	* Method to get the site referer for the registration process if the client provided
	* a referer and the session is still available. Returns null if no referer has been set.
	*/
	public static function getReferer()
	{
		$session = Yii::app()->getSession();
		if( isset($session['site-referer']) ) {
			return $session['site-referer'];
		}

		return null;
	}

	/**
	* Method to set the site referer which may be shown during the registration process
	* and which may be used to redirect the user to the original site on the end of the
	* registration process.
	*
	* @param string $referer
	*/
	public static function setReferer($referer)
	{
		$session = Yii::app()->getSession();
		$session['site-referer'] = $referer;
	}

	/**
	* Helper function to check the HTTP referer header and set a session variable if the referer
	* matches a whitelist.
	*/
	public function checkReferer()
	{
		if( ((self::getReferer() == null) && isset($_SERVER['HTTP_REFERER'])) ) {
			if( $this->checkWhitelist($_SERVER['HTTP_REFERER']) ) {
				self::setReferer($_SERVER['HTTP_REFERER']);
			}
		}
	}

	private function checkWhitelist($referer)
	{
		foreach( $this->refererWhiteList as $allowedReferer ) {
			if( preg_match('#^(http|https)://' . $allowedReferer . '.*#', $referer) ) {
				return true;
			}
		}
		return false;
	}
}
