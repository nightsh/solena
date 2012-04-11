<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
$consoleConfig = array(
	'basePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
);

// We merge in the main configuration file to reduce duplication
$mainConfig = require( dirname(__FILE__) . '/main.php' );
return array_merge($mainConfig, $consoleConfig);