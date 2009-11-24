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
?>

<div id="content-header" class="full">
	<h2><?php echo $this->title .': '. ucfirst($this->resource); ?></h2>
</div>
<div class="main section">

	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$profile->get('uidNumber').'&task=raiselimit'); ?>" method="post" name="hubForm" id="hubForm">
		<div class="explaination">
			<p class="info">
				When you have time, please leave some <a href="<?php echo JRoute::_('index.php?option=com_feedback'); ?>">feedback</a>. We would like to know a little more about how you are using the site so that we can make improvements for everyone.
			</p>
		</div>
		<fieldset>
<?php if ($this->resource != 'select') { ?>
			<p>
				Please provide a short reason why you would like this increase in resources. Your 
				request for additional resources will then be e-mailed to the site administrators 
				who will grant your request or provide a reason why we are unable to meet your 
				request at this time.
			</p>
			<label>
				Reason for Increase:
				<textarea name="request" id="request" rows="6" cols="32"></textarea>
			</label>
		</fieldset>
		<div class="clear"></div>

		<p class="submit">
			<input type="submit" name="raiselimit[<?php echo $this->resource; ?>]" value="Submit Request" />
		</p>
<?php } else { ?>
			<h3>HUB Resources</h3>

			<table summary="Form for requesting more resources">
				<tbody>
					<?php if ($this->authorized == 'admin') { ?>
					<tr>
						<th>User Login:</th>
						<td colspan="2">
							<a href="<?php echo JRoute::_('index.php?option='.$this->option.'task=whois&username='.$username); ?>"><?php echo htmlentities($username,ENT_COMPAT,'UTF-8');?></a>
							<input name="login" id="login" type="hidden" value="<?php echo htmlentities($username,ENT_COMPAT,'UTF-8');?>" />
						</td>
					</tr>
					<?php } ?>
					<tr>
						<th>Maximum Concurrent Sessions:</th>
						<td><?php echo $jobs_allowed; ?></td>
						<td><span class="submit"><input type="submit" name="raiselimit[sessions]" id="raiselimitsessions" value="<?php echo $submit_button; ?>" /></span></td>
					</tr>
					<tr>
						<th>Online Disk Storage Limit:</th>
						<td><?php echo $quota; ?></td>
						<td><span class="submit"><input type="submit" name="raiselimit[storage]" id="raiselimitstorage" value="<?php echo $submit_button; ?>" /></span></td>
					</tr>
					<tr>
						<th>Maximum Online Meetings:</th>
						<td><?php echo $max_meetings; ?></td>
						<td><span class="submit"><input type="submit" name="raiselimit[meetings]" id="raiselimitmeetings" value="<?php echo $submit_button; ?>" /></span></td>
					</tr>
				</tbody>
			</table>

			<div class="help">
				<h4>How do I get more resources?</h4>
				<p>
					Click "Increase" for the resource you wish to request more. Depending on the resource and your 
					current limits, you will either be automatically granted more resources, asked to fill out some 
					feedback, asked to review a resource for others, or asked to email support.
				</p>
			</div>
		</fieldset>
		<div class="clear"></div>
<?php } ?>
	</form>
	
</div><!-- / .main section -->