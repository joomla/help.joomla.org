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
use Joomla\Event\Dispatcher;
use Joomla\Event\DispatcherInterface;
use Joomla\Help\EventListener\ErrorSubscriber;
use Joomla\Renderer\RendererInterface;
use Psr\Log\LoggerInterface;

/**
 * Event service provider
 */
class EventProvider implements ServiceProviderInterface
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
		$container->alias(Dispatcher::class, DispatcherInterface::class)
			->share(
				DispatcherInterface::class,
				static function (Container $container): DispatcherInterface
				{
					$dispatcher = new Dispatcher;

					foreach ($container->getTagged('event.subscriber') as $subscriber)
					{
						$dispatcher->addSubscriber($subscriber);
					}

					return $dispatcher;
				}
			);

		$container->share(
			ErrorSubscriber::class,
			static function (Container $container): ErrorSubscriber
			{
				$subscriber = new ErrorSubscriber($container->get(RendererInterface::class));
				$subscriber->setLogger($container->get(LoggerInterface::class));

				return $subscriber;
			}
		)
			->tag('event.subscriber', [ErrorSubscriber::class]);
	}
}
