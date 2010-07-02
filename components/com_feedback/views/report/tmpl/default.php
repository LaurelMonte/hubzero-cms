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

$browsers = array(
	'[unspecified]' => JText::_('COM_FEEDBACK_TROUBLE_SELECT_BROWSER'),
	'msie' => 'Internet Explorer',
	'chrome' => 'Google Chrome',
	'safari' => 'Safari',
	'firefox' => 'Firefox',
	'opera' => 'Opera',
	'mozilla' => 'Mozilla',
	'netscape' => 'Netscape',
	'camino' => 'Camino',
	'omniweb' => 'Omniweb',
	'shiira' => 'Shiira',
	'icab' => 'iCab',
	'flock' => 'Flock',
	'avant' => 'Avant Browser',
	'seamonkey' => 'SeaMonkey',
	'konqueror' => 'Konqueror',
	'lynx' => 'Lynx',
	'aol' => 'Aol',
	'amaya' => 'Amaya',
	'other' => 'Other'
);
				  
$oses = array(
	'[unspecified]' => JText::_('COM_FEEDBACK_TROUBLE_SELECT_OS'),
	'Windows' => 'Windows',
	'Mac OS' => 'Mac OS',
	'Linux' => 'Linux',
	'Unix' => 'Unix',
	'Google Chrome OS' => 'Google Chrome OS',
	'Other' => 'Other'
);

$topics = array(
	'???' => 'Unsure/Don\'t know',
	'Access Denied' => 'Access Denied',
	'Account/Login' => 'Account/Login',
	'Content' => 'Content',
	'Contributions' => 'Contributions',
	'Online Meetings' => 'Online Meetings',
	'Tools' => 'Tools',
	'other' => 'other'
);
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div class="main section">
	<p class="information"><?php echo JText::_('COM_FEEDBACK_TROUBLE_TICKET_TIMES'); ?></p>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo JText::_('COM_FEEDBACK_ERROR_MISSING_FIELDS'); ?></p>
<?php } ?>
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=report_problems'); ?>" id="hubForm" method="post" enctype="multipart/form-data">
		<div class="explaination">
			<p><?php echo JText::_('COM_FEEDBACK_TROUBLE_OTHER_OPTIONS'); ?></p>
		</div>
		<fieldset>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="sendreport" />
			<input type="hidden" name="verified" value="<?php echo $this->verified; ?>" />
			
			<input type="hidden" name="problem[referer]" value="<?php echo $this->problem['referer']; ?>" />
			<input type="hidden" name="problem[tool]" value="<?php echo $this->problem['tool']; ?>" />
			<input type="hidden" name="problem[osver]" value="<?php echo $this->problem['osver']; ?>" />
			<input type="hidden" name="problem[browserver]" value="<?php echo $this->problem['browserver']; ?>" />
			<input type="hidden" name="problem[short]" value="<?php echo $this->problem['short']; ?>" />
			
			<input type="hidden" name="krhash" value="<?php echo $this->problem['key']; ?>" />
<?php if ($this->verified) { ?>
			<input type="hidden" name="answer" value="<?php echo $this->problem['sum']; ?>" />
<?php } ?>
			<h3><?php echo JText::_('COM_FEEDBACK_TROUBLE_USER_INFORMATION'); ?></h3>
			
			<label>
				<?php echo JText::_('COM_FEEDBACK_USERNAME'); ?>
				<input type="text" name="reporter[login]" value="<?php echo (isset($this->reporter['login'])) ? $this->reporter['login'] : ''; ?>" id="reporter_login" />
			</label>
			
			<label<?php echo ($this->getError() && $this->reporter['name'] == '') ? ' class="fieldWithErrors"' : ''; ?>>
				<?php echo JText::_('NAME'); ?> <span class="required"><?php echo JText::_('COM_FEEDBACK_REQUIRED'); ?></span>
				<input type="text" name="reporter[name]" value="<?php echo (isset($this->reporter['name'])) ? $this->reporter['name'] : ''; ?>" id="reporter_name" />
			</label>
<?php if ($this->getError() && $this->reporter['name'] == '') { ?>
			<p class="error"><?php echo JText::_('COM_FEEDBACK_ERROR_MISSING_NAME'); ?></p>
<?php } ?>

			<label>
				<?php echo JText::_('COM_FEEDBACK_ORGANIZATION'); ?>
				<input type="text" name="reporter[org]" value="<?php echo (isset($this->reporter['org'])) ? $this->reporter['org'] : ''; ?>" id="reporter_org" />
			</label>

			<label<?php echo ($this->getError() && $this->reporter['email'] == '') ? ' class="fieldWithErrors"' : ''; ?>>
				<?php echo JText::_('COM_FEEDBACK_EMAIL'); ?> <span class="required"><?php echo JText::_('COM_FEEDBACK_REQUIRED'); ?></span>
				<input type="text" name="reporter[email]" value="<?php echo (isset($this->reporter['email'])) ? $this->reporter['email'] : ''; ?>" id="reporter_email" />
			</label>
<?php if ($this->getError() && $this->reporter['email'] == '') { ?>
			<p class="error"><?php echo JText::_('COM_FEEDBACK_ERROR_MISSING_EMAIL'); ?></p>
<?php } ?>
			<div class="group">
				<label<?php echo ($this->getError() && $this->problem['os'] == '') ? ' class="fieldWithErrors"' : ''; ?>>
					<?php echo JText::_('COM_FEEDBACK_OS'); ?>
					<select name="problem[os]" id="problem_os">
<?php
					foreach ($oses as $avalue => $alabel) 
					{
?>
						<option value="<?php echo $avalue; ?>"<?php echo ($avalue == $this->problem['os'] || $alabel == $this->problem['os']) ? ' selected="selected"' : ''; ?>><?php echo $alabel; ?></option>
<?php
					}
?>
					</select>
				</label>
				
				<label<?php echo ($this->getError() && $this->problem['browser'] == '') ? ' class="fieldWithErrors"' : ''; ?>>
					<?php echo JText::_('COM_FEEDBACK_BROWSER'); ?>
					<select name="problem[browser]" id="problem_browser">
<?php
					foreach ($browsers as $avalue => $alabel) 
					{
?>
						<option value="<?php echo $avalue; ?>"<?php echo ($avalue == $this->problem['browser'] || $alabel == $this->problem['browser']) ? ' selected="selected"' : ''; ?>><?php echo $alabel; ?></option>
<?php
					}
?>
					</select>
				</label>
			</div><!-- / .group -->
		</fieldset><div class="clear"></div>
		
<?php if (!$this->verified) { ?>
		<div class="explaination">
			<h4><?php echo JText::_('COM_FEEDBACK_WHY_THE_MATH_QUESTION'); ?></h4>
			<p><?php echo JText::_('COM_FEEDBACK_MATH_EXPLANATION'); ?></p>
		</div>
<?php } ?>
		<fieldset>
			<h3><?php echo JText::_('COM_FEEDBACK_TROUBLE_YOUR_PROBLEM'); ?></h3>
			
			<label<?php echo ($this->getError() && $this->problem['long'] == '') ? ' class="fieldWithErrors"' : ''; ?>>
				<?php echo JText::_('COM_FEEDBACK_TROUBLE_DESCRIPTION'); ?> <span class="required"><?php echo JText::_('COM_FEEDBACK_REQUIRED'); ?></span>
				<textarea name="problem[long]" cols="40" rows="10" id="problem_long"><?php echo (isset($this->problem['long'])) ? stripslashes($this->problem['long']) : ''; ?></textarea>
			</label>
			<?php if ($this->getError() && (!isset($this->problem['long']) || $this->problem['long'] == '')) { ?>
			<p class="error"><?php echo JText::_('COM_FEEDBACK_ERROR_MISSING_DESCRIPTION'); ?>
			<?php } ?>
			
			<label>
				<?php echo JText::_('Attach a screenshot'); ?>:
				<input type="file" name="upload" id="trUpload" />
			</label>
			
<?php if (!$this->verified) { ?>
			<label<?php echo ($this->getError() == 3) ? ' class="fieldWithErrors"' : ''; ?>>
				<?php echo JText::sprintf('COM_FEEDBACK_TROUBLE_MATH', $this->problem['operand1'], $this->problem['operand2']); ?>
				<input type="text" name="answer" value="" size="3" id="answer" class="option" /> <span class="required"><?php echo JText::_('COM_FEEDBACK_REQUIRED'); ?></span>
			</label>
			<?php if ($this->getError() == 3) { ?>
			<p class="error"><?php echo JText::_('COM_FEEDBACK_ERROR_BAD_CAPTCHA_ANSWER'); ?>
			<?php } ?>
<?php } ?>
		</fieldset><div class="clear"></div>
		<p class="submit"><input type="submit" name="submit" value="<?php echo JText::_('COM_FEEDBACK_SUBMIT'); ?>" /></p>
	</form>
</div><!-- / .main section -->
