<?php
/**
 * Joomla! Help Site
 *
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 *
 * Portions of this code are derived from the previous help screen proxy component,
 * please see https://github.com/joomla-projects/help-proxy for attribution
 */

namespace Joomla\Help\Service;

use Joomla\Application as JoomlaApplication;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Help\Controller\HelpScreenController;
use Joomla\Help\Controller\LegacyController;
use Joomla\Help\Model\HelpScreenModel;
use Joomla\Help\Router;
use Joomla\Help\View\HelpScreenHtmlView;
use Joomla\Help\WebApplication;
use Joomla\Http\HttpFactory;
use Joomla\Input\Input;
use Joomla\Registry\Registry;

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
		$container->alias(JoomlaApplication\AbstractWebApplication::class, WebApplication::class)
			->share(
				WebApplication::class,
				function (Container $container)
				{
					$application = new WebApplication($container->get(Input::class), $container->get('config'));

					// Inject extra services
					$application->setContainer($container);
					$application->setLogger($container->get('monolog.logger.application'));
					$application->setRouter($container->get(Router::class));

					return $application;
				},
				true
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
			function (Container $container)
			{
				$router = (new Router($container->get(Input::class)))
					->setContainer($container)
					->setControllerPrefix('Joomla\\Help\\Controller\\')
					->setDefaultController('LegacyController')
					->addMap('/proxy', 'HelpScreenController')
					->addMap('/proxy/index.php', 'HelpScreenController')
					->addMap('/*', 'LegacyController');

				return $router;
			},
			true
		);

		$container->share(
			HelpScreenController::class,
			function (Container $container)
			{
				$controller = new HelpScreenController(
					$container->get('Joomla\Help\View\HelpScreenHtmlView'),
					$container->get('cache')
				);

				$controller->setApplication($container->get('app'));
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
					$container->get('renderer')
				);

				$controller->setApplication($container->get('app'));
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
					(new HttpFactory)->getHttp()
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
					$container->get('renderer')
				);
			},
			true
		);
	}
}
