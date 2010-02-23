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
JPlugin::loadLanguage( 'plg_usage_region' );

//-----------

class plgUsageRegion extends JPlugin
{
	public function plgUsageRegion(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'usage', 'region' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	//-----------

	public function onUsageAreas()
	{
		$areas = array(
			'region' => JText::_('PLG_USAGE_REGION')
		);
		return $areas;
	}
	
	//----------------------------------------------------------
	//  Print Region List from Database
	//----------------------------------------------------------
	
	private function regionlist(&$db, $region, $t=0, $enddate = 0) 
	{
		// Set region list parameters...
		$hub = 1;
		if (!$enddate) {
			$dtmonth = date("m") - 1;
			$dtyear = date("Y");
			if (!$dtmonth) {
				$dtmonth = 12;
				$dtyear = $dtyear - 1;
			}
			$enddate = $dtyear . "-" . $dtmonth;
		}

		// Look up region list information...
		$regionname = '';
		$sql = "SELECT name, valfmt, size FROM regions WHERE region = '" . mysql_escape_string($region) . "'";
		$db->setQuery( $sql );
		$result = $db->loadRow();
		if ($result) {
			$regionname = $result[0];
			$valfmt = $result[1];
			$size = $result[2];
		}
		$html = '';
		if ($regionname) {
			// Prepare some date ranges...
			$enddate .= "-00";
			$dtmonth = floor(substr($enddate, 5, 2));
			$dtyear = floor(substr($enddate, 0, 4));
			$dt = $dtyear . "-" . sprintf("%02d", $dtmonth) . "-00";
			$dtmonthnext = floor(substr($enddate, 5, 2) + 1);
			$dtyearnext = $dtyear + 1;
			if ($dtmonthnext > 12) {
				$dtmonthnext = 1;
				$dtyearnext++;
			}
			$dtyearprior = substr($enddate, 0, 4) - 1;
			$monthtext = date("F", mktime(0, 0, 0, $dtmonth, 1, $dtyear)) . " " . $dtyear;
			$yeartext = "Jan - " . date("M", mktime(0, 0, 0, $dtmonth, 1, $dtyear)) . " " . $dtyear;
			$twelvetext = date("M", mktime(0, 0, 0, $dtmonthnext, 1, $dtyear)) . " " . $dtyearprior . " - " . date("M", mktime(0, 0, 0, $dtmonth, 1, $dtyear)) . " " . $dtyear;
			$period = array(
				array("key" => 1,  "name" => $monthtext),
				array("key" => 0,  "name" => $yeartext),
				array("key" => 12, "name" => $twelvetext)
			);

			// Process each different date/time periods/range...
			$maxrank = 0;
			$regionlist = array();
			for ($pidx = 0; $pidx < count($period); $pidx++) 
			{
				// Calculate the total value for this regionlist...
				$regionlistset = array();
				$sql = "SELECT regionvals.name, regionvals.value FROM regions, regionvals WHERE regions.region = regionvals.region AND regionvals.hub = '" . mysql_escape_string($hub) . "' AND regions.region = '" . mysql_escape_string($region) . "' AND regionvals.datetime = '" . mysql_escape_string($dt) . "' AND regionvals.period = '" . mysql_escape_string($period[$pidx]["key"]) . "' AND regionvals.rank = '0'";
				$db->setQuery( $sql );
				$results = $db->loadObjectList();
				if ($results) {
					foreach ($results as $row) 
					{
						$formattedval = UsageHelper::valformat($row->value, $valfmt);
						if (strstr($formattedval, "day") !== FALSE) {
							$chopchar = strrpos($formattedval, ",");
							if ($chopchar !== FALSE) {
								$formattedval = substr($formattedval, 0, $chopchar) . "+";
							}
						}
						array_push($regionlistset, array($row->name, $row->value, $formattedval, sprintf("%0.1f%%", 100)));
					}
				}
				if (!count($regionlistset)) {
					array_push($regionlistset, array("n/a", 0, "n/a", "n/a"));
				}

				// Calculate the region values for the regionlist...
				$rank = 1;
				$sql = "SELECT regionvals.rank, regionvals.name, regionvals.value FROM regions, regionvals WHERE regions.region = regionvals.region AND regionvals.hub = '" . mysql_escape_string($hub) . "' AND regions.region = '" . mysql_escape_string($region) . "' AND datetime = '" . mysql_escape_string($dt) . "' AND regionvals.period = '" . mysql_escape_string($period[$pidx]["key"]) . "' AND regionvals.rank > '0' ORDER BY regionvals.rank, regionvals.name";
				$db->setQuery( $sql );
				$results = $db->loadObjectList();
				if ($results) {
					foreach ($results as $row)
					{
						if ($row->rank > 0 && (!$size || $row->rank <= $size)) {
							while ($rank < $row->rank) 
							{
								array_push($regionlistset, array("n/a", 0, "n/a", "n/a"));
								$rank++;
							}
							$formattedval = UsageHelper::valformat($row->value, $valfmt);
							if (strstr($formattedval, "day") !== FALSE) {
								$chopchar = strrpos($formattedval, ",");
								if ($chopchar !== FALSE) {
									$formattedval = substr($formattedval, 0, $chopchar) . "+";
								}
							}
							if ($regionlistset[0][1] > 0) {
								array_push($regionlistset, array($row->name, $row->value, $formattedval, sprintf("%0.1f%%", (100 * $row->value / $regionlistset[0][1]))));
							} else {
								array_push($regionlistset, array($row->name, $row->value, $formattedval, "n/a"));
							}
							$rank++;
						}
					}
				}
				while ($rank <= $size || $rank == 1) 
				{
					array_push($regionlistset, array("n/a", 0, "n/a", "n/a"));
					$rank++;
				}
				array_push($regionlist, $regionlistset);
				if ($rank > $maxrank) {
					$maxrank = $rank;
				}
			}
			
			$cls = 'even';
			
			// Print region list table...
			$html .= '<table summary="'.$regionname.'">'."\n";
			$html .= "\t".'<caption>'.JText::_('Table').' '.$t.': '.$regionname.'</caption>'."\n";
			$html .= "\t".'<thead>'."\n";
			$html .= "\t\t".'<tr>'."\n";
			for ($pidx = 0; $pidx < count($period); $pidx++) 
			{
				$html .= "\t\t\t".'<th colspan="3" scope="colgroup">'. $period[$pidx]["name"] .'</th>'."\n";
			}
			$html .= "\t\t".'</tr>'."\n";
			$html .= "\t".'</thead>'."\n";
			$html .= "\t".'<tbody>'."\n";
			
			$cls = ($cls == 'even') ? 'odd' : 'even';
			$html .= "\t\t".'<tr class="summary">'."\n";
			$k = 0;
			for ($pidx = 0; $pidx < count($period); $pidx++) 
			{
				$k++;
				$tdcls = ($k != 2) ? ' class="group"' : '';
				$html .= "\t\t\t".'<th'.$tdcls.' scope="row">'. $regionlist[$pidx][0][0] .'</th>'."\n";
				$html .= "\t\t\t".'<td'.$tdcls.'>'. $regionlist[$pidx][0][2] .'</td>'."\n";
				$html .= "\t\t\t".'<td'.$tdcls.'>'. $regionlist[$pidx][0][3] .'</td>'."\n";
			}
			$html .= "\t\t".'</tr>'."\n";
			
			for ($i = 1; $i < $maxrank; $i++) 
			{
				$cls = ($cls == 'even') ? 'odd' : 'even';
				$k = 0;
				$html .= "\t\t".'<tr class="'. $cls .'">'."\n";
				for ($pidx = 0; $pidx < count($period); $pidx++) 
				{
					$k++;
					$tdcls = ($k != 2) ? ' class="group"' : '';
					$html .= "\t\t\t".'<th'.$tdcls.' scope="row">';
					$html .= (isset($regionlist[$pidx][$i][0])) ? $regionlist[$pidx][$i][0] : ''; 
					$html .= '</th>'."\n";
					$html .= "\t\t\t".'<td'.$tdcls.'>';
					$html .= (isset($regionlist[$pidx][$i][2])) ? $regionlist[$pidx][$i][2] : '';
					$html .= '</td>'."\n";
					$html .= "\t\t\t".'<td'.$tdcls.'>';
					$html .= (isset($regionlist[$pidx][$i][3])) ? $regionlist[$pidx][$i][3] : '';
					$html .= '</td>'."\n";
				}
				$html .= "\t\t".'</tr>'."\n";
			}
			$html .= "\t".'</tbody>'."\n";
			$html .= '</table>'."\n";
		}
		return $html;
	}
	
	//-----------
	
	public function onUsageDisplay( $option, $task, $db, $months, $monthsReverse, $enddate ) 
	{
		// Check if our task is the area we want to return results for
		if ($task) {
			if (!in_array( $task, $this->onUsageAreas() ) 
			 && !in_array( $task, array_keys( $this->onUsageAreas() ) )) {
				return '';
			}
		}
		
		// Set tome vars
		$thisyear = date("Y");
		
		$o = UsageHelper::options( $db, $enddate, $thisyear, $monthsReverse, 'check_for_regiondata' );

		// Build HTML
		$html  = '<form method="post" action="'. JRoute::_('index.php?option='.$option.'&task='.$task) .'">'."\n";
		$html .= "\t".'<fieldset class="filters">'."\n";
		$html .= "\t\t".'<label>'."\n";
		$html .= "\t\t\t".JText::_('PLG_USAGE_SHOW_DATA_FOR').': '."\n";
		$html .= "\t\t\t".'<select name="selectedPeriod" id="selectedPeriod">'."\n";
		$html .= $o;
		$html .= "\t\t\t".'</select>'."\n";
		$html .= "\t\t".'</label> <input type="submit" value="'.JText::_('PLG_USAGE_VIEW').'" />'."\n";
		$html .= "\t".'</fieldset>'."\n";
		$html .= '</form>'."\n";
		$html .= $this->regionlist($db, 1, 1, $enddate);
		$html .= $this->regionlist($db, 2, 2, $enddate);
		$html .= $this->regionlist($db, 5, 3, $enddate);
		$html .= $this->regionlist($db, 4, 4, $enddate);
		$html .= $this->regionlist($db, 6, 5, $enddate);
		$html .= $this->regionlist($db, 3, 6, $enddate);
		$html .= $this->regionlist($db, 7, 7, $enddate);

		// Return HTML
		return $html;
	}
}
?>