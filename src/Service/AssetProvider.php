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
use Joomla\DI\ServiceProviderInterface;
use Joomla\Help\Asset\Context\ApplicationContext;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

/**
 * Asset service provider
 */
class AssetProvider implements ServiceProviderInterface
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
			Packages::class,
			static function (Container $container): Packages
			{
				/** @var AbstractApplication $app */
				$app = $container->get(AbstractApplication::class);

				$context = new ApplicationContext($app);

				$mediaPath = $app->get('uri.media.path', '/media/');

				$defaultPackage = new PathPackage($mediaPath, new EmptyVersionStrategy, $context);

				return new Packages($defaultPackage);
			},
			true
		);
	}
}
