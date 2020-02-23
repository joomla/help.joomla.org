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

use Joomla\Preload\PreloadManager;
use Symfony\Component\Asset\Packages;

/**
 * Twig runtime service for asset related functionality
 */
class AssetService
{
	/**
	 * The asset packages manager
	 *
	 * @var  Packages
	 */
	private $packages;

	/**
	 * The HTTP/2 preload manager
	 *
	 * @var  PreloadManager
	 */
	private $preloadManager;

	/**
	 * Constructor.
	 *
	 * @param   Packages        $packages        The asset packages manager
	 * @param   PreloadManager  $preloadManager  The HTTP/2 preload manager
	 */
	public function __construct(Packages $packages, PreloadManager $preloadManager)
	{
		$this->packages       = $packages;
		$this->preloadManager = $preloadManager;
	}

	/**
	 * Returns the public path for an asset.
	 *
	 * @param   string  $asset        The asset
	 * @param   string  $packageName  The optional name of the asset package to use
	 *
	 * @return  string  The public path for an asset
	 */
	public function getAssetUrl(string $asset, ?string $package = null): string
	{
		return $this->packages->getUrl($asset, $package);
	}

	/**
	 * Preload a resource
	 *
	 * @param   string  $uri         The URI for the resource to preload
	 * @param   string  $linkType    The preload method to apply
	 * @param   array   $attributes  The attributes of this link (e.g. "array('as' => true)", "array('pr' => 0.5)")
	 *
	 * @return  string
	 *
	 * @throws  \InvalidArgumentException
	 */
	public function preloadAsset(string $uri, string $linkType = 'preload', array $attributes = []): string
	{
		$this->preloadManager->link($uri, $linkType, $attributes);

		return $uri;
	}
}
