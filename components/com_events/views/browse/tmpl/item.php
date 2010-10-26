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

$this->row->content = stripslashes($this->row->content);
$this->row->content = str_replace('<br />','',$this->row->content);
//$this->row->content = EventsHtml::encode_html($this->row->content);

//$fields = $config->getCfg('fields');
if (!empty($this->fields)) {
	for ($i=0, $n=count( $this->fields ); $i < $n; $i++) 
	{
		// explore the text and pull out all matches
		array_push($this->fields[$i], EventsController::parseTag($this->row->content, $this->fields[$i][0]));
		// clean the original text of any matches
		$this->row->content = str_replace('<ef:'.$this->fields[$i][0].'>'.end($this->fields[$i]).'</ef:'.$this->fields[$i][0].'>','',$this->row->content);
	}
	$this->row->content = trim($this->row->content);
}

$event_up = new EventsDate( $this->row->publish_up );
$this->row->start_date = EventsHtml::getDateFormat($event_up->year,$event_up->month,$event_up->day,0);
$this->row->start_time = (defined('_CAL_USE_STD_TIME') && _CAL_USE_STD_TIME == 'YES') 
				 ? $event_up->get12hrTime() 
				 : $event_up->get24hrTime();

$event_down = new EventsDate( $this->row->publish_down );
$this->row->stop_date = EventsHtml::getDateFormat($event_down->year,$event_down->month,$event_down->day,0);
$this->row->stop_time = (defined('_CAL_USE_STD_TIME') && _CAL_USE_STD_TIME == 'YES') 
				? $event_down->get12hrTime() 
				: $event_down->get24hrTime();

$html  = "\t".'<li id="event'.$this->row->id.'">'."\n";
$html .= "\t\t".'<dl class="event-details">'."\n";
if ($this->row->start_date == $this->row->stop_date) {
	if ($this->showdate) {
		$html .= "\t\t\t".'<dt>'.JHTML::_('date',$this->row->publish_up, '%d %b, %Y', 0).'</dt>'."\n";
	}
	$html .= "\t\t\t".'<dd class="starttime">'.JHTML::_('date',$this->row->publish_up, '%I:%M %p', 0).'</dd>'."\n";
	$html .= "\t\t\t".'<dd class="endtime">'.strtolower(JText::_('EVENTS_CAL_LANG_TO')).' '.JHTML::_('date',$this->row->publish_down, '%I:%M %p', 0).'</dd>'."\n";
} else {
	if ($this->showdate) {
		$html .= "\t\t\t".'<dt class="starttime">'.JHTML::_('date',$this->row->publish_up, '%d %b, %Y', 0).'</dt>'."\n";
	}
	$html .= "\t\t\t".'<dd class="starttime">'.JHTML::_('date',$this->row->publish_up, '%I:%M %p', 0).'</dd>'."\n";
	if ($this->showdate) {
		$html .= "\t\t\t".'<dt class="endtime">'.strtolower(JText::_('EVENTS_CAL_LANG_TO')).' '.JHTML::_('date',$this->row->publish_down, '%d %b, %Y', 0).'</dt>'."\n";
	}
	$html .= "\t\t\t".'<dd class="endtime">'.JHTML::_('date',$this->row->publish_down, '%I:%M %p', 0).'</dd>'."\n";
}
$html .= "\t\t".'</dl><div class="ewrap">'."\n";
$html .= "\t\t".'<p class="title"><a href="'. JRoute::_('index.php?option='.$this->option.'&task=details&id='.$this->row->id) .'">'. stripslashes($this->row->title) .'</a></p>'."\n";
$html .= "\t\t".'<p class="category"><strong>Category:</strong> '. stripslashes($this->categories[$this->row->catid]) .'</p>'."\n";
$info = '';
foreach ($this->fields as $field) 
{
	if ($field[4] == 1 && end($field) != '') {
		$info .= "\t\t".'<p class="'.$field[0].'"><strong>'. $field[1] .':</strong> '. end($field) .'</p>'."\n";
	}
}
$html .= $info;
if (!$info) {
	$html .= "\t\t".'<p class="description">'. Hubzero_View_Helper_Html::shortenText($this->row->content,300,0) .'</p>'."\n";
}
$html .= "\t".'</div></li>'."\n";

echo $html;
?>