<?php

abstract class SLdapModel extends CModel
{
	/**
	 * Setting - Object Classes which this object *must* have
	 */
	protected $_requiredObjectClasses = null;
	
	/**
	 * Internal - LDAP Entry we represent
	 */
	private $_entry = null;
	/**
	 * Internal - Copy of the LDAP Entry we represent, used to provide "original" state
	 */
	private $_originalEntry = null;

	/**
	 * Internal - Cache of static Model instances
	 */
	private static $_models = array();
	/**
	 * Internal - Cache of LDAP Server connection
	 */
	private static $_ldap = null;
	/**
	 * Internal - List of operational attributes
	 */
	private static $_operationalAttributes = array("pwdAccountLockedTime", "pwdChangedTime", "pwdFailureTime", "pwdHistory");
	
	/**
	 * Constructor.
	 * @param string $scenario scenario name. See {@link CModel::scenario} for more details about this parameter.
	 */
	public function __construct($scenario='create')
	{
		if( $scenario === null ) { // Used internally
			return;
		}

		$this->setScenario($scenario);
		// Create the entry with no DN specified - a parent will need to be set and the RDN will be auto re-generated
		$this->setEntry();

		$this->init();
		$this->attachBehaviors($this->behaviors());
		$this->afterConstruct();
	}

	/**
	 * Initializes the model.
	 * Called once model has been created, and the scenario set
	 * Override this to provide code needed to initialize the model
	 * (Eg: initial setup)
	 */
	public function init()
	{
	}

	/**
	 * PHP getter magic method.
	 * Allows LDAP attributes to be accessed like properties.
	 * @param string $name property name
	 * @return mixed property value
	 * @see getAttribute
	 */
	public function __get($name)
	{
		if( $this->hasAttribute($name) ) {
			return $this->getAttribute($name);
		}
		
		return parent::__get($name);
	}

	/**
	 * PHP setter magic method.
	 * Allows LDAP attributes to be accessed like properties.
	 * @param string $name property name
	 * @param mixed $value property value
	 * @see replaceAttribute
	 */
	public function __set($name,$value)
	{
		if( $this->replaceAttribute($name, $value) === true ) {
			return;
		}
		parent::__set($name,$value);
	}

	/**
	 * Checks if a property value is null.
	 * @param string $name property name
	 * @return boolean whether the property is null
	 * @see hasAttribute
	 */
	public function __isset($name)
	{
		if( $this->_entry->exists($name) ) {
			return true;
		} else if( $this->hasAttribute($name) ) {
			return false;
		}
		
		return parent::__isset($name);
	}

	/**
	 * Sets a component property to be null.
	 * Clears the specified LDAP attribute
	 * Attempting to clear objectClass will result in a CException
	 * @param string $name the property name or the event name
	 * @see removeAttribute
	 */
	public function __unset($name)
	{
		if( $this->_entry->exists($name) ) {
			$this->removeAttribute($name);
		} else {
			parent::__unset($name);
		}
	}

	/**
	 * Returns the ldap server connection used by this LDAP model.
	 * By default, the "ldap" application component is used
	 * You may override this method if you want to use a different ldap server connection.
	 * @return LDAPServer the ldap server used by this model
	 */
	public static function getLdapConnection()
	{
		if( self::$_ldap !== null ) {
			return self::$_ldap;
		}

		$ldap = Yii::app()->ldap;
		if( !$ldap instanceof SLdapServer ) {
			throw new CException(Yii::t('ldapsuite','LDAP requires a "ldap" LdapServer application component.'));
		}
		self::$_ldap = $ldap;
		return self::$_ldap;
	}

	/**
	 * Returns the unique attribute(s) which are to be used in the order they appear.
	 * The presence of this attribute is required on matching items.
	 * Implementing models must implement this.
	 * @return array the unique attribute(s) used by objects represented by this model
	 */
	public function uniqueAttributes()
	{
		return array();
	}

	/**
	 * Returns a list of attributes which are intended to be multi-valued.
	 * These attributes will always be returned as an array by the Model accessors.
	 * @return array the attributes which are intended to be multi-valued.
	 */
	public function multivaluedAttributes()
	{
		return array();
	}

