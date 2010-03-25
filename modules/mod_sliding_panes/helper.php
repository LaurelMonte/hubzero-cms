<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class modSlidingPanes
{
	private $attributes = array();

	//-----------

	public function __construct( $params ) 
	{
		$this->params = $params;
	}

	//-----------

	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}

	//-----------

	public function __get($property)
	{
		if (isset($this->attributes[$property])) {
			return $this->attributes[$property];
		}
	}

	//-----------

	private function _getList()
	{
		//global $mainframe;

		$db =& JFactory::getDBO();
		//$user =& JFactory::getUser();
		//$aid = $user->get('aid', 0);

		$catid 	 = (int) $this->params->get('catid', 0);
		$random  = $this->params->get('random', 0);
		$orderby = $random ? 'RAND()' : 'a.ordering';
		$limit   = (int) $this->params->get('limitslides', 0);
		$limitby = $limit ? ' LIMIT 0,'.$limit : '';

		//$contentConfig =& JComponentHelper::getParams( 'com_content' );
		//$noauth	= !$contentConfig->get('shownoauth');

		$date =& JFactory::getDate();
		$now = $date->toMySQL();

		$nullDate = $db->getNullDate();

		// query to determine article count
		$query = 'SELECT a.*,' .
			' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'.
			' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'.
			' FROM #__content AS a' .
			' INNER JOIN #__categories AS cc ON cc.id = a.catid' .
			' INNER JOIN #__sections AS s ON s.id = a.sectionid' .
			' WHERE a.state = 1 ' .
			//($noauth ? ' AND a.access <= ' .(int) $aid. ' AND cc.access <= ' .(int) $aid. ' AND s.access <= ' .(int) $aid : '').
			' AND (a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).' ) ' .
			' AND (a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).' )' .
			' AND cc.id = '. (int) $catid .
			' AND cc.section = s.id' .
			' AND cc.published = 1' .
			' AND s.published = 1' .
			' ORDER BY '.$orderby.' '.$limitby;
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}

	//-----------

	public function display() 
	{
		$jdocument =& JFactory::getDocument();
		
		// Check if we have multiple instances of the module running
		// If so, we only want to push the CSS and JS to the template once
		if (!$this->multiple_instances) {
			// Push some CSS to the template
			ximport('xdocument');
			XDocument::addModuleStylesheet('mod_sliding_panes');

			// Push some javascript to the template
			if (is_file(JPATH_ROOT.'/modules/mod_sliding_panes/mod_sliding_panes.js')) {
				$jdocument->addScript('/modules/mod_sliding_panes/mod_sliding_panes.js');
			}
		}
		
		$id = rand();
		
		$this->content = $this->_getList();
		
		$this->container = $this->params->get('container', 'pane-sliders');
		
		$js = "window.addEvent('domready', function(){
			if ($('".$this->container."')) {
				myTabs".$id." = new ModSlidingPanes('".$this->container."', ".$this->params->get('rotate', 1).");

				// this sets it up to work even if it's width isn't a set amount of pixels
				window.addEvent('resize', myTabs".$id.".recalcWidths.bind(myTabs".$id."));
			}
		});";
		
		$jdocument->addScriptDeclaration($js);
	}
}
