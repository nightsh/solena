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
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		$this->render('index');
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
				$this->redirect( Yii::app()->user->returnUrl );
			}
		}

		// Display the login form
		$this->render('login', array('model' => $model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect( Yii::app()->homeUrl );
	}
}