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

function toolsBuildRoute(&$query)
{
	$segments = array();

	/*if (!empty($query['invoke'])) {
		$segments[] = 'invoke';
		$segments[] = $query['invoke'];
		unset($query['invoke']);
	}*/
	if (!empty($query['app'])) {
		$segments[] = $query['app'];
		unset($query['app']);
	}
	if (!empty($query['task'])) {
		$segments[] = $query['task'];
		unset($query['task']);
	}
	if (!empty($query['version'])) {
		$segments[] = $query['version'];
		unset($query['version']);
	}
	if (isset($query['sess'])) {
		$segments[] = $query['sess'];
		unset($query['sess']);
	}

	return $segments;
}

function toolsParseRoute($segments)
{
	$vars = array();

	if (empty($segments)) {
		return $vars;
	}

	if (isset($segments[0])) {
		switch ($segments[0]) 
		{
			case 'login':
			case 'accessdenied':
			case 'quotaexceeded':
			case 'storageexceeded':
			case 'storage':
			case 'rename':
			case 'diskusage':
			case 'purge':
			//case 'share':
			//case 'unshare':
			//case 'invoke':
			//case 'view':
			//case 'stop':
			case 'images':
			case 'listfiles':
			case 'download':
			case 'deletefolder':
			case 'deletefile':
				$vars['task'] = $segments[0];
			break;
			
			default:
				$vars['option'] = 'com_resources';
				$vars['alias'] = $segments[0];
			break;
		}
	}
	if (isset($segments[1])) {
		switch ($segments[1]) 
		{
			case 'invoke':
				$vars['option'] = 'com_tools';
				$vars['app'] = $segments[0];
				$vars['task'] = $segments[1];
				if (isset($segments[2])) {
					$vars['version'] = $segments[2];
				}
			break;
			case 'session':
			case 'share':
			case 'unshare':
			case 'stop':
				$vars['option'] = 'com_tools';
				$vars['app'] = $segments[0];
				$vars['task'] = $segments[1];
				if (isset($segments[2])) {
					$vars['sess'] = $segments[2];
				}
			break;
			case 'report':
				$xhub =& XFactory::getHub();
				$xhub->redirect(JRoute::_('index.php?option=com_support&task=tickets&find=group:app-' . $segments[0]));
			break;
			case 'forge.png':
				$vars['task'] = 'image';
			break;
			case 'site_css.cs':
				$vars['task'] = 'css';
			break;
			default:
				$vars['sess'] = $segments[1];
			break;
		}
		/*switch ($segments[1]) 
		{
			case 'accessdenied':
			case 'share':
			case 'unshare':
			case 'invoke':
			case 'view':
			case 'stop':
				$vars['option'] = 'com_tools';
				$vars['task'] = $segments[1];
			break;
			
			default:
				$vars['option'] = 'com_resources';
				$vars['alias'] = $segments[0];
			break;
		}*/
	}

	return $vars;
}

?>
