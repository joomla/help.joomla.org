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

namespace Joomla\Help;

use Joomla\Application\AbstractWebApplication;
use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;

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
		try
		{
			$this->router->getController($this->get('uri.route'))->execute();
		}
		catch (\Exception $e)
		{
			// Log the error for reference
			$this->getLogger()->error(
				sprintf('Uncaught Exception of type %s caught.', get_class($e)),
				['exception' => $e]
			);

			$this->setErrorHeader($e);
			$this->setErrorOutput($e);
		}
	}

	/**
	 * Set the HTTP Response Header for error conditions.
	 *
	 * @param   \Exception  $exception  The Exception object to process.
	 *
	 * @return  void
	 */
	private function setErrorHeader(\Exception $exception)
	{
		switch ($exception->getCode())
		{
			case 401:
				$this->setHeader('HTTP/1.1 401 Unauthorized', 401, true);

				break;

			case 403:
				$this->setHeader('HTTP/1.1 403 Forbidden', 403, true);

				break;

			case 404:
				$this->setHeader('HTTP/1.1 404 Not Found', 404, true);

				break;

			case 500:
			default:
				$this->setHeader('HTTP/1.1 500 Internal Server Error', 500, true);

				break;
		}
	}

	/**
	 * Set the body for error conditions.
	 *
	 * @param   \Exception  $exception  The Exception object.
	 *
	 * @return  void
	 */
	private function setErrorOutput(\Exception $exception)
	{
		switch (strtolower($this->input->getWord('format', 'html')))
		{
			case 'html' :
			default :
				$body = $this->getContainer()->get('renderer')->render('exception.html', ['exception' => $exception]);
				break;
		}

		$this->setBody($body);
	}

	/**
	 * Set the application's router.
	 *
	 * @param   Router  $router  Router object to set.
	 *
	 * @return  $this
	 */
	public function setRouter(Router $router)
	{
		$this->router = $router;

		return $this;
	}
}
