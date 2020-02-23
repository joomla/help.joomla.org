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

namespace Joomla\Help\Command;

use Joomla\Console\Command\AbstractCommand;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Command to clear the cache pool
 */
class ClearCacheCommand extends AbstractCommand
{
	/**
	 * The default command name
	 *
	 * @var  string|null
	 */
	protected static $defaultName = 'cache:clear';

	/**
	 * Cache pool
	 *
	 * @var  CacheItemPoolInterface
	 */
	private $cache;

	/**
	 * Instantiate the command.
	 *
	 * @param   CacheItemPoolInterface  $cache  Cache pool.
	 */
	public function __construct(CacheItemPoolInterface $cache)
	{
		$this->cache = $cache;

		parent::__construct();
	}

	/**
	 * Internal function to execute the command.
	 *
	 * @param   InputInterface   $input   The input to inject into the command.
	 * @param   OutputInterface  $output  The output to inject into the command.
	 *
	 * @return  integer  The command exit code
	 */
	protected function doExecute(InputInterface $input, OutputInterface $output): int
	{
		$symfonyStyle = new SymfonyStyle($input, $output);

		$symfonyStyle->title('Clear Cache');

		$this->cache->clear();

		$symfonyStyle->success('Cache cleared.');

		return 0;
	}

	/**
	 * Configures the current command.
	 *
	 * @return  void
	 */
	protected function configure(): void
	{
		$this->setDescription('Clear the application cache pool');
	}
}
