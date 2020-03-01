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

use DebugBar\Bridge\MonologCollector;
use DebugBar\Bridge\Twig\TimeableTwigExtensionProfiler;
use DebugBar\Bridge\TwigProfileCollector;
use DebugBar\DebugBar;
use DebugBar\HttpDriverInterface;
use DebugBar\StandardDebugBar;
use Joomla\Application\AbstractWebApplication;
use Joomla\Application\Controller\ControllerResolverInterface;
use Joomla\Application\Web\WebClient;
use Joomla\DI\Container;
use Joomla\DI\Exception\DependencyResolutionException;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\Help\Cache\Adapter\DebugAdapter;
use Joomla\Help\Controller\DebugControllerResolver;
use Joomla\Help\DebugBar\JoomlaHttpDriver;
use Joomla\Help\DebugWebApplication;
use Joomla\Help\Event\DebugDispatcher;
use Joomla\Help\EventListener\DebugSubscriber;
use Joomla\Help\Http\HttpFactory;
use Joomla\Help\Router\DebugRouter;
use Joomla\Http\HttpFactory as BaseHttpFactory;
use Joomla\Input\Input;
use Joomla\Router\RouterInterface;
use Psr\Cache\CacheItemPoolInterface;
use Twig\Loader\LoaderInterface;
use Twig\Profiler\Profile;

/**
 * Debug bar service provider
 */
class DebugBarProvider implements ServiceProviderInterface
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 */
	public function register(Container $container): void
	{
		$container->alias(StandardDebugBar::class, DebugBar::class)
			->share(
				DebugBar::class,
				static function (Container $container): DebugBar
				{
					if (!class_exists(StandardDebugBar::class))
					{
						throw new DependencyResolutionException(sprintf('The %s class is not loaded.', StandardDebugBar::class));
					}

					$debugBar = new StandardDebugBar;

					// Add collectors
					foreach ($container->getTagged('debug.collector') as $collector)
					{
						$debugBar->addCollector($collector);
					}

					// Ensure the assets are dumped
					$renderer = $debugBar->getJavascriptRenderer();
					$renderer->dumpCssAssets(JPATH_ROOT . '/www/media/css/debugbar.css');
					$renderer->dumpJsAssets(JPATH_ROOT . '/www/media/js/debugbar.js');

					return $debugBar;
				}
			);

		$container->share(
			MonologCollector::class,
			static function (Container $container): MonologCollector
			{
				$collector = new MonologCollector;
				$collector->addLogger($container->get('monolog.logger.application'));

				return $collector;
			}
		);

		$container->share(
			TwigProfileCollector::class,
			static function (Container $container): TwigProfileCollector
			{
				return new TwigProfileCollector($container->get(Profile::class), $container->get(LoaderInterface::class));
			}
		);

		$container->share(
			Profile::class,
			static function (): Profile
			{
				return new Profile;
			}
		);

		$container->share(
			TimeableTwigExtensionProfiler::class,
			static function (Container $container): TimeableTwigExtensionProfiler
			{
				return new TimeableTwigExtensionProfiler($container->get(Profile::class), $container->get(DebugBar::class)['time']);
			}
		);

		$container->alias(JoomlaHttpDriver::class, HttpDriverInterface::class)
			->share(
				HttpDriverInterface::class,
				static function (Container $container): HttpDriverInterface
				{
					return new JoomlaHttpDriver($container->get(AbstractWebApplication::class));
				}
			);

		$container->share(
			DebugSubscriber::class,
			static function (Container $container): DebugSubscriber
			{
				return new DebugSubscriber($container->get(DebugBar::class));
			}
		);

		$container->extend(
			CacheItemPoolInterface::class,
			static function (CacheItemPoolInterface $cache, Container $container): CacheItemPoolInterface
			{
				return new DebugAdapter($container->get(DebugBar::class), $cache);
			}
		);

		$container->extend(
			ControllerResolverInterface::class,
			static function (ControllerResolverInterface $resolver, Container $container): ControllerResolverInterface
			{
				return new DebugControllerResolver($resolver, $container->get(DebugBar::class));
			}
		);

		$container->extend(
			DispatcherInterface::class,
			static function (DispatcherInterface $dispatcher, Container $container): DispatcherInterface
			{
				$dispatcher = new DebugDispatcher($dispatcher, $container->get(DebugBar::class));
				$dispatcher->addSubscriber($container->get(DebugSubscriber::class));

				return $dispatcher;
			}
		);

		$container->extend(
			BaseHttpFactory::class,
			static function (BaseHttpFactory $httpFactory, Container $container): HttpFactory
			{
				return new HttpFactory($container->get(DebugBar::class));
			}
		);

		$container->extend(
			AbstractWebApplication::class,
			static function (AbstractWebApplication $application, Container $container): DebugWebApplication
			{
				$application = new DebugWebApplication(
					$container->get(DebugBar::class),
					$container->get(ControllerResolverInterface::class),
					$container->get(RouterInterface::class),
					$container->get(Input::class),
					$container->get('config'),
					$container->get(WebClient::class)
				);

				$application->httpVersion = '2';

				// Inject extra services
				$application->setDispatcher($container->get(DispatcherInterface::class));
				$application->setLogger($container->get('monolog.logger.application'));

				return $application;
			}
		);

		$container->extend(
			RouterInterface::class,
			static function (RouterInterface $router, Container $container): RouterInterface
			{
				return new DebugRouter($router, $container->get(DebugBar::class));
			}
		);

		$this->tagDebugCollectors($container);
		$this->tagTwigExtensions($container);
	}

	/**
	 * Tag services which are collectors for the debug bar
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 */
	private function tagDebugCollectors(Container $container): void
	{
		$container->tag(
			'debug.collector',
			[
				MonologCollector::class,
				TwigProfileCollector::class,
			]
		);
	}

	/**
	 * Tag services which are Twig extensions
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 */
	private function tagTwigExtensions(Container $container): void
	{
		$container->tag('twig.extension', [TimeableTwigExtensionProfiler::class]);
	}
}
