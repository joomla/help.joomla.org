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
use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Renderer\RendererInterface;
use Joomla\Renderer\TwigRenderer;
use Twig\Cache\CacheInterface;
use Twig\Cache\FilesystemCache;
use Twig\Cache\NullCache;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;
use Twig\RuntimeLoader\ContainerRuntimeLoader;
use Twig\RuntimeLoader\RuntimeLoaderInterface;

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
			static function (Container $container): RendererInterface
			{
				return new TwigRenderer($container->get(Environment::class));
			},
			true
		);

		$container->alias(\Twig_CacheInterface::class, CacheInterface::class)
			->share(
				CacheInterface::class,
				static function (Container $container): CacheInterface
				{
					/** @var \Joomla\Registry\Registry $config */
					$config = $container->get('config');

					// Pull down the template config
					$cacheEnabled = $config->get('template.cache.enabled', false);
					$cachePath    = $config->get('template.cache.path', 'cache/twig');
					$debug        = $config->get('template.debug', false);

					if ($debug === false && $cacheEnabled !== false)
					{
						return new FilesystemCache(JPATH_ROOT . '/' . $cachePath);
					}

					return new NullCache;
				},
				true
			);

		$container->alias(\Twig_Environment::class, Environment::class)
			->share(
				Environment::class,
				static function (Container $container): Environment {
					/** @var \Joomla\Registry\Registry $config */
					$config = $container->get('config');

					$debug = $config->get('template.debug', false);

					$environment = new Environment(
						$container->get(LoaderInterface::class),
						['debug' => $debug]
					);

					// Add the runtime loader
					$environment->addRuntimeLoader($container->get(RuntimeLoaderInterface::class));

					// Set up the environment's caching service
					$environment->setCache($container->get(CacheInterface::class));

					// Add the Twig extensions
					$environment->setExtensions($container->getTagged('twig.extension'));

					// Add globals tracking the debug state
					$environment->addGlobal('system_debug', $config->get('system.debug', false));
					$environment->addGlobal('template_debug', $debug);

					return $environment;
				},
				true
			);

		$container->alias(\Twig_LoaderInterface::class, LoaderInterface::class)
			->share(
				LoaderInterface::class,
				static function (Container $container): LoaderInterface
				{
					return new FilesystemLoader([JPATH_ROOT . '/templates']);
				},
				true
			);

		$container->alias(\Twig_RuntimeLoaderInterface::class, RuntimeLoaderInterface::class)
			->alias(\Twig_ContainerRuntimeLoader::class, RuntimeLoaderInterface::class)
			->alias(\ContainerRuntimeLoader::class, RuntimeLoaderInterface::class)
			->share(
				RuntimeLoaderInterface::class,
				static function (Container $container): RuntimeLoaderInterface
				{
					return new ContainerRuntimeLoader($container);
				},
				true
			);
	}
}
