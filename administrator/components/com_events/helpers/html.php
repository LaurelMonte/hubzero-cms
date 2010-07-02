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

if (!defined("n")) {
	define("t","\t");
	define("n","\n");
	define("br","<br />");
	define("sp","&#160;");
	define("a","&amp;");
}

class EventsHtml 
{
	public function error( $msg )
	{
		return '<p class="error">'.$msg.'</p>';
	}
	
	//-----------
	
	public function alert( $msg )
	{
		return "<script> alert('".$msg."'); window.history.go(-1); </script>";
	}
	
	//-----------
	
	public function buildRadioOption( $arr, $tag_name, $tag_attribs, $key, $text, $selected ) 
	{  
		$html = '';
		for ($i=0, $n=count( $arr ); $i < $n; $i++ ) 
		{
			$k = $arr[$i]->$key;
			$t = $arr[$i]->$text;
			
			$sel = '';
			if (is_array( $selected )) {
				foreach ($selected as $obj) 
				{
					$k2 = $obj->$key;
					if ($k == $k2) {
						$sel = ' checked="checked"';
						break;
					}
				}
			} else {
				$sel = ($k == $selected ? ' checked="checked"' : '');
			}
			$html .= '<label><input name="'.$tag_name.'" id="'.$tag_name.$i.'" type="radio" value="'.$k.'"'.$sel.' '.$tag_attribs.'/>'.$t.'</label>'.n;
		}
		return $html;
	}
	
	//-----------
	
	public function buildCategorySelect($catid, $args, $gid, $option)
	{
		$database =& JFactory::getDBO();

		$catsql = "SELECT id AS value, name AS text FROM #__categories "
				. "WHERE section='$option' AND access<='$gid' AND published='1' ORDER BY ordering";	

		$categories[] = JHTML::_('select.option', '0', JText::_('EVENTS_CAL_LANG_EVENT_CHOOSE_CATEG'), 'value', 'text');

		$database->setQuery($catsql);
		$categories = array_merge( $categories, $database->loadObjectList() );
		$clist = JHTML::_('select.genericlist', $categories, 'catid', $args, 'value', 'text', $catid, false, false );
		
		echo $clist;
	}
	
	//-----------
	
	public function buildReccurDaySelect($reccurday, $tag_name, $args) 
	{
		$day_name = array('<span style="color:red;">'.JText::_('EVENTS_CAL_LANG_SUNDAYSHORT').'</span>',
							JText::_('EVENTS_CAL_LANG_MONDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_TUESDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_WEDNESDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_THURSDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_FRIDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_SATURDAYSHORT'));        
		$daynamelist[] = JHTML::_('select.option', '-1', '&nbsp;'.JText::_('EVENTS_CAL_LANG_BYDAYNUMBER').'<br />', 'value', 'text');
		for ($a=0; $a<7; $a++) 
		{
			$name_of_day = '&nbsp;'.$day_name[$a];
			$daynamelist[] = JHTML::_('select.option', $a, $name_of_day, 'value', 'text');
        }
		$tosend = EventsHtml::buildRadioOption( $daynamelist, $tag_name, $args, 'value', 'text', $reccurday );
		echo $tosend;
    }

	//-----------

	public function buildWeekDaysCheck($reccurweekdays, $args) 
	{
		$day_name = array('<span style="color:red;">'.JText::_('EVENTS_CAL_LANG_SUNDAYSHORT').'</span>',
							JText::_('EVENTS_CAL_LANG_MONDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_TUESDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_WEDNESDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_THURSDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_FRIDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_SATURDAYSHORT'));    
		$tosend = '';
		if ($reccurweekdays == '') {
			$split = array();
			$countsplit = 0;
		} else {
			$split = explode("|", $reccurweekdays);
			$countsplit = count($split);
		}
        
		for ($a=0; $a<7; $a++) 
		{
			$checked = '';
			for ($x = 0; $x < $countsplit; $x++) 
			{
				if ($split[$x] == $a) {
					$checked = 'checked="checked"';
				}
			}
			$tosend .= '<input type="checkbox" id="cb_wd'.$a.'" name="reccurweekdays" value="'.$a.'" '.$args.' '.$checked.'/>&nbsp;'.$day_name[$a].n;
		}
		echo $tosend;
	}

