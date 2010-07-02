<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
	<div id="help-pane">
		<div id="help-container">
			<h1><?php echo JText::_('MOD_REPORTPROBLEMS_SUPPORT'); ?></h1>

			<div class="three columns first">
				<h2><?php echo JText::_('Support Options'); ?></h2>
				<ul>
					<li class="help-kb">
						<h3><a href="<?php echo JRoute::_('index.php?option=com_kb'); ?>"><?php echo JText::_('Knowledge Base'); ?></a></h3>
						<p><?php echo JText::_('Find information on common issues.'); ?></p>
					</li>
					<li class="help-qa">
						<h3><a href="<?php echo JRoute::_('index.php?option=com_answers'); ?>"><?php echo JText::_('Ask the Community'); ?></a></h3>
						<p><?php echo JText::_('Ask questions and find answers from other users.'); ?></p>
					</li>
					<li class="help-wish">
						<h3><a href="<?php echo JRoute::_('index.php?option=com_wishlist'); ?>"><?php echo JText::_('Wish List'); ?></a></h3>
						<p><?php echo JText::_('Suggest a new site feature or improvement.'); ?></p>
					</li>
					<li class="help-tickets">
						<h3><a href="<?php echo JRoute::_('index.php?option=com_support&task=tickets'); ?>"><?php echo JText::_('Support Tickets'); ?></a></h3>
						<p><?php echo JText::_('Check on status of your tickets.'); ?></p>
					</li>
				</ul>
			</div><!-- / .three columns first -->
			<div class="three columns second third">
				<h2>
					<?php echo JText::_('Submit a Support Ticket'); ?>
				</h2>
				<form method="post" action="index.php" id="troublereport" enctype="multipart/form-data">
					<fieldset class="reporter">
						<label for="trLogin">
							<?php echo JText::_('MOD_REPORTPROBLEMS_LABEL_LOGIN'); ?>: <span class="optional"><?php echo JText::_('MOD_REPORTPROBLEMS_OPTIONAL'); ?></span>
<?php if (!$modreportproblems->juser->get('guest')) { ?>
							<input type="hidden" name="reporter[login]" id="trLogin" value="<?php echo $modreportproblems->juser->get('username'); ?>" /><br /><span class="info-block"><?php echo $modreportproblems->juser->get('username'); ?></span>
<?php } else { ?>
							<input type="text" name="reporter[login]" id="trLogin" value="" />
<?php } ?>
						</label>

						<label for="trName">
							<?php echo JText::_('MOD_REPORTPROBLEMS_LABEL_NAME'); ?>: <span class="required"><?php echo JText::_('MOD_REPORTPROBLEMS_REQUIRED'); ?></span>
<?php if (!$modreportproblems->juser->get('guest')) { ?>
							<input type="hidden" name="reporter[name]" id="trName" value="<?php echo $modreportproblems->juser->get('name'); ?>" /><br /><span class="info-block"><?php echo $modreportproblems->juser->get('name'); ?></span>
<?php } else { ?>
							<input type="text" name="reporter[name]" id="trName" value="" />
<?php } ?>
						</label>

						<label for="trEmail">
							<?php echo JText::_('MOD_REPORTPROBLEMS_LABEL_EMAIL'); ?>: <span class="required"><?php echo JText::_('MOD_REPORTPROBLEMS_REQUIRED'); ?></span>
<?php if (!$modreportproblems->juser->get('guest')) { ?>
							<input type="hidden" name="reporter[email]" id="trEmail" value="<?php echo $modreportproblems->juser->get('email'); ?>" /><br /><span class="info-block"><?php echo $modreportproblems->juser->get('email'); ?></span>
<?php } else { ?>
							<input type="text" name="reporter[email]" id="trEmail" value="" />
<?php } ?>
						</label>
<?php if (!$modreportproblems->verified) { ?>
						<label for="trAnswer">
							Anti-spam check: <span class="required"><?php echo JText::_('MOD_REPORTPROBLEMS_REQUIRED'); ?></span><br />
							<?php echo JText::sprintf('MOD_REPORTPROBLEMS_MATH_CAPTCHA', $modreportproblems->problem['operand1'], $modreportproblems->problem['operand2']); ?>
							<input type="text" name="answer" id="trAnswer" value="" class="option" size="3" />
						</label>
<?php } ?>
					</fieldset>
					<fieldset>
						<label for="trProblem">
							<?php echo JText::_('MOD_REPORTPROBLEMS_LABEL_PROBLEM'); ?>: <span class="required"><?php echo JText::_('MOD_REPORTPROBLEMS_REQUIRED'); ?></span>
							<textarea name="problem[long]" id="trProblem" <?php //if (!$modreportproblems->verified) { echo 'class="long" '; } ?>rows="10" cols="40"></textarea>
						</label>
						
						<label>
							<?php echo JText::_('Attach a screenshot'); ?>: <span class="optional"><?php echo JText::_('MOD_REPORTPROBLEMS_OPTIONAL'); ?></span>
							<input type="file" name="upload" id="trUpload" />
						</label>
						
						<input type="hidden" name="problem[topic]" value="???" />
						<input type="hidden" name="problem[short]" value="" />
						<input type="hidden" name="problem[referer]" value="<?php echo $modreportproblems->referrer; ?>" />
						<input type="hidden" name="problem[tool]" value="" />
						<input type="hidden" name="problem[os]" value="<?php echo $modreportproblems->os; ?>" />
						<input type="hidden" name="problem[osver]" value="<?php echo $modreportproblems->os_version; ?>" />
						<input type="hidden" name="problem[browser]" value="<?php echo $modreportproblems->browser; ?>" />
						<input type="hidden" name="problem[browserver]" value="<?php echo $modreportproblems->browser_ver; ?>" />
						<input type="hidden" name="krhash" value="<?php echo $modreportproblems->krhash; ?>" />
						<input type="hidden" name="verified" value="<?php echo $modreportproblems->verified; ?>" />
						<input type="hidden" name="reporter[org]" value="<?php echo (!$modreportproblems->juser->get('guest')) ? $modreportproblems->juser->get('org') : ''; ?>" />
						<input type="hidden" name="option" value="com_feedback" />
						<input type="hidden" name="task" value="sendreport" />
						<input type="hidden" name="no_html" value="1" />
<?php if ($modreportproblems->verified) { ?>
						<input type="hidden" name="answer" id="trAnswer" value="<?php echo $modreportproblems->sum; ?>" />
<?php } ?>
				 	</fieldset>
					<div class="submit"><input type="submit" id="send-form" value="<?php echo JText::_('MOD_REPORTPROBLEMS_SUBMIT'); ?>" /></div>
				</form>
				<div id="trSending">
				</div>
				<div id="trSuccess">
				</div>
			</div><!-- / .three columns second third -->
			<div class="clear"></div>
		</div><!-- / #help-container -->
	</div><!-- / #help-pane -->