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

use Joomla\Help\Twig\Service\CdnRendererService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extension integrating `joomla.org` template elements from the CDN
 */
final class CdnExtension extends AbstractExtension
{
	/**
	 * Returns a list of functions to add to the existing list.
	 *
	 * @return  TwigFunction[]  An array of functions.
	 */
	public function getFunctions()
	{
		return [
			new TwigFunction('cdn_footer', [CdnRendererService::class, 'getCdnFooter'], ['is_safe' => ['html']]),
			new TwigFunction('cdn_menu', [CdnRendererService::class, 'getCdnMenu'], ['is_safe' => ['html']]),
		];
	}
}
