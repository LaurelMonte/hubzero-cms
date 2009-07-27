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

//----------------------------------------------------------

$config = JFactory::getConfig();

//if ($config->getValue('config.debug')) {
	error_reporting(E_ALL);
	@ini_set('display_errors','1');
//}


ximport( 'bankaccount' );
ximport('xuser');
ximport('textfilter');


require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'store.class.php' );
require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'store.config.php' );
require_once( JPATH_ROOT.DS.'components'.DS.$option.DS.'store.html.php' );
require_once( JPATH_ROOT.DS.'components'.DS.$option.DS.'controller.php' );


// Instantiate controller
$controller = new StoreController();
$controller->mainframe = $mainframe;
$controller->execute();
$controller->redirect();


?>