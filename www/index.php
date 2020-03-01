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

\define('APP_START', microtime(true));
\define('JPATH_ROOT', \dirname(__DIR__));

if (!file_exists(JPATH_ROOT . '/vendor/autoload.php'))
{
	header('HTTP/1.1 500 Internal Server Error', null, 500);
	header('Content-Type: text/html; charset=utf-8');
	echo 'ERROR: This installation is not properly set up!';

	exit(1);
}

require JPATH_ROOT . '/vendor/autoload.php';

use DebugBar\DebugBar;
use DebugBar\HttpDriverInterface;
use Joomla\Application\AbstractApplication;
use Joomla\Application\AbstractWebApplication;
use Joomla\DI\Container;
use Joomla\Help\Service as Services;
use Joomla\Preload\Service\PreloadProvider;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

// Wrap in a try/catch so we can display an error if need be
try
{
	$container = (new Container)
		->registerServiceProvider(new Services\ApplicationProvider)
		->registerServiceProvider(new Services\AssetProvider)
		->registerServiceProvider(new Services\CacheProvider)
		->registerServiceProvider(new Services\ConfigProvider(JPATH_ROOT . '/etc/config.json'))
		->registerServiceProvider(new Services\ConsoleProvider)
		->registerServiceProvider(new Services\EventProvider)
		->registerServiceProvider(new Services\HttpProvider)
		->registerServiceProvider(new Services\LoggingProvider)
		->registerServiceProvider(new Services\TemplatingProvider)
		->registerServiceProvider(new PreloadProvider)
	;

	// Conditionally include the DebugBar service provider based on the app being in debug mode
	if ((bool) $container->get('config')->get('system.debug', false))
	{
		$container->registerServiceProvider(new Services\DebugBarProvider);
	}

	// Alias the web application to Joomla's base application class and the `app` shortcut as this is the primary application for the environment
	$container->alias(AbstractApplication::class, AbstractWebApplication::class);

	// Alias the `monolog.logger.application` service to the Monolog Logger class and PSR-3 interface as this is the primary logger for the environment
	$container->alias(Logger::class, 'monolog.logger.application')
		->alias(LoggerInterface::class, 'monolog.logger.application');

	// Set error reporting based on config
	$errorReporting = (int) $container->get('config')->get('system.error_reporting', 0);
	error_reporting($errorReporting);
}
catch (\Throwable $e)
{
	if (isset($container))
	{
		// Try to write to a log
		try
		{
			$container->get(LoggerInterface::class)->critical(
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

if ($container->has(DebugBar::class))
{
	try
	{
		// There is a circular dependency in building the HTTP driver while the application is being resolved, so it'll need to be set here for now
		/** @var DebugBar $debugBar */
		$debugBar = $container->get(DebugBar::class);
		$debugBar->setHttpDriver($container->get(HttpDriverInterface::class));

		/** @var \DebugBar\DataCollector\TimeDataCollector $collector */
		$collector = $debugBar['time'];
		$collector->addMeasure('initialisation', APP_START, microtime(true));
	}
	catch (\Throwable $e)
	{
		// Try to write to a log
		try
		{
			$container->get(LoggerInterface::class)->critical(
				sprintf(
					'Exception of type %1$s thrown while configuring the debug bar',
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

		echo 'An error occurred while configuring the debug bar: ' . $e->getMessage();

		exit(1);
	}
}

// Execute the application
try
{
	$container->get(AbstractApplication::class)->execute();
}
catch (\Throwable $e)
{
	// Try to write to a log
	try
	{
		$container->get(LoggerInterface::class)->critical(
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
