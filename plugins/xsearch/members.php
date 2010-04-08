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
JPlugin::loadLanguage( 'plg_xsearch_members' );

//-----------

class plgXSearchMembers extends JPlugin
{
	public function plgXSearchMembers(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'xsearch', 'members' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function &onXSearchAreas()
	{
		$areas = array(
			'members' => JText::_('PLG_XSEARCH_MEMBERS')
		);
		return $areas;
	}
	
	//-----------

	public function onXSearch( $searchquery, $limit=0, $limitstart=0, $areas=null )
	{
		if (is_array( $areas ) && $limit) {
			if (!array_intersect( $areas, $this->onXSearchAreas() ) && !array_intersect( $areas, array_keys( $this->onXSearchAreas() ) )) {
				return array();
			}
		}

		// Do we have a search term?
		$t = $searchquery->searchTokens;
		if (empty($t)) {
			return array();
		}
		
		$database =& JFactory::getDBO();

		// An array for all the words and phrases
		$words = $searchquery->searchTokens;

		// Build the query
		$c_count = "SELECT COUNT(*) ";
		$b = '';
		foreach ($words as $word) 
		{
			if (trim($word) != '') {
				$word = addslashes($word);
				$b .= "CASE WHEN LOWER(m.givenName) LIKE '%$word%' THEN 5 ELSE 0 END + ";
				$b .= "CASE WHEN LOWER(m.surname) LIKE '%$word%' THEN 5 ELSE 0 END + ";
				$b .= "CASE WHEN LOWER(m.name) LIKE '%".addslashes(implode(' ',$searchquery->searchTokens))."%' THEN 20 ELSE 0 END + ";
				$b .= "CASE WHEN LOWER(b.bio) LIKE '%$word%' THEN 5 ELSE 0 END + ";
			}
		}
		$b = substr($b, 0, -3);
		$c_fields = "SELECT m.uidNumber AS id, CONCAT(m.givenName,' ', m.middleName,' ', m.surname) AS title, m.username AS alias, b.bio AS itext,  NULL AS ftext, m.public AS state, NULL AS created, m.modifiedDate AS modified, NULL AS publish_up, NULL AS params,
					CONCAT( 'index.php?option=com_members&id=', m.uidNumber ) as href, 'members' AS section, m.organization AS area, m.picture AS category, NULL AS rating, NULL AS times_rated, NULL AS ranking, NULL AS access, ($b) AS relevance ";
		$c_from = "FROM #__xprofiles AS m LEFT JOIN #__xprofiles_bio AS b ON m.uidNumber=b.uidNumber 
				WHERE m.public=1 AND (";
		foreach ($words as $word) 
		{
			if (trim($word) != '') {
				$word = addslashes($word);
				$c_from .= "(LOWER(m.givenName) LIKE '%$word%') OR (LOWER(m.surname) LIKE '%$word%') OR (LOWER(b.bio) LIKE '%$word%') OR ";
			}
		}
		$c_from = substr($c_from, 0, -4);
		$c_from .= ")";
		$c_order = " ORDER BY relevance DESC";
		$c_limit = ($limit != 'all') ? " LIMIT $limitstart,$limit" : "";

		if (!$limit) {
			// Get a count
			$database->setQuery( $c_count.$c_from );
			return $database->loadResult();
		} else {
			if (count($areas) > 1) {
				ximport('xdocument');
				XDocument::addComponentStylesheet('com_members');

				return $c_fields.$c_from;
			}
			
			// Get results
			$database->setQuery( $c_fields.$c_from.$c_order.$c_limit );
			$rows = $database->loadObjectList();

			foreach ($rows as $key => $row) 
			{
				$rows[$key]->href = JRoute::_('index.php?option=com_members&id='.$row->id);
			}

			return $rows;
		}
	}

	//----------------------------------------------------------
	// Optional custom functions
	// uncomment to use
	//----------------------------------------------------------

	public function documents() 
	{
		ximport('xdocument');
		XDocument::addComponentStylesheet('com_members');
	}

	//-----------

	/*public function before()
	{
		// ...
	}*/

	//-----------

	public function out( $row, $keyword )
	{
		$config =& JComponentHelper::getParams( 'com_members' );
		
		if ($row->category) {
			$thumb  = $config->get('webpath');
			if (substr($thumb, 0, 1) != DS) {
				$thumb = DS.$thumb;
			}
			if (substr($thumb, -1, 1) == DS) {
				$thumb = substr($thumb, 0, (strlen($thumb) - 1));
			}
			if ($row->id < 0) {
				$id = abs($row->id);
				$thumb .= DS.'n'.plgXSearchMembers::niceidformat($id).DS.$row->category;
			} else {
				$thumb .= DS.plgXSearchMembers::niceidformat($row->id).DS.$row->category;
			}
			
			$thumb = plgXSearchMembers::thumbit($thumb);
		} else {
			$thumb = '';
		}
		
		$dfthumb = $config->get('defaultpic');
		if (substr($dfthumb, 0, 1) != DS) {
			$dfthumb = DS.$dfthumb;
		}
		$dfthumb = plgXSearchMembers::thumbit($dfthumb);
		
		if (strstr( $row->href, 'index.php' )) {
			$row->href = JRoute::_($row->href);
		}
		$juri =& JURI::getInstance();
		if (substr($row->href,0,1) == '/') {
			$row->href = substr($row->href,1,strlen($row->href));
		}
		
		$html  = "\t".'<li class="member">'."\n";
		if (is_file(JPATH_ROOT.$thumb)) {
			$p = $thumb;
		} else if (is_file(JPATH_ROOT.$dfthumb)) {
			$p = $dfthumb;
		}
		if ($p) {
			$html .= "\t\t".'<p class="photo"><img width="50" height="50" src="'.$p.'" alt="" /></p>'."\n";
		}
		$html .= "\t\t".'<p class="title"><a href="'.$row->href.'">'.stripslashes($row->title).'</a></p>'."\n";
		if ($row->itext) {
			$html .= "\t\t".'<p>&#133; '.stripslashes($row->itext).' &#133;</p>'."\n";
		}
		$html .= "\t\t".'<p class="href">'.$juri->base().$row->href.'</p>'."\n";
		$html .= "\t".'</li>'."\n";
		return $html;
	}
	
	//-----------
	
	public function thumbit($thumb) 
	{
		$image = explode('.',$thumb);
		$n = count($image);
		$image[$n-2] .= '_thumb';
		$end = array_pop($image);
		$image[] = $end;
		$thumb = implode('.',$image);
		
		return $thumb;
	}
	
	//-----------

	public function niceidformat($someid) 
	{
		while (strlen($someid) < 5) 
		{
			$someid = 0 . "$someid";
		}
		return $someid;
	}

	//-----------

	/*public function after()
	{
		// ...
	}*/
}