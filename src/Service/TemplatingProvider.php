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

use Joomla\Application\AbstractApplication;
use Joomla\DI\Container;
use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Help\Templating\JoomlaTemplateExtension;
use Joomla\Http\Http;
use Joomla\Renderer\PlatesRenderer;
use Joomla\Renderer\RendererInterface;
use League\Plates\Engine;
use League\Plates\Extension\Asset;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Templating service provider
 */
class TemplatingProvider implements ServiceProviderInterface, ContainerAwareInterface
{
	use ContainerAwareTrait;

	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 */
	public function register(Container $container)
	{
		$this->setContainer($container);

		$container->share(
			RendererInterface::class,
			function (Container $container)
			{
				$engine = new Engine(JPATH_ROOT . '/templates');
				$engine->addFolder('partials', JPATH_ROOT . '/templates/partials');

				// Add extensions to the renderer
				$engine->loadExtensions($container->getTagged('plates.extension'));

				// Add functions to the renderer
				$engine->registerFunction(
					'current_url',
					function () use ($container) : string
					{
						return $container->get(AbstractApplication::class)->get('uri.request');
					}
				);

				return new PlatesRenderer($engine);
			},
			true
		);

		$container->share(
			Asset::class,
			function () : Asset
			{
				return new Asset(JPATH_ROOT . '/www');
			},
			true
		)
			->tag('plates.extension', [Asset::class]);

		$container->share(
			JoomlaTemplateExtension::class,
			function (Container $container)
			{
				return new JoomlaTemplateExtension(
					$container->get(CacheItemPoolInterface::class),
					$container->get(Http::class),
					$container->get('config')->get('cache.lifetime')
				);
			},
			true
		)
			->tag('plates.extension', [JoomlaTemplateExtension::class]);
	}
}
