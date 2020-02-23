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

use Joomla\Application\ConfigurationAwareApplicationInterface;

/**
 * Twig runtime service for routing related functionality
 */
class RoutingService
{
	/**
	 * The web application
	 *
	 * @var  ConfigurationAwareApplicationInterface
	 */
	private $application;

	/**
	 * Constructor.
	 *
	 * @param   ConfigurationAwareApplicationInterface  $application  The web application
	 */
	public function __construct(ConfigurationAwareApplicationInterface $application)
	{
		$this->application = $application;
	}

	/**
	 * Returns the current URL.
	 *
	 * @return  string
	 */
	public function getCurrentUrl(): string
	{
		return $this->application->get('uri.request');
	}
}
