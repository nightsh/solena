<?php

class DeveloperApplicationCommand extends CConsoleCommand
{
	public function actionApprove($username)
	{
		// Find the user given, and make sure they have a application to actually approve....
		$model = DeveloperApplication::model()->findByAttributes( array('uid' => $username, 'status' => DeveloperApplication::StatusPending) );
		if( !$model instanceof DeveloperApplication ) {
			throw new CException('The username provided does not have a pending developer application.');
		}

		// Now approve their application (this will load the user ssh key, update group memberships and mark it as approved)
		$model->approveApplication();
	}

	public function actionReject($username)
	{
		// Find the user given, and make sure they have a application to actually reject....
		$model = DeveloperApplication::model()->findByAttributes( array('uid' => $username, 'status' => DeveloperApplication::StatusPending) );
		if( !$model instanceof DeveloperApplication ) {
			throw new CException('The username provided does not have a pending developer application.');
		}

		// Mark the application as rejected
		$model->rejectApplication();
	}
};

?>