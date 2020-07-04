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

use Joomla\Help\Twig\Service\AssetService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extension class integrating asset support
 */
class AssetExtension extends AbstractExtension
{
	/**
	 * Returns a list of functions to add to the existing list.
	 *
	 * @return  TwigFunction[]  An array of TwigFunction instances
	 */
	public function getFunctions()
	{
		return [
			new TwigFunction('asset', [AssetService::class, 'getAssetUrl']),
			new TwigFunction('preload', [AssetService::class, 'preloadAsset']),
            new TwigFunction('sri', [AssetService::class, 'getSriAttributes'], ['is_safe' => ['html']]),
		];
	}
}
