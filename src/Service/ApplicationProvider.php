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
use Joomla\Application\Web\WebClient;
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
use Joomla\Router\RouterInterface;
use Psr\Cache\CacheItemPoolInterface;

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
		$container->alias(WebApplication::class, AbstractWebApplication::class)
			->share(
				AbstractWebApplication::class,
				static function (Container $container): WebApplication
				{
					$application = new WebApplication(
						$container->get(ControllerResolverInterface::class),
						$container->get(Router::class),
						$container->get(Input::class),
						$container->get('config')
					);

					$application->httpVersion = '2';

					// Inject extra services
					$application->setDispatcher($container->get(DispatcherInterface::class));
					$application->setLogger($container->get('monolog.logger.application'));

					return $application;
				}
			);

		$container->share(
			ControllerResolverInterface::class,
			static function (Container $container): ControllerResolverInterface
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
			static function (): Input
			{
				return new Input($_REQUEST);
			}
		);

		$container->alias(Router::class, RouterInterface::class)
			->share(
				RouterInterface::class,
				static function (): RouterInterface
				{
					$router = new Router;

					$router->addRoute(
						new Route(['GET', 'HEAD'], '/', LegacyController::class)
					);

					$router->get(
						'/proxy',
						HelpScreenController::class,
						[],
						[
							'_proxy' => true,
						]
					);

					$router->get(
						'/proxy/index.php',
						HelpScreenController::class,
						[],
						[
							'_proxy' => true,
						]
					);

					$router->get(
						'/*',
						LegacyController::class
					);

					return $router;
				}
			);

		$container->share(
			WebClient::class,
			static function (Container $container): WebClient
			{
				/** @var Input $input */
				$input          = $container->get(Input::class);
				$userAgent      = $input->server->getString('HTTP_USER_AGENT', '');
				$acceptEncoding = $input->server->getString('HTTP_ACCEPT_ENCODING', '');
				$acceptLanguage = $input->server->getString('HTTP_ACCEPT_LANGUAGE', '');

				return new WebClient($userAgent, $acceptEncoding, $acceptLanguage);
			}
		);

		$container->share(
			HelpScreenController::class,
			static function (Container $container): HelpScreenController
			{
				$controller = new HelpScreenController(
					$container->get(HelpScreenHtmlView::class),
					$container->get(CacheItemPoolInterface::class)
				);

				$controller->setApplication($container->get(WebApplication::class));
				$controller->setInput($container->get(Input::class));

				return $controller;
			}
		);

		$container->share(
			LegacyController::class,
			static function (Container $container): LegacyController
			{
				$controller = new LegacyController(
					$container->get(RendererInterface::class)
				);

				$controller->setApplication($container->get(WebApplication::class));
				$controller->setInput($container->get(Input::class));

				return $controller;
			}
		);

		$container->share(
			HelpScreenModel::class,
			static function (Container $container): HelpScreenModel
			{
				return new HelpScreenModel(
					new Registry,
					$container->get(Http::class)
				);
			}
		);

		$container->share(
			HelpScreenHtmlView::class,
			static function (Container $container): HelpScreenHtmlView
			{
				return new HelpScreenHtmlView(
					$container->get(HelpScreenModel::class),
					$container->get(RendererInterface::class)
				);
			}
		);
	}
}
