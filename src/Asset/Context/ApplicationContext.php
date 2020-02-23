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

namespace Joomla\Help\Asset\Context;

use Joomla\Application\ApplicationInterface;
use Joomla\Application\WebApplicationInterface;
use Symfony\Component\Asset\Context\ContextInterface;

/**
 * Joomla! application aware context
 */
class ApplicationContext implements ContextInterface
{
	/**
	 * Application object
	 *
	 * @var  ApplicationInterface
	 */
	private $app;

	/**
	 * Constructor
	 *
	 * @param   ApplicationInterface  $app  The application object
	 */
	public function __construct(ApplicationInterface $app)
	{
		$this->app = $app;
	}

	/**
	 * Gets the base path.
	 *
	 * @return  string  The base path
	 */
	public function getBasePath()
	{
		return rtrim($this->app->get('uri.base.path'), '/');
	}

	/**
	 * Checks whether the request is secure or not.
	 *
	 * @return  boolean
	 */
	public function isSecure()
	{
		if ($this->app instanceof WebApplicationInterface)
		{
			return $this->app->isSslConnection();
		}

		return false;
	}
}
