<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_resources_wishlist' );
JPlugin::loadLanguage( 'com_wishlist' );
	
//-----------

class plgResourcesWishlist extends JPlugin
{
	function plgResourcesWishlist(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'resources', 'wishlist' );
		$this->_params = new JParameter( $this->_plugin->params );
		
		// Get the component parameters
		$wconfig = & JComponentHelper::getParams( 'com_wishlist' );
		$this->config = $wconfig;
	}
	
	//-----------
	
	function &onResourcesAreas( $resource ) 
	{
		if ($resource->_type->_params->get('plg_wishlist')) {
			$areas = array(
				'wishlist' => JText::_('Wishlist')
			);
		} else {
			$areas = array();
		}
		
		return $areas;
	}

	//-----------

	function onResources( $resource, $option, $areas, $rtrn='all' )
	{
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) {
			if (!array_intersect( $areas, $this->onResourcesAreas( $resource ) ) 
			&& !array_intersect( $areas, array_keys( $this->onResourcesAreas( $resource ) ) )) {
				$rtrn = 'metadata';
			}
		}
		
		// Display only for tools		
		if ($resource->type != 7) {
			return array('html'=>'','metadata'=>'');
		}
		
		$database =& JFactory::getDBO();
		$juser 	  =& JFactory::getUser();
		
		$option = 'com_wishlist';
		$cat 	= 'resource';
		$refid  = $resource->id;
		$items  = 0;
		$admin  = 0;
		$html	= '';		
		
		// Include some classes & scripts
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'wishlist.wishlist.php' );
		ximport('Hubzero_View_Helper_Html');
		require_once( JPATH_ROOT.DS.'components'.DS.$option.DS.'controller.php' );
		//require_once( JPATH_ROOT.DS.'components'.DS.$option.DS.'wishlist.html.php' );
		
		// Configure controller
		WishlistController::setVar('_option', $option);
		WishlistController::setVar('banking', $this->config->get('banking'));
		
		// Get filters
		$filters = WishlistController::getFilters(0);
		$filters['limit'] = $this->_params->get('limit');
		
		// Load some objects
		$obj = new Wishlist( $database );
		$objWish = new Wish( $database );
		$objOwner = new WishlistOwner( $database );
		
		// Get wishlist id
		$id = $obj->get_wishlistID($refid, $cat);
		
		// Create a new list if necessary
		if(!$id) {
			if($resource->title && $resource->standalone == 1  && $resource->published == 1) {
				$rtitle = ($resource->type=='7'  && isset($resource->alias)) ? JText::_('WISHLIST_NAME_RESOURCE_TOOL').' '.$resource->alias : JText::_('WISHLIST_NAME_RESOURCE_ID').' '.$resource->id;
				$id = $obj->createlist($cat, $refid, 1, $rtitle, $resource->title);
			}				
		}
		
		// get wishlist data
		$wishlist = $obj->get_wishlist($id, $refid, $cat);
		
		if(!$wishlist) {
			$html = Hubzero_View_Helper_Html::error(JText::_('ERROR_WISHLIST_NOT_FOUND'));
		}
		else {
			// Get list owners
			$owners   = $objOwner->get_owners($id, $this->config->get('group') , $wishlist);
			
			// Authorize admins & list owners
			if(!$juser->get('guest')) {
				if ($juser->authorize($option, 'manage')) {
					$admin = 1;
				}
				if(in_array($juser->get('id'), $owners['individuals'])) {
					$admin = 2;
				}
				else if(in_array($juser->get('id'), $owners['advisory'])) {
					$admin = 3;
				}
			}
			else if(!$wishlist->public && $rtrn != 'metadata') {
				// not authorized
				JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
				return;
			}
			
			$items = $objWish->get_count ($id, $filters, $admin);	
			
			if($rtrn != 'metadata') {
				// Add the CSS to the template
				WishlistController::_getStyles();
				
				// Thumbs voting CSS & JS
				WishlistController::_getStyles('com_answers', 'vote.css');
				
				// Get wishes
				$wishlist->items = $objWish->get_wishes($wishlist->id, $filters, $admin, $juser);
				
				$title = ($admin) ?  JText::_('WISHLIST_TITLE_PRIORITIZED') : JText::_('WISHLIST_TITLE_RECENT_WISHES');
				if(count($wishlist->items) > 0 && $items > $filters['limit']) {
					$title.= ' (<a href="'.JRoute::_('index.php?option='.$option.a.'task=wishlist'.a.'category='. $wishlist->category.a.'rid='.$wishlist->referenceid).'">'.JText::_('view all') .' '.$items.'</a>)';
				}
				else {
					$title .= ' ('.$items.')';
				}
				// HTML output
				// Instantiate a view
				ximport('Hubzero_Plugin_View');
				$view = new Hubzero_Plugin_View(
					array(
						'folder'=>'resources',
						'element'=>'wishlist',
						'name'=>'browse'
					)
				);
	
				// Pass the view some info
				$view->option = $option;
				$view->resource = $resource;
				$view->title = $title;
				$view->wishlist = $wishlist;
				$view->filters = $filters;
				$view->admin = $admin;
				$view->config = $this->config;
				if ($this->getError()) {
					$view->setError( $this->getError() );
				}
	
				// Return the output
				$html = $view->loadTemplate();
				}						
		}
					
		// Build the HTML meant for the "about" tab's metadata overview
		$metadata = '';
			if ($rtrn == 'all' || $rtrn == 'metadata') {
				$metadata  = '<p class="wishlist"><a href="'.JRoute::_('index.php?option=com_resources'.a.'id='.$resource->id.a.'active=wishlist').'">'.JText::sprintf('NUM_WISHES',$items);
				$metadata .= '</a> (<a href="'.JRoute::_('index.php?option='.$option.a.'id='.$id.a.'task=add').'">'.JText::_('ADD_NEW_WISH').'</a>)</p>'.n;
		}
		
		$arr = array(
				'html'=>$html,
				'metadata'=>$metadata
			);

		return $arr;
	}
	
	//-------------------
}