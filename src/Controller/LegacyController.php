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

use Joomla\Controller\AbstractController;
use Joomla\Renderer\RendererInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;

/**
 * Controller to catch legacy help.joomla.org routes
 *
 * @method         \Joomla\Application\WebApplication  getApplication()  Get the application object.
 * @property-read  \Joomla\Application\WebApplication  $app              Application object
 */
class LegacyController extends AbstractController
{
	/**
	 * The template renderer.
	 *
	 * @var  RendererInterface
	 */
	private $renderer;

	/**
	 * Constructor.
	 *
	 * @param   RendererInterface  $renderer  The template renderer.
	 */
	public function __construct(RendererInterface $renderer)
	{
		$this->renderer = $renderer;
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

		if ($this->getInput()->getString('task', 'display') === 'findkey')
		{
			// Render the notice for Joomla! 1.0 and 1.5 sites
			$this->getApplication()->setResponse(new HtmlResponse($this->renderer->render('helpscreen/eol.html.twig')));
		}
		else
		{
			// Redirect to the documentation wiki
			$this->getApplication()->setResponse(new RedirectResponse('https://docs.joomla.org', 301));
		}

		return true;
	}
}
