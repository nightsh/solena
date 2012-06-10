<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// Page action renders "static" pages stored under 'protected/views/site/pages'
			'page' => array(
				'class' => 'CViewAction',
			),
		);
	}

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
			array('deny',  // Everyone who is authenticated cannot initiate a password reset
				'actions' => array('passwordReset', 'performPasswordReset'),
				'users' => array('@'),
			),
			array('allow',  // Everything else is permitted
				'users' => array('*'),
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		$model = new LoginForm;
		$this->render('index', array('model' => $model));
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		$error = Yii::app()->errorHandler->error;
		if( $error ) {
			if(Yii::app()->request->isAjaxRequest) {
				echo $error['message'];
			} else {
				$this->render('error', $error);
			}
		}
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model = new LoginForm;

		// If they are already logged in, they cannot login again...
		if( !Yii::app()->user->isGuest ) {
			$this->redirect( Yii::app()->homeUrl );
		}

		// Handle a AJAX validation request if we have one
		if( isset($_POST['ajax']) && $_POST['ajax'] === 'login-form' ) {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// Handle input
		if( isset($_POST['LoginForm']) ) {
			$model->attributes = $_POST['LoginForm'];
			// Validate the login and redirect to the previous page if valid
			if( $model->validate() && $model->login() ) {
				$return = Yii::app()->user->getReturnUrl( array('/people/view', 'uid' => Yii::app()->user->id) );
				$this->redirect( $return );
			} else if( $model->validate() ) {
				$this->redirect( array('loginTwoFactor', 'username' => $model->username) );
			}
		}

		// Display the login form
		$this->render('login', array('model' => $model));
	}

	/**
	 * Performs two factor authentication if necessary
	 */
	public function actionLoginTwoFactor( $username )
	{
		$model = new LoginForm('twoFactor');
		$model->username = $username;

		// Handle input
		if( isset($_POST['LoginForm']) ) {
			$model->attributes = $_POST['LoginForm'];
			// Validate the login and redirect to the previous page if needed
			if( $model->validate() && $model->login() ) {
				$return = Yii::app()->user->getReturnUrl( array('/people/view', 'uid' => Yii::app()->user->id) );
				$this->redirect( $return );
			}
		}

		// Display the two-factor authentication form
		$gridPosition = Yii::app()->tokenGrid->getRandomGridPosition( $model->username );
		$this->render('loginTwoFactor', array('model' => $model, 'gridPosition' => $gridPosition));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect( Yii::app()->homeUrl );
	}

	public function actionPasswordReset()
	{
		$model = new Token('reset');
		$model->type = Token::TypeResetPassword;

		// Handle input
		if( isset($_POST['Token']) ) {
			$model->attributes = $_POST['Token'];
			// Validate the username and email address they have provided...
			if( $model->save() ) {
				$this->sendEmail($model->mail, '/mail/passwordReset', array('model' => $model));
				$this->render('passwordResetPending', array('model' => $model));
				Yii::app()->end();
			}
		}

		// Display the password reset form
		$this->render('passwordReset', array('model' => $model));
	}

	public function actionPerformPasswordReset($uid, $token)
	{
		// Load the token first - if that is invalid then they cannot reset the password
		$entry = Token::model()->findByAttributes( array('uid' => $uid, 'type' => Token::TypeResetPassword, 'token' => $token) );
		if( !$entry instanceof Token ) {
			throw new CHttpException(404, 'The given token could not be authenticated.');
		}

		// Load the user now
		$filter = Net_LDAP2_Filter::create('uid', 'equals', $entry->uid);
		$model = User::model()->findFirstByFilter($filter);
		if( !$model instanceof User ) {
			throw new CHttpException(404, 'The specified user could not be found.');
		}
		$model->setScenario('changePassword');

		// Handle the actual password reset
		if( isset($_POST['User']) ) {
			$model->attributes = $_POST['User'];
			if( $model->save() ) {
				$entry->delete();
				$this->render('passwordResetSuccessful', array('model' => $model));
				Yii::app()->end();
			}
		}

		// Display the new password form
		$this->render('performPasswordReset', array('model' => $model));
	}
}