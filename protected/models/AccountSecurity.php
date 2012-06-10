<?php

class AccountSecurity extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AccountSecurity the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'account_security';
	}

	public function retrieveAccount($username)
	{
		// First we see if an entry for the requested account exists
		$entry = $this->findByAttributes( array('uid' => $username) );
		if( !$entry instanceof CActiveRecord ) {
			$entry = new AccountSecurity();
			$entry->uid = $username;
		}
		// Now we check the date
		if( $entry->last_event <= (time() - 3600) ) {
			$entry->event_count = 0;
		}
		// All checks done now
		return $entry;
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('uid', 'required'),
			array('uid', 'length', 'max' => 64),
			array('event_count', 'numerical', 'integerOnly' => true),
		);
	}

	protected function beforeSave()
	{
		// Increase the event count
		$this->event_count = $this->event_count + 1;
		// Update the event time to have it occur now
		$this->last_event = time();
		// We are all ok here
		return true;
	}
}