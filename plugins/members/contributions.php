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

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_members_contributions' );

//-----------

class plgMembersContributions extends JPlugin
{
	public function plgMembersContributions(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'members', 'contributions' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function &onMembersAreas( $authorized )
	{
		$areas = array(
			'contributions' => JText::_('PLG_MEMBERS_CONTRIBUTIONS')
		);
		return $areas;
	}

	//-----------

	public function onMembers( $member, $option, $authorized, $areas )
	{
		$returnhtml = true;
		
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) {
			if (!array_intersect( $areas, $this->onMembersAreas( $authorized ) ) 
			&& !array_intersect( $areas, array_keys( $this->onMembersAreas( $authorized ) ) )) {
				$returnhtml = false;
			}
		}
		
		$arr = array(
			'html'=>'',
			'metadata'=>''
		);
		
		$database =& JFactory::getDBO();
		$dispatcher =& JDispatcher::getInstance();

		// Incoming paging vars
		$limit = JRequest::getInt( 'limit', 25 );
		$limitstart = JRequest::getInt( 'limitstart', 0 );
		$sort = JRequest::getVar( 'sort', 'date' );

		// Trigger the functions that return the areas we'll be using
		$areas = array();
		$searchareas = $dispatcher->trigger( 'onMembersContributionsAreas', array($authorized) );
		foreach ($searchareas as $area) 
		{
			$areas = array_merge( $areas, $area );
		}

		// Get the active category
		$area = JRequest::getVar( 'area', '' );
		if ($area) {
			$activeareas = array($area);
		} else {
			$limit = 5;
			$activeareas = $areas;
		}
		
		// If we're just returning metadata, we set the limitstart to -1 to use as a flag
		// This allows us to reduce the overall number of queries
		if (!$returnhtml) {
			$limitstart = -1;
		}
		
		// Get the search result totals
		$totals = $dispatcher->trigger( 'onMembersContributions', array(
				$member,
				$option,
				$authorized,
				0,
				$limitstart,
				$sort,
				$activeareas)
			);

		// Get the total results found (sum of all categories)
		$i = 0;
		$total = 0;
		$cats = array();
		foreach ($areas as $c=>$t) 
		{
			$cats[$i]['category'] = $c;

			// Do sub-categories exist?
			if (is_array($t) && !empty($t)) {
				// They do - do some processing
				$cats[$i]['title'] = ucfirst($c);
				$cats[$i]['total'] = 0;
				$cats[$i]['_sub'] = array();
				$z = 0;
				// Loop through each sub-category
				foreach ($t as $s=>$st) 
				{
					// Ensure a matching array of totals exist
					if (is_array($totals[$i]) && !empty($totals[$i]) && isset($totals[$i][$z])) {
						// Add to the parent category's total
						$cats[$i]['total'] = $cats[$i]['total'] + $totals[$i][$z];
						// Get some info for each sub-category
						$cats[$i]['_sub'][$z]['category'] = $s;
						$cats[$i]['_sub'][$z]['title'] = $st;
						$cats[$i]['_sub'][$z]['total'] = $totals[$i][$z];
					}
					$z++;
				}
			} else {
				// No sub-categories - this should be easy
				$cats[$i]['title'] = $t;
				$cats[$i]['total'] = (!is_array($totals[$i])) ? $totals[$i] : 0;
			}

			// Add to the overall total
			$total = $total + intval($cats[$i]['total']);
			$i++;
		}

		// Build the HTML
		if ($returnhtml) {
			$document =& JFactory::getDocument();
			if (is_file(JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'resources.js')) {
				$document->addScript('components'.DS.'com_resources'.DS.'resources.js');
			}
			
			$limit = ($limit == 0) ? 'all' : $limit;
		
			// Get the search results
			$results = $dispatcher->trigger( 'onMembersContributions', array(
				$member,
				$option,
				$authorized,
				$limit,
				$limitstart,
				$sort,
				$activeareas)
			);
			
			// Do we have an active area?
			if (count($activeareas) == 1 && !is_array(current($activeareas))) {
				$active = $activeareas[0];
			} else {
				$active = '';
			}
			
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'members',
					'element'=>'contributions',
					'name'=>'display'
				)
			);
			$view->authorized = $authorized;
			$view->totals = $totals;
			$view->results = $results;
			$view->cats = $cats;
			$view->active = $active;
			$view->option = $option;
			$view->start = $limitstart;
			$view->limit = $limit;
			$view->total = $total;
			$view->member = $member;
			$view->sort = $sort;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			
			$arr['html'] = $view->loadTemplate();
		} else {
			// Build the metadata
			foreach ($cats as $cat) 
			{
				if ($cat['total'] > 0) {
					$arr['metadata'] .= '<p class="'.strtolower($cat['title']).'"><a href="'.JRoute::_('index.php?option='.$option.'&id='.$member->get('uidNumber').'&active=contributions').'">'.$cat['total'].' '.strtolower($cat['title']).'</a></p>'."\n";
				}
			}
		}

		return $arr;
	}
}