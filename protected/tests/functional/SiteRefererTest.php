<?php

/**
 * Unit test for the SiteReferer handling
 *
 */
class SiteRefererTest extends CDbTestCase
{
	public function testRefererSuccess()
	{
		//set test value
		$_SERVER['HTTP_REFERER'] = 'http://forum.kde.org';

		$helper = new SiteReferer();
		$helper->checkReferer();

		$this->assertEquals(SiteReferer::getReferer(), 'http://forum.kde.org', 'Referer handling failed');
	}

	public function testRefererWhiteList()
	{
		//check value not on the whitelist
		$_SERVER['HTTP_REFERER'] = 'http://forum.example.com';

		$helper = new SiteReferer();
		$helper->checkReferer();

		$this->assertNull(SiteReferer::getReferer(), 'Referer white list failure');
	}

}

?>
