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
JPlugin::loadLanguage( 'plg_members_usage' );

//-----------

class plgMembersUsage extends JPlugin
{
	public function plgMembersUsage(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'members', 'usage' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function &onMembersAreas( $authorized ) 
	{
		$areas = array(
			'usage' => JText::_('PLG_MEMBERS_USAGE')
		);
		return $areas;
	}

	//-----------

	public function onMembers( $member, $option, $authorized, $areas )
	{
		$returnhtml = true;
		
		$arr = array(
			'html'=>'',
			'metadata'=>''
		);
		
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) {
			if (!array_intersect( $areas, $this->onMembersAreas( $authorized ) ) 
			&& !array_intersect( $areas, array_keys( $this->onMembersAreas( $authorized ) ) )) {
				$returnhtml = false;
			}
		}
		
		$database =& JFactory::getDBO();
		$tables = $database->getTableList();
		$table = $database->_table_prefix.'author_stats';

		if (!in_array($table,$tables)) {
			ximport('Hubzero_View_Helper_Html');
			$arr['html'] = Hubzero_View_Helper_Html::error( JText::_('USAGE_ERROR_MISSING_TABLE') );
			$arr['metadata'] = '<p class="usage"><a href="'.JRoute::_('index.php?option='.$option.'&id='.$member->get('uidNumber').'&active=usage').'">'.JText::_('PLG_MEMBERS_USAGE_DETAILED_USAGE').'</a></p>'."\n";
			return $arr;
		}

		$html = '';
		if ($returnhtml) {
			ximport('xdocument');
			XDocument::addComponentStylesheet('com_usage');
			
			//$sort = JRequest::getVar('sort','');
			
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'members',
					'element'=>'usage',
					'name'=>'summary'
				)
			);
			
			$view->member = $member;
			$view->option = $option;
			$view->contribution = $this->first_last_contribution($member->get('uidNumber'));
			$view->rank = $this->get_rank($member->get('uidNumber'));

			$view->total_tool_users = $this->get_total_stats($member->get('uidNumber'), 'tool_users',14);
			$view->total_andmore_users = $this->get_total_stats($member->get('uidNumber'), 'andmore_users',14);
			$view->citation_count = $this->get_citationcount(null, $member->get('uidNumber'));

			
			$sql = "SELECT res.id, res.title, DATE_FORMAT(res.publish_up, '%d %b %Y') AS publish_up, restypes.type 
					FROM #__resources res, #__author_assoc aa, #__resource_types restypes 
					WHERE res.id = aa.subid AND res.type = restypes.id AND aa.authorid = '".$member->get('uidNumber')."' AND res.published = 1 AND res.access != 1 AND res.type = 7 AND res.access != 4 AND aa.subtable = 'resources' AND standalone = 1 ORDER BY res.publish_up DESC";

			$database->setQuery( $sql );
			$view->tool_stats = $database->loadObjectList();
			$view->tool_total_12 = $this->get_total_stats($member->get('uidNumber'), 'tool_users', 12);
			$view->tool_total_14 = $this->get_total_stats($member->get('uidNumber'), 'tool_users', 14);
			
			$sql = "SELECT res.id, res.title, DATE_FORMAT(res.publish_up, '%d %b %Y') AS publish_up, restypes.type 
					FROM #__resources res, #__author_assoc aa, #__resource_types restypes 
					WHERE res.id = aa.subid AND res.type = restypes.id AND aa.authorid = '".$member->get('uidNumber')."' AND res.published = 1 AND res.access != 1 AND res.type <> 7 AND res.access != 4 AND aa.subtable = 'resources' AND standalone = 1 ORDER BY res.publish_up DESC";

			$database->setQuery( $sql );
			$view->andmore_stats = $database->loadObjectList();
			$view->andmore_total_12 = $this->get_total_stats($member->get('uidNumber'), 'andmore_users', 12);
			$view->andmore_total_14 = $this->get_total_stats($member->get('uidNumber'), 'andmore_users', 14);
			

			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			
			$arr['html'] = $view->loadTemplate();
		}

		$arr['metadata'] = '<p class="usage"><a href="'.JRoute::_('index.php?option='.$option.'&id='.$member->get('uidNumber').'&active=usage').'">'.JText::_('PLG_MEMBERS_USAGE_DETAILED_USAGE').'</a></p>'."\n";
		if (is_file(JPATH_ROOT.DS.'site/stats/contributor_impact/impact_'.$this->uid($member->get('uidNumber')).'_th.gif')) {
			$arr['metadata'] .= '<p><a rel="lightbox" href="/site/stats/contributor_impact/impact_'.$this->uid($member->get('uidNumber')).'.gif"><img src="/site/stats/contributor_impact/impact_'.$this->uid($member->get('uidNumber')).'_th.gif" alt="'.JText::_('PLG_MEMBERS_USAGE_IMPACT_PLOT').'" /></a></p>'."\n";
		}

		return $arr;
	}
	
	//-----------
	
	public function uid($uid) 
	{
		if ($uid < 0) {
			return 'n' . -$uid;
		} else {
			return $uid;
		}
	}

	//-----------
	
	public function first_last_contribution($authorid) 
	{
		$database =& JFactory::getDBO();
		
		$sql = "SELECT COUNT(DISTINCT aa.subid) as contribs, DATE_FORMAT(MIN(res.publish_up), '%d %b %Y') AS first_contrib, DATE_FORMAT(MAX(res.publish_up), '%d %b %Y') AS last_contrib FROM #__resources res, #__author_assoc aa, #__resource_types restypes WHERE res.id = aa.subid AND res.type = restypes.id AND aa.authorid = '".$authorid."' AND res.published = 1 AND res.access != 1 AND res.access != 4 AND aa.subtable = 'resources' AND standalone = 1";
		
		$database->setQuery( $sql );
		$results = $database->loadObjectList();
		
		$contribution = array();
		$contribution['contribs'] = '';
		$contribution['first'] = '';
		$contribution['last'] = '';
		
		if ($results) {
			foreach ($results as $row) 
			{
				$contribution['contribs'] = $row->contribs;
				$contribution['first'] = $row->first_contrib;
				$contribution['last'] = $row->last_contrib;
	        }
		}
		
		return $contribution;
	}

	//-----------

	public function get_simcount($resid, $period)
	{
		$database =& JFactory::getDBO();
		
		$sql = 'SELECT jobs FROM #__resource_stats_tools WHERE resid="'.$resid.'" AND period="'.$period.'" ORDER BY datetime DESC LIMIT 1';

		$database->setQuery( $sql );
		$result = $database->loadResult();
		if ($result) {
			return $result;
		}
		
		return 0;
	}

	//-----------
	
	public function get_usercount($resid, $period, $restype='0') 
	{
		$database =& JFactory::getDBO();
		
		if ($restype == '7') {
			$table = "#__resource_stats_tools";
		} else {
			$table = "#__resource_stats";
		}

		$data = '-';
		$sql = "SELECT MAX(datetime), users FROM ".$table." WHERE resid = '".$resid."' AND period = '".$period."' GROUP BY datetime ORDER BY datetime DESC LIMIT 1";

		$database->setQuery( $sql );
		$results = $database->loadObjectList();
		if ($results) {
			foreach ($results as $row) 
			{
				$data = $row->users;
			}
		}
		
		return $data;
	}
	
	//-----------
	
	public function get_citationcount($resid, $authorid=0) 
	{
		$database =& JFactory::getDBO();
		
		if ($authorid) {
			$sql = 'SELECT COUNT(DISTINCT (c.id) ) FROM #__citations c, #__citations_assoc ca, #__author_assoc aa, #__resources r WHERE c.id = ca.cid AND r.id = ca.oid AND r.id = aa.subid AND  aa.subtable = "resources" AND ca.table = "resource" AND r.published = "1" AND r.standalone = "1" AND aa.authorid = "'.$authorid.'"';
		} else {
			$sql = 'SELECT COUNT( DISTINCT (c.id) ) AS citations FROM #__resources r, #__citations c, #__citations_assoc ca WHERE r.id = ca.oid AND ca.cid = c.id AND ca.table = "resource" AND standalone = "1" AND r.id = "'.$resid.'"';
		}
		
		$database->setQuery( $sql );
		$result = $database->loadResult();
		if ($result) {
			return $result;
		} else {
			return '-';
		}
	}
	
	//-----------
	
	public function get_rank($authorid) 
	{
		$database =& JFactory::getDBO();
		
		$rank = 1;
		$sql = "SELECT aa.authorid, COUNT(DISTINCT aa.subid) as contribs 
				FROM #__resources res, #__author_assoc aa, #__resource_types restypes 
				WHERE res.id = aa.subid AND res.type = restypes.id AND res.published = 1 AND res.access != 1 AND res.access != 4 AND aa.subtable = 'resources' AND standalone = 1 
				GROUP BY aa.authorid having contribs > (
					SELECT COUNT(DISTINCT aa.subid) as contribs 
					FROM #__resources res, #__author_assoc aa, #__resource_types restypes 
					WHERE res.id = aa.subid AND res.type = restypes.id AND res.published = 1 AND res.access != 1 AND res.access != 4 AND aa.subtable = 'resources' AND standalone = 1 AND aa.authorid = '".$authorid."'
				)"; 
		
		$database->setQuery( $sql );
		$results = $database->loadObjectList();
				
		if ($results) {
			foreach ($results as $row) 
			{
				$rank++;
	    	}
		}
		
		$sql = "SELECT COUNT(DISTINCT a.uidNumber) as authors 
				FROM #__xprofiles a, #__author_assoc aa, #__resources res 
				WHERE a.uidNumber=aa.authorid AND aa.subid=res.id AND aa.subtable='resources' AND res.published=1 AND res.access !=1 AND res.access!=4 AND res.standalone=1";
		
		$database->setQuery( $sql );
		$total_authors = $database->loadResult();
		
		$rank = $rank.' / '.$total_authors;
		return $rank;
	}
	
	//-----------
	
	public function get_total_stats($authorid, $user_type, $period) 
	{
		$database =& JFactory::getDBO();
		
		$sql = "SELECT ".$user_type." FROM #__author_stats WHERE authorid = '".$authorid."' AND period = '".$period."' ORDER BY datetime DESC LIMIT 1";
		
		$database->setQuery( $sql ); 
		return $database->loadResult();
	}
}
