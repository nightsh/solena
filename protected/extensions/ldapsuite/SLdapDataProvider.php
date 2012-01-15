<?php

class SLdapDataProvider extends CDataProvider
{
	/**
	 * The class name of the SLdapModel we are providing data for.
	 */
	public $modelName = null;
	/**
	 * Model instance used to provide the data.
	 */
	public $model = null;
	/**
	 * Name of the unique attribute which should be used instead of the unique attribute specified by the Model
	 */
	public $uniqueAttributes = null;
	/**
	 * Names of the attributes this data provider will retrieve from LDAP
	 */
	public $attributesToLoad = array();
	/**
	 * The filter which this data provider will apply to all searches and other operations it performs
	 */
	private $_filter;
	/**
	 * Cached search results, used for both count() and fetchData() calls
	 */
	private $_results = null;

	/**
	 * Constructor.
	 * @param mixed $model the name of the model class, or an instance of it
	 * @param array $config configuration (name=>value) to be applied as the initial property values of this class.
	 */
	public function __construct($model, $config=array())
	{
		// Set ourselves up with a model...
		if( is_string($model) ) {
			$this->modelName = $model;
			$this->model = SLdapModel::model($this->modelName);
		} else if($model instanceof SLdapModel) {
			$this->modelName = get_class($model);
			$this->model = SLdapModel::model($this->modelName);
		}
		// Load the configuration....
		foreach($config as $key=>$value) {
			$this->$key=$value;
		}
		$this->setId($this->modelName);
		// Setup the alternative sorting class we need to use...
		$sorter = new SLdapSort($this->model);
		$sorter->sortVar = $this->modelName . '_sort';
		$this->setSort($sorter);
	}

	/**
	 * Returns the (optional) filter which this data provider will use when retrieving data
	 * @return Net_LDAP2_Filter
	 */
	public function getFilter()
	{
		return $this->_filter;
	}

	/**
	 * Sets the (optional) filter which this data provider will use when retrieving data
	 * @param mixed $value the new filter which should now be used
	 */
	public function setFilter($value)
	{
		if( $value instanceof Net_LDAP2_Filter ) {
			$this->_filter = $value;
		}
	}

	/**
	 * Set the current filter to be the values of a Model instance
	 * @param SLdapModel $model Model to filter on
	 * @param array $attributes The attributes sourced from the model to be filtered on
	 */
	public function setFilterByModel($model, $attributes)
	{
		$filters = array();
		// Build the filter...
		foreach($attributes as $attrName) {
			$value = $model->getAttribute($attrName);
			if( $value !== null && $value != '' ) {
				$filters[] = Net_LDAP2_Filter::create($attrName, 'contains', $value);
			}
		}
		// Apply the filter...
		if( count($filters) == 1 ) {
			$this->_filter = array_shift($filters);
		} else if( count($filters) > 1 ) {
			$this->_filter = Net_LDAP2_Filter::combine('and', $filters);
		}
	}

	/**
	 * Returns the names of the attributes that this data provider will be loading
	 * If an empty array is returned, then all attributes held by items will be retrieved
	 */
	public function getAttributesToLoad()
	{
		return $this->attributesToLoad;
	}

	/**
	 * Sets the attributes which this data provider will be loading
	 * If a requested attribute is not found on a retrieved entry, then the value requested will be null.
	 * Attempting to read attributes not included in this list, other than 'objectClass' will lead to null being returned.
	 */
	public function setAttributesToLoad(array $attributesToLoad)
	{
		$this->attributesToLoad = $attributesToLoad;
	}

	/**
	 * Fetches the data from the persistent data storage.
	 * @return array list of data items
	 */
	protected function fetchData()
	{
		if( is_null($this->_results) ) {
			$this->_results = $this->performSearch();
		}
		return $this->_results->entries();
	}

	/**
	 * Fetches the data item keys from the persistent data storage.
	 * @return array list of data item keys.
	 */
	protected function fetchKeys()
	{
		$keys = array();
		$uniqueAttrs = is_null($this->uniqueAttributes) ? $this->model->uniqueAttributes() : $this->uniqueAttributes;
		foreach($this->getData() as $k => $data) {
			$value = array();
			foreach($uniqueAttrs as $name) {
				$value[$name] = $data->$name;
			}
			$keys[$k] = implode(',', $value);
		}
		return $keys;
	}

	/**
	 * Calculates the total number of data items.
	 * @return integer the total number of data items.
	 */
	protected function calculateTotalItemCount()
	{
		if( is_null($this->_results) ) {
			$this->_results = $this->performSearch();
		}
		return $this->_results->count();
	}
	
	/**
	 * Performs the search on the LDAP server
	 */
	private function performSearch()
	{
		// Retrieve paginator and sorter...
		$pagination = $this->getPagination();
		$sort = $this->getSort();
		
		// Do we have a valid filter?
		$filter = $this->getFilter();
		if( is_null($filter) ) {
			$filter = $this->buildFilter();
		}

		$results = $this->model->findByFilter($filter,  $this->attributesToLoad, $pagination, $sort, array('sizelimit' => 150));

		// Set some metadata which the displays will use later...
		if( $pagination !== false ) {
			$pagination->setItemCount( $results->count() );
		}
		return $results;
	}
	
	/**
	 * Builds a filter to be used in case the user has not specified one
	 * The filter simply assures that all unique attributes, specified either by the user or model are present
	 */
	private function buildFilter()
	{
		$entries = array();
		$uniqueAttrs = is_null($this->uniqueAttributes) ? $this->model->uniqueAttributes() : $this->uniqueAttributes;
		foreach( $uniqueAttrs as $attribute ) {
			$entries[] = Net_LDAP2_Filter::create($attribute, 'present');
		}
		if( count($entries) == 1 ) {
			return array_shift($entries);
		}
		return Net_LDAP2_Filter::combine('and', $entries);
	}
}

?>