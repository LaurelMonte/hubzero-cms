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

class modEventsCalendar
{
	private $attributes = array();

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
		$lang =& JFactory::getLanguage();
		$Config_lang = $lang->getBackwardLang();

		// Check the events component
		if (file_exists( JPATH_ROOT.DS.'components'.DS.'com_events'.DS.'events.html.php' ) ) { 
			include_once( JPATH_ROOT.DS.'components'.DS.'com_events'.DS.'events.html.php' );
			include_once( JPATH_ROOT.DS.'components'.DS.'com_events'.DS.'events.date.php');
			include_once( JPATH_ROOT.DS.'components'.DS.'com_events'.DS.'events.repeat.php');
		} else { 
			return JText::_('EVENTS_COMPONENT_REQUIRED');   
		}
		
		// Get the module parameters
		$params =& $this->params;
		
		// Display last month?
		$displayLastMonth = $params->get( 'display_last_month' );
		switch ($displayLastMonth)
		{
			case 'YES_stop':
				$disp_lastMonthDays = abs(intval( $params->get( 'display_last_month_days' ) ));
				$disp_lastMonth = 1;
				break;
			case 'YES_stop_events':
				$disp_lastMonthDays = abs(intval( $params->get( 'display_last_month_days' ) ));
				$disp_lastMonth = 2;
				break;
			case 'ALWAYS':
				$disp_lastMonthDays = 0;
				$disp_lastMonth = 1;
				break;
			case 'ALWAYS_events':
				$disp_lastMonthDays = 0;
				$disp_lastMonth = 2;
				break;
			case 'NO':
			default:
				$disp_lastMonthDays = 0;
				$disp_lastMonth = 0;
				break;
		}

		// Display next month?
		$displayNextMonth = $params->get( 'display_next_month' );
		switch ($displayNextMonth) 
		{
			case 'YES_stop':
				$disp_nextMonthDays = abs(intval( $params->get( 'display_next_month_days' ) ));
				$disp_nextMonth = 1;
				break;
			case 'YES_stop_events':
				$disp_nextMonthDays = abs(intval( $params->get( 'display_next_month_days' ) ));
				$disp_nextMonth = 2;
				break;
			case 'ALWAYS':
				$disp_nextMonthDays = 0;
				$disp_nextMonth = 1;
				break;
			case 'ALWAYS_events':
				$disp_nextMonthDays = 0;
				$disp_nextMonth = 2;
				break;
			case 'NO':
			default:
				$disp_nextMonthDays = 0;
				$disp_nextMonth = 0;
				break;
		}
		
		// Get the time with offset
		$config = JFactory::getConfig();
		$timeWithOffset = time() + ($config->getValue('config.offset')*60*60);

		// Get the start day
		$startday = $params->get( 'start_day' );
		if (!defined('_CAL_CONF_STARDAY')) {
			define('_CAL_CONF_STARDAY',$startday);
		}
		//define('_CAL_CONF_DATEFORMAT',1);
		//define('_CAL_CONF_MAILVIEW','YES');
		if ((!$startday) || ($startday > 1)) {
			$startday = 0;
		}

		// An array of the names of the days of the week
		$day_name = array(
				JText::_('EVENTS_CAL_LANG_SUNDAYSHORT'),
				JText::_('EVENTS_CAL_LANG_MONDAYSHORT'),
				JText::_('EVENTS_CAL_LANG_TUESDAYSHORT'),
				JText::_('EVENTS_CAL_LANG_WEDNESDAYSHORT'),
				JText::_('EVENTS_CAL_LANG_THURSDAYSHORT'),
				JText::_('EVENTS_CAL_LANG_FRIDAYSHORT'),
				JText::_('EVENTS_CAL_LANG_SATURDAYSHORT')
			);

		$content = '';

		// Display a calendar. Want to show 1,2, or 3 calendars optionally
		// depending upon module parameters. (IE. Last Month, This Month, or Next Month)

		$thisDayOfMonth = date("j", $timeWithOffset);
		$daysLeftInMonth = date("t", $timeWithOffset) - date("j", $timeWithOffset) + 1;

		// Display last month?
		if ($disp_lastMonth && (!$disp_lastMonthDays || $thisDayOfMonth <= $disp_lastMonthDays)) {
			// Build last month calendar
			$content .= $this->calendar($timeWithOffset, $startday, mktime(0,0,0,date("n")-1,1,date("Y")), JText::_('_CAL_LANG_LAST_MONTH'), $day_name, $disp_lastMonth == 2);
		}
		
		// Build this month
		$content .= $this->calendar($timeWithOffset, $startday, mktime(0,0,0,date("n"),1,date("Y")), JText::_('EVENTS_CAL_LANG_THIS_MONTH'), $day_name);
		
		// Display next month?
		if ($disp_nextMonth && (!$disp_nextMonthDays || $daysLeftInMonth <= $disp_nextMonthDays)) {
			// Build next month calendar
			$content .= $this->calendar($timeWithOffset, $startday, mktime(0,0,0,date("n")+1,1,date("Y")), JText::_('_CAL_LANG_NEXT_MONTH'), $day_name, $disp_nextMonth == 2);
		}
		
		return $content;
	}
	
	//-----------
	
	public function calendar( $timeWithOffset, $startday, $time, $linkString, &$day_name, $monthMustHaveEvent=false )
	{
		$database =& JFactory::getDBO();

		ximport('xdocument');
		XDocument::addModuleStyleSheet('mod_events_cal');

		$juser =& JFactory::getUser();
		$gid = $juser->get('gid');

		$cal_year  = date("Y",$time);
		$cal_month = date("m",$time);
		$calmonth  = date("n",$time);
		$to_day    = date("Y-m-d", $timeWithOffset);
		
		// Start building the table
		$content  = '<table class="mod_events_calendar" summary="'.JText::_('TABLE_SUMMARY').'">'."\n";
		$content .= ' <caption><a class="monthyear" href="'.JRoute::_('index.php?option=com_events&amp;year='.$cal_year.'&amp;month='.$cal_month).'">'.EventsHtml::getMonthName($cal_month).'</a></caption>'."\n";
		$content .= ' <thead>'."\n";
	    $content .= '  <tr>'."\n";
		// Days name rows
		for ($i=0;$i<7;$i++) 
		{
			$content.='   <th>'.$day_name[($i+$startday)%7].'</th>'."\n";
		}
		$content .= '  </tr>'."\n";
		$content .= ' </thead>'."\n";
		$content .= ' <tbody>'."\n";
		$content .= '  <tr>'."\n";

		// Fix to fill in end days out of month correctly
		$dayOfWeek = $startday;
		$start = (date("w",mktime(0,0,0,$cal_month,1,$cal_year))-$startday+7)%7;
		$d = date("t",mktime(0,0,0,$cal_month,0,$cal_year))-$start + 1;
		$kownt = 0;
		for ($a=$start; $a>0; $a--) 
		{
			$content .= '   <td class="daylink">&nbsp;</td>'."\n";
			$dayOfWeek++;
			$kownt++;
		}

		$monthHasEvent = false;
		$eventCheck = new EventsRepeat;
		$lastDayOfMonth = date("t",mktime(0,0,0,$cal_month,1,$cal_year));
		$rd = 0;
		for ($d=1;$d<=$lastDayOfMonth;$d++) 
		{ 
			$do = ($d<10) ? "0$d" : "$d";
			$selected_date = "$cal_year-$cal_month-$do";

			$sql = "SELECT #__events.* FROM #__events, #__categories as b"
				. "\n WHERE #__events.catid = b.id AND b.access <= $gid AND #__events.access <= $gid"
				. "\n AND ((publish_up >= '$selected_date 00:00:00' AND publish_up <= '$selected_date 23:59:59')"
				. "\n OR (publish_down >= '$selected_date 00:00:00' AND publish_down <= '$selected_date 23:59:59')"
				. "\n OR (publish_up <= '$selected_date 00:00:00' AND publish_down >= '$selected_date 23:59:59')) AND state='1'"
				. "\n ORDER BY publish_up ASC";

			$database->setQuery($sql);
			$rows = $database->loadObjectList();
			$mark_bold = '';
			$mark_close_bold = '';
			$class = ($selected_date == $to_day) ? 'todaynoevents' : 'daynoevents';       

			for ($r = 0; $r < count($rows); $r++) 
			{
				if ($eventCheck->EventsRepeat($rows[$r], $cal_year, $cal_month, $do)) {
					$monthHasEvent = true;
					$mark_bold = '<b>';
					$mark_close_bold = '</b>';
					$class = ($selected_date == $to_day) ? 'todaywithevents' : 'daywithevents';
					break;
				}
			}

			// Only adds link if event scheduled that day
			$content.='   <td class="'.$class.'">';
			if ($class == 'todaywithevents' || $class == 'daywithevents') {
				$content .= '<a class="mod_events_daylink" href="'.JRoute::_('index.php?option=com_events&year='.$cal_year.'&month='.$cal_month.'&day='.$do).'">'.$d.'</a>';
			} else {
				$content .= "$d";
			}
	        $content .= '</td>'."\n";
			$rd++;
			
			// Check if Next week row
			if ((1 + $dayOfWeek++)%7 == $startday) {
				$content .= '  </tr>'."\n".'  <tr>'."\n";
				$rd = ($rd >= 7) ? 0 : $rd;
			}
		}
		
		// Fill in any blank days for the rest of the row
		for ($d=$rd;$d<=6;$d++) 
		{
			$content .= '   <td>&nbsp;</td>'."\n";
		}

		// Finish off the table
		$content .= '  </tr>'."\n";
		$content .= ' </tbody>'."\n";
		$content .= '</table>'."\n";
		
		// Now check to see if this month needs to have at least 1 event in order to display
	    if (!$monthMustHaveEvent || $monthHasEvent) {
			return $content;
	    } else {
			return '';
		}
	}
}
