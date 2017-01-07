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

/**
 * Web application for the help site.
 */
class WebApplication extends AbstractWebApplication implements ContainerAwareInterface
{
	use ContainerAwareTrait;

	/**
	 * The template to use for error pages.
	 *
	 * @var  string
	 */
	private $errorTemplate = 'exception.html';

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
		catch (\Throwable $e)
		{
			// Do not browser cache an error page
			$this->allowCache(false);

			// Log the error for reference
			$this->getLogger()->error(
				sprintf('Uncaught Throwable of type %s caught.', get_class($e)),
				['exception' => $e]
			);

			$this->setErrorHeader($e);
			$this->setErrorOutput($e);
		}
	}

	/**
	 * Set the HTTP Response Header for error conditions.
	 *
	 * @param   \Throwable  $exception  The Throwable object to process.
	 *
	 * @return  void
	 */
	private function setErrorHeader(\Throwable $exception)
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
	 * @param   \Throwable  $exception  The Throwable object.
	 *
	 * @return  void
	 */
	private function setErrorOutput(\Throwable $exception)
	{
		switch (strtolower($this->input->getWord('format', 'html')))
		{
			case 'html' :
			default :
				$body = $this->getContainer()->get('renderer')->render($this->errorTemplate, ['exception' => $exception]);
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
	public function setRouter(Router $router) : WebApplication
	{
		$this->router = $router;

		return $this;
	}

	/**
	 * Set the application's error template.
	 *
	 * @param   string  $template  Name of the template to use for error pages.
	 *
	 * @return  $this
	 */
	public function setErrorTemplate(string $template) : WebApplication
	{
		$this->errorTemplate = $template;

		return $this;
	}
}