	/**
	 * Returns the static model of the specified LM class.
	 * The model returned is a static instance of the LM class.
	 * It is provided for invoking class-level methods (something similar to static class methods.)
	 *
	 * EVERY derived LM class must override this method as follows,
	 * <pre>
	 * public static function model($className=__CLASS__)
	 * {
	 *     return parent::model($className);
	 * }
	 * </pre>
	 *
	 * @param string $className ldap model class name.
	 * @return SLdapModel ldap model instance.
	 */
	public static function model($className=__CLASS__)
	{
		if( isset(self::$_models[$className]) ) {
			return self::$_models[$className];
		}
		
		$model = self::$_models[$className] = new $className(null);
		$model->setEntry();
		$model->attachBehaviors($model->behaviors());
		return $model;
	}

	/**
	 * Returns whether there is an element at the specified offset.
	 * This method is required by the interface ArrayAccess.
	 * @param mixed $offset the offset to check on
	 * @return boolean
	 */
	public function offsetExists($offset)
	{
		return $this->__isset($offset);
	}

	/**
	 * Returns the DN for the current LDAP entry
	 * Will return null if this is a new entry which does not yet have a DN unless it has been set with {@link setDn}
	 * @return mixed Current DN
	 */
	public function getDn()
	{
		return $this->_entry->dn();
	}

	/**
	 * Returns the DN which is currently in LDAP for this LDAP entry.
	 * Will return null if this is a new entry.
	 * @return mixed DN currently saved in LDAP
	 */
	public function getOriginalDn()
	{
		return $this->_originalEntry->dn();
	}

	/**
	 * Sets a new DN for the current entry
	 * This will fail if $newdn does not exist, or the rdn attributes are not currently in the entry
	 * @return boolean status of DN change
	 */
	public function setDn($newdn)
	{
		// Don't bother trying to do a move if the DN's are the same....
		if( $newdn == $this->getDn() ) {
			return false;
		}
		
		return $this->_entry->move( $newdn );
	}
	
	/**
	 * Creates a new DN for the current entry, based on an automaticaly generated RDN and the DN of a parent
	 * @return boolean status of DN change
	 */
	public function setDnByParent($parent)
	{
		// We accept a Model instance to make things easier - so convert it if need be...
		if( $parent instanceof SLdapModel ) {
			$parent = $parent->getDn();
		}
		
		// Generate a RDN
		$rdn = array();
		foreach( $this->uniqueAttributes() as $attribute ) {
			$rdn[$attribute] = $this->getAttribute($attribute);
		}
		// Remove all null values, then check to see if we even have a possible RDN (as all values might have been empty)
		$rdn = array_filter($rdn, 'strlen');
		if( empty($rdn) ) {
			return false;
		}
		
		// Generate the New DN
		$parent = Net_LDAP2_Util::ldap_explode_dn($parent);
		$new_dn = array_merge(array($rdn), $parent);
		$new_dn = Net_LDAP2_Util::canonical_dn($new_dn);
		
		// Is the change even needed? - if the DN's match then don't do anything
		if( $this->getDn() == strtolower($new_dn) ) {
			return true;
		}
		// Set the New DN
		return $this->setDn($new_dn);
	}

	/**
	 * Returns the current Parent of this Entry in the LDAP Directory.
	 * If this entry has not yet been created, then null will be returned
	 */
	public function getParentDn()
	{
		$dn = $this->getDn();
		if( is_null($dn) ) {
			return null;
		}
		// Split the current DN up - and make sure we succeeded at that
		$parent = Net_LDAP2_Util::ldap_explode_dn($dn);
		if( Net_LDAP2::isError($parent) ) {
			return null;
		}
		// Seperate our RDN off and return the Parent DN
		$child = array_shift($parent);
		return strtolower( Net_LDAP2_Util::canonical_dn($parent) );
	}

	/**
	 * Returns the list of all attribute names of the model.
	 * This would return a list of all attribute names currently in use
	 * Adding new attributes will cause this to change (however new attributes must be permitted by current object classes)
	 * @return array list of attribute names.
	 */
	public function attributeNames()
	{
		$objectClasses = $this->getAttribute("objectClass");
		return $this->attributesAllowedByObjectClass($objectClasses);
	}
	
	/**
	 * Returns an array of the default attributes which will be applied to all new entries.
	 * It is expected that the default object classes (including structural ones such as 'top') are included in this
	 * This must be implemented, otherwise new object creation will likely fail
	 * @return array of default attributes
	 */
	public function defaultAttributes()
	{
		return array();
	}

