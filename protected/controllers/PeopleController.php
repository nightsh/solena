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
	 * Return the Avatar image of the given person
	 */
	public function actionViewAvatar($uid)
	{
		$model = $this->loadModel($uid);
		header('Content-type: octet-stream');
		header('Content-Transfer-Encoding: binary');
		echo $model->jpegPhoto;
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
		
		if( isset($_POST['User']) ) {
			$model->attributes = $_POST['User'];
			if( $model->save() ) {
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
		
		$sshForm = new SSHForm;
		$sshForm->existingKeys = (array) $model->getAttribute("sshPublicKey");
		
		// Are we removing any keys?
		if( isset($_POST['removeKeys']) && isset($_POST['selectedKeys']) ) {
			$this->processKeyRemoval($model, $_POST['selectedKeys']);
		}
		
		// Maybe we are adding keys then?
		if( isset($_POST['addKeys']) && isset($_POST['SSHForm']) ) {
			$sshForm->attributes = $_POST['SSHForm'];
			$this->processKeyAddition($model, $sshForm);
		}
		
		$dataProvider = new CArrayDataProvider($model->getProcessedSshKeys());
		$this->render('editKeys', array(
			'model' => $model,
			'sshForm' => $sshForm,
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
		
		// Handle the unlocking of an account
		if( isset($_POST['unlockAccount']) ) {
			$model->removeAttribute("pwdAccountLockedTime");
			if( $model->save() ) {
				Yii::app()->user->setFlash('success', 'Account lock released');
			}
		}
		
		// Handle the clearing of the avatar
		if( isset($_POST['lockAccount']) ) {
			// Magic value of 000001010000Z means 'locked infinitely by an administrator'
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
	 * Process the addition of a SSH Key to a user
	 * To be used by actionEditKeys only
	 */
	protected function processKeyAddition($model, $sshForm)
	{
		// Make sure the SSH Form is valid....
		if( !$sshForm->validate() ) {
			return false;
		}
		// We will be adding a SSH Key now - so make sure we have the appropriate Object Class added
		if( !$model->hasObjectClass("ldapPublicKey") ) {
			$model->addAttribute("objectClass", "ldapPublicKey");
		}
		// We are assured the SSH Key is valid - and not a duplicate now, so we can add it - and clear the form
		$model->addAttribute("sshPublicKey", $sshForm->newKey);
		$sshForm->newKey = null;
		// Now try and save - if we succeed add a flash message so the user knows we succeeded
		if( $model->save() ) {
			Yii::app()->user->setFlash('success', 'SSH Keys updated');
			return true;
		}
		return false;
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
		// If we no longer have any SSH Keys, then remove the needed Object Class
		if( empty($model->sshPublicKey) ) {
			$model->removeAttribute("objectClass", "ldapPublicKey");
		}
		// Now try and save - if we succeed add a flash message so the user knows we succeeded
		if( $model->save() ) {
			Yii::app()->user->setFlash('success', 'SSH Keys updated');
			return true;
		}
		return false;
	}
};

?>