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

use Joomla\Application\AbstractWebApplication;
use Joomla\Application\Controller\ContainerControllerResolver;
use Joomla\Application\Controller\ControllerResolverInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\Help\Controller\HelpScreenController;
use Joomla\Help\Controller\LegacyController;
use Joomla\Help\Model\HelpScreenModel;
use Joomla\Help\View\HelpScreenHtmlView;
use Joomla\Help\WebApplication;
use Joomla\Http\Http;
use Joomla\Input\Input;
use Joomla\Registry\Registry;
use Joomla\Renderer\RendererInterface;
use Joomla\Router\Route;
use Joomla\Router\Router;

/**
 * Application service provider
 */
class ApplicationProvider implements ServiceProviderInterface
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
		$container->alias(AbstractWebApplication::class, WebApplication::class)
			->share(
				WebApplication::class,
				function (Container $container)
				{
					$application = new WebApplication(
						$container->get(ControllerResolverInterface::class),
						$container->get(Router::class),
						$container->get(Input::class),
						$container->get('config')
					);

					// Inject extra services
					$application->setDispatcher($container->get(DispatcherInterface::class));
					$application->setLogger($container->get('monolog.logger.application'));

					return $application;
				},
				true
			);

		$container->share(
			ControllerResolverInterface::class,
			function (Container $container) : ControllerResolverInterface
			{
				return new ContainerControllerResolver($container);
			}
		)
			->alias(
				ContainerControllerResolver::class,
				ControllerResolverInterface::class
			);

		$container->share(
			Input::class,
			function ()
			{
				return new Input($_REQUEST);
			},
			true
		);

		$container->share(
			Router::class,
			function ()
			{
				$router = new Router;

				$router->addRoute(
					new Route(['GET', 'HEAD'], '/', LegacyController::class)
				);

				$router->get(
					'/proxy',
					HelpScreenController::class
				);

				$router->get(
					'/proxy/index.php',
					HelpScreenController::class
				);

				$router->get(
					'/*',
					LegacyController::class
				);

				return $router;
			},
			true
		);

		$container->share(
			HelpScreenController::class,
			function (Container $container)
			{
				$controller = new HelpScreenController(
					$container->get(HelpScreenHtmlView::class),
					$container->get('cache')
				);

				$controller->setApplication($container->get(WebApplication::class));
				$controller->setInput($container->get(Input::class));

				return $controller;
			},
			true
		);

		$container->share(
			LegacyController::class,
			function (Container $container)
			{
				$controller = new LegacyController(
					$container->get(RendererInterface::class)
				);

				$controller->setApplication($container->get(WebApplication::class));
				$controller->setInput($container->get(Input::class));

				return $controller;
			},
			true
		);

		$container->share(
			HelpScreenModel::class,
			function (Container $container)
			{
				return new HelpScreenModel(
					new Registry,
					$container->get(Http::class)
				);
			},
			true
		);

		$container->share(
			HelpScreenHtmlView::class,
			function (Container $container)
			{
				return new HelpScreenHtmlView(
					$container->get(HelpScreenModel::class),
					$container->get(RendererInterface::class)
				);
			},
			true
		);
	}
}
