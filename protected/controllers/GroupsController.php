<?php

class GroupsController extends Controller
{
	/**
	 * Use the appropriate layout
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		// Note: finer grained access control wrt profile/contact details/avatar/ssh key/password changing is done at action level
		return array(
			array('allow',  // Everyone who is authenticated may list people and view them (including avatars)
				'actions' => array('index', 'view', 'edit', 'removeMember', 'addMember'),
				'users' => array('@'),
			),
			array('allow', // Creating and Deleting is for Sysadmins only
				'actions' => array('create', 'delete'),
				'roles' => array('sysadmins'),
			),
			array('deny',  // If not permitted above - access is denied...
				'users' => array('*'),
			),
		);
	}

	/**
	 * Show the list of people
	 */
	public function actionIndex()
	{
		// Create the model instance we will be using for searches....
		$model = new Group('search');
		if( isset($_GET['Group']) ) {
			$model->attributes = $_GET['Group'];
		}

		// Prepare the data provider - and apply a filter to it to ensure filters are applied
		$dataProvider = new SLdapDataProvider($model);
		$dataProvider->setFilterByModel($model, array('cn', 'description'));
		$dataProvider->setAttributesToLoad( array('cn', 'description') );

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
		$model = new Group;

		// Are we attempting to perform the create action?
		if( isset($_POST['Group']) ) {
			// Assign the attributes
			$model->attributes = $_POST['Group'];

			// Set the new group DN
			$filter = Net_LDAP2_Filter::create('ou', 'equals', 'groups');
			$parent = OrganisationalUnit::model()->findFirstByFilter( $filter );
			$model->setDnByParent($parent);

			// Create the new group
			if( $model->save() ) {
				$this->redirect(array('view', 'cn' => $model->cn));
			}
		}

		$this->render('create', array(
			'model' => $model,
		));
	}

	/**
	 * Show a person's profile and contact details
	 */
	public function actionView($cn)
	{
		// Load the group we are displaying here...
		$model = $this->loadModel($cn);

		// Create the model instance we will be using for searches....
		$user = new User('search');
		if( isset($_GET['User']) ) {
			$user->attributes = $_GET['User'];
		}

		// Create a data provider
		$filter = Net_LDAP2_Filter::create('groupMember', 'equals', $model->cn);
		$dataProvider = $this->createUserSearchProvider($filter);
		$dataProvider->retrieveLimit = 500;

		$this->render('view', array(
			'model' => $model,
			'dataProvider' => $dataProvider,
		));
	}

	/**
	 * Edit the person's profile values
	 */
	public function actionEdit($cn)
	{
		$model = $this->loadModel($cn);
		$model->setScenario('edit');

		if( !Yii::app()->user->checkAccess('manageGroup', array('group' => $model)) ) {
			throw new CHttpException(403, 'You are not permitted to change this group');
		}

		if( isset($_POST['Group']) ) {
			// Update all attributes...
			$model->attributes = $_POST['Group'];
			$model->setDnByParent( $model->getParentDn() );
			if( $model->save() ) {
				$this->redirect( array('view', 'cn' => $model->cn) );
			}
		}

		$this->render('edit', array(
			'model' => $model,
		));
	}

	/**
	 * Delete an existing person
	 */
	public function actionDelete($cn)
	{
		$model = $this->loadModel($cn);
		$model->setScenario('delete');

		if( isset($_POST['confirmDeletion']) && isset($_POST['deleteGroup']) ) {
			if( $model->delete() ) {
				$this->redirect( array('index') );
			}
		}

		$this->render('delete', array(
			'model' => $model,
		));
	}

	/**
	 * Add a new member to a group
	 */
	public function actionAddMember($cn)
	{
		$model = $this->loadModel($cn);
		$model->setScenario('addMember');

		if( !Yii::app()->user->checkAccess('manageGroup', array('group' => $model)) ) {
			throw new CHttpException(403, 'You are not permitted to change this group');
		}

		// Maybe we need to add people to the group?
		if( isset($_POST['selectedPerson']) ) {
			$this->processMemberAdd( $model, $_POST['selectedPerson'][0] );
		}

		// Create a data provider
		$filter = Net_LDAP2_Filter::create('groupMember', 'equals', $model->cn);
		$filter = Net_LDAP2_Filter::combine('not', $filter);
		$dataProvider = $this->createUserSearchProvider($filter);

		$this->render('addMember', array(
			'model' => $model,
			'dataProvider' => $dataProvider,
		));
	}

	public function actionRemoveMember($cn)
	{
		$model = $this->loadModel($cn);
		$model->setScenario('removeMember');

		if( !Yii::app()->user->checkAccess('manageGroup', array('group' => $model)) ) {
			throw new CHttpException(403, 'You are not permitted to change this group');
		}

		// Make sure this is a valid request....
		if( !isset($_POST['selectedPerson']) ) {
			throw new CHttpException(400, 'Invalid request - manipulation prohibited');
		}

		// Load the member we will be working with
		$member = User::model()->findByDn( $_POST['selectedPerson'][0] );
		if( is_null($member) ) {
			throw new CHttpException(400, 'Invalid request - could not locate the member whose removal has been requested');
		}

		// Has the removal been confirmed?
		if( isset($_POST['confirmRemoval']) ) {
			$model->removeMember($member);
			if( $model->save() && $member->save() ) {
				Yii::app()->user->setFlash('success', sprintf('Person %s removed from group successfully', $member->cn) );
				$this->redirect(array('view', 'cn' => $model->cn));
			}
		}

		$this->render('removeMember', array(
			'model' => $model,
			'member' => $member,
		));
	}

	/**
	 * Retrieve the group specified by $cn from the LDAP Server
	 */
	protected function loadModel($cn, $attributes = array())
	{
		$filter = Net_LDAP2_Filter::create('cn', 'equals', $cn);
		$entry = Group::model()->findFirstByFilter($filter, $attributes);
		if($entry === null) {
			throw new CHttpException(404,'The requested page does not exist.');
		}
		return $entry;
	}

	/**
	 * Generate a Menu for one of the Model based views...
	 */
	protected function generateMenu($model)
	{
		$menu = array();
		$params = array('group' => $model);
		// General actions first..
		if( $this->action->id != 'view' ) {
			$menu[] = array('label' => 'View Group', 'url' => array('view', 'cn' => $model->cn));
		}
		// Group management actions...
		if( Yii::app()->user->checkAccess('manageGroup') && $this->action->id != 'edit' ) {
			$menu[] = array('label' => 'Edit Group', 'url' => array('edit', 'cn' => $model->cn));
		}
		if( Yii::app()->user->checkAccess('manageGroup') && $this->action->id != 'addMember' ) {
			$menu[] = array('label' => 'Add Member To Group', 'url' => array('addMember', 'cn' => $model->cn));
		}
		// Sysadmin only actions now
		if( Yii::app()->user->checkAccess('sysadmins') && $this->action->id != 'delete' ) {
			$menu[] = array('label' => 'Delete Group', 'url' => array('delete', 'cn' => $model->cn));
		}
		// Return results
		return $menu;
	}

	/**
	 * Create a Data Provider, configured with a User model for searching purposes
	 */
	protected function createUserSearchProvider( $filter )
	{
		// Create the model instance we will be using for searches....
		$user = new User('search');
		if( isset($_GET['User']) ) {
			$user->attributes = $_GET['User'];
		}

		// Create a data provider
		$dataProvider = new SLdapDataProvider($user);
		$dataProvider->setAttributesToLoad( array('uid', 'cn', 'mail') );

		// Setup filters on the data provider - to provide their requested filters + the search done by the user
		$dataProvider->setFilter($filter);
		$dataProvider->setFilterByModel($user, array('uid', 'cn', 'mail'));

		return $dataProvider;
	}

	/**
	 * Handle the addition of users to a group
	 */
	protected function processMemberAdd( $group, $userDn )
	{
		// Take the proposed candidate - and turn it into a valid user instance
		$selectedUser = User::model()->findByDn( $userDn );
		if( !$selectedUser instanceof User ) {
			return false;
		}

		// Add them into the given group
		$group->addMember( $selectedUser );
		if( $group->save() && $selectedUser->save() ) {
			Yii::app()->user->setFlash('success', sprintf('Person %s added to group successfully', $selectedUser->cn) );
			$this->redirect(array('view', 'cn' => $group->cn));
			return true;
		}
		return false;
	}
};