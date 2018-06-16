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
use Joomla\Http\Http;
use Joomla\Http\HttpFactory;

/**
 * HTTP service provider
 */
class HttpProvider implements ServiceProviderInterface
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
			Http::class,
			function (Container $container) : Http
			{
				/** @var HttpFactory $factory */
				$factory = $container->get(HttpFactory::class);

				return $factory->getHttp();
			},
			true
		);

		$container->share(
			HttpFactory::class,
			function (Container $container) : HttpFactory
			{
				return new HttpFactory;
			},
			true
		);
	}
}