	/**
	 * Checks if this LDAP Model has or can have the named attribute
	 * @param string $name attribute name
	 * @return boolean whether this LDAP Model has the named attribute (permitted by object classes)
	 */
	public function hasAttribute($name)
	{
		if( $this->_entry->exists($name) ) {
			return true;
		}
		if( in_array($name, self::$_operationalAttributes) ) {
			return true;
		}
		
		$schema = $this->getLdapConnection()->schema();
		$objectClasses = $this->getAttribute("objectClass");
		return $schema->checkAttribute($name, (array) $objectClasses);
	}

	/**
	 * Checks if this LDAP Model has the named object class
	 * @param string $objectClassName object class name
	 * @return boolean whether this LDAP Model has the specified object class
	 */
	public function hasObjectClass($objectClassName)
	{
		$objectClasses = $this->getAttribute("objectClass");
		return in_array($objectClassName, $objectClasses);
	}

	/**
	 * Returns the named attribute value.
	 * If this is a new object and the attribute is not set before then null will be returned.
	 * If this object is the result of a search and the attribute is not loaded,
	 * null will be returned.
	 * You may also use $this->AttributeName to obtain the attribute value.
	 * @param string $name the attribute name
	 * @return mixed the attribute value. Null if the attribute is not set or does not exist.
	 * @see hasAttribute
	 */
	public function getAttribute($name, $original = false)
	{
		// Use the necessary entry object
		$entry = $original ? $this->_originalEntry : $this->_entry;
		// There is no point trying to retrieve the value if it does not exist...
		if( !$entry->exists($name) ) {
			return null;
		}
		
		$value = $entry->getValue($name);
		return in_array($name, $this->multivaluedAttributes()) ? (array) $value : $value;
	}

	/**
	 * Removes the named attribute from the entry
	 * If $value is provided, only the $name attribute with that value will be removed
	 * If $value is null, then all attributes with $name will be removed from the entry
	 * @param string $name the attribute name to remove
	 * @param string $value the attribute value to remove
	 * @return boolean whether the removal failed. If the attribute is not set, or the value does not exist it will fail
	 */
	public function removeAttribute($name, $value = null)
	{
		// Make sure the name we were passed is actually valid...
		if( is_null($name) ) {
			return false;
		}
		// Check if said attribute is even valid
		if( !$this->hasAttribute($name) ) {
			return false;
		}
		
		$to_remove = is_null($value) ? array($name) : array( $name => $value );
		return $this->_entry->delete( $to_remove );
	}

	/**
	 * Removes all attributes supported by the named object class
	 * If an attribute is supported by another class on the object, then it will not be removed
	 * The named class will also be removed from the list of objectClass'es the entry has
	 * @param string $objectClassName the object class to remove
	 * @return boolean true if the object class was removed successfully. false if the object class is not present in the entry
	 */
	public function removeAttributesByObjectClass($objectClassName)
	{
		$objectClasses = $this->getAttribute("objectClass");

		// Check if we even have the given object class - removing it if we do and bailing out if we don't
		$key = array_search($objectClassName, (array) $objectClasses);
		if( $key === false ) {
			return false;
		}
		unset($objectClasses[$key]);

		// Build up the list of attributes to remove
		$nowAllowed = $this->attributesAllowedByObjectClass($objectClasses);
		$wasAllowed = $this->attributesAllowedByObjectClass($objectClassName);
		$toRemove   = array_diff($wasAllowed, $nowAllowed);

		// Remove the now prohibited attributes, and the mention from objectClass itself
		foreach( $toRemove as $attribute ) {
			$this->removeAttribute( $attribute );
		}
		return $this->removeAttribute( "objectClass", $objectClassName );
	}

	/**
	 * Adds a attribute to the entry with the specified value
	 * If an attribute with the given name already exists, it will not be replaced
	 * If the attribute is not permitted by the current object class then it will fail
	 * If needed, the specified value may be an array of values to add
	 * @param string $name the name of the attribute to add
	 * @param mixed $value the values of the newly added attribute
	 */
	public function addAttribute($name, $value)
	{
		// Check if said attribute is even valid
		if( !$this->hasAttribute($name) ) {
			return false;
		}
		// If it is an array, sanitize it - remove empty fields then re-key it.
		if( is_array($value) ) {
			$value = array_values( array_filter($value) );
		}
		
		$addition = array($name => $value);
		return $this->_entry->add($addition);
	}

