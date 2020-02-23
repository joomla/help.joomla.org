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

namespace Joomla\Help\Twig;

use Joomla\Help\Twig\Service\RoutingService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Help site's Twig extension class
 */
class ApplicationExtension extends AbstractExtension
{
	/**
	 * Returns a list of filters to add to the existing list
	 *
	 * @return  TwigFilter[]  An array of TwigFilter instances
	 */
	public function getFilters()
	{
		return [
			new TwigFilter('get_class', 'get_class'),
			new TwigFilter('strip_root_path', [$this, 'stripRootPath']),
		];
	}

	/**
	 * Returns a list of functions to add to the existing list.
	 *
	 * @return  TwigFunction[]  An array of TwigFunction instances
	 */
	public function getFunctions()
	{
		return [
			new TwigFunction('current_url', [RoutingService::class, 'getCurrentUrl']),
		];
	}

	/**
	 * Removes the application root path defined by the constant "JPATH_ROOT"
	 *
	 * @param   string  $string  The string to process
	 *
	 * @return  string
	 */
	public function stripRootPath(string $string): string
	{
		return str_replace(JPATH_ROOT . DIRECTORY_SEPARATOR, '/', $string);
	}
}
