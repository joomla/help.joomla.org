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

namespace Joomla\Help\Controller;

use Joomla\Cache\Item\Item;
use Joomla\Controller\AbstractController;
use Joomla\Help\View\HelpScreenHtmlView;
use Joomla\Uri\Uri;
use Psr\Cache\CacheItemPoolInterface;
use Zend\Diactoros\Response\HtmlResponse;

/**
 * Controller to render Joomla! help screens
 *
 * @method         \Joomla\Application\WebApplication  getApplication()  Get the application object.
 * @property-read  \Joomla\Application\WebApplication  $app              Application object
 */
class HelpScreenController extends AbstractController
{
	/**
	 * The cache item pool.
	 *
	 * @var  CacheItemPoolInterface
	 */
	private $cache;

	/**
	 * The view to render.
	 *
	 * @var  HelpScreenHtmlView
	 */
	private $view;

	/**
	 * Constructor.
	 *
	 * @param   HelpScreenHtmlView      $view   The view to render.
	 * @param   CacheItemPoolInterface  $cache  The cache item pool.
	 */
	public function __construct(HelpScreenHtmlView $view, CacheItemPoolInterface $cache)
	{
		$this->cache = $cache;
		$this->view  = $view;
	}

	/**
	 * Execute the controller.
	 *
	 * @return  boolean
	 */
	public function execute() : bool
	{
		// Enable browser caching
		$this->getApplication()->allowCache(true);

		// Set the layout for the view
		$this->view->setLayout('helpscreen/live.html');

		// Store data into the model
		$state = $this->view->getModel()->getState();

		$state->set('page', $this->getInput()->getString('keyref', 'Main_Page'));
		$state->set('lang', $this->getInput()->getString('lang', 'en'));
		$state->set('max_redirects', $this->getApplication()->get('help.wiki_max_redirects', 5));

		$this->view->getModel()->setCurrentUri(new Uri($this->getApplication()->get('uri.request')));
		$this->view->getModel()->setWikiUrl($this->getApplication()->get('help.wiki', 'https://docs.joomla.org'));

		// Serve cached data if the cache layer is enabled
		if ($this->getApplication()->get('cache.enabled', false))
		{
			$key = md5(get_class($this->view) . __METHOD__ . serialize($state));

			if ($this->cache->hasItem($key))
			{
				$item = $this->cache->getItem($key);

				// Make sure we got a hit on the item, otherwise we'll have to re-cache
				if ($item->isHit())
				{
					$body = $item->get();
				}
				else
				{
					$body = $this->view->render();

					$item = (new Item($key, $this->getApplication()->get('cache.lifetime', 900)))
						->set($body);

					$this->cache->save($item);
				}
			}
			else
			{
				$body = $this->view->render();

				$item = (new Item($key, $this->getApplication()->get('cache.lifetime', 900)))
					->set($body);

				$this->cache->save($item);
			}
		}
		else
		{
			$body = $this->view->render();
		}

		$this->getApplication()->setResponse(new HtmlResponse($body));

		return true;
	}
}
