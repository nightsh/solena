<?php

class PeopleController extends Controller
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
				'actions' => array('index', 'view', 'viewAvatar'),
				'users' => array('@'),
			),
			array('allow', // Everyone is allowed to change their profile, contact details, avatar and password
				'actions' => array('editProfile', 'editContactDetails', 'editAvatar', 'changePassword'),
				'users' => array('@'),
			),
			array('allow', // Developers, Disabled Developers and Sysadmins are allowed to change SSH keys
				'actions' => array('editKeys'),
				'roles' => array('developers', 'disabled-developers', 'sysadmins'),
			),
			array('allow', // Creating, Deleting, Moving and Locking is for Sysadmins only
				'actions' => array('create', 'delete', 'move', 'toggleLock'),
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
				Yii::app()->user->setFlash('success', 'Person created successfully');
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
	 * Return the Avatar image of the given person
	 */
	public function actionViewAvatar($uid)
	{
		header('Content-type: octet-stream');
		header('Content-Transfer-Encoding: binary');
		$model = $this->loadModel($uid);
		if( isset($model->jpegPhoto) ) {
			echo $model->jpegPhoto;
		} else {
			$path = Yii::app()->basePath . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "default-avatar.png";
			echo file_get_contents($path);
		}
	}

	/**
	 * Delete an existing person
	 */
	public function actionDelete($uid)
	{
		$model = $this->loadModel($uid);
		$model->setScenario('delete');

		if( isset($_POST['confirmDeletion']) && isset($_POST['deleteAccount']) ) {
			if( $model->delete() ) {
				Yii::app()->user->setFlash('success', 'Person deleted successfully');
				$this->redirect( array('index') );
			}
		}

		$this->render('delete', array(
			'model' => $model,
		));
	}

	/**
	 * Edit the person's profile values
	 */
	public function actionEditProfile($uid)
	{
		$model = $this->loadModel($uid);
		$model->setScenario('editProfile');

		if( !Yii::app()->user->checkAccess('changeUserDetails', array('user' => $model)) ) {
			throw new CHttpException(403, 'You are not permitted to change the details of this person');
		}

		if( isset($_POST['User']) ) {
			// If we have the permission to, update the users username - and update their DN
			if( Yii::app()->user->checkAccess('changeUserUsername', array('user' => $model)) ) {
				$model->uid = $_POST['User']['uid'];
				$model->setDnByParent( $model->getParentDn() );
			}
			// If we have permission to update their KDE e.V membership
			if( Yii::app()->user->checkAccess('manageEvMembershipData', array('user' => $model)) ) {
				$model->memberStatus = $_POST['User']['memberStatus'];
			}
			// Update all other attriutes...
			$model->attributes = $_POST['User'];
			if( $model->save() ) {
				Yii::app()->user->setFlash('success', 'Profile updated successfully');
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

		if( !Yii::app()->user->checkAccess('changeUserDetails', array('user' => $model)) ) {
			throw new CHttpException(403, 'You are not permitted to change the details of this person');
		}

		if( isset($_POST['User']) ) {
			$model->attributes = $_POST['User'];
			if( $model->save() ) {
				Yii::app()->user->setFlash('success', 'Contact details updated successfully');
				$this->redirect( array('view', 'uid' => $model->uid) );
			}
		}
		
		$this->render('editContactDetails', array(
			'model' => $model,
		));
	}

	/**
	 * Edit the person's avatar
	 */
	public function actionEditAvatar($uid)
	{
		$model = $this->loadModel($uid);
		$model->setScenario('editAvatar');

		if( !Yii::app()->user->checkAccess('changeUserAvatar', array('user' => $model)) ) {
			throw new CHttpException(403, 'You are not permitted to change the details of this person');
		}

		// Handle the upload of an image
		if( isset($_POST['User']) ) {
			$model->jpegPhoto = CUploadedFile::getInstance($model, 'jpegPhoto');
			if( $model->save() ) {
				Yii::app()->user->setFlash('success', 'Avatar updated');
			}
		}
		
		// Handle the clearing of the avatar
		if( isset($_POST['clearAvatar']) ) {
			$model->removeAttribute("jpegPhoto");
			if( $model->save() ) {
				Yii::app()->user->setFlash('success', 'Avatar deleted');
			}
		}
		
		$this->render('editAvatar', array(
			'model' => $model,
		));
	}

	/**
	 * Edit the person's SSH keys
	 */
	public function actionEditKeys($uid)
	{
		$model = $this->loadModel($uid);
		$model->setScenario('editKeys');

		if( !Yii::app()->user->checkAccess('changeUserSshKeys', array('user' => $model)) ) {
			throw new CHttpException(403, 'You are not permitted to change the details of this person');
		}

		// Are we removing any keys?
		if( isset($_POST['removeKeys']) && isset($_POST['selectedKeys']) ) {
			$this->processKeyRemoval($model, $_POST['selectedKeys']);
		}
		
		// Maybe we are adding keys then?
		if( isset($_POST['uploadKeys']) ) {
			$keyUpload = CUploadedFile::getInstance($model, 'sshKeysAdded');
			$model->addSSHKeys($keyUpload);
			if( $model->save() ) {
				Yii::app()->user->setFlash('success', 'SSH key(s) added');
			}
		}
		
		$dataProvider = new CArrayDataProvider($model->getProcessedSshKeys());
		$this->render('editKeys', array(
			'model' => $model,
			'dataProvider' => $dataProvider,
		));
	}

	/**
	 * Move a person from their current Base DN to a newly selected Base DN
	 */
	public function actionMove($uid)
	{
		$model = $this->loadModel($uid);
		$model->setScenario('move');

		// Are we attempting to perform the create action?
		if( isset($_POST['User']) ) {
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
		
		$this->render('move', array(
			'model' => $model,
		));
	}

	/**
	 * Locks or Unlocks a person
	 */
	public function actionToggleLock($uid)
	{
		$model = $this->loadModel($uid, array('pwdAccountLockedTime', 'uid', 'cn'));
		$model->setScenario('toggleLock');
		
		// Locking yourself is prohibited
		if($model->dn == Yii::app()->user->dn) {
			throw new CHttpException(403, 'You are not permitted to lock yourself out');
		}
		
		// Handle the unlocking of an account
		if( isset($_POST['unlockAccount']) ) {
			$model->removeAttribute("pwdAccountLockedTime");
			if( $model->save() ) {
				Yii::app()->user->setFlash('success', 'Account lock released');
			}
		}
		
		// Handle the (infinite) locking of an account
		if( isset($_POST['lockAccount']) ) {
			$model->replaceAttribute("pwdAccountLockedTime", User::InfinitelyLocked);
			if( $model->save() ) {
				Yii::app()->user->setFlash('success', 'Account infinitely locked');
			}
		}
		
		$this->render('toggleLock', array(
			'model' => $model,
		));
	}

	/**
	 * Change the password of the person
	 */
	public function actionChangePassword($uid)
	{
		$model = $this->loadModel($uid);
		$model->setScenario('changePassword');

		if( !Yii::app()->user->checkAccess('changeUserPassword', array('user' => $model)) ) {
			throw new CHttpException(403, 'You are not permitted to change the details of this person');
		}

		if( isset($_POST['User']) ) {
			$model->attributes = $_POST['User'];
			if( $model->save() ) {
				Yii::app()->user->setFlash('success', 'Password changed successfully');
				$this->redirect( array('view', 'uid' => $model->uid) );
			}
		}
		
		$this->render('changePassword', array(
			'model' => $model,
		));
	}

	/**
	 * Retrieve the user specified by $uid from the LDAP Server
	 */
	protected function loadModel($uid, $attributes = array())
	{
		$filter = Net_LDAP2_Filter::create('uid', 'equals', $uid);
		$entry = User::model()->findFirstByFilter($filter, $attributes);
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
		$params = array('user' => $model);
		// General actions first..
		if( $this->action->id != 'view' ) {
			$menu[] = array('label' => 'View Profile', 'url' => array('view', 'uid' => $model->uid));
		}
		if( Yii::app()->user->checkAccess('changeUserDetails', $params) && $this->action->id != 'editProfile' ) {
			$menu[] = array('label' => 'Edit Profile', 'url' => array('editProfile', 'uid' => $model->uid));
		}
		if( Yii::app()->user->checkAccess('changeUserDetails', $params) && $this->action->id != 'editContactDetails' ) {
			$menu[] = array('label' => 'Edit Contact Details', 'url' => array('editContactDetails', 'uid' => $model->uid));
		}
		if( Yii::app()->user->checkAccess('changeUserAvatar', $params) && $this->action->id != 'editAvatar' ) {
			$menu[] = array('label' => 'Change Avatar', 'url' => array('editAvatar', 'uid' => $model->uid));
		}
		if( Yii::app()->user->checkAccess('changeUserSshKeys', $params) && $this->action->id != 'editKeys' ) {
			$menu[] = array('label' => 'Manage SSH Keys', 'url' => array('editKeys', 'uid' => $model->uid));
		}
		if( Yii::app()->user->checkAccess('changeUserPassword', $params) && $this->action->id != 'changePassword' ) {
			$menu[] = array('label' => 'Change Password', 'url' => array('changePassword', 'uid' => $model->uid));
		}
		// Sysadmin only actions now
		if( Yii::app()->user->checkAccess('sysadmins') ) {
			if( $this->action->id != 'toggleLock' ) {
				$menu[] = array('label' => 'Toggle Account Lock', 'url' => array('toggleLock', 'uid' => $model->uid));
			}
			if( $this->action->id != 'move' ) {
				$menu[] = array('label' => 'Move Entry', 'url' => array('move', 'uid' => $model->uid));
			}
			if( $this->action->id != 'delete' ) {
				$menu[] = array('label' => 'Delete Person', 'url' => array('delete', 'uid' => $model->uid));
			}
		}
		// Return results
		return $menu;
	}

	/**
	 * Process the removal of SSH Key's from a user
	 * To be used by actionEditKeys only
	 */
	protected function processKeyRemoval($model, $selectedKeys)
	{
		// Determine which SSH Keys we want to remove based on their index - then make sure we have SSH Keys to remove
		$selectedKeys = array_intersect_key( $model->sshPublicKey, array_flip($selectedKeys) );
		if( empty($selectedKeys) ) {
			return false;
		}
		// Remove the key(s) we need to remove....
		$model->removeAttribute("sshPublicKey", $selectedKeys);
		// Now try and save - if we succeed add a flash message so the user knows we succeeded
		if( $model->save() ) {
			Yii::app()->user->setFlash('success', 'SSH key(s) removed');
			return true;
		}
		return false;
	}
};

?>