	/**
	 * Replaces an attribute on the entry with the specified value
	 * If the attribute does not exist, it will simply be added
	 * All current values of the named attribute will be replaced with $value
	 * If needed, the specified value may be an array of values to replace with
	 * @param string $name the name of the attribute to add
	 * @param mixed $value the values to replace the named attribute with
	 */
	public function replaceAttribute($name,$value)
	{
		if( !$this->hasAttribute($name) ) {
			return false;
		}
		// If it is an array, sanitize it - remove empty fields then re-key it.
		if( is_array($value) ) {
			$value = array_values( array_filter($value) );
		}
		
		$replacement = array($name => $value);
		return $this->_entry->replace($replacement, true);
	}

	/**
	 * Saves the current object.
	 *
	 * The object will be created in ldap if its {@link isNewObject} property is true
	 * (usually the case when the object is created using the 'new' operator).
	 *
	 * Otherwise, it will be used to update the corresponding object in the ldap server
	 * (usually the case if the object is obtained using one of the 'find' methods.)
	 *
	 * Validation will be performed before saving the object. If the validation fails,
	 * the object will not be saved. You can call {@link getErrors()} to retrieve the
	 * validation errors.
	 *
	 * If the object is saved via creation, its {@link isNewObject} property will be
	 * set false, and its {@link scenario} property will be set to be 'update'.
	 *
	 * @param boolean $runValidation whether to perform validation before saving the object.
	 * If the validation fails, the object will not be saved to ldap.
	 * @return boolean whether the saving succeeds
	 */
	public function save($runValidation=true)
	{
		$ldap = $this->getLdapConnection();
		if( $runValidation && !$this->validate() ) {
			return false;
		}
		
		// If the user has not specified a DN, we should bail out...
		if( is_null($this->getDn()) ) {
			$this->addError("dn", "No DN has been set");
			return false;
		}
		
		// Does beforeSave permit us to save?
		if( !$this->beforeSave() ) {
			return false;
		}
		
		// Save our moving status then perform the save
		$moveStatus = $this->_entry->willBeMoved();
		$result = $this->_entry->update($ldap->getConnection());
		if( PEAR::isError($result) ) {
			$this->addError("system", $result->message);
			return false;
		}
		
		// Have we moved? If so indicate it
		if( $moveStatus ) {
			$this->afterMove();
		}
		
		// Save is successful as far as we can tell, indicate that
		$this->afterSave();
		$this->_originalEntry = clone $this->_entry;
		return $result;
	}
    
	/**
	 * Returns if the current object is new.
	 * @return boolean whether the object is new and should be created when calling {@link save}.
	 * This property is automatically set based on the value of the internal LDAP object
	 */
	public function getIsNewObject()
	{
		return $this->_entry->isNew();
	}
    
	/**
	 * Deletes the object from the LDAP server
	 * If this deletion is recursive (default is false) then all children entries will also be deleted
	 * If the item is new, then this will fail
	 * @param boolean should the deletion be recursive?
	 * @return boolean whether the deletion is successful.
	 */
	public function delete($recursive=false)
	{
		// It is simply not possible to delete new entries, so bail out
		if( $this->isNewObject ) {
			throw new CException('Entries which have not yet been saved cannot be deleted');
		}
		
		// Does beforeDelete permit us to delete this entry?
		if( !$this->beforeDelete() ) {
			return false;
		}
		
		// Perform the deletion - and did it succeed?
		$status = $this->getLdapConnection()->getConnection()->delete( $this->_entry, $recursive );
		if( $status ) {
			$this->_entry->markAsNew();
			$this->afterDelete();
		}
		return $status;
	}

	/**
	 * Reloads this object with the latest data from the LDAP server
	 * All changes made will be lost
	 * @return boolean if the information was retrieved successfully.
	 */
	public function refresh()
	{
		$ldap = $this->getLdapConnection()->getConnection();
		$entry = $ldap->getEntry( $this->getDn(), $this->attributeNames() );
		$this->setEntry($entry);
	}
	
	/**
	 * Retrieves the given attributes for the DN specified from the LDAP server
	 * @param string $dn The DN to retrieve from the directory
	 * @param string|array $attributes The attributes to retrieve from the specified object
	 * @return SLdapModel representing the DN specified, or null if retrieval failed
	 */
	public function findByDn( $dn, $attributes = array() )
	{
		// Sanitize input if we need to...
		if( !is_array($attributes) ) {
			$attributes = array($attributes);
		}
		
		// Prepare to, then perform the search
		$filter = $this->generateFilter();
		$ldap = $this->getLdapConnection();
		$results = $ldap->search($filter, $attributes, null, null, array('scope' => 'base', 'basedn' => $dn));
		
		// Extract the first (and only?) result, and check it to ensure it is valid
		$results = $results->entries();
		$entry = array_shift($results);
		if (false == $entry) {
			return null;
		}
		
		// Return a model instance
		return $this->createInstance($entry);
	}
	
	/**
	 * Perform a search on the LDAP server, then sort and paginate the results of that search locally if needed
	 *
	 * By default, the search will be executed using the default Base DN.
	 * To change this, specify a 'basedn' key in the $options array.
	 *
	 * Both $paginator and $sorting may be null for any query, in which case no pagination or sorting will be done respectively.
	 *
	 * @param Net_LDAP2_Filter $filter The filter to execute on the LDAP Server. Will be combined with the default filter
	 * @param array $attributes The attributes to retrieve for the results of the search
	 * @param null|CPagination $paginator Object representing how we should paginate our results
	 * @param null|CSort $sorting Object representing how results should be sorted
	 * @param array $options Parameters to be passed on to Net_LDAP2::search.
	 * @returns Array of SLdapModel instances representing the results
	 */
	public function findByFilter( $filter, $attributes = array(), $paginator = null, $sorting = null, $options = array() )
	{
		// Sanitize input if we need to...
		if( !is_array($attributes) ) {
			$attributes = array($attributes);
		}
		
		// Prepare the filter
		$search_filter = $this->generateFilter();
		if( !is_null($filter) ) {
			$search_filter = Net_LDAP2_Filter::combine('and', array($filter, $search_filter));
		}
		
		// Do the search...
		$ldap = $this->getLdapConnection();
		$results = $ldap->search($search_filter, $attributes, $paginator, $sorting, $options);
		
		// Return the results....
		$results->setModelClass( get_class($this) );
		return $results;
	}

	/**
	 * Retrieve a single item, using the filter specified.
	 * 
	 * By default, the search will be executed using the default Base DN.
	 * To change this, specify a 'basedn' key in the $options array.
	 *
	 * @param Net_LDAP2_Filter $filter The filter to execute on the LDAP Server. Will be combined with the default filter
	 * @param array $attributes The attributes to retrieve for the results of the search
	 * @param array $options Parameters to be passed on to Net_LDAP2::search.
	 * @returns SLdapModel single result model instance 
	 */
	public function findFirstByFilter( $filter, $attributes = array(), $options = array() )
	{
		// Perform the search - ensuring we only ask for one result
		$options = array_merge( $options, array('sizelimit' => 1) );
		$results = $this->findByFilter( $filter, $attributes, null, null, $options );
		
		// Extract the single result
		$results = $results->entries();
		return empty($results) ? null : array_shift($results);
	}

	/**
	 * Returns an array of attributes permitted by the given object classes
	 */
	protected function attributesAllowedByObjectClass( $objectClasses )
	{
		$schema = $this->getLdapConnection()->schema();
		$allowedAttributes = array();
		foreach( (array) $objectClasses as $class ) {
			$required = $schema->must( $class );
			if( !PEAR::isError($required) ) {
				$allowedAttributes[] = $required;
			}
			$permitted = $schema->may( $class );
			if( !PEAR::isError($permitted) ) {
				$allowedAttributes[] = $permitted;
			}
		}
		return call_user_func_array( "array_merge", $allowedAttributes );
	}

	/**
	 * Returns a Net_LDAP2_Filter which will be merged in with all user supplied filters
	 * when performing search operations
	 *
	 * If models wish to override this, then they must begin with the data produced by the parent function.
	 */
	protected function generateFilter()
	{
		$filters = array();
		foreach( $this->_requiredObjectClasses as $objectClass ) {
			$filters[] = Net_LDAP2_Filter::create('objectClass', 'equals', $objectClass);
		}
		if( count($filters) == 1 ) {
			return array_shift($filters);
		}
		return Net_LDAP2_Filter::combine('and', $filters);
	}

