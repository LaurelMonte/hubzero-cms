<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
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
<form action="" method="post" id="hubForm">
	<div class="explaination">
		<h4>No account?</h4><p><a href="/register">Create an account</a>. It's free!</p>
		<h4>Is this really free?</h4>
		<p>Yes! Use of <?php echo $this->hubShortName;?> resources and tools is <em>free</em> for registered users. There are no hidden costs or fees.</p>

		<h4>Why is registration required for parts of the <?php echo $this->hubShortName; ?>?</h4>
	
		<p>Our sponsors ask us who uses the <?php echo $this->hubShortName;?> and what they use it for. Registration 
		helps us answer these questions. Usage statistics also focus our attention on improvements, making the 
		<?php echo $this->hubShortName; ?> experience better for <em>you</em>.</p>

	</div>
	<fieldset>
		<h3>Sign in using your<br /><?php echo $this->realmName;?></h3>
		
		<label>
			<?php echo JText::_('_USERNAME'); ?>:
			<input type="text" tabindex="1" size="10" name="username" id="username"<?php if(!empty($this->usrnm)) { echo ' value="'.$this->usrnm.'"'; } ?> />
		</label>
		
		<p class="hint">
			<?php if ($this->realm == 'hzldap') { ?>
				<a href="/myaccount/resend"><?php echo JText::_('_LOST_USERNAME');?></a><br />
			<?php } 
			echo JText::_('_NO_USERNAME'); 
			?>
			<a href="/register"><?php echo JText::_('_CREATE_ACCOUNT'); ?></a>
		</p>
		
		<label>
			<?php echo JText::_('_PASSWORD'); ?>:
			<input type="password" tabindex="2" name="passwd" id="passwd" />
		</label>
		
		<?php if ($this->realm == 'hzldap') { ?>
		<p class="hint">
			<a href="/password/lost"><?php echo JText::_('_LOST_PASSWORD'); ?></a>
		</p>
		<?php } ?>

		<label>
			<input type="checkbox" class="option" name="remember" id="remember" value="yes" alt="Remember Me" /> 
			<?php echo JText::_('_REMEMBER_ME'); ?>
		</label>

		<input type="hidden" name="realm" value="<?php echo $this->realm;?>" />
		<input type="hidden" name="la" value="<?php echo empty($this->login_attempts) ? 0 : $this->login_attempts; ?>" />
		<input type="hidden" name="option" value="com_hub" />
		<input type="hidden" name="view" value="login" />
		<input type="hidden" name="task" value="login" />
		<input type="hidden" name="act" value="submit" />
		<input type="hidden" name="return" value="<?php echo base64_encode($this->return); ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</fieldset>
	<div class="clear"></div>
	<p class="submit"><input type="submit" name="Submit" value="<?php echo JText::_('_BUTTON_LOGIN'); ?>" /></p>
</form>

<?php
	if (!empty($this->error_message))
		echo '<p class="error">'. $this->error_message . '</p>';
	if (!empty($this->login_attempts) && $this->login_attempts >= 2) 
		echo '<p class="hint">Having trouble logging in? <a href="support/report_problems/">Report problems to Support</a>.</p>';
?>
