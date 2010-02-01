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

$cls = 'even';
?>
<h3><a name="usage"></a><?php echo JText::_('PLG_MEMBERS_USAGE'); ?></h3>
<div class="aside">
	<p class="info"><?php echo JText::_('PLG_MEMBERS_USAGE_EXPLANATION'); ?></p>
</div><!-- / .aside -->
<div class="subject" id="statistics">
	<table class="data" summary="<?php echo JText::_('PLG_MEMBERS_USAGE_TBL_SUMMARY_OVERVIEW'); ?>">
		<caption><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_CAPTION_OVERVIEW'); ?></caption>
		<thead>
			<tr>
				<th scope="col" class="textual-data"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_ITEM'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_VALUE'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
				<th scope="row"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_CONTRIBUTIONS'); ?>:</th>
				<td><?php echo $this->contribution['contribs']; ?></td>
			</tr>
<?php
	if ($this->total_tool_users) {
?>
			<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
				<th scope="row"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_USERS_SERVED_TOOLS'); ?>:</th>
				<td><?php echo number_format($this->total_tool_users); ?></td>
			</tr>
<?php
	}
	if ($this->total_andmore_users) {
?>
			<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
				<th scope="row"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_USERS_SERVED_ANDMORE'); ?>:</th>
				<td><?php echo number_format($this->total_andmore_users); ?></td>
			</tr>
<?php
	}
?>
			<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
				<th scope="row"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_CONTRIBUTIONS_RANK'); ?>:</th>
				<td><?php echo $this->rank; ?></td>
			</tr>
			<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
				<th scope="row"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_CONTRIBUTIONS_FIRST'); ?>:</th>
				<td><?php echo $this->contribution['first']; ?></td>
			</tr>
			<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
				<th scope="row"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_CONTRIBUTIONS_LAST'); ?>:</th>
				<td><?php echo $this->contribution['last']; ?></td>
			</tr>
			<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
				<th scope="row"><?php echo JText::_('PLG_MEMBERS_USAGE_CITATIONS'); ?>:</th>
				<td><?php echo $this->citation_count; ?></td>
			</tr>
		</tbody>
	</table>
	
	<table class="data" summary="<?php echo JText::_('PLG_MEMBERS_USAGE_TBL_SUMMARY_TOOLS'); ?>">
		<caption><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_CAPTION_TOOLS'); ?></caption>
		<thead>
			<tr>
				<th scope="col"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_NUMBER'); ?></th>
				<th scope="col"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_TOOL_TITLE'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_USERS_YEAR'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_SIM_RUNS_YEAR'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_USERS_TOTAL'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_SIM_RUNS_TOTAL'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_CITATIONS'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_PUBLISHED'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php
	if ($this->tool_stats) {	
		$count = 1;
		$cls = 'even';
		$sum_simcount_12 = 0;
		$sum_simcount_14 = 0;
		foreach ($this->tool_stats as $row) 
		{
			$sim_count_12 = plgMembersUsage::get_simcount($row->id, 12);
			$sim_count_14 = plgMembersUsage::get_simcount($row->id, 14);
			$sum_simcount_12 += $sim_count_12;
			$sum_simcount_14 += $sim_count_14;
?>
			<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
				<td><?php echo $count; ?></td>
				<td class="textual-data"><a href="<?php echo JRoute::_('index.php?option=com_resources&id='.$row->id); ?>"><?php echo $row->title; ?></a></td>
				<td><a href="<?php echo JRoute::_('index.php?option=com_usage&task=tools&id='.$row->id.'&period=12'); ?>"><?php echo number_format(plgMembersUsage::get_usercount($row->id, 12, 7)); ?></a></td>
				<td><a href="<?php echo JRoute::_('index.php?option=com_usage&task=tools&id='.$row->id.'&period=12'); ?>"><?php echo number_format($sim_count_12); ?></a></td>
				<td><a href="<?php echo JRoute::_('index.php?option=com_usage&task=tools&id='.$row->id.'&period=14'); ?>"><?php echo number_format(plgMembersUsage::get_usercount($row->id, 14, 7)); ?></a></td>
				<td><a href="<?php echo JRoute::_('index.php?option=com_usage&task=tools&id='.$row->id.'&period=14'); ?>"><?php echo number_format($sim_count_14); ?></a></td>
				<td><?php echo plgMembersUsage::get_citationcount($row->id, 0); ?></td>
				<td><?php echo $row->publish_up; ?></td>
			</tr>
<?php
			$count++;
    	}
	} else {
?>
			<tr class="odd">
				<td colspan="8" class="textual-data"><?php echo JText::_('PLG_MEMBERS_USAGE_NO_RESULTS'); ?></td>
			</tr>
<?php
	}
	if ($this->tool_total_14 && $this->tool_total_12) {
?>
			<tr class="summary">
				<td></td>
				<td class="textual-data"><?php echo JText::_('TOTAL'); ?></td>
				<td><?php echo number_format($this->tool_total_12); ?></td>
				<td><?php echo number_format($sum_simcount_12); ?></td>
				<td><?php echo number_format($this->tool_total_14); ?></td>
				<td><?php echo number_format($sum_simcount_14); ?></td>
				<td></td>
				<td></td>
			</tr>
<?php
	}
?>
		</tbody>
	</table>
	
	<table class="data" summary="<?php echo JText::_('PLG_MEMBERS_USAGE_TBL_SUMMARY_RESOURCES'); ?>">
		<caption><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_CAPTION_RESOURCES'); ?></caption>
		<thead>
			<tr>
				<th scope="col"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_NUMBER'); ?></th>
				<th scope="col"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_RESOURCE_TITLE'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_USERS_YEAR'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_USERS_TOTAL'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_CITATIONS'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('PLG_MEMBERS_USAGE_TBL_TH_PUBLISHED'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php 
	if ($this->andmore_stats) {
		$cls = 'even';
		$count = 1;
		foreach ($this->andmore_stats as $row) 
		{
?>
			<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
				<td><?php echo $count; ?></td>
				<td class="textual-data"><a href="<?php echo JRoute::_('index.php?option=com_resources&id='.$row->id); ?>"><?php echo $row->title; ?></a> <span class="small"><?php echo $row->type; ?></span></td>
				<td><?php echo number_format(plgMembersUsage::get_usercount($row->id,12)); ?></td>
				<td><?php echo number_format(plgMembersUsage::get_usercount($row->id,14)); ?></td>
				<td><?php echo plgMembersUsage::get_citationcount($row->id, 0); ?></td>
				<td><?php echo $row->publish_up; ?></td>
			</tr>
<?php
			$count++;
    	}
	} else {
?>
			<tr class="odd">
				<td colspan="6" class="textual-data"><?php echo JText::_('PLG_MEMBERS_USAGE_NO_RESULTS'); ?></td>
			</tr>
<?php
	}
	if ($this->andmore_total_14 && $this->andmore_total_12) {
?>
			<tr class="summary">
				<td></td>
				<td><?php echo JText::_('TOTAL'); ?></td>
				<td><?php echo number_format($this->andmore_total_12); ?></td>
				<td><?php echo number_format($this->andmore_total_14); ?></td>
				<td></td>
				<td></td>
			</tr>
<?php
	}
?>
		</tbody>
	</table>
</div><!-- / .subject -->