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

namespace Joomla\Help\Model;

use Joomla\Http\Http;
use Joomla\Model\AbstractModel;
use Joomla\Registry\Registry;
use Joomla\String\Normalise;
use Joomla\Uri\Uri;
use Psr\Http\Message\ResponseInterface;

/**
 * Model to process Joomla! help screens
 */
class HelpScreenModel extends AbstractModel
{
	/**
	 * The HTTP connector.
	 *
	 * @var  Http
	 */
	private $connector;

	/**
	 * Uri object for the current request
	 *
	 * @var  Uri
	 */
	private $currentUri;

	/**
	 * Language of the returned page.
	 *
	 * @var  string
	 */
	private $language;

	/**
	 * The last Response object from the API call
	 *
	 * @var  ResponseInterface
	 */
	private $lastResponse;

	/**
	 * Current page for rendering.
	 *
	 * @var  string
	 */
	private $page;

	/**
	 * URL slug for the rendered page.
	 *
	 * @var  string
	 */
	private $pageUrlSlug;

	/**
	 * The decoded response body
	 *
	 * @var  object
	 */
	private $responseBody;

	/**
	 * Title of the returned page.
	 *
	 * @var  string
	 */
	private $title;

	/**
	 * Uri object for the wiki's API
	 *
	 * @var  Uri
	 */
	private $uriApi;

	/**
	 * Uri object for the wiki's base URL
	 *
	 * @var  Uri
	 */
	private $uriWiki;

	/**
	 * The base wiki URL.
	 *
	 * @var  string
	 */
	private $wikiUrl;

	/**
	 * Constructor.
	 *
	 * @param   Registry  $state  The model state.
	 * @param   Http      $http   The HTTP connector.
	 */
	public function __construct(Registry $state, Http $http)
	{
		parent::__construct($state);

		$this->connector = $http;
	}

	/**
	 * Get a rendered page from the remote wiki.
	 *
	 * @return  string
	 *
	 * @throws  \RuntimeException
	 */
	public function getPage() : string
	{
		$this->requestPage($this->state->get('page'), $this->state->get('lang'));

		// If a language coded page was not found, try to fall back to English.
		if (isset($this->responseBody['error']) && $this->responseBody['error']['code'] === 'missingtitle')
		{
			if ($this->state->get('lang') !== null)
			{
				$this->requestPage($this->state->get('page'));
			}

			// Maybe the language was part of the keyref?
			$langPos = strpos($this->state->get('page'), '/');

			if ($langPos !== false)
			{
				$this->state->set('page', substr($this->state->get('page'), 0, $langPos));

				$this->requestPage($this->state->get('page'));
			}
		}

		if (isset($this->responseBody['error']))
		{
			throw new \RuntimeException(sprintf('Error fetching page from MediaWiki API: %s', $this->responseBody['error']['info']));
		}

		if (is_null($this->responseBody))
		{
			throw new \RuntimeException('Error fetching page from MediaWiki API.');
		}

		// Store the title to be used later
		$this->title = $this->responseBody['parse']['displaytitle'];

		// Store the URL slug to be used later
		$this->setPageUrlSlug($this->responseBody['parse']['title']);

		// Store the rendered page reference
		$this->page = $this->responseBody['parse']['text']['*'];

		// Follow wiki redirects
		$max = $this->state->get('max_redirects', 5);
		$i   = 0;

		while (($redirect = $this->isRedirect()) && $i < $max)
		{
			$this->requestPage($redirect);

			if (isset($this->responseBody['error']))
			{
				throw new \RuntimeException(sprintf('Error fetching page from MediaWiki API: %s', $this->responseBody['error']['info']));
			}

			if (is_null($this->responseBody))
			{
				throw new \RuntimeException('Error fetching page from MediaWiki API.');
			}

			// Store the title to be used later
			$this->title = $this->responseBody['parse']['displaytitle'];

			// Store the URL slug to be used later
			$this->setPageUrlSlug($this->responseBody['parse']['title']);

			// Store the rendered page reference
			$this->page = $this->responseBody['parse']['text']['*'];
		}

		// Remove links to unwritten articles.
		if ($this->state->get('remove_redlinks', true))
		{
			$this->removeRedLinks();
		}

		// Amend or remove links from wiki page.
		$this->amendLinks();

		// Remove table of contents.
		if ($this->state->get('remove_toc', false))
		{
			$this->removeToc();
		}

		return $this->page;
	}

	/**
	 * Get the URL slug for the rendered page.
	 *
	 * @return  string
	 */
	public function getPageUrlSlug() : string
	{
		return $this->pageUrlSlug;
	}

	/**
	 * Get the title of the rendered page.
	 *
	 * @return  string
	 */
	public function getTitle() : string
	{
		return $this->title;
	}

	/**
	 * Get the base wiki URL.
	 *
	 * @return  string
	 */
	public function getWikiUrl() : string
	{
		return $this->wikiUrl;
	}

	/**
	 * Set a Uri object representing the current request URL
	 *
	 * @param   Uri  $uri  The Uri instance
	 *
	 * @return  $this
	 */
	public function setCurrentUri(Uri $uri) : HelpScreenModel
	{
		$this->currentUri = $uri;

		return $this;
	}

	/**
	 * Set the URL slug for the rendered page.
	 *
	 * @param   string  $slug  The URL slug
	 *
	 * @return  $this
	 */
	private function setPageUrlSlug(string $slug) : HelpScreenModel
	{
		$this->pageUrlSlug = Normalise::toUnderscoreSeparated($slug);

		return $this;
	}

	/**
	 * Set the base wiki URL.
	 *
	 * @param   string  $url  The URL
	 *
	 * @return  $this
	 */
	public function setWikiUrl(string $url) : HelpScreenModel
	{
		$this->wikiUrl = $url;

		return $this;
	}

	/**
	 * Amend or remove links from a wiki page.
	 *
	 * @return  void
	 */
	public function amendLinks()
	{
		// Remove links to wiki image information pages.
		$imglink = '!<a href="' . $this->uriWiki->getPath() . '/([^>]+)" class="image">(.+)</a>!';
		//$this->page = preg_replace($imglink, '$2', $this->page);

		// Remove links for new image uploads
		$imgUploadlink = '!<a href="' . $this->uriWiki->getPath() . '/([^>]+)" class="new"(.+)>(.+)</a>!';
		$this->page = preg_replace($imgUploadlink, '$3', $this->page);

		// Remove <translate> </translate> and translation markers from page output.
		$translationTags = '!(<(\/|)translate>|<!--T:\d+-->)+!';
		//$this->page = preg_replace($translationTags, '', $this->page);

		// Remove Special:MyLanguage/ or Special:MyLanguage/: from page links.
		$specialMyLanguage = '!(Special:MyLanguage\/(:)?)+!';
		$this->page = preg_replace($specialMyLanguage, '', $this->page);

		// Replace links to other wiki pages with links to the proxy.
		$replace = '<a href="' . $this->currentUri->toString(['scheme', 'host', 'path']) . '?keyref=';
		$pattern = '<a href="/';
		$this->page = str_replace($pattern, $replace, $this->page);

		// Replace relative links to images with absolute links to the wiki that bypass the proxy.
		$replace = $this->uriWiki->toString(['scheme', 'host', 'path']) . '/images/';
		$pattern = $this->uriWiki->getPath() . '/images/';
		$this->page = str_replace($pattern, $replace, $this->page);

		// Remove [edit] links.
		$pattern = '!<span class="mw-editsection-bracket">\[</span>(.+)<span class="mw-editsection-bracket">\]</span></span>!msU';
		$this->page = preg_replace($pattern, '', $this->page);

		// Replace any anchor based links
		$pattern = '<a href="#';
		$replace = '<a href="' . $this->currentUri->toString() . '#';
		$this->page = str_replace($pattern, $replace, $this->page);
	}

	/**
	 * Create the Uri objects for the request.
	 *
	 * @return  void
	 */
	private function createUriObjects()
	{
		$this->uriApi = new Uri($this->wikiUrl . '/api.php');
		$this->uriWiki = new Uri($this->wikiUrl);
	}

	/**
	 * Do a HTTP request for the configured Uri instance.
	 *
	 * @return  string
	 */
	private function doHttpRequest() : string
	{
		$this->connector->setOption('userAgent', 'HelpProxy/3.0');
		$this->connector->setOption('follow_location', 'false');

		try
		{
			$response = $this->connector->get($this->uriApi);
		}
		catch (\Exception $e)
		{
			return '';
		}

		$this->lastResponse = $response;

		return (string) $response->getBody();
	}

	/**
	 * Check if current page contains a REDIRECT.
	 *
	 * If a REDIRECT is present, returns the wiki page name to redirect to.
	 *
	 * @return  string|boolean  Page name to redirect to; false otherwise
	 */
	private function isRedirect()
	{
		$pattern = '!<li>REDIRECT <a href="' . $this->uriWiki->getPath() . '/([^"]+)"!';

		if (preg_match($pattern, $this->page, $matches))
		{
			return $matches[1];
		}

		return false;
	}

	/**
	 * Remove links to pages that have not yet been written (replace with just the text instead).
	 *
	 * @return  void
	 */
	private function removeRedLinks()
	{
		// Remove red links.
		$redlink = '!<a href="' . $this->uriWiki->getPath() . '/index.php\?title=([^&]+)\&amp;action=edit&amp;redlink=1" class="new" title="([^"]+) \(([^)]+)\)">([^<]+)</a>!';

		$this->page = preg_replace($redlink, '$4', $this->page);
	}

	/**
	 * Remove table of contents.
	 *
	 * @return  void
	 */
	public function removeToc()
	{
		// Remove table of contents.
		$toc = '!<table id="toc" class="toc">(.+)</table>!msU';
		$this->page = preg_replace($toc, '', $this->page);

		// Remove navbox too.
		$toc = '!<table cellspacing="0" class="navbox"(.+)</table>!msU';
		$this->page = preg_replace($toc, '', $this->page);
	}

	/**
	 * Request a page from the MediaWiki API.
	 *
	 * @param   string  $keyref  Key reference of help page to retrieve.
	 * @param   string  $lang    An optional language code for requesting a translated page.
	 *
	 * @return  boolean
	 */
	private function requestPage(string $keyref, string $lang = '') : bool
	{
		$this->createUriObjects();

		$this->uriApi->setVar('action', 'parse');
		$this->uriApi->setVar('format', 'json');

		// Build the lookup title
		$title = $keyref;

		// Append the language code if present and not already part of the keyref
		if (!empty($lang) && strpos($title, '/') === false)
		{
			$title .= "/$lang";
		}

		$this->uriApi->setVar('page', $title);

		// This has to decode to an array because of the parsed text's property name
		$this->responseBody = json_decode($this->doHttpRequest(), true);

		return true;
	}
}
