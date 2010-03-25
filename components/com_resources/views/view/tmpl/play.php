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

$html = '';
if ($this->resource->type == 4) {
	$parameters =& new JParameter( $this->resource->params );
	
	$this->helper->getChildren();

	$children = $this->helper->children;
	
	// We're going through a learning module
	$html .= '<div class="aside">'."\n";
	$n = count($children);
	$i = 0;
	$blorp = 0;
		
	$html .= '<ul class="sub-nav">'."\n";
	foreach ($children as $child) 
	{
		$attribs =& new JParameter( $child->attribs );

		if ($attribs->get( 'exclude', '' ) != 1) {
			$params =& new JParameter( $child->params );
			$link_action = $params->get( 'link_action', '' );
			switch ($child->logicaltype)
			{
				case 19: $class = ' class="withoutaudio'; break;
				case 20: $class = ' class="withaudio';    break;
				default: 
					if ($child->type == 33) {
						$class = ' class="pdf';
					} else {
						$class = ' class="';
					}
					break;
			}
			$class .= ($this->resid == $child->id || ($this->resid == '' && $i == 0)) ? ' active"': '"';

			$i++;
			if ((!$child->grouping && $blorp) || ($child->grouping && $blorp && $child->grouping != $blorp)) {
				$blorp = '';
				$html .= "\t".'</ul>'."\n";
				$html .= ' </li>'."\n";
			}
			if ($child->grouping && !$blorp) {
				$blorp = $child->grouping;

				$type = new ResourcesType( $this->database );
				$type->load( $child->grouping );
				
				$html .= ' <li class="grouping"><span>'.$type->type.'</span>'."\n";
				$html .= "\t".'<ul id="'.strtolower($type->type).$i.'">'."\n";
			}
			$html .= ($blorp) ? "\t" : '';
			$html .= ' <li'.$class.'>';
		
			$url  = ($link_action == 1) 
				  ? checkPath($child->path, $child->type, $child->logicaltype)
				  : JRoute::_('index.php?option='.$this->option.'&id='.$this->resource->id.'&resid='. $child->id);
			$html .= '<a href="'.$url.'" ';
			if ($link_action == 1) {
				$html .= 'target="_blank" ';
			} elseif($link_action == 2) {
				$html .= 'onclick="popupWindow(\''.$child->path.'\', \''.$child->title.'\', 400, 400, \'auto\');" ';
			}
			$html .= '>'. $child->title .'</a>';
			$html .= ($child->type == 33) 
				   ? ' '.ResourcesHtml::getFileAttribs( $child->path, '', $this->fsize ) 
				   : '';
			$html .= '</li>'."\n";
			if ($i == $n && $blorp) {
				$html .= "\t".'</ul>'."\n";
				$html .= ' </li>'."\n";
			}
		}
	}
	$html .= '</ul>'."\n";
	$html .= ResourcesHtml::license( $parameters->get( 'license', '' ) );
	$html .= '</div><!-- / .aside -->'."\n";
	$html .= '<div class="subject">'."\n";

	// Playing a learning module
	if (is_object($this->activechild)) {
		if (!$this->activechild->path) {
			// Output just text
			$html .= '<h3>'.stripslashes($this->activechild->title).'</h3>';
			$html .= stripslashes($this->activechild->fulltext);
		} else {
			// Output content in iFrame
			$html .= '<iframe src="'.$this->activechild->path.'" width="97%" height="500" name="lm_resource" frameborder="0" bgcolor="white"></iframe>'."\n";
		}
	}

	$html .= '</div><!-- / .subject -->'."\n";
	$html .= '<div class="clear"></div>'."\n";
} else {
	$url = $this->activechild->path;
	
	// Get some attributes
	$attribs =& new JParameter( $this->activechild->attribs );
	$width  = $attribs->get( 'width', '' );
	$height = $attribs->get( 'height', '' );
	
	$type = '';
	$arr  = explode('.',$url);
	$type = end($arr);
	$type = (strlen($type) > 4) ? 'html' : $type;
	$type = (strlen($type) > 3) ? substr($type, 0, 3) : $type;

	$width = (intval($width) > 0) ? $width : 0;
	$height = (intval($height) > 0) ? $height : 0;
	
	if (is_file(JPATH_ROOT.$url)) {
		if (strtolower($type) == 'swf') {
			$height = '400px';
			if ($this->no_html) {
				$height = '100%';
			}
			$html .= '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,65,0" width="100%" height="'.$height.'" id="SlideContent" VIEWASTEXT>'."\n";
			$html .= ' <param name="movie" value="'. $url .'" />'."\n";
			$html .= ' <param name="quality" value="high" />'."\n";
			$html .= ' <param name="menu" value="false" />'."\n";
			$html .= ' <param name="loop" value="false" />'."\n";
			$html .= ' <param name="scale" value="showall" />'."\n";
			$html .= ' <embed src="'. $url .'" menu="false" quality="best" loop="false" width="100%" height="'.$height.'" scale="showall" name="SlideContent" align="" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" swLiveConnect="true"></embed>'."\n";
			$html .= '</object>'."\n";
		} else {
			$html .= '<applet code="Silicon" archive="'. $url .'" width="';
			$html .= ($width > 0) ? $width : '';
			$html .= '" height="';
			$html .= ($height > 0) ? $height : '';
			$html .= '">'."\n";
			if ($width > 0) {
				$html .= ' <param name="width" value="'. $width .'" />'."\n";
			}
			if ($height > 0) {
				$html .= ' <param name="height" value="'. $height .'" />'."\n";
			}
			$html .= '</applet>'."\n";
		}
	} else {
		$html .= '<p class="error">'.JText::_('COM_RESOURCES_FILE_NOT_FOUND').'</p>'."\n";
	}
}
echo $html;
?>