<?php

include_once('Net/LDAP2.php');

final class SLdapServer extends CApplicationComponent
{
	/**
	 * Hostname of the LDAP server we will be connecting to
	 * 'localhost' by default
	 */
	public $host = 'localhost';
	/**
	 * Port number of the LDAP server we will be connecting to
	 * '389' by default
	 */
	public $port = 389;
	/**
	 * Should TLS be enabled in our connection to the LDAP server
	 * 'false' by default, as the connection is to localhost by default and not all servers have TLS enabled
	 */
	public $tlsEnabled = false;
	/**
	 * Base DN on the LDAP server which we should operate under
	 * No value is set by default, this must be provided
	 * If not set, a CException will be raised
	 */
	public $baseDn = null;
	/**
	 * DN we should Bind to the LDAP server as for general operations
	 * This will only be used when the user is logged out if $operateAsUser is true
	 * If not provided, then an anonymous connection will be attempted
	 * $bindPassword must also be set otherwise this will not be used
	 */
	public $bindDn = null;
	/**
	 * Password of the DN we should bind to LDAP server as for general operations
	 * This will only be used when the user is logged out if $operateAsUser is true
	 * This will not be used if $bindDn has not been set
	 */
	public $bindPassword = null;
	/**
	 * Should we operate as the user currently logged into the application?
	 * 'false' by default
	 * If enabled, then SLdapServer will bind using their credentials instead
	 * If binding as the user fails and this is enabled, then the user will be logged out
	 */
	public $operateAsUser = false;
	/** 
	 * Path to the cache file on disk to be used for schema operations
	 * This file must be writable by the application
	 * Whilst this may be disabled, it will cause a severe performance impact as the LDAP schemas must be retrieved each time
	 * Defaults to protected/data/ldap_schema.cache 
	 */
	public $schemaCachePrefix = "data/ldap_schema.cache";
	
	/**
	 * Internal LDAP server connection
	 */
	private $_ldap = null;
	/**
	 * Copy of credentials we reauthenticated with
	 */
	private $_credentials = array();

    public function init()
	{
		// Assemble our configuration for Net_LDAP2 object
		$config = array('host'      => $this->host,
						'port'      => $this->port,
						'starttls'  => $this->tlsEnabled,
						'basedn'    => $this->baseDn);

		// See if we have a Bind DN and Password....
		if( !is_null($this->bindDn) && !is_null($this->bindPassword) ) {
			$config['binddn'] = $this->bindDn;
			$config['bindpw'] = $this->bindPassword;
		}

		// See if the users credentials are available and should be used...
		if( $this->operateAsUser && !Yii::app()->user->isGuest ) {
			$state = $this->loadPersistentCredentials($config);
			if( !$state ) {
				throw new CException('An unexpected error has occurred');
			}
		}

		$ldap = Net_LDAP2::connect($config);
		if( PEAR::isError($ldap) ) {
			throw new CException('Failure to connect to the LDAP server: ' . $ldap->message);
		}

		// Check to see if the LDAP Schema is writable
		$schemaPath = Yii::app()->basePath . DIRECTORY_SEPARATOR . $this->schemaCachePrefix;
		if( !is_null($this->schemaCachePrefix) ) {
			$cacheConfig = array(
				'path'    => $schemaPath,
				'max_age' => 1200, // Number of seconds cache lasts for, 0 for no automatic refresh
			);
			$cacheObject = new Net_LDAP2_SimpleFileSchemaCache($cacheConfig);
			$ldap->registerSchemaCache($cacheObject);
		}

		// We are now all setup and ready to rock and roll
		$this->_ldap = $ldap;
	}
	
	public function getConnection()
	{
		return $this->_ldap;
	}
	
	public function &schema()
	{
		$schema = &$this->_ldap->schema();
		if( PEAR::isError($schema) ) {
			return null;
		}
		return $schema;
	}
	
	public function search( $filter, $attributes = array(), $paginator = null, $sorting = null, $options = array() )
	{
		// Assemble options to perform the search
		$base = $this->baseDn;
		
		// Ensure objectClass is retrieved as we rely on it in places...
		if( !in_array('objectClass', $attributes) && !empty($attributes) ) {
			$attributes[] = 'objectClass';
		}
		
		// Has a limit on the attributes to retrieve been given?
		if( $attributes ) {
			$options['attributes'] = $attributes;
		}
		
		// Has the Base DN been overridden?
		if( isset($options['basedn']) ) {
			$base = $options['basedn'];
			unset($options['basedn']);
		}
		
		// Perform the search, then return the results. SLdapSearchResult will handle cases of the search failing....
		$results = $this->getConnection()->search( $base, $filter, $options );
		return new SLdapSearchResult($results, $paginator, $sorting);
	}

	public function reauthenticate($dn, $password)
	{
		// Try to bind using the credentials we were given...
		$result = $this->getConnection()->bind($dn, $password);
		// Did we fail? If so, rebind as who we originally were
		if( PEAR::isError($result) ) {
			$this->getConnection()->bind();
			return false;
		}
		// Bind succeeded, stow away the credentials
		$this->_credentials['binddn'] = $dn;
		$this->_credentials['bindpw'] = $password;
		return $result;
	}

	/**
	 * This function stores the previously given credentials so that the application will be able to "operate as the user".
	 *
	 * Every user session will have an encryption key uniquely generated for them
	 * The password will then be encyrpted using CSecurityManager, and HMAC armoured to prevent tampering
	 *
	 * The encryption key will be stored in the user's session - and the password stored in a seperate cookie
	 * The cookie will be restricted to prevent it being read in javascript, and will be stored for the length of the session only.
	 *
	 * This process will fail if the user's session is being maintained using Cookies - a PHP session must be used
	 */
	public function retainCredentials()
	{
		// First - make sure the session is not attempting to use cookies at all
		if( Yii::app()->user->allowAutoLogin ) {
			throw new CException('Credentials cannot be retained if Cookie based sessions are in use for security reasons');
		}
		// Do we have any retained credentials?
		if( empty($this->_credentials) ) {
			return;
		}
		
		// Generate a new key to encrypt the password with then encrypt the password with it
		$key = sprintf('%08x%08x%08x%08x',mt_rand(),mt_rand(),mt_rand(),mt_rand());
		$securePassword = Yii::app()->securityManager->encrypt($this->_credentials['bindpw'], $key);
		
		// Send the now encyrpted and armoured password back to the user
		$cookie = new CHttpCookie('accessKey', $securePassword);
		$cookie->secure = Yii::app()->request->isSecureConnection;
		$cookie->httpOnly = true;
		
		// Send the Cookie to the user, and save the given DN and the encryption key for future usage in the session
		Yii::app()->request->cookies['accessKey'] = $cookie;
		Yii::app()->user->setState('userDn', $this->_credentials['binddn']);
		Yii::app()->user->setState('userPasswordKey', $key);
	}

	public function discardCredentials()
	{
		unset(Yii::app()->request->cookies['accessKey']);
	}

	private function loadPersistentCredentials(&$config)
	{
		// Make sure we have an access key to use...
		if( !isset(Yii::app()->request->cookies['accessKey']) ) {
			return false;
		}
		
		// Retrieve and decrypt the password
		$securedPassword = Yii::app()->request->cookies['accessKey']->value;
		$passwordKey = Yii::app()->user->getState('userPasswordKey');
		$plainPassword = Yii::app()->securityManager->decrypt($securedPassword, $passwordKey);
		
		// Set the configuration
		$config['binddn'] = Yii::app()->user->getState('userDn');
		$config['bindpw'] = $plainPassword;
		return true;
	}
}

?>