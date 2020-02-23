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

use Joomla\Console\Application;
use Joomla\Console\Loader\ContainerLoader;
use Joomla\Console\Loader\LoaderInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\Help\Command\ClearCacheCommand;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Console service provider
 */
class ConsoleProvider implements ServiceProviderInterface
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
		$container->share(
			Application::class,
			static function (Container $container): Application
			{
				$application = new Application(new ArgvInput, new ConsoleOutput, $container->get('config'));

				$application->setCommandLoader($container->get(LoaderInterface::class));
				$application->setDispatcher($container->get(DispatcherInterface::class));
				$application->setLogger($container->get(LoggerInterface::class));
				$application->setName('Joomla! Help Website');

				return $application;
			},
			true
		);

		$container->alias(ContainerLoader::class, LoaderInterface::class)
			->share(
				LoaderInterface::class,
				static function (Container $container): LoaderInterface
				{
					$mapping = [
						ClearCacheCommand::getDefaultName() => ClearCacheCommand::class,
					];

					return new ContainerLoader($container, $mapping);
				},
				true
			);

		$container->share(
			ClearCacheCommand::class,
			static function (Container $container): ClearCacheCommand
			{
				return new ClearCacheCommand($container->get(CacheItemPoolInterface::class));
			},
			true
		);
	}
}
