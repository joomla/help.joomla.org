<?php
/**
 * Joomla! Help Site
 *
 * @copyright  Copyright (C) 2016 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 *
 * Portions of this code are derived from the previous help screen proxy component,
 * please see https://github.com/joomla-projects/help-proxy for attribution
 */

defined('JPATH_ROOT') or define('JPATH_ROOT', dirname(__DIR__));

$composerPath = JPATH_ROOT . '/vendor/autoload.php';

if (!file_exists($composerPath))
{
	header('HTTP/1.1 500 Internal Server Error', null, 500);
	header('Content-Type: text/html; charset=utf-8');
	echo 'ERROR: This installation is not properly set up!';

	exit(1);
}

require $composerPath;

use Joomla\DI\Container;
use Joomla\Help\Service as Services;

// Wrap in a try/catch so we can display an error if need be
try
{
	$container = (new Container)
		->registerServiceProvider(new Services\ApplicationProvider)
		->registerServiceProvider(new Services\CacheProvider)
		->registerServiceProvider(new Services\ConfigProvider(JPATH_ROOT . '/conf/config.json'))
		->registerServiceProvider(new Services\EventProvider)
		->registerServiceProvider(new Services\HttpProvider)
		->registerServiceProvider(new Services\LoggingProvider)
		->registerServiceProvider(new Services\TemplatingProvider);

	// Alias the web application to Joomla's base application class and the `app` shortcut as this is the primary application for the environment
	$container->alias('Joomla\Application\AbstractApplication', 'Joomla\Help\WebApplication')
		->alias('app', 'Joomla\Help\WebApplication');

	// Alias the `monolog.logger.application` service to the Monolog Logger class and PSR-3 interface as this is the primary logger for the environment
	$container->alias('Monolog\Logger', 'monolog.logger.application')
		->alias('Psr\Log\LoggerInterface', 'monolog.logger.application');

	// Set error reporting based on config
	$errorReporting = (int) $container->get('config')->get('system.error_reporting', 0);
	error_reporting($errorReporting);
	ini_set('display_errors', (bool) $errorReporting);
}
catch (\Throwable $e)
{
	if (isset($container))
	{
		// Try to write to a log
		try
		{
			$container->get('monolog.logger.application')->critical(
				sprintf(
					'Exception of type %1$s thrown while booting the application',
					get_class($e)
				),
				['exception' => $e]
			);
		}
		catch (\Throwable $nestedException)
		{
			// Do nothing, we tried our best
		}
	}
	else
	{
		// The container wasn't built yet, log to the PHP error log so we at least have something
		error_log($e);
	}

	if (!headers_sent())
	{
		header('HTTP/1.1 500 Internal Server Error', null, 500);
		header('Content-Type: text/html; charset=utf-8');
	}

	echo 'An error occurred while booting the application: ' . $e->getMessage();

	exit(1);
}

// Execute the application
try
{
	$container->get('app')->execute();
}
catch (\Throwable $e)
{
	// Try to write to a log
	try
	{
		$container->get('monolog.logger.application')->critical(
			sprintf(
				'Exception of type %1$s thrown while executing the application',
				get_class($e)
			),
			['exception' => $e]
		);
	}
	catch (\Throwable $nestedException)
	{
		// Do nothing, we tried our best
	}

	if (!headers_sent())
	{
		header('HTTP/1.1 500 Internal Server Error', null, 500);
		header('Content-Type: text/html; charset=utf-8');
	}

	echo 'An error occurred while executing the application: ' . $e->getMessage();

	exit(1);
}
