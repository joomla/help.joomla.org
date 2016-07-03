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

use Joomla\Cache\Adapter as CacheAdapter;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

/**
 * Cache service provider
 */
class CacheProvider implements ServiceProviderInterface
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
		$container->alias('cache', 'Psr\Cache\CacheItemPoolInterface')
			->alias('Joomla\Cache\CacheItemPoolInterface', 'Psr\Cache\CacheItemPoolInterface')
			->alias('Joomla\Cache\AbstractCacheItemPool', 'Psr\Cache\CacheItemPoolInterface')
			->share(
				'Psr\Cache\CacheItemPoolInterface',
				function (Container $container)
				{
					/** @var \Joomla\Registry\Registry $config */
					$config = $container->get('config');

					// If caching isn't enabled then just return a void cache
					if (!$config->get('cache.enabled', false))
					{
						return new CacheAdapter\None;
					}

					$adapter = $config->get('cache.adapter', 'file');

					switch ($adapter)
					{
						case 'file':
							$path = $config->get('cache.filesystem.path', 'cache');

							// If no path is given, fall back to the system's temporary directory
							if (empty($path))
							{
								$path = sys_get_temp_dir();
							}

							// If the path is relative, make it absolute... Sorry Windows users, this breaks support for your environment
							if (substr($path, 0, 1) !== '/')
							{
								$path = JPATH_ROOT . '/' . $path;
							}

							$options = [
								'file.path' => $path,
							];

							return new CacheAdapter\File($options);

						case 'none':
							return new CacheAdapter\None;

						case 'runtime':
							return new CacheAdapter\Runtime;
					}

					throw new \InvalidArgumentException(sprintf('The "%s" cache adapter is not supported.', $adapter));
				},
				true
			);
	}
}
