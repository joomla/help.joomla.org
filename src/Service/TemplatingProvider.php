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
use Joomla\Help\Twig\ApplicationExtension;
use Joomla\Help\Twig\AssetExtension;
use Joomla\Help\Twig\CdnExtension;
use Joomla\Help\Twig\Service\AssetService;
use Joomla\Help\Twig\Service\CdnRendererService;
use Joomla\Help\Twig\Service\RoutingService;
use Joomla\Help\WebApplication;
use Joomla\Http\Http;
use Joomla\Preload\PreloadManager;
use Joomla\Renderer\RendererInterface;
use Joomla\Renderer\TwigRenderer;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Asset\Packages;
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

		$container->share(
			ApplicationExtension::class,
			static function (Container $container): ApplicationExtension
			{
				return new ApplicationExtension;
			},
			true
		);

		$container->share(
			AssetExtension::class,
			static function (Container $container): AssetExtension
			{
				return new AssetExtension;
			},
			true
		);

		$container->share(
			CdnExtension::class,
			static function (Container $container): CdnExtension
			{
				return new CdnExtension;
			},
			true
		);

		$container->share(
			AssetService::class,
			static function (Container $container): AssetService
			{
				return new AssetService(
					$container->get(Packages::class),
					$container->get(PreloadManager::class),
                    JPATH_ROOT . '/www/media/sri-manifest.json'
				);
			},
			true
		);

		$container->share(
			CdnRendererService::class,
			static function (Container $container): CdnRendererService
			{
				return new CdnRendererService(
					$container->get(CacheItemPoolInterface::class),
					$container->get(Http::class)
				);
			},
			true
		);

		$container->share(
			RoutingService::class,
			static function (Container $container): RoutingService
			{
				return new RoutingService(
					$container->get(WebApplication::class)
				);
			},
			true
		);

		$this->tagTwigExtensions($container);
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
		/** @var \Joomla\Registry\Registry $config */
		$config = $container->get('config');

		$debug = $config->get('template.debug', false);

		$twigExtensions = [
			ApplicationExtension::class,
			AssetExtension::class,
			CdnExtension::class,
		];

		$container->tag('twig.extension', $twigExtensions);
	}
}