	//-----------

	public function buildWeeksCheck($reccurweeks, $args) 
	{
		$week_name = array('',
							JText::_('EVENTS_CAL_LANG_REP_WEEK').' 1<br />',
							JText::_('EVENTS_CAL_LANG_REP_WEEK').' 2<br />',
							JText::_('EVENTS_CAL_LANG_REP_WEEK').' 3<br />',
							JText::_('EVENTS_CAL_LANG_REP_WEEK').' 4<br />',
							JText::_('EVENTS_CAL_LANG_REP_WEEK').' 5<br />');        
		$tosend = '';
		$checked = '';
    
		if ($reccurweeks == '') {
			$split = array();
			$countsplit = 0;
		} else {
			$split = explode("|", $reccurweeks);
			$countsplit = count($split);
		}
        
		for ($a=1; $a<6; $a++) 
		{
			$checked = '';
			if ($reccurweeks == '') { 
				$checked = 'checked="checked"';
			}
			for ($x = 0; $x < $countsplit; $x++) 
			{
				if ($split[$x] == $a) {
					$checked = 'checked="checked"';
				}
			}
			$tosend .= '<input type="checkbox" id="cb_wn'.$a.'" name="reccurweeks" value="'.$a.'" '.$args.' '.$checked.'/>&nbsp;'.$week_name[$a].n;     
		}
		echo $tosend;
	}
	
	//-----------

	public function getLongDayName($daynb) 
	{
		$dayname = '';
		switch ($daynb) 
		{
			case '0': $dayname = JText::_('EVENTS_CAL_LANG_SUNDAY');    break;
			case '1': $dayname = JText::_('EVENTS_CAL_LANG_MONDAY');    break;
			case '2': $dayname = JText::_('EVENTS_CAL_LANG_TUESDAY');   break;
			case '3': $dayname = JText::_('EVENTS_CAL_LANG_WEDNESDAY'); break;
			case '4': $dayname = JText::_('EVENTS_CAL_LANG_THURSDAY');  break;
			case '5': $dayname = JText::_('EVENTS_CAL_LANG_FRIDAY');    break;
			case '6': $dayname = JText::_('EVENTS_CAL_LANG_SATURDAY');  break;
		}
		return $dayname;
	}

	//-----------

	public function getColorBar($event_id=NULL,$newcolor)
	{
		$database =& JFactory::getDBO();
		
		if ($event_id != NULL) {
			$database->setQuery( "SELECT color_bar FROM #__events WHERE id = '$event_id'" );
			$rows = $database->loadResultList();
			$row = $rows[0];
			if ($newcolor) {
				if ($newcolor <> $row->color_bar) {
					$database->setQuery( "UPDATE #__events SET color_bar = '$newcolor' WHERE id = '$event_id'" );
					return $newcolor;
				}
			} else {
				return $row->color_bar;
			}
		} else {
			// dmcd May 20/04  check the new config parameter to see what the default
			// color should be
			switch (_CAL_CONF_DEFCOLOR) 
			{
				case 'none':
					return '';
				case 'category':
					// fetch the category color for this event?
					// Note this won't work for a new event since
					// the user can change the category on-the-fly
					// in the event entry form.  We need to dump a
					// javascript array of all the category colors
					// into the event form so the color can track the
					// chosen category.
					return '';
				case 'random':
				default:
					$event_id = rand(1,50);
					// BAR COLOR GENERATION
					//$start_publish = mktime (0, 0, 0, date("m"),date("d"),date("Y"));
	                             
					//$colorgenerate = intval(($start_publish/$event_id));
					//$bg1color = substr($colorgenerate, 5, 1);
					//$bg2color = substr($colorgenerate, 3, 1);
					//$bg3color = substr($colorgenerate, 7, 1);
					$bg1color = rand(0,9);
					$bg2color = rand(0,9);
					$bg3color = rand(0,9);
					$newcolorgen = "#".$bg1color."F".$bg2color."F".$bg3color."F";
       
					return $newcolorgen;
			}
		}
	}
	
