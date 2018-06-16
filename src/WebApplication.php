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

namespace Joomla\Help;

use Joomla\Application\AbstractWebApplication;
use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;
use Joomla\Router\Router;

/**
 * Web application for the help site.
 */
class WebApplication extends AbstractWebApplication implements ContainerAwareInterface
{
	use ContainerAwareTrait;

	/**
	 * Application router.
	 *
	 * @var  Router
	 */
	private $router;

	/**
	 * Method to run the application routines.
	 *
	 * @return  void
	 */
	protected function doExecute()
	{
		$route = $this->router->parseRoute($this->get('uri.route'), $this->input->getMethod());

		// Add variables to the input if not already set
		foreach ($route->getRouteVariables() as $key => $value)
		{
			$this->input->def($key, $value);
		}

		/** @var ControllerInterface $controller */
		$controller = $this->getContainer()->get($route->getController());
		$controller->execute();
	}

	/**
	 * Set the application's router.
	 *
	 * @param   Router  $router  Router object to set.
	 *
	 * @return  $this
	 */
	public function setRouter(Router $router) : WebApplication
	{
		$this->router = $router;

		return $this;
	}
}
