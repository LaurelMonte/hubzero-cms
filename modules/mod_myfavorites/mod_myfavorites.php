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

//-------------------------------------------------------------
// Joomla module
// "My Favorites"
//    This module displays "favorites" assigned to the 
//    user currently logged in.
// Members "favorites" plugin REQUIRED
//-------------------------------------------------------------

class modMyFavorites
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
	
	public function display() 
	{
		$juser =& JFactory::getUser();
		$database =& JFactory::getDBO();
		
		$params =& $this->params;
		$this->moduleclass = $params->get( 'moduleclass' );
		$limit = intval( $params->get( 'limit' ) );
		$limit = ($limit) ? $limit : 5;
		
		$this->error = false;
		
		// Check for the existence of required tables that should be
		// installed with the com_support component
		$database->setQuery("SHOW TABLES");
		$tables = $database->loadResultArray();
		
		if ($tables && array_search($database->_table_prefix.'xfavorites', $tables)===false) {
			// Support tickets table not found!
			$this->error = true;
			return false;
	    }

		$authorized = true;

		JPluginHelper::importPlugin( 'members' );
		$dispatcher =& JDispatcher::getInstance();
		
		// Trigger the functions that return the areas we'll be using
		$areas = array();
		$searchareas = $dispatcher->trigger( 'onMembersFavoritesAreas', array($authorized) );
		foreach ($searchareas as $area) 
		{
			$areas = array_merge( $areas, $area );
		}

		// Get the active category
		$area = '';
		$limitstart = 0;
		$activeareas = $areas;

		$option = 'com_members';
		
		ximport('xprofile');
		$member = new XProfile();
		$member->load( $juser->get('id') );

		// Get the search result totals
		$totals = $dispatcher->trigger( 'onMembersFavorites', array(
				$member,
				$option,
				$authorized,
				0,
				0,
				$activeareas)
			);

		// Get the search results
		$this->results = $dispatcher->trigger( 'onMembersFavorites', array(
				$member,
				$option,
				$authorized,
				$limit,
				$limitstart,
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

		// Do we have an active area?
		if (count($activeareas) == 1 && !is_array(current($activeareas))) {
			$this->active = $activeareas[0];
		} else {
			$this->active = '';
		}
		
		$this->cats = $cats;
		
		// Push the module CSS to the template
		ximport('xdocument');
		XDocument::addModuleStyleSheet('mod_myfavorites');
	}
}

//-------------------------------------------------------------

$modmyfavorites = new modMyFavorites( $params );
$modmyfavorites->display();

require( JModuleHelper::getLayoutPath('mod_myfavorites') );
?>