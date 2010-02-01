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
<h3><a name="contributions"></a><?php echo JText::_('PLG_MEMBERS_CONTRIBUTIONS'); ?></h3>
<form method="get" action="<?php JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=contributions'); ?>">
<div class="aside">
<?php
// Add the "all" category
$all = array('category'=>'','title'=>JText::_('PLG_MEMBERS_CONTRIBUTIONS_ALL_CATEGORIES'),'total'=>$this->total);

array_unshift($this->cats, $all);

// An array for storing all the links we make
$links = array();

// Loop through each category
foreach ($this->cats as $cat) 
{
	// Only show categories that have returned search results
	if ($cat['total'] > 0) {
		// Is this the active category?
		$a = '';
		if ($cat['category'] == $this->active) {
			$a = ' class="active"';
		}
		// If we have a specific category, prepend it to the search term
		$blob = '';
		if ($cat['category']) {
			$blob = $cat['category'];
		}
		// Build the HTML
		$l = "\t".'<li'.$a.'><a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=contributions&area='. urlencode(stripslashes($blob))) .'">' . $cat['title'] . ' ('.$cat['total'].')</a>';
		// Are there sub-categories?
		if (isset($cat['_sub']) && is_array($cat['_sub'])) {
			// An array for storing the HTML we make
			$k = array();
			// Loop through each sub-category
			foreach ($cat['_sub'] as $subcat) 
			{
				// Only show sub-categories that returned search results
				if ($subcat['total'] > 0) {
					// Is this the active category?
					$a = '';
					if ($subcat['category'] == $this->active) {
						$a = ' class="active"';
					}
					// If we have a specific category, prepend it to the search term
					$blob = '';
					if ($subcat['category']) {
						$blob = $subcat['category'];
					}
					// Build the HTML
					$k[] = "\t\t\t".'<li'.$a.'><a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=contributions&area='. urlencode(stripslashes($blob))) .'">' . $subcat['title'] . ' ('.$subcat['total'].')</a></li>';
				}
			}
			// Do we actually have any links?
			// NOTE: this method prevents returning empty list tags "<ul></ul>"
			if (count($k) > 0) {
				$l .= "\t\t".'<ul>'."\n";
				$l .= implode( "\n", $k );
				$l .= "\t\t".'</ul>'."\n";
			}
		}
		$l .= '</li>';
		$links[] = $l;
	}
}
// Do we actually have any links?
// NOTE: this method prevents returning empty list tags "<ul></ul>"
if (count($links) > 0) {
?>
		<fieldset>
			<legend><?php echo JText::_('PLG_MEMBERS_CONTRIBUTIONS_FILTER'); ?></legend>
			<label>
				<?php echo JText::_('PLG_MEMBERS_CONTRIBUTIONS_SORT_BY'); ?>
				<select name="sort">
					<option value="date"<?php if ($this->sort == 'date') { echo' selected="selected"'; } ?>><?php echo JText::_('PLG_MEMBERS_CONTRIBUTIONS_SORT_BY_DATE'); ?></option>
					<option value="title"<?php if ($this->sort == 'title') { echo ' selected="selected"'; } ?>><?php echo JText::_('PLG_MEMBERS_CONTRIBUTIONS_SORT_BY_TITLE'); ?></option>
				</select>
			</label>
			<input type="submit" value="<?php echo JText::_('PLG_MEMBERS_CONTRIBUTIONS_GO'); ?>" />
		</fieldset>
		<ul class="sub-nav">
			<?php echo implode( "\n", $links ); ?>
		</ul>
<?php
}
?>
		<input type="hidden" name="area" value="<?php echo $this->active; ?>" />
</div><!-- / .aside -->
<div class="subject">
<?php
ximport('Hubzero_View_Helper_Html');

$foundresults = false;
$dopaging = false;
$html = '';
$k = 1;

foreach ($this->results as $category)
{
	$amt = count($category);
	
	if ($amt > 0) {
		$foundresults = true;
		
		$name  = $this->cats[$k]['title'];
		$total = $this->cats[$k]['total'];
		$divid = 'search'.$this->cats[$k]['category'];
		
		// Is this category the active category?
		if (!$this->active || $this->active == $this->cats[$k]['category']) {
			// It is - get some needed info
			$name  = $this->cats[$k]['title'];
			$total = $this->cats[$k]['total'];
			$divid = 'search'.$this->cats[$k]['category'];
			
			if ($this->active == $this->cats[$k]['category']) {
				$dopaging = true;
			}
		} else {
			// It is not - does this category have sub-categories?
			if (isset($this->cats[$k]['_sub']) && is_array($this->cats[$k]['_sub'])) {
				// It does - loop through them and see if one is the active category
				foreach ($this->cats[$k]['_sub'] as $sub) 
				{
					if ($this->active == $sub['category']) {
						// Found an active category
						$name  = $sub['title'];
						$total = $sub['total'];
						$divid = 'search'.$sub['category'];
						
						$dopaging = true;
						break;
					}
				}
			}
		}
		
		$num  = $this->total .' result';
		$num .= ($this->total > 1) ? 's' : '';
	
		// A function for category specific items that may be needed
		// Check if a function exist (using old style plugins)
		$f = 'plgMembers'.ucfirst($this->cats[$k]['category']).'Doc';
		if (function_exists($f)) {
			$f();
		}
		// Check if a method exist (using JPlugin style)
		$obj = 'plgMembers'.ucfirst($this->cats[$k]['category']);
		if (method_exists($obj, 'documents')) {
			$html .= call_user_func( array($obj,'documents') );
		}
	
		// Build the category HTML
		$html .= '<h4 class="category-header opened" id="rel-'.$divid.'">'.$name.' <small>'.$num.'</small></h4>'."\n";
		$html .= '<div class="category-wrap" id="'.$divid.'">'."\n";
		
		// Does this category have custom output?
		// Check if a function exist (using old style plugins)
		$func = 'plgMembers'.ucfirst($this->cats[$k]['category']).'Before';
		if (function_exists($func)) {
			$html .= $func();
		}
		// Check if a method exist (using JPlugin style)
		$obj = 'plgMembers'.ucfirst($this->cats[$k]['category']);
		if (method_exists($obj, 'before')) {
			$html .= call_user_func( array($obj,'before') );
		}
		
		$html .= '<ol class="search results">'."\n";			
		foreach ($category as $row) 
		{
			$row->href = str_replace('&amp;', '&', $row->href);
			$row->href = str_replace('&', '&amp;', $row->href);
			
			// Does this category have a unique output display?
			$func = 'plgMembers'.ucfirst($row->section).'Out';
			// Check if a method exist (using JPlugin style)
			$obj = 'plgMembers'.ucfirst($this->cats[$k]['category']);
			
			if (function_exists($func)) {
				$html .= $func( $row, $this->authorized );
			} elseif (method_exists($obj, 'out')) {
				$html .= call_user_func( array($obj,'out'), $row, $this->authorized );
			} else {
				$html .= "\t".'<li>'."\n";
				$html .= "\t\t".'<p class="title"><a href="'.$row->href.'">'.stripslashes($row->title).'</a></p>'."\n";
				if ($row->text) {
					$html .= "\t\t".MembersHtml::shortenText(stripslashes($row->text))."\n";
				}
				$html .= "\t".'</li>'."\n";
			}
		}
		$html .= '</ol>'."\n";
		// Initiate paging if we we're displaying an active category
		if ($dopaging) {
			jimport('joomla.html.pagination');
			$pageNav = new JPagination( $this->total, $this->start, $this->limit );

			//$html .= $pageNav->getListFooter();
			$pf = $pageNav->getListFooter();
			
			$nm = str_replace('com_','',$this->option);

			$pf = str_replace($nm.'/?',$nm.'/'.$this->member->get('uidNumber').'/contributions/?',$pf);
			$html .= $pf;
		} else {
			$html .= '<p class="moreresults">'.JText::sprintf('PLG_MEMBERS_CONTRIBUTIONS_NUMBER_SHOWN', $amt);
			// Ad a "more" link if necessary
			//if ($totals[$k] > 5) {
			if ($this->cats[$k]['total'] > 5) {
				$qs = 'area='.urlencode(strToLower($this->cats[$k]['category']));
				$seff = JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=contributions');
				if (strstr( $seff, 'index' )) {
					$seff .= '&amp;'.$qs;
				} else {
					$seff .= '?'.$qs;
				}
				$html .= ' | <a href="'.$seff.'">'.JText::_('PLG_MEMBERS_CONTRIBUTIONS_MORE').'</a>';
			}
		}
		$html .= '</p>'."\n\n";
		$html .= '</div><!-- / #'.$divid.' -->'."\n";
	}
	$k++;
}
echo $html;
if (!$foundresults) {
	echo Hubzero_View_Helper_Html::warning( JText::_('PLG_MEMBERS_FAVORITES_NONE') );
}
?>
</div><!-- / .subject -->
	<div class="clear"></div>
</form>