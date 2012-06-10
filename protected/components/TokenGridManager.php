<?php

/**
 * TokenGridManager provides convenient access to a two-factor authentication TokenGrid for the current user
 */
final class TokenGridManager extends CApplicationComponent
{
	/**
	 * Name of the state field used to store the current token position on the grid
	 */
	const GRID_POSITION_NAME = 'tokenGridPosition';
	/**
	 * Number of rows in the generated grids
	 */
	public $gridRows = 10;
	/**
	 * Number of columns in the generated grids
	 */
	public $gridColumns = 10;
	/**
	 * Length of tokens in the generated grids
	 */
	public $tokenLength = 4;
	/**
	 * Salt used to securely generate and validate the grids
	 * This must be set to use the TokenGridManager
	 */
	public $gridSalt = null;

	/**
	 * TokenGrid instance used internally
	 */
	private $_tokenGrid = null;

	public function init()
	{
		// Create the TokenGrid instance used internally
		$this->_tokenGrid = new TokenGrid( $this->gridRows, $this->gridColumns, $this->tokenLength, $this->gridSalt );
	}

	/**
	 * Validates the token given
	 */
	public function validateToken( $token, $username = null )
	{
		// Make sure we have a token grid position to use first
		if( !Yii::app()->user->hasState(self::GRID_POSITION_NAME) ) {
			return false;
		}

		// Provide a username if needed
		if( is_null($username) ) {
			$username = Yii::app()->user->id;
		}
		// Validate the token
		$position = Yii::app()->user->getState(self::GRID_POSITION_NAME);
		return $this->_tokenGrid->IsTokenValid($position, $username, $token);
	}

	/**
	 * Provides a random position on the TokenGrid of a user
	 */
	public function getRandomGridPosition( $username = null )
	{
		// Provide a username if needed
		if( is_null($username) ) {
			$username = Yii::app()->user->id;
		}
		// Retrieve the random position
		$position = $this->_tokenGrid->GetRandomGridPosition($username);
		// Store it for later use (validation primarily)
		Yii::app()->user->setState(self::GRID_POSITION_NAME, $position);
		return $position;
	}

	/**
	 * Provides a HTML Table containing the grid values for the current user
	 * Retrieval of another users grid is explicitly prohibited for security reasons
	 */
	public function getXHTMLGrid()
	{
		$username = Yii::app()->user->id;
		return $this->_tokenGrid->GetXhtmlGrid($username);
	}
};