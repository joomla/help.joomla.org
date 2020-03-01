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

use DebugBar\DebugBar;
use Joomla\Application\AbstractWebApplication;
use Joomla\Application\ApplicationEvents;
use Joomla\Application\Event\ApplicationErrorEvent;
use Joomla\Application\Event\ApplicationEvent;
use Joomla\Event\Priority;
use Joomla\Event\SubscriberInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;

/**
 * Debug event subscriber
 */
class DebugSubscriber implements SubscriberInterface
{
	/**
	 * Application debug bar
	 *
	 * @var  DebugBar
	 */
	private $debugBar;

	/**
	 * Event subscriber constructor.
	 *
	 * @param   DebugBar  $debugBar  Application debug bar
	 */
	public function __construct(DebugBar $debugBar)
	{
		$this->debugBar = $debugBar;
	}

	/**
	 * Returns an array of events this subscriber will listen to.
	 *
	 * @return  array
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			ApplicationEvents::BEFORE_EXECUTE => ['markBeforeExecute', Priority::HIGH],
			ApplicationEvents::AFTER_EXECUTE  => ['markAfterExecute', Priority::LOW],
			ApplicationEvents::BEFORE_RESPOND => 'handleDebugResponse',
			ApplicationEvents::ERROR          => 'handleError',
		];
	}

	/**
	 * Handle the response for the debug bar element.
	 *
	 * @param   ApplicationEvent  $event  Event object
	 *
	 * @return  void
	 */
	public function handleDebugResponse(ApplicationEvent $event): void
	{
		/** @var AbstractWebApplication $application */
		$application = $event->getApplication();

		// Ensure responses are not cached
		$application->allowCache(false);

		if ($application->getResponse() instanceof HtmlResponse)
		{
			$debugBarOutput = $this->debugBar->getJavascriptRenderer()->render();

			// Fetch the body
			$body = $application->getBody();

			// If for whatever reason we're missing the closing body tag, just append the scripts
			if (!stristr($body, '</body>'))
			{
				$body .= $debugBarOutput;
			}
			else
			{
				// Find the closing tag and put the scripts in
				$pos = strripos($body, '</body>');

				if ($pos !== false)
				{
					$body = substr_replace($body, $debugBarOutput . '</body>', $pos, \strlen('</body>'));
				}
			}

			// Reset the body
			$application->setBody($body);
		}
		elseif ($application->getResponse() instanceof RedirectResponse)
		{
			$this->debugBar->stackData();
		}
		else
		{
			$this->debugBar->sendDataInHeaders();
		}
	}

	/**
	 * Handle application errors.
	 *
	 * @param   ApplicationErrorEvent  $event  Event object
	 *
	 * @return  void
	 */
	public function handleError(ApplicationErrorEvent $event): void
	{
		/** @var \DebugBar\DataCollector\ExceptionsCollector $collector */
		$collector = $this->debugBar['exceptions'];

		$collector->addThrowable($event->getError());

		/** @var \DebugBar\DataCollector\TimeDataCollector $collector */
		$collector = $this->debugBar['time'];

		foreach (['routing', 'controller', 'execution'] as $measure)
		{
			if ($collector->hasStartedMeasure($measure))
			{
				$collector->stopMeasure($measure);
			}
		}
	}

	/**
	 * Mark the timestamp after the application is executed.
	 *
	 * @param   ApplicationEvent  $event  Event object
	 *
	 * @return  void
	 */
	public function markAfterExecute(ApplicationEvent $event): void
	{
		/** @var \DebugBar\DataCollector\TimeDataCollector $collector */
		$collector = $this->debugBar['time'];

		$collector->stopMeasure('execution');
	}

	/**
	 * Mark the timestamp before the application is executed.
	 *
	 * @param   ApplicationEvent  $event  Event object
	 *
	 * @return  void
	 */
	public function markBeforeExecute(ApplicationEvent $event): void
	{
		/** @var \DebugBar\DataCollector\TimeDataCollector $collector */
		$collector = $this->debugBar['time'];

		$collector->startMeasure('execution');
	}
}