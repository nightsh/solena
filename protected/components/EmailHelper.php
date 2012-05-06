<?php

class EmailHelper
{
	static public function showVerification( $data )
	{
		// If the address is pending, show a button to re-send the verification mail
		if( $data['type'] == 'pending' ) {
			$params = array('action' => 'resend', 'Token[mail]' => $data['mail']);
			return CHtml::submitButton('Resend Verification', array('submit' => '', 'class' => 'btn', 'params' => $params));
		} else if( $data['type'] == 'secondary' ) { // If it is a secondary address, then let them make it the primary
			$params = array('action' => 'primary', 'Token[mail]' => $data['mail']);
			return CHtml::submitButton('Set as Primary', array("submit" => '', 'class' => 'btn', 'params' => $params));
		}
		return 'Primary Address';
	}

	static public function showDelete( $data )
	{
		if( $data['type'] != 'primary' ) {
			$params = array("action" => "remove", "Token[mail]" => $data["mail"]);
			return CHtml::submitButton("Remove Address", array("submit" => "", "class" => "btn", "params" => $params));
		}
		return '';
	}
};