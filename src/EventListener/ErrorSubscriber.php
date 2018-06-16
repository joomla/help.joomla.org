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

namespace Joomla\Help\EventListener;

use Joomla\Application\ApplicationEvents;
use Joomla\Application\Event\ApplicationErrorEvent;
use Joomla\Event\SubscriberInterface;
use Joomla\Help\WebApplication;
use Joomla\Renderer\RendererInterface;
use Joomla\Router\Exception\MethodNotAllowedException;
use Joomla\Router\Exception\RouteNotFoundException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Zend\Diactoros\Response\HtmlResponse;

/**
 * Error handling event subscriber
 */
class ErrorSubscriber implements SubscriberInterface, LoggerAwareInterface
{
	use LoggerAwareTrait;

	/**
	 * Layout renderer
	 *
	 * @var  RendererInterface
	 */
	private $renderer;

	/**
	 * Event subscriber constructor.
	 *
	 * @param   RendererInterface  $renderer  Layout renderer
	 */
	public function __construct(RendererInterface $renderer)
	{
		$this->renderer = $renderer;
	}

	/**
	 * Returns an array of events this subscriber will listen to.
	 *
	 * @return  array
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			ApplicationEvents::ERROR => 'handleError',
		];
	}

	/**
	 * Handle application errors.
	 *
	 * @param   ApplicationErrorEvent  $event  Event object
	 *
	 * @return  void
	 */
	public function handleError(ApplicationErrorEvent $event)
	{
		/** @var WebApplication $app */
		$app = $event->getApplication();

		switch (true)
		{
			case ($event->getError() instanceof MethodNotAllowedException) :
				// Log the error for reference
				$this->logger->error(
					sprintf('Route `%s` not supported by method `%s`', $app->get('uri.route'), $app->input->getMethod()),
					['exception' => $event->getError()]
				);

				$this->prepareResponse($event);

				$app->setHeader('Allow', implode(', ', $event->getError()->getAllowedMethods()));

				break;

			case ($event->getError() instanceof RouteNotFoundException) :
				// Log the error for reference
				$this->logger->error(
					sprintf('Route `%s` not found', $app->get('uri.route')),
					['exception' => $event->getError()]
				);

				$this->prepareResponse($event);

				break;

			default:
				$this->logError($event->getError());

				$this->prepareResponse($event);

				break;
		}
	}

	/**
	 * Log the error.
	 *
	 * @param   \Throwable  $throwable  The error being processed
	 *
	 * @return  void
	 */
	private function logError(\Throwable $throwable)
	{
		$this->logger->error(
			sprintf('Uncaught Throwable of type %s caught.', get_class($throwable)),
			['exception' => $throwable]
		);
	}

	/**
	 * Prepare the response for the event
	 *
	 * @param   ApplicationErrorEvent  $event  Event object
	 *
	 * @return  void
	 */
	private function prepareResponse(ApplicationErrorEvent $event)
	{
		/** @var WebApplication $app */
		$app = $event->getApplication();

		$app->allowCache(false);

		$uri = $app->get('uri.route', '');

		$template = strpos($uri, 'proxy') === 0 ? 'helpscreen/exception.html' : 'exception.html';

		$response = new HtmlResponse(
			$this->renderer->render($template, ['exception' => $event->getError()])
		);

		switch ($event->getError()->getCode())
		{
			case 404 :
				$response = $response->withStatus(404);

				break;

			case 405 :
				$response = $response->withStatus(405);

				break;

			case 500 :
			default  :
				$response = $response->withStatus(500);

				break;
		}

		$app->setResponse($response);
	}
}