	/**
	 * Instantiates a instance of the Model and initializes it appropriately
	 * This function is strictly for internal usage inside ldapsuite and must never be invoked otherwise
	 * It must be public for technical reasons, and should be considered as a private function
	 */
	public function createInstance( $entry )
	{
		$class = get_class($this);
		$instance = new $class(null);
		
		$instance->setScenario('update');
		$instance->setEntry($entry);

		$instance->init();
		$instance->attachBehaviors($this->behaviors());
		$instance->afterConstruct();
		return $instance;
	}

	private function setEntry( $entry = null )
	{
		if( is_null($entry) ) {
			$entry = Net_LDAP2_Entry::createFresh(null, $this->defaultAttributes());
		}
		$this->_entry = $entry;
		$this->_originalEntry = clone $entry;
	}

	/**
	 * This event is raised prior to the entry being saved
	 * @param CEvent $event the event parameter
	 */
	public function onBeforeSave($event)
	{
		$this->raiseEvent('onBeforeSave', $event);
	}

	/**
	 * This event is raised after the entry has been saved.
	 * @param CEvent $event the event parameter
	 */
	public function onAfterSave($event)
	{
		$this->raiseEvent('onAfterSave', $event);
	}

	/**
	 * This event is raised after the DN of an entry has been changed during a save.
	 * It executes before onAfterSave but after the new DN has been saved.
	 * @param CEvent $event the event parameter
	 */
	public function onAfterMove($event)
	{
		$this->raiseEvent('onAfterMove', $event);
	}

	/**
	 * This event is raised prior to the entry being deleted.
	 * @param CEvent $event the event parameter
	 */
	public function onBeforeDelete($event)
	{
		$this->raiseEvent('onBeforeDelete', $event);
	}

	/**
	 * This event is raised after the entry has been deleted.
	 * @param CEvent $event the event parameter
	 */
	public function onAfterDelete($event)
	{
		$this->raiseEvent('onAfterDelete', $event);
	}

	/**
	 * This method is called prior to an entry being saved, after validation is performed if required
	 * By default this raises the {@link onBeforeSave} event.
	 * This method may be overridden to prepare the object to be saved.
	 * If overridden then the parent implementation must be invoked otherwise the event will not be raised properly.
	 * Use {@link isNewObject} to determine if the save is for a new entry or a pre-existing entry.
	 * @return boolean whether the saving should be executed. Defaults to true.
	 */
	protected function beforeSave()
	{
		if( $this->hasEventHandler('onBeforeSave') ) {
			$event = new CEvent($this);
			$this->onBeforeSave($event);
		}
		return true;
	}

	/**
	 * This method is called after an entry has been saved successfully.
	 * By default this raises the {@link onAfterSave} event.
	 * This method may be overridden to perform needed post-processing,
	 * If overridden then the parent implementation must be invoked otherwise the event will not be raised properly.
	 */
	protected function afterSave()
	{
		if( $this->hasEventHandler('onAfterSave') ) {
			$event = new CEvent($this);
			$this->onAfterSave($event);
		}
	}

	/**
	 * This method is called after an entry dn has been changed and then saved successfully.
	 * By default this raises the {@link onAfterMove} event.
	 * This method may be overridden to perform needed post-processing,
	 * If overridden then the parent implementation must be invoked otherwise the event will not be raised properly.
	 * @param SLdapModel $previousState copy of the SLdapModel instance made right before the save was performed
	 */
	protected function afterMove()
	{
		if( $this->hasEventHandler('onAfterMove') ) {
			$event = new CEvent($this);
			$this->onAfterMove($event);
		}
	}

	/**
	 * This method is called prior to the deletion of an entry,
	 * By default this raises the {@link onBeforeDelete} event.
	 * This method may be overridden to perform pre-deletion preperations.
	 * If overridden then the parent implementation must be invoked otherwise the event will not be raised properly.
	 * @return boolean whether the entry should be deleted. Defaults to true.
	 */
	protected function beforeDelete()
	{
		if( $this->hasEventHandler('onBeforeDelete') ) {
			$event = new CEvent($this);
			$this->onBeforeDelete($event);
		}
		return true;
	}

	/**
	 * This method is invoked after an entry has been deleted.
	 * By default this raises the {@link onAfterDelete} event.
	 * This method may be overridden to perform needed post-processing,
	 * If overridden then the parent implementation must be invoked otherwise the event will not be raised properly.
	 */
	protected function afterDelete()
	{
		if( $this->hasEventHandler('onAfterDelete') ) {
			$event = new CEvent($this);
			$this->onAfterDelete($event);
		}
	}
}

?>