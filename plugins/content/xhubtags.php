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

$mainframe->registerEvent( 'onPrepareContent', 'plgContentXHubTags' );

function plgContentXHubTags( &$row, &$params, $page=0 ) 
{
	// expression to search for
	$regex = "/\{xhub:\s*[^\}]*\}/i";
    
	if (!is_object($params)) // weblinks is somehow calling this with null params
		return false;

	// check whether plugin has been unpublished
	if ( !$params->get( 'enabled', 1 ) ) {
		$row->text = preg_replace( $regex, '', $row->text );

		return true;
	}

	// find all instances of plugin and put in $matches
	$count = preg_match_all( $regex, $row->text, $matches );

	if ( $count )
		plgContentXHubTagsProcess( $row, $matches, $count, $regex);
}

function plgContentXHubTagsProcess( &$row, &$matches, $count, $regex )
{
	for ( $i=0; $i < $count; $i++ )
	{
		$regex = "/\{xhub:\s*([^\s]+)\s*(.*)/i";
		if ( preg_match($regex, $matches[0][$i], $tag) ) 
		{
			if ($tag[1] == "include")
				$text = plgContentXHubTagsInclude($tag[2]);
			else if ($tag[1] == "image")
				$text = plgContentXHubTagsImage($tag[2]);
			else if ($tag[1] == "module")
				$text = plgContentXHubTagsModules($tag[2]);
			else if (($tag[1] == 'templatedir') || ($tag[1] == 'templatedir}'))
				$text = plgContentXHubTagsTemplateDir();
			else if (($tag[1] == 'getCfg') || ($tag[1] == 'getcfg'))
				$text = plgContentXhubTagsGetCfg($tag[2]);
			else
				$text = "";

			$row->text = str_replace($matches[0][$i], $text, $row->text);
		}
	}
}

/*
 * {xhub:module position="position" style="style"}
 */

function plgContentXHubTagsModules($options)
{
    global $mainframe;

    $regex = "/position\s*=\s*(\"|&quot;)([^\"]+)(\"|&quot;)/i";
    
	if (!preg_match($regex, $options, $position))
        return "";

    $regex = "/style\s*=\s*(\"|&quot;)([^\"]+)(\"|&quot;)/i";

	if (!preg_match($regex, $options, $style))
        $style[2] = "-2";

    ximport('xmodule');

    return XModuleHelper::renderModules($position[2],$style[2]);
}

/*
 * {xhub:templatedir}
 *
 */

function plgContentXhubTagsTemplateDir()
{
	global $mainframe;

	$template = $mainframe->getTemplate();
	return "/templates/$template";
}

/*
 * {xhub:include type="script" component="component" filename="filename"}
 * {xhub:include type="stylesheet" component="component" filename="filename"}
 */

function plgContentXHubTagsInclude($options)
{
	global $mainframe;

	$regex = "/type\s*=\s*(\"|&quot;)(script|stylesheet)(\"|&quot;)/i";

	if (!preg_match($regex, $options, $type))
		return "";

	$regex = "/filename\s*=\s*(\"|&quot;)([^\"&]+)(\"|&quot;)/i";
    
	if (!preg_match($regex, $options, $file))
		return "";

	$regex = "/component\s*=\s*(\"|&quot;)([^\"&]+)(\"|&quot;)/i";
	
	$template = $mainframe->getTemplate();

	if ($file[2][0] == '/')
		$filename = $file[2];
	else if (preg_match($regex, $options, $component))  {
		$filename = 'templates/' . $template . '/html/' . $component[2] . '/' . $file[2];
		if (!file_exists(JPATH_SITE . DS . $filename))
			$filename  = 'components/' . $component[2] . '/' . $file[2];
		$filename = DS.$filename;
		//$filename = JURI::base() . $filename;
	}
	else
	{
		//$filename = JURI::base(). "templates/$template/";
		// Removed JURI::base() because it would add http:// to files even 
		// when the site is https:// thus causing warnings in browsers
		$filename = "/templates/$template/";
		if ($type[2] == 'script')
			$filename .= 'js/';
		else
			$filename .= 'css/';
		$filename .= $file[2];
	}

	$document = &JFactory::getDocument();

	if ($type[2] == "script")
		$document->addScript($filename);
	else if ($type[2] == "stylesheet")
		$document->addStyleSheet($filename,"text/css","screen");

	return "";
}

/* {xhub:image component="component" filename="filename"} */

function plgContentXHubTagsImage($options)
{
	global $mainframe;

	$regex = "/filename\s*=\s*(\"|&quot;)([^\"&]+)(\"|&quot;)/i";

	if (!preg_match($regex, $options, $file))
	                return "";

	$regex = "/component\s*=\s*(\"|&quot;)([^\"&]+)(\"|&quot;)/i";

	if (!preg_match($regex, $options, $component))
	{
		$regex = "/module\s*=\s*(\"|&quot;)([^\"&]+)(\"|&quot;)/i";

		preg_match($regex, $options, $module);
	}

        ximport('xdocument');
	$template = $mainframe->getTemplate();
	if (empty($component) && empty($module))
		return substr(XDocument::getHubImage($file[2]),1);
	else if (!empty($component))
		return substr(XDocument::getComponentImage($component[2], $file[2]),1);
	else if (!empty($module))
		return substr(XDcoument::getModuleImage($module[2],$file[2]),1);
	
	return "";
}

/* {xhub:getcfg variable} */

function plgContentXhubTagsGetCfg($options)
{
	$options = trim($options," \n\t\r}");

	$xhub =& XFactory::getHub();

	return $xhub->getCfg($options);
}
		
?>