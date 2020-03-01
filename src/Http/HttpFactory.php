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

namespace Joomla\Help\Http;

use DebugBar\DebugBar;
use Joomla\Help\Http\Transport\DebugTransport;
use Joomla\Http\HttpFactory as BaseFactory;
use Joomla\Http\TransportInterface;

/**
 * Extended HTTP factory
 */
class HttpFactory extends BaseFactory
{
	/**
	 * Application debug bar
	 *
	 * @var  DebugBar
	 */
	private $debugBar;

	/**
	 * Constructor.
	 *
	 * @param   DebugBar  $debugBar  Application debug bar.
	 */
	public function __construct(DebugBar $debugBar)
	{
		$this->debugBar = $debugBar;
	}

	/**
	 * Finds an available TransportInterface object for communication
	 *
	 * @param   array|\ArrayAccess  $options  Options for creating TransportInterface object
	 * @param   array|string        $default  Adapter (string) or queue of adapters (array) to use
	 *
	 * @return  TransportInterface|boolean  Interface sub-class or boolean false if no adapters are available
	 *
	 * @throws  \InvalidArgumentException
	 */
	public function getAvailableDriver($options = [], $default = null)
	{
		$wrappedDriver = parent::getAvailableDriver($options, $default);

		return new DebugTransport($this->debugBar, $wrappedDriver, $options);
	}
}
