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

$showsizes = $this->showsizes;
$min_font_size = $this->min_font_size;
$max_font_size = $this->max_font_size;
$min_qty = $this->min_qty;
$step = $this->step;
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div>

<div id="introduction" class="section">
	<div class="aside">
		<h3><?php echo JText::_('COM_TAGS_QUESTIONS'); ?></h3>
		<ul>
			<li><a href="/kb/tags/faq"><?php echo JText::_('COM_TAGS_FAQ'); ?></a></li>
		</ul>
	</div><!-- / .aside -->
	<div class="subject">
		<div class="two columns first">
			<h3><?php echo JText::_('COM_TAGS_WHAT_ARE_TAGS'); ?></h3>
			<p><?php echo JText::_('COM_TAGS_WHAT_ARE_TAGS_EXPLANATION'); ?></p>
		</div>
		<div class="two columns second">
			<h3><?php echo JText::_('COM_TAGS_HOW_DO_TAGS_WORK'); ?></h3>
			<p><?php echo JText::_('COM_TAGS_HOW_DO_TAGS_WORK_EXPLANATION'); ?></p>
		</div>
		<div class="clear"></div>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / #introduction.section -->

<div class="section">
	
	<div class="four columns first">
		<h2><?php echo JText::_('COM_TAGS_FIND_CONTENT_WITH_TAG'); ?></h2>
	</div><!-- / .four columns first -->
	<div class="four columns second third fourth">
		<div class="two columns first">
			<form action="<?php echo JRoute::_('index.php?option='.$option.'&task=view'); ?>" method="get" class="search">
				<fieldset>
					<p>
						<?php 
						JPluginHelper::importPlugin( 'tageditor' );
						$dispatcher =& JDispatcher::getInstance();
						$tf = $dispatcher->trigger( 'onTagsEdit', array(array('tag','actags','','','')) );
						
						if (count($tf) > 0) {
							echo $tf[0];
						} else { ?>
							<input type="text" name="tag" value="" />
						<?php } ?>
						<input type="submit" value="<?php echo JText::_('COM_TAGS_SEARCH'); ?>" />
					</p>
				</fieldset>
			</form>
		</div><!-- / .two columns first -->
		<div class="two columns second">
			<div>
				<p>Using more than one tag will perform an "AND" operation. For example, if you enter "circuits" and "devices", it will find all content tagged with <strong>both</strong> tags.</p>
			</div><!-- / .browse -->
		</div><!-- / .two columns second -->
	</div><!-- / .four columns second third fourth -->
	<div class="clear"></div>

	<div class="four columns first">
		<h2><?php echo JText::_('COM_TAGS_RECENTLY_USED'); ?></h2>
	</div><!-- / .four columns first -->
	<div class="four columns second third fourth">
		<div class="block">
<?php
$newtags = $this->newtags;
$html = '';
if ($newtags) {
	$html .= '<ol class="tags">'."\n";
	$tl = array();
	foreach ($newtags as $newtag)
	{
		$class = ($newtag->admin == 1) ? ' class="admin"' : '';

		$newtag->raw_tag = str_replace( '&amp;', '&', $newtag->raw_tag );
		$newtag->raw_tag = str_replace( '&', '&amp;', $newtag->raw_tag );

		if ($showsizes == 1) {
			$size = $min_font_size + ($newtag->tcount - $min_qty) * $step;
			$size = ($size > $max_font_size) ? $max_font_size : $size;
			$tl[$newtag->tag] = "\t".'<li'.$class.'><span style="font-size: '. round($size,1) .'em"><a href="'.JRoute::_('index.php?option='.$option.'&tag='.$newtag->tag).'">'.stripslashes($newtag->raw_tag).'</a></span></li>'."\n";
		} else {
			$tl[$newtag->tag] = "\t".'<li'.$class.'><a href="'.JRoute::_('index.php?option='.$option.'&tag='.$newtag->tag).'">'.stripslashes($newtag->raw_tag).'</a></li>'."\n";
		}
	}
	ksort($tl);
	$html .= implode('',$tl);
	$html .= '</ol>'."\n";
} else {
	$html  = Hubzero_View_Helper_Html::warning( JText::_('COM_TAGS_NO_TAGS') )."\n";
}
echo $html;
?>
		</div><!-- / .block -->
	</div><!-- / .four columns second third fourth -->
	<div class="clear"></div>

	<div class="four columns first">
		<h2><?php echo JText::_('COM_TAGS_TOP_100'); ?></h2>
	</div><!-- / .four columns first -->
	<div class="four columns second third fourth">
		<div class="block">
<?php
$tags = $this->tags;
$html = '';
if ($tags) {
	$html .= '<ol class="tags">'."\n";
	$tll = array();
	foreach ($tags as $tag)
	{
		$class = ($tag->admin == 1) ? ' class="admin"' : '';

		$tag->raw_tag = str_replace( '&amp;', '&', $tag->raw_tag );
		$tag->raw_tag = str_replace( '&', '&amp;', $tag->raw_tag );

		if ($showsizes == 1) {
			$size = $min_font_size + ($tag->tcount - $min_qty) * $step;
			$size = ($size > $max_font_size) ? $max_font_size : $size;
			$tll[$tag->tag] = "\t".'<li'.$class.'><span style="font-size: '. round($size,1) .'em"><a href="'.JRoute::_('index.php?option='.$option.'&tag='.$tag->tag).'">'.stripslashes($tag->raw_tag).'</a></span></li>'."\n";
		} else {
			$tll[$tag->tag] = "\t".'<li'.$class.'><a href="'.JRoute::_('index.php?option='.$option.'&tag='.$tag->tag).'">'.stripslashes($tag->raw_tag).'</a></li>'."\n";
		}
	}
	ksort($tll);
	$html .= implode('',$tll);
	$html .= '</ol>'."\n";
} else {
	$html  = Hubzero_View_Helper_Html::warning( JText::_('COM_TAGS_NO_TAGS') )."\n";
}
echo $html;
?>
		</div><!-- / .block -->
	</div><!-- / .four columns second third fourth -->
	<div class="clear"></div>

	<div class="four columns first">
		<h2><?php echo JText::_('COM_TAGS_FIND_A_TAG'); ?></h2>
	</div><!-- / .four columns first -->
	<div class="four columns second third fourth">
		<div class="two columns first">
			<form action="<?php echo JRoute::_('index.php?option='.$option.'&task=browse'); ?>" method="get" class="search">
				<fieldset>
					<p>
						<input type="text" name="search" value="" />
						<input type="submit" value="<?php echo JText::_('COM_TAGS_SEARCH'); ?>" />
					</p>
				</fieldset>
			</form>
		</div><!-- / .two columns first -->
		<div class="two columns second">
			<div class="browse">
				<p><a href="<?php echo JRoute::_('index.php?option='.$option.'&task=browse'); ?>"><?php echo JText::_('COM_TAGS_BROWSE_LIST'); ?></a></p>
			</div><!-- / .browse -->
		</div><!-- / .two columns second -->
	</div><!-- / .four columns second third fourth -->
	<div class="clear"></div>

</div><!-- / .section -->