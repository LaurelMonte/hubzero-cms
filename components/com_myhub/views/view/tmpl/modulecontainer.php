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

if ($this->getError()) {
	$html = '<p class="error">'.$this->getError().'</p>';
} else {
	$sttc = explode(',',$this->config->get('static'));
	if (!is_array($sttc)) {
		$sttc = array();
	}
	$sttc = array_map('trim',$sttc);

	$html  = '';

	if ($this->container) {
		$html .= '<div class="draggable" id="mod_'.$this->module->id.'"';
		$html .= '>'."\n";
	}

	if ($this->extras) {
		$html .= "\t".'<div class="cwrap';
		if (!in_array($this->module->module, $sttc)) {
			$html .= '">'."\n";
			// Add the 'close' button
			if ($this->act == 'customize') {
				$html .= '<a class="close" href="'.JRoute::_('index.php?option='.$this->option).'#" onclick="HUB.Myhub.removeModule(this);return false;" title="'.JText::_('COM_MYHUB_REMOVE_MODULE').'">[ X ]</a>';
			}
		} else {
			$html .= ' emphasis">'."\n";
		}
		// Add the module title
		$html .= "\t\t".'<h3 class="handle">'.$this->module->title.'</h3>'."\n";
		$html .= "\t\t".'<div class="body">'."\n";
		if (!in_array($this->module->module, $sttc)) {
			if ($this->rendered != '') {
				$html .= "\t\t\t".'<p class="modcontrols">';
				// Add the 'edit' button
				if ($this->act == 'customize') {
					$html .= '<a class="edimodl" id="e_'.$this->module->id.'" href="'.JRoute::_('index.php?option='.$this->option.'#').'" title="'.JText::_('COM_MYHUB_EDIT_TITLE').'" onclick="return HUB.Myhub.editModule(this, \'f_'.$this->module->id.'\');">'.JText::_('COM_MYHUB_EDIT').'</a>';
				} else {
					$html .= '<a class="edimodl" id="e_'.$this->module->id.'" href="'.JRoute::_('index.php?option='.$this->option.'&act=customize').'" title="'.JText::_('COM_MYHUB_EDIT_TITLE').'">'.JText::_('COM_MYHUB_EDIT').'</a>';
				}
				$html .= '</p>'."\n";
				$html .= "\t\t\t".'<form class="fparams" id="f_'.$this->module->id.'" onsubmit="return HUB.Myhub.saveModule(this,'.$this->module->id.');">'."\n";
				$html .= $this->rendered;
				$html .= "\t\t\t\t".'<input type="submit" name="submit" value="'.JText::_('COM_MYHUB_BUTTON_SAVE').'" />'."\n";
				$html .= "\t\t\t".'</form>'."\n";
			}
		}
	}

	// Is it a custom module (i.e., HTML)?
	if ($this->module->module == 'mod_custom') { 
		$html .= $this->module->content;
	} else {
		$rparams = array();
		$rparams['style'] = 'none';
		$this->module->user = false;
		$html .= JModuleHelper::renderModule($this->module, $rparams);
	}

	if ($this->extras) {
		$html .= "\t\t".'</div><!-- / .body -->'."\n";
		$html .= "\t".'</div><!-- / .cwrap -->'."\n";
	}

	if ($this->container) {
		$html .= '</div><!-- / .draggable #mod_'.$this->module->id.' -->'."\n\n";
	}
}
echo $html;
?>