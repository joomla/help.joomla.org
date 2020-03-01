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

namespace Joomla\Help\Service;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Monolog\Processor\WebProcessor;

/**
 * Logging service provider
 */
class LoggingProvider implements ServiceProviderInterface
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 */
	public function register(Container $container)
	{
		// Register the PSR-3 processor
		$container->share(
			'monolog.processor.psr3',
			static function (): PsrLogMessageProcessor
			{
				return new PsrLogMessageProcessor;
			}
		);

		// Register the web processor
		$container->share(
			'monolog.processor.web',
			static function (): WebProcessor
			{
				return new WebProcessor;
			}
		);

		// Register the web application handler
		$container->share(
			'monolog.handler.application',
			static function (Container $container): StreamHandler
			{
				/** @var \Joomla\Registry\Registry $config */
				$config = $container->get('config');

				$level = strtoupper($config->get('log.application', $config->get('log.level', 'error')));

				return new StreamHandler(
					JPATH_ROOT . '/logs/app.log',
					constant('\\Monolog\\Logger::' . $level)
				);
			}
		);

		// Register the web application Logger
		$container->share(
			'monolog.logger.application',
			static function (Container $container): Logger
			{
				return new Logger(
					'Help',
					[
						$container->get('monolog.handler.application'),
					],
					[
						$container->get('monolog.processor.psr3'),
						$container->get('monolog.processor.web'),
					]
				);
			}
		);

		// Register the CLI application Logger
		$container->share(
			'monolog.logger.cli',
			static function (Container $container): Logger
			{
				return new Logger(
					'Help',
					[
						$container->get('monolog.handler.application')
					],
					[
						$container->get('monolog.processor.psr3')
					]
				);
			}
		);
	}
}