	//-----------

	private static $field_ordering = array(
		'name' => 0, 'email' => 1, 'telephone' => 2, 'affiliation' => 3, 'position' => 4, 'address' => 5, 
		'arrival' => 6, 'departure' => 7, 'website' => 8, 'gender' => 9, 'disability' => 10, 
		'dietary' => 11, 'dinner' => 12, 'abstract' => 13, 'comments' => 14, 'degree' => 15,
		'race' => 16, 
		'fax' => 17, 'title' => 18 // folded into previous entries
	);

	//-----------

	public function fieldSorter($a, $b)
	{
		return EventsHtml::$field_ordering[$a] < EventsHtml::$field_ordering[$b] ? -1 : 1;
	}

	//-----------

	public function quoteCsv($val)
	{
		if (!isset($val)) return '';
		if (strpos($val, "\n") !== false || strpos($val, ',') !== false)
			return '"'.str_replace(array('\\', '"'), array('\\\\', '""'), $val).'"';

		return $val;
	}

	//-----------

	public function quoteCsvRow($vals)
	{
		return implode(',', array_map(array('EventsHtml', 'quoteCsv'), $vals))."\n";
	}
	
	//-----------

	public function downloadlist($resp, $option)
	{
		$database =& JFactory::getDBO();
		$ee = new EventsEvent( $database );
		header('Content-type: text/comma-separated-values');
		header('Content-disposition: attachment; filename="eventrsvp.csv"');
		$fields = array_merge($ee->getDefinedFields(JRequest::getVar('id', array())), array('name'));
		// Output header
		usort($fields, array('EventsHtml', 'fieldSorter'));
		echo EventsHtml::quoteCsvRow(array_map('ucfirst', $fields));
		
		$rows = $resp->getRecords();
		
		// Get a list of IDs to query the race identification for all of them at once to avoid
		// querying for it in a loop later
		$race_ids = array();
		foreach ($rows as $re) 
		{
			$race_ids[$re->id] = array('identification' => '');
		}
			
		foreach (EventsRespondent::getRacialIdentification(array_keys($race_ids)) as $id=>$val) 
		{
			$race_ids[$id] = $val;
		}
		
		// Output rows
		foreach ($rows as $re)
		{
			$row = array(
				$re->last_name . ', ' . $re->first_name
			);
			// TODO: Oops, I should have made these fields match up better in the first place.
			foreach ($fields as $field) 
			{
				switch ($field)
				{
					case 'name': break;
					case 'position': $row[] = $re->position_description; break;
					case 'comments': $row[] = $re->comment; break;
					case 'degree': $row[] = $re->highest_degree; break;
					case 'race': $row[] = $race_ids[$re->id]['identification']; break;
					case 'address': 
						$address = array();
						if ($re->city) $address[] = $re->city;
						if ($re->state) $address[] = $re->state;
						if ($re->zip) $address[] = $re->zip;
						if ($re->country) $address[] = $re->country;
						$row[] = implode(', ', $address);
					break;
					case 'disability': $row[] = $re->disability_needs ? 'Yes' : 'No'; break;
					case 'dietary': $row[] = $re->dietary_needs; break;
					case 'dinner': $row[] = $re->attending_dinner ? 'Yes' : 'No'; break;
					default:
						$row[] = $re->$field;
					break;
				}
			}
			echo EventsHtml::quoteCsvRow($row);
		}
		exit;
	}
}
