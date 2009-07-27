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
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div>

<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last"><a class="group" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=new'); ?>">Create User Group</a></li>
	</ul>
</div><!-- / #content-header-extra -->

<div id="introduction" class="section">
	<div class="aside">
		<h3>Questions?</h3>
		<ul>
			<li><a href="/kb/groups/faq">Groups FAQ</a></li>
			<li><a href="/kb/groups/guide">Group Guidelines</a></li>
		</ul>
	</div><!-- / .aside -->
	<div class="subject">
		<div class="two columns first">
			<h3>What are groups?</h3>
			<p>Groups are an easy way to share content and conversation, either privately or with the world. Many times, a group already exist for a specific interest or topic. If you can't find one you like, feel free to start your own.</p>
		</div>
		<div class="two columns second">
			<h3>How do groups work?</h3>
			<p>Groups can either be public, restricted (users may read a brief description or overview but not view content) or completely private. Every group has a wiki, a pool for resources, and a discussion board for talking.</p>
		</div>
		<div class="clear"></div>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / #introduction.section -->

<div class="section">
	
	<div class="four columns first">
		<h2>Find a group</h2>
	</div><!-- / .four columns first -->
	<div class="four columns second third fourth">
		<div class="two columns first">
			<form action="<?php echo JRoute::_('index.php?option='.$option.a.'task=browse'); ?>" method="get" class="search">
				<fieldset>
					<p>
						<input type="text" name="search" value="" />
						<input type="submit" value="Search" />
					</p>
					<p>
						Search group names and public descriptions. Private groups do not show up in results.
					</p>
				</fieldset>
			</form>
		</div><!-- / .two columns first -->
		<div class="two columns second">
			<div class="browse">
				<p><a href="<?php echo JRoute::_('index.php?option='.$option.a.'task=browse'); ?>">Browse the list of available groups</a></p>
				<p>A list of all public and restricted groups. Private groups are not listed.</p>
			</div><!-- / .browse -->
		</div><!-- / .two columns second -->
	</div><!-- / .four columns second third fourth -->
	<div class="clear"></div>

<?php
$groups = $this->groups;
if ($groups) {
	ximport('wiki.parser');
	$config = $this->config;
?>
	<div class="four columns first">
		<h2>Popular groups</h2>
	</div><!-- / .four columns first -->
<?php
	$i = 1;
	foreach ($groups as $group) 
	{
		$p = new WikiParser( $group->cn, $option, $group->cn.DS.'wiki', 'group', $group->gidNumber, $config->get('uploadpath') );
		
		switch ($i) 
		{
			case 1: $cls = 'second'; break;
			case 2: $cls = 'third'; break;
			case 3: $cls = 'fourth'; break;
		}
		
		$public_desc = '(No public description available.)';
		if ($group->public_desc) {
			$public_desc = $p->parse( n.stripslashes($group->public_desc) );
			$public_desc = strip_tags($public_desc);
			$UrlPtrn  = "[^=\"\'](https?:|mailto:|ftp:|gopher:|news:|file:)" . "([^ |\\/\"\']*\\/)*([^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_])";
			$public_desc = preg_replace("/$UrlPtrn/", '', $public_desc);
			$public_desc = GroupsHtml::shortenText($public_desc, 300, 0);
		}
?>
	<div class="four columns <?php echo $cls; ?>">
		<div class="group">
			<h3><a href="<?php echo JRoute::_('index.php?option='.$option.a.'gid='.$group->cn); ?>"><?php echo stripslashes($group->description); ?></a></h3>
			<!-- <p><?php echo $group->members; ?> Members</p> -->
			<p><?php echo $public_desc; ?></p>
			<p><a href="<?php echo JRoute::_('index.php?option='.$option.a.'gid='.$group->cn); ?>">Learn more &rsaquo;</a>
		</div>
	</div><!-- / .four columns second -->
<?php
		$i++;
	}
?>
	<div class="clear"></div>
<?php
}
?>

</div><!-- / .section -->