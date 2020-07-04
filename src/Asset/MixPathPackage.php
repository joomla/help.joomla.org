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

namespace Joomla\Help\Asset;

use Symfony\Component\Asset\Context\ContextInterface;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

/**
 * Extended path package for resolving assets from a Laravel Mix manifest
 */
class MixPathPackage extends PathPackage
{
	/**
	 * Decorated Package instance
	 *
	 * @var  Package
	 */
	private $decoratedPackage;

	/**
	 * Constructor
	 *
	 * @param   Package                   $decoratedPackage  Decorated Package instance
	 * @param   string                    $basePath          The base path to be prepended to relative paths
	 * @param   VersionStrategyInterface  $versionStrategy   The version strategy
	 * @param   ContextInterface          $context           The context
	 */
	public function __construct(
		Package $decoratedPackage,
		string $basePath,
		VersionStrategyInterface $versionStrategy,
		ContextInterface $context = null
	) {
		parent::__construct($basePath, $versionStrategy, $context);

		$this->decoratedPackage = $decoratedPackage;
	}

	/**
	 * Returns an absolute or root-relative public path.
	 *
	 * @param   string  $path  A path
	 *
	 * @return  string  The public path
	 */
	public function getUrl(string $path)
	{
		if ($this->isAbsoluteUrl($path))
		{
			return $path;
		}

		$versionedPath = $this->getVersionStrategy()->applyVersion("/$path");

		if ($versionedPath === $path)
		{
			return $this->decoratedPackage->getUrl($path);
		}

		return $this->getBasePath() . ltrim($versionedPath, '/');
	}
}
