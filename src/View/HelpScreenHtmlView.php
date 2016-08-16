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

namespace Joomla\Help\View;

use Joomla\Help\Model\HelpScreenModel;
use Joomla\View\BaseHtmlView;

/**
 * View to render Joomla! help screens
 *
 * @property-read  HelpScreenModel  $model  The model object.
 */
class HelpScreenHtmlView extends BaseHtmlView
{
	/**
	 * Get the view's model
	 *
	 * @return  HelpScreenModel
	 */
	public function getModel() : HelpScreenModel
	{
		return $this->model;
	}

	/**
	 * Method to render the view.
	 *
	 * @return  string  The rendered view.
	 */
	public function render() : string
	{
		$this->setData(
			[
				'page'     => $this->getModel()->getPage(),
				'pageName' => $this->getModel()->getPageUrlSlug(),
				'title'    => $this->getModel()->getTitle(),
				'wikiUrl'  => $this->getModel()->getWikiUrl(),
			]
		);

		return parent::render();
	}
}
