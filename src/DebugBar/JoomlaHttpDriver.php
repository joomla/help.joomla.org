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

namespace Joomla\Help\DebugBar;

use DebugBar\HttpDriverInterface;
use Joomla\Application\SessionAwareWebApplicationInterface;
use Joomla\Application\WebApplicationInterface;

/**
 * HTTP Driver for the DebugBar integrating into the Joomla API
 */
class JoomlaHttpDriver implements HttpDriverInterface
{
	/**
	 * Web application
	 *
	 * @var  WebApplicationInterface
	 */
	private $application;

	/**
	 * Constructor.
	 *
	 * @param   WebApplicationInterface  $application  Web application
	 */
	public function __construct(WebApplicationInterface $application)
	{
		$this->application = $application;
	}

	/**
	 * Sets HTTP headers
	 *
	 * @param   array  $headers  Headers to add to the request
	 *
	 * @return  void
	 */
	public function setHeaders(array $headers)
	{
		foreach ($headers as $name => $value)
		{
			$this->application->setHeader($name, $value);
		}
	}

	/**
	 * Checks if the session is started
	 *
	 * @return  boolean
	 */
	public function isSessionStarted()
	{
		if ($this->application instanceof SessionAwareWebApplicationInterface)
		{
			return $this->application->getSession()->isStarted();
		}

		return false;
	}

	/**
	 * Sets a value in the session
	 *
	 * @param   string  $name   Name of a variable.
	 * @param   mixed   $value  Value of a variable.
	 *
	 * @return  void
	 */
	public function setSessionValue($name, $value)
	{
		if ($this->application instanceof SessionAwareWebApplicationInterface)
		{
			return $this->application->getSession()->set($name, $value);
		}
	}

	/**
	 * Checks if a value is in the session
	 *
	 * @param   string  $name  Name of variable
	 *
	 * @return  boolean
	 */
	public function hasSessionValue($name)
	{
		if ($this->application instanceof SessionAwareWebApplicationInterface)
		{
			return $this->application->getSession()->has($name);
		}

		return false;
	}

	/**
	 * Returns a value from the session
	 *
	 * @param   string  $name  Name of variable
	 *
	 * @return  mixed
	 */
	public function getSessionValue($name)
	{
		if ($this->application instanceof SessionAwareWebApplicationInterface)
		{
			return $this->application->getSession()->get($name);
		}
	}

	/**
	 * Deletes a value from the session
	 *
	 * @param   string  $name  Name of variable
	 *
	 * @return  void
	 */
	public function deleteSessionValue($name)
	{
		if ($this->application instanceof SessionAwareWebApplicationInterface)
		{
			return $this->application->getSession()->remove($name);
		}
	}
}
