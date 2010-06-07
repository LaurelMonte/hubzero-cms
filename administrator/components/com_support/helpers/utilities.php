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

//----------------------------------------------------------
// Support Utilities class
//----------------------------------------------------------

class SupportUtilities 
{
	public function sendEmail($email, $subject, $message, $from) 
	{
		if ($from) {
			$args = "-f '" . $from['email'] . "'";
			$headers  = "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/plain; charset=utf-8\n";
			$headers .= 'From: ' . $from['name'] .' <'. $from['email'] . ">\n";
			$headers .= 'Reply-To: ' . $from['name'] .' <'. $from['email'] . ">\n";
			$headers .= "X-Priority: 3\n";
			$headers .= "X-MSMail-Priority: High\n";
			$headers .= 'X-Mailer: '. $from['name'] .n;
			if (mail($email, $subject, $message, $headers, $args)) {
				return(1);
			}
		}
		return(0);
	}
	
	//-----------

	public function checkValidLogin($login) 
	{
		if (eregi("^[_0-9a-zA-Z]+$", $login)) {
			return(1);
		} else {
			return(0);
		}
	}
	
	//-----------

	public function checkValidEmail($email) 
	{
		if (eregi("^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$", $email)) {
			return(1);
		} else {
			return(0);
		}
	}
	
	//-----------
	
	public function getSeverities($severities) 
	{
		if ($severities) {
			$s = array();
			$svs = explode(',', $severities);
			foreach ($svs as $sv) 
			{
				$s[] = trim($sv);
			}
		} else {
			$s = array('critical','major','normal','minor','trivial');
		}
		return $s;
	}
	
	//-----------
	
	public function getFilters()
	{
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();
		
		// Query filters defaults
		$filters = array();
		$filters['search'] = '';
		$filters['status'] = 'open';
		$filters['type'] = 0;
		$filters['owner'] = '';
		$filters['reportedby'] = '';
		$filters['severity'] = 'normal';
		$filters['severity'] = '';
		//$filters['section'] = 0;
		//$filters['category'] = '';
		$filters['sort'] = trim($app->getUserStateFromRequest($this->_option.'.tickets.sort', 'filter_order', 'created'));
		$filters['sortdir'] = trim($app->getUserStateFromRequest($this->_option.'.tickets.sortdir', 'filter_order_Dir', 'DESC'));
		
		// Paging vars
		$filters['limit'] = $app->getUserStateFromRequest($this->_option.'.tickets.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$filters['start'] = $app->getUserStateFromRequest($this->_option.'.tickets.limitstart', 'limitstart', 0, 'int');
		
		// Incoming
		$filters['_find'] = urldecode(trim($app->getUserStateFromRequest($this->_option.'.tickets.find', 'find', '')));
		$filters['_show'] = urldecode(trim($app->getUserStateFromRequest($this->_option.'.tickets.show', 'show', '')));
		
		// Break it apart so we can get our filters
		// Starting string hsould look like "filter:option filter:option"
		if ($filters['_find'] != '') {
			$chunks = explode(' ', $filters['_find']);
			$filters['_show'] = '';
		} else {
			$chunks = explode(' ', $filters['_show']);
		}
		
		// Loop through each chunk (filter:option)
		foreach ($chunks as $chunk) 
		{
			// Break each chunk into its pieces (filter, option)
			$pieces = explode(':', $chunk);
			
			// Find matching filters and ensure the vaule provided is valid
			switch ($pieces[0])
			{
				case 'q':
					$pieces[0] = 'search';
					if (isset($pieces[1])) {
						// Queries must be in quotes. If they're not, we ignore it
						if ((substr($pieces[1], 0, 1) == '"' 
						|| substr($pieces[1], 0, 1) == "'") 
						&& (substr($pieces[1], -1) == '"' 
						|| substr($pieces[1], -1) == "'")) {
							$pieces[1] = substr($pieces[1], 1, -1);  // Remove any surrounding quotes
						}
					} else {
						$pieces[1] = $filters[$pieces[0]];
					}
				break;
				case 'status':
					$allowed = array('open','closed','all','waiting','new');
					if (!in_array($pieces[1],$allowed)) {
						$pieces[1] = $filters[$pieces[0]];
					}
				break;
				case 'type':
					$allowed = array('submitted'=>0,'automatic'=>1,'none'=>2,'tool'=>3);
					if (in_array($pieces[1],$allowed)) {
						//$pieces[1] = ($pieces[1] == $allowed[0]) ? 0 : 1;
						$pieces[1] = $allowed[$pieces[1]];
					} else {
						$pieces[1] = 0;
					}
				break;
				case 'owner':
				case 'reportedby':
					if (isset($pieces[1])) {
						if ($pieces[1] == 'me') {
							$juser =& JFactory::getUser();
							$pieces[1] = $juser->get('username');
						} else if ($pieces[1] == 'none') {
							$pieces[1] = 'none';
						}
					}
				break;
				case 'severity':
					$allowed = array('critical', 'major', 'normal', 'minor', 'trivial');
					if (!in_array($pieces[1],$allowed)) {
						$pieces[1] = $filters[$pieces[0]];
					}
				break;
			}
			
			$filters[$pieces[0]] = (isset($pieces[1])) ? $pieces[1] : '';
		}

		// Check if we have a section:category
		/*$secat = trim(JRequest::getVar( 'category', '' ));
		if ($secat) {
			// Break it apart to get the individual pieces
			$bits = explode(':',$filters['category']);
			$filters['category'] = end($bits);
			$filters['section'] = $bits[0];
		}*/

		// Return the array
		return $filters;
	}
}
