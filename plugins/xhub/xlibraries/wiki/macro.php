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

//----------------------------------------------------------

class WikiMacro
{
	protected $_name  = NULL;
	protected $_data  = array();
	protected $_db    = NULL;
	protected $_error = NULL;
	
	static protected $thisInstance = NULL;
	
	//-----------
	
	public function __construct( $config=array() )
	{
		$database =& JFactory::getDBO();
		
		$this->_db =& $database;

		$this->args = '';

		// Set the controller name
		if (empty( $this->_name )) {
			if (isset($config['name'])) {
				$this->_name = $config['name'];
			} else {
				$r = null;
				if (!preg_match('/(.*)Macro/i', get_class($this), $r)) {
					echo "Controller::__construct() : Can't get or parse class name.";
				}
				$this->_name = strtolower( $r[1] );
			}
		}
	}

	//-----------

	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}

	//-----------

	static public function getInstance( $config=array() ) 
	{
		if (self::$thisInstance == null) {
			if (isset($config['name'])) {
				$name = $config['name'];
			} else {
				$name = get_class();
			}
			self::$thisInstance = new $name();
		}
		return self::$thisInstance;
	}

	//-----------
	
	public function render() 
	{
		// Abstract function for overloading
	}
	
	//-----------
	
	public function description() 
	{
		return JText::_('Not implemented.');
	}
}
?>