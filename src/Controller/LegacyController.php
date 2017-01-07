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

/**
 * Controller to catch legacy help.joomla.org routes
 *
 * @method         \Joomla\Help\WebApplication  getApplication()  Get the application object.
 * @property-read  \Joomla\Help\WebApplication  $app              Application object
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

		// Set the layout based on the requested task; the 'findkey' task maps to Joomla! 1.0 and 1.5 help screen requests
		$layout = $this->getInput()->getString('task', 'display') == 'findkey' ? 'helpscreen/eol.html' : 'main.html';

		$this->getApplication()->setBody($this->renderer->render($layout));

		return true;
	}
}
