<?php

class DeveloperApplicationController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout = '//layouts/column2';

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
		return array(
			array('allow',  // Allow users to create, view and update their applications
				'actions' => array('index', 'view', 'supporterAutocomplete'),
				'roles' => array('users'),
			),
			array('allow', // Allow sysadmins to manage applications
				'actions' => array('list', 'view', 'update', 'supporterAutocomplete'),
				'roles' => array('sysadmins'),
			),
			array('deny',  // Everything else is not permitted
				'users' => array('*'),
			),
		);
	}

	/**
	 * If the person has a current application, we redirect them to it, otherwise we show the form that allows them to file a request
	 */
	public function actionIndex()
	{
		// Find their pending developer application if they have one (we only redirect for pending ones so the user can reapply later)
		$uid = Yii::app()->user->id;
		$model = DeveloperApplication::model()->findByAttributes( array('uid' => $uid, 'status' => DeveloperApplication::StatusPending) );
		// If we found a currently pending application redirect them
		if( $model instanceof DeveloperApplication ) {
			$this->redirect( array('view', 'id' => $model->id) );
		}

		// Create a new developer application, and set some sane default values
		$model = new DeveloperApplication;
		$model->uid = Yii::app()->user->id;
		$model->status = DeveloperApplication::StatusPending;
		$model->special_reason = DeveloperApplication::ReasonNone;

		// Maybe we have a submission?
		if( isset($_POST['DeveloperApplication']) ) {
			// Transfer normally submitted data over
			$model->attributes = $_POST['DeveloperApplication'];
			// If a SSH Key was uploaded, transfer the content into the database, pending validation
			$model->uploadedSSHKey = CUploadedFile::getInstance($model, 'ssh_key');
			// Try and save our information
			if( $model->save() ) {
				$this->sendApplicationEmails($model);
				$this->redirect( array('view', 'id' => $model->id) );
			}
		}

		$this->render('index', array(
			'model' => $model,
		));
	}

	public function actionSupporterAutocomplete($term)
	{
		// Build the user filter
		$userFilters = array();
		$userFilters[] = Net_LDAP2_Filter::create('uid', 'contains', $term);
		$userFilters[] = Net_LDAP2_Filter::create('cn', 'contains', $term);
		$userFilters[] = Net_LDAP2_Filter::create('mail', 'contains', $term);
		// Build the final filter
		$filters = array();
		$filters[] = Net_LDAP2_Filter::combine('or', $userFilters);
		$filters[] = Net_LDAP2_Filter::create('groupMember', 'equals', Yii::app()->params['developerGroup']);

		// Perform the search, with a size limit of only 10 results
		$filter = Net_LDAP2_Filter::combine('and', $filters);
		$results = User::model()->findByFilter($filter, array('uid', 'cn'), null, null, array('sizelimit' => 10));

		// Produce the needed output
		$data = array();
		foreach( $results as $entry ) {
			$data[] = array('label' => $entry->cn, 'value' => $entry->uid, 'id' => count($data));
		}
		echo CJSON::encode($data);
	}

	/**
	 * Displays a already existing developer application.
	 */
	public function actionView($id)
	{
		$model = $this->loadModel($id);

		if( !Yii::app()->user->checkAccess('manageDeveloperApplications', array('application' => $model)) ) {
			throw new CHttpException(403, 'You are not permitted to view this developer account application.');
		}

		$this->render('view', array(
			'model' => $model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);

		if( isset($_POST['DeveloperApplication']) ) {
			// Transfer normally submitted data over
			$model->attributes = $_POST['DeveloperApplication'];
			// Maybe they are allowed to update the status?
			if( Yii::app()->user->checkAccess('sysadmins') ) {
				$model->status = $_POST['DeveloperApplication']['status'];
			}
			// If a SSH Key was uploaded, transfer the content into the database, pending validation
			$model->uploadedSSHKey = CUploadedFile::getInstance($model, 'ssh_key');
			// Try and save our information
			if( $model->save() ) {
				$this->redirect( array('view', 'id' => $model->id) );
			}
		}

		$this->render('update', array(
			'model' => $model,
		));
	}

	/**
	 * Provides a list of developer applications for a sysadmin to review
	 */
	public function actionList()
	{
		// Setup the search model, load some nice defaults, and then set the values we want to search by
		$model = new DeveloperApplication('search');
		$model->status = DeveloperApplication::StatusPending;
		if( isset($_GET['DeveloperApplication']) ) {
			$model->attributes = $_GET['DeveloperApplication'];
		}

		// Setup the data provider
		$criteria = new CDbCriteria;
		$criteria->compare('status', $model->status);
		$criteria->compare('uid', $model->uid, true);
		$criteria->compare('supporter_uid', $model->supporter_uid, true);
		$criteria->compare('special_reason', $model->special_reason);
		$dataProvider = new CActiveDataProvider($model, array(
			'criteria' => $criteria,
		));

		$this->render('list', array(
			'model' => $model,
			'dataProvider' => $dataProvider,
		));
	}

	protected function loadModel($id)
	{
		$model = DeveloperApplication::model()->findByPk($id);
		if( $model === null ) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}

	protected function sendApplicationEmails($model)
	{
		// First we send the general mail to the site administrator
		$this->sendEmail(Yii::app()->params['adminEmail'], '/mail/notifyDeveloperApplication', array('model' => $model));
		// Now we send the mail to the supporter
		if( $model->supporter instanceof User ) {
			$this->sendEmail($model->supporter->mail, '/mail/confirmDeveloperApplicationSupport', array('model' => $model));
		}
		// Finally, we notify the requester that their application has been submitted for approval
		$this->sendEmail($model->applicant->mail, '/mail/developerApplicationSubmitted', array('model' => $model));
	}
}
