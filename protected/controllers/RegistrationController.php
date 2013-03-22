<?php

class RegistrationController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
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
		return array(
			array('deny', // Currently logged in users may not register, that does not make sense
				'actions' => array('index', 'enterDetails', 'confirm'),
				'users' => array('@'),
			),
			array('allow', // Allow users not yet logged in to register accounts
				'actions' => array('index', 'enterDetails', 'confirm'),
				'users' => array('*'),
			),
			array('allow', // Allow sysadmins to see pending registrations, and view or delete them
				'actions' => array('list', 'view', 'update', 'delete'),
				'roles' => array('sysadmins'),
			),
			array('deny',  // Any other action is denied
				'users' => array('*'),
			),
		);
	}

	public function actionIndex()
	{
		// If they have confirmed they accept our terms, mark them as accepted
		if( isset($_POST['confirmAcceptance']) && isset($_POST['continue']) ) {
			Yii::app()->user->setState('registerTermsAccepted', 'confirmed');
		}
		// If they have already accepted our terms, do not reprompt them
		if( Yii::app()->user->getState('registerTermsAccepted') == 'confirmed' ) {
			$this->redirect( array('enterDetails') );
		}
		// Make sure they are not an attempted spammer
		if( $this->performSpamCheck() ) {
			throw new CHttpException(403, "Client rejected by automatic spammer detection system");
		}

		/**
		 * Check referer during the registration process to be able to show the user
		 * the page he is coming from and be able to redirect him at the end of the
		 * registration process.
		 */
		$refererHelper = new SiteReferer();
		$refererHelper->checkReferer();

		$this->render('index');
	}

	public function actionEnterDetails()
	{
		// Make sure they have accepted our terms....
		if( Yii::app()->user->getState('registerTermsAccepted') != 'confirmed' ) {
			$this->redirect( array('index') );
		}

		$model = new Token('register');

		// Maybe they have provided the needed information, in which case we need to act on it...
		if( isset($_POST['Token']) ) {
			$model->type = Token::TypeRegisterAccount;
			$model->attributes = $_POST['Token'];
			if( $this->performSpamCheck($model->mail) ) {
				throw new CHttpException(403, "Client rejected by automatic spammer detection system");
			}
			if( $model->save() ) {
				$this->sendEmail($model->mail, '/mail/confirmRegistration', array('model' => $model));
				$this->render('confirmationSent', array('model' => $model));
				Yii::app()->end();
			}
		}

		$this->render('enterDetails', array(
			'model' => $model,
		));
	}

	public function actionConfirm($id, $token)
	{
		// Ensure the provided registration confirmation is valid
		$tokenModel = Token::model()->findByAttributes( array('id' => $id, 'type' => Token::TypeRegisterAccount, 'token' => $token) );
		if( !$tokenModel instanceof CActiveRecord ) {
			throw new CHttpException(404, 'The given validation could not be confirmed, please contact the site administrator.');
		}

		// Prepare the user which we will be finalising
		$model = new User('register');
		$knownData = $tokenModel->getAttributes( array('uid', 'givenName', 'sn', 'mail') );
		$model->setAttributes( $knownData, false ); // Copy unsafe values as well as this is internally stored

		// Maybe we have a submission to handle?
		if( isset($_POST['User']) ) {
			// Assign the inbound data
			$model->attributes = $_POST['User'];
			// Set the DN the new user will be created in
			$model->setDnByParent( Yii::app()->params['registrationUnit'] );
			// Try to create the user now. If the username/password is bad then it will fail
			if( $model->save() ) {
				// Creation succeeded, so cleanup....
				$tokenModel->delete();
				// Inform the site administrator of the account creation
				$this->sendEmail(Yii::app()->params['adminEmail'], '/mail/notifyRegistration', array('model' => $model));
				// Give the user a page informing them of their account details
				$this->render('complete', array('model' => $model));
				Yii::app()->end();
			}
		}

		$this->render('confirm', array(
			'model' => $model,
		));
	}

	public function actionList()
	{
		$model = new Token('search');
		if( isset($_GET['Token']) ) {
			$model->attributes = $_GET['Token'];
		}

		$dataProvider = new CActiveDataProvider($model, array(
			'criteria' => array('condition' => 'type = ' . Token::TypeRegisterAccount),
			'pagination' => array('pageSize' => 20),
		));

		$this->render('list', array(
			'model' => $model,
			'dataProvider' => $dataProvider,
		));
	}

	public function actionView($id)
	{
		$model = $this->loadModel($id);

		if( isset($_POST['resendConfirmation']) ) {
			$this->sendEmail($model->mail, '/mail/confirmRegistration', array('model' => $model));
			Yii::app()->user->setFlash('success', 'Registration confirmation has been resent.');
		}

		$this->render('view', array(
			'model' => $model,
		));
	}

	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);
		$model->setScenario('register');

		if( isset($_POST['Token']) ) {
			$model->attributes = $_POST['Token'];
			if( $model->save() ) {
				$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('update', array(
			'model' => $model,
		));
	}

	public function actionDelete($id)
	{
		$model = $this->loadModel($id);

		if( isset($_POST['confirmDeletion']) && isset($_POST['deleteAccount']) ) {
			if( $model->delete() ) {
				$this->redirect( array('list') );
			}
		}

		$this->render('delete', array(
			'model' => $model,
		));
	}

	protected function performSpamCheck($email = "")
	{
		// Address of the API we are using
		$query = "http://www.stopforumspam.com/api";
		// We want php serialized data
		$query .= "?f=serial";
		// Add email address and ip address parameters if we have them...
		if( $email != "" ) {
			$query .= "&email=" . trim($email);
		}
		// Add the ip address
		$query .= "&ip=" . Yii::app()->request->userHostAddress;

		// Perform the query...
		$result = file_get_contents($query);

		// if we have network issues, let him through.
		if( $result === false ) {
			return false;
		}

		// Decode the data we recieved
		$result = unserialize($result);

		// if the query failed, permit the request
		if( $result["success"] != 1 ) {
			return false;
		}

		// If there is a greater than 80% the ip address is used by spammers, reject the request
		if( isset($result["ip"]["confidence"] ) && $result["ip"]["confidence"] > 80 ) {
			return true;
		}

		// If the email address has been used to spam anywhere, reject the request
		if( $email != "" && isset($result["email"]["confidence"]) && $result["email"]["confidence"] > 0 ) {
			return true;
		}

		// let everyone through.
		return false;
	}

	protected function loadModel($id)
	{
		$model = Token::model()->findByAttributes( array('id' => $id, 'type' => Token::TypeRegisterAccount) );
		if( $model === null ) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}
}
