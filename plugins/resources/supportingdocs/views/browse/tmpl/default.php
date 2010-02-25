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

$database =& JFactory::getDBO();
?>
<div class="supportingdocs">
<h3>
	<a name="supportingdocs"></a>
	<?php echo JText::_('PLG_RESOURCES_SUPPORTINGDOCS'); ?> 
</h3>
<?php
switch ($this->resource->type)
{
	case 7:
		$dls = ResourcesHtml::writeChildren( $this->config, $this->option, $database, $this->resource, $this->helper->children, '', '', '', $this->resource->id, 0 );									
	break;
		
	case 4:
		$dls = '';

		$database->setQuery( "SELECT r.path, r.type, r.title, r.access, r.id, r.standalone, a.* FROM #__resources AS r, #__resource_assoc AS a WHERE a.parent_id=".$this->resource->id." AND r.id=a.child_id AND r.access=1 ORDER BY a.ordering" );
		if ($database->query()) {
			$downloads = $database->loadObjectList();
		}
		$base = $this->config->get('uploadpath');
		if ($downloads) {
			$dls .= '<ul>'."\n";
			foreach ($downloads as $download)
			{
				$ftype = '';
				$liclass = '';
				$file_name_arr = explode('.',$download->path);
				$ftype = end($file_name_arr);
				$ftype = (strlen($ftype) > 3) ? substr($ftype, 0, 3): $ftype;

				if ($download->type == 12) {
					$liclass = ' class="html"';
				} else {
					$liclass = ' class="'.$ftype.'"';
				}

				$url = ResourcesHtml::processPath($this->option, $download, $this->resource->id);

				$dls .= "\t".'<li'.$liclass.'><a href="'.$url.'">'.$download->title.'</a> ';
				$dls .= ResourcesHtml::getFileAttribs( $download->path, $base, 0 );
				$dls .= '</li>'."\n";
			}
			$dls .= '</ul>'."\n";
		} else {
			$dls .= '<p>'.JText::_('PLG_RESOURCES_SUPPORTINGDOCS_NONE').'</p>';
		}
	break;
		
	case 8:
		// show no docs
	break;
	
	case 6:
	case 31:
	case 2:					
		$this->helper->getChildren( $this->resource->id, 0, 'no' );
		$dls = ResourcesHtml::writeChildren( $this->config, $this->option, $database, $this->resource, $this->helper->children, $this->live_site, '', '', $this->resource->id, 0 );
	break;
		
	default:
		$dls = ResourcesHtml::writeChildren( $this->config, $this->option, $database, $this->resource, $this->helper->children, '', '', '', $this->resource->id, 0 );
	break;
}
echo $dls;
?>
</div><!-- / .supportingdocs -->