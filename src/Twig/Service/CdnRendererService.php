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

namespace Joomla\Help\Twig\Service;

use Joomla\Http\Http;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Service class rendering the `joomla.org` CDN template elements
 */
class CdnRendererService
{
	/**
	 * Cache pool
	 *
	 * @var  CacheItemPoolInterface
	 */
	private $cache;

	/**
	 * HTTP connector
	 *
	 * @var  Http
	 */
	private $http;

	/**
	 * Constructor.
	 *
	 * @param   CacheItemPoolInterface  $cache  Cache pool
	 * @param   Http                    $http   HTTP connector
	 */
	public function __construct(CacheItemPoolInterface $cache, Http $http)
	{
		$this->cache = $cache;
		$this->http  = $http;
	}

	/**
	 * Retrieve the template footer contents from the CDN.
	 *
	 * @return  string
	 */
	public function getCdnFooter() : string
	{
		$key = md5(__METHOD__);

		$remoteRequest = function () use ($key)
		{
			try
			{
				// Set a very short timeout to try and not bring the site down
				$response = $this->http->get('https://cdn.joomla.org/template/renderer.php?section=footer', [], 2);

				if ($response->getStatusCode() !== 200)
				{
					return 'Could not load template section.';
				}

				$body = (string) $response->getBody();

				// Remove the login link
				$body = str_replace("\t\t<li><a href=\"%loginroute%\">%logintext%</a></li>\n", '', $body);

				// Replace the placeholders
				$body = strtr(
					$body,
					[
						'%reportroute%' => 'https://github.com/joomla/joomla-websites/issues/new?title=[jhelp]%20&amp;body=Please%20describe%20the%20problem%20or%20your%20issue',
						'%currentyear%' => date('Y'),
					]
				);

				$item = $this->cache->getItem($key);
				$item->set($body);
				$item->expiresAfter(null);

				$this->cache->save($item);

				return $body;
			}
			catch (\RuntimeException $e)
			{
				return 'Could not load template section.';
			}
		};

		$item = $this->cache->getItem($key);

		// Make sure we got a hit on the item, otherwise we'll have to re-cache
		if ($item->isHit())
		{
			$body = $item->get();
		}
		else
		{
			$body = $remoteRequest();
		}

		return $body;
	}

	/**
	 * Retrieve the template mega menu from the CDN.
	 *
	 * @return  string
	 */
	public function getCdnMenu() : string
	{
		$key = md5(__METHOD__);

		$remoteRequest = function () use ($key)
		{
			try
			{
				// Set a very short timeout to try and not bring the site down
				$response = $this->http->get('https://cdn.joomla.org/template/renderer.php?section=menu', [], 2);

				if ($response->getStatusCode() !== 200)
				{
					return 'Could not load template section.';
				}

				$body = (string) $response->getBody();

				// Remove the search module
				$body = str_replace(
					"\t<div id=\"nav-search\" class=\"navbar-search pull-right\">\n\t\t<jdoc:include type=\"modules\" name=\"position-0\" style=\"none\" />\n\t</div>\n",
					'',
					$body
				);

				$item = $this->cache->getItem($key);
				$item->set($body);
				$item->expiresAfter(null);

				$this->cache->save($item);

				return $body;
			}
			catch (\RuntimeException $e)
			{
				return 'Could not load template section.';
			}
		};

		$item = $this->cache->getItem($key);

		// Make sure we got a hit on the item, otherwise we'll have to re-cache
		if ($item->isHit())
		{
			$body = $item->get();
		}
		else
		{
			$body = $remoteRequest();
		}

		return $body;
	}
}
