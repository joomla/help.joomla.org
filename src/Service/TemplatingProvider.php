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

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Renderer\PlatesRenderer;
use Joomla\Renderer\RendererInterface;

/**
 * Templating service provider
 */
class TemplatingProvider implements ServiceProviderInterface
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
		$container->alias('renderer', RendererInterface::class)
			->share(
				RendererInterface::class,
				function (Container $container) {
					$renderer = new PlatesRenderer(['path' => JPATH_ROOT . '/templates', 'extension' => '.php']);
					$renderer->addFolder(JPATH_ROOT . '/templates/partials', 'partials');

					// Add functions to the renderer
					$engine = $renderer->getRenderer();

					$engine->registerFunction(
						'media',
						function ($asset) use ($container)
						{
							return $container->get('app')->get('uri.media.full') . $asset;
						}
					);

					$engine->registerFunction(
						'current_url',
						function () use ($container)
						{
							return $container->get('app')->get('uri.request');
						}
					);

					return $renderer;
				},
				true
			);
	}
}
