<?php

class PeopleController extends Controller
{
	/**
	 * Use the appropriate layout
	 */
	public $layout='//layouts/column2';

	/**
	 * Show the list of people
	 */
	public function actionIndex()
	{
		// Create the model instance we will be using for searches....
		$model = new User('search');
		if( isset($_GET['User']) ) {
			$model->attributes = $_GET['User'];
		}
		
		// Prepare the data provider - and apply a filter to it to ensure filters are applied
		$dataProvider = new SLdapDataProvider($model);
		$dataProvider->setFilterByModel($model, array('uid', 'cn', 'mail'));
		$dataProvider->setAttributesToLoad( array('uid', 'cn', 'mail') );

		$this->render('index', array(
			'dataProvider' => $dataProvider,
			'model' => $model,
		));
	}

	/**
	 * Create a new person
	 */
	public function actionCreate()
	{
		$model = new User;
		
		// Are we attempting to perform the create action?
		if( isset($_POST['User']) ) {
			// Assign the attributes
			$model->attributes = $_POST['User'];
			
			// Determine the DN we want to be under
			$parent = OrganisationalUnit::model()->findByDn( $_POST['User']['parentDn'] );
			if( $parent instanceof OrganisationalUnit ) {
				$model->setDnByParent($parent);
			}
			
			// Create the new person
			if( $model->save() ) {
				$this->redirect(array('view', 'uid' => $model->uid));
			}
		}
		
		$this->render('create', array(
			'model' => $model,
		));
	}

	/**
	 * Show a person's profile and contact details
	 */
	public function actionView($uid)
	{
		$this->render('view', array(
			'model' => $this->loadModel($uid),
		));
	}

	/**
	 * Delete an existing person
	 */
	public function actionDelete($uid)
	{
	}

	/**
	 * Edit the person's profile values
	 */
	public function actionEditProfile($uid)
	{
		$model = $this->loadModel($uid);
		$model->setScenario('editProfile');

		if( isset($_POST['User']) ) {
			$model->attributes = $_POST['User'];
			$model->setDnByParent( $model->getParentDn() );
			if( $model->save() ) {
				$this->redirect( array('view', 'uid' => $model->uid) );
			}
		}

		$this->render('editProfile', array(
			'model' => $model,
		));
	}

	/**
	 * Edit the person's contact details
	 */
	public function actionEditContactDetails($uid)
	{
		$model = $this->loadModel($uid);
		$model->setScenario('editContactDetails');
		
		$this->render('editContactDetails', array(
			'model' => $model,
		));
	}

	/**
	 * Edit the person's avatar
	 */
	public function actionEditAvatar($uid)
	{
	}

	/**
	 * Edit the person's SSH keys
	 */
	public function actionEditKeys($uid)
	{
	}

	/**
	 * Move a person from their current Base DN to a newly selected Base DN
	 */
	public function actionMove($uid)
	{
	}

	/**
	 * Locks or Unlocks a person
	 */
	public function actionToggleLock($uid)
	{
	}

	/**
	 * Change the password of the person
	 */
	public function actionChangePassword($uid)
	{
	}

	/**
	 * Retrieve the user specified by $uid from the LDAP Server
	 */
	protected function loadModel($uid)
	{
		$filter = Net_LDAP2_Filter::create('uid', 'equals', $uid);
		$entry = User::model()->findFirstByFilter($filter);
		if($entry === null) {
			throw new CHttpException(404,'The requested page does not exist.');
		}
		return $entry;
	}
};

?>