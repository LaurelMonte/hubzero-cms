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

if ($modmymessages->error) {
	echo '<p class="error">'.JText::_('MOD_MYMESSAGES_MISSING_TABLE').'</p>'."\n";
} else {
	$juser =& JFactory::getUser();

	$rows = $modmymessages->rows;
?>
<div<?php echo ($modmymessages->moduleclass) ? ' class="'.$modmymessages->moduleclass.'"' : ''; ?>>
<?php if (count($rows) <= 0) { ?>
	<p><?php echo JText::_('MOD_MYMESSAGES_NO_MESSAGES'); ?></p>
<?php } else { ?>
	<ul class="expandedlist">
<?php
	foreach ($rows as $row)
	{
		if ($row->actionid) {
			$cls = 'actionitem';
		} else {
			$cls = 'box';
		}
		if ($row->component == 'support' || $row->component == 'com_support') {
			$fg = explode(' ',$row->subject);
			$fh = array_pop($fg);
			$row->subject = implode(' ',$fg);
		}
?>
		<li class="<?php echo $cls; ?>">
			<a href="<?php echo JRoute::_('index.php?option=com_members&id='.$juser->get('id').'&active=messages&msg='.$row->id); ?>"><?php echo stripslashes($row->subject); ?></a>
			<span><span><?php echo JHTML::_('date', $row->created, '%d %b, %Y %I:%M %p'); ?></span></span>
		</li>
<?php
	}
?>
	</ul>
<?php } ?>
	<ul class="module-nav">
		<li><a href="<?php echo JRoute::_('index.php?option=com_members&id='. $juser->get('id') .'&active=messages'); ?>"><?php echo JText::_('MOD_MYMESSAGES_ALL_MESSAGES'); ?></a></li>
		<li><a href="<?php echo JRoute::_('index.php?option=com_members&id='. $juser->get('id') .'&active=messages&task=settings'); ?>"><?php echo JText::_('MOD_MYMESSAGES_MESSAGE_SETTINGS'); ?></a></li>
	</ul>
</div>
<?php } ?>