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
// XForum database class
//----------------------------------------------------------

class XForum extends JTable 
{
	var $id         = NULL;  // @var int(11) Primary key
	var $topic      = NULL;  // @var varchar(255)
	var $comment    = NULL;  // @var text
	var $created    = NULL;  // @var datetime (0000-00-00 00:00:00)
	var $created_by = NULL;  // @var int(11)
	var $state      = NULL;  // @var int(2)
	var $sticky     = NULL;  // @var int(2)
	var $parent     = NULL;  // @var int(11)
	var $hits       = NULL;  // @var int(11)
	var $group      = NULL;  // @var int(11)
	var $access     = NULL;  // @var tinyint(2)  0=public, 1=registered, 2=special, 3=protected, 4=private
	var $anonymous  = NULL;  // @var tinyint(2)
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__xforum', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->comment ) == '' or trim( $this->comment ) == JText::_('Enter your comments...')) {
			$this->setError( JText::_('Please provide a comment') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function buildQuery( $filters=array() ) 
	{
		$query  = "FROM $this->_tbl AS c WHERE ";
		if (isset($filters['parent']) && $filters['parent'] != 0) {
			$query .= "c.parent=".$filters['parent']." OR c.id=".$filters['parent']." ORDER BY c.created ASC";
		} else {
			if (isset($filters['group']) && $filters['group'] != 0) {
				$query .= "c.group=".$filters['group']." AND ";
			}
			if (!isset($filters['authorized']) || !$filters['authorized']) {
				$query .= "c.access=0 AND ";
			}
			if (isset($filters['search']) && $filters['search'] != '') {
				$query .= "(c.topic LIKE '%".$filters['search']."%' OR c.comment LIKE '%".$filters['search']."%') AND ";
			}
			$query .= "c.parent=0";
			if (isset($filters['limit']) && $filters['limit'] != 0) {
				if (isset($filters['sticky']) && $filters['sticky'] == false) {
					$query .= " ORDER BY lastpost DESC, c.created DESC";
				} else {
					$query .= " ORDER BY c.sticky DESC, lastpost DESC, c.created DESC";
				}
			}
		}
				
		return $query;
	}
	
	//-----------
	
	public function getCount( $filters=array() ) 
	{
		$filters['limit'] = 0;
		
		$query = "SELECT COUNT(*) ".$this->buildQuery( $filters );
		
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function getRecords( $filters=array() ) 
	{
		$query = "SELECT c.*";
		if (!isset($filters['parent']) || $filters['parent'] == 0) {
			$query .= ", (SELECT COUNT(*) FROM $this->_tbl AS r WHERE r.parent=c.id) AS replies ";
			$query .= ", (SELECT d.created FROM $this->_tbl AS d WHERE d.parent=c.id ORDER BY created DESC LIMIT 1) AS lastpost ";
		}
		$query .= $this->buildQuery( $filters );
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getLastPost( $parent=null ) 
	{
		if (!$parent) {
			$parent = $this->parent;
		}
		if (!$parent) {
			return null;
		}
		
		$query = "SELECT r.* FROM $this->_tbl AS r WHERE r.parent=$parent ORDER BY created DESC LIMIT 1";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function deleteReplies( $parent=null ) 
	{
		if (!$parent) {
			$parent = $this->parent;
		}
		if (!$parent) {
			return null;
		}
		
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE parent=$parent" );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		} else {
			return true;
		}
	}
}

//----------------------------------------------------------
// XForum pagination class
//----------------------------------------------------------

class XForumPagination extends JObject
{
	var $limitstart = null;  // The record number to start dislpaying from
	var $limit = null;       // Number of rows to display per page
	var $total = null;       // Total number of rows
	var $_viewall = false;   // View all flag
	var $forum = null;       // The forum we're paging for

	// Constructor
	public function __construct($total, $limitstart, $limit, $forum)
	{
		// Value/Type checking
		$this->total		= (int) $total;
		$this->limitstart	= (int) max($limitstart, 0);
		$this->limit		= (int) max($limit, 0);
		$this->forum		= (int) $forum;

		if ($this->limit > $this->total) {
			$this->limitstart = 0;
		}

		if (!$this->limit) {
			$this->limit = $total;
			$this->limitstart = 0;
		}

		if ($this->limitstart > $this->total) {
			$this->limitstart -= $this->limitstart % $this->limit;
		}

		// Set the total pages and current page values
		if ($this->limit > 0) {
			$this->set( 'pages.total', ceil($this->total / $this->limit));
			$this->set( 'pages.current', ceil(($this->limitstart + 1) / $this->limit));
		}

		// Set the pagination iteration loop values
		$displayedPages	= 10;
		$this->set( 'pages.start', (floor(($this->get('pages.current') -1) / $displayedPages)) * $displayedPages +1);
		if ($this->get('pages.start') + $displayedPages -1 < $this->get('pages.total')) {
			$this->set( 'pages.stop', $this->get('pages.start') + $displayedPages -1);
		} else {
			$this->set( 'pages.stop', $this->get('pages.total'));
		}

		// If we are viewing all records set the view all flag to true
		if ($this->limit == $total) {
			$this->_viewall = true;
		}
	}

	// Return the rationalised offset for a row with a given index.
	public function getRowOffset($index)
	{
		return $index +1 + $this->limitstart;
	}

	// Return the pagination data object, only creating it if it doesn't already exist
	public function getData()
	{
		static $data;
		if (!is_object($data)) {
			$data = $this->_buildDataObject();
		}
		return $data;
	}

	// Create and return the pagination page list string, ie. Previous, Next, 1 2 3 ... x
	public function getPagesLinks()
	{
		//$lang =& JFactory::getLanguage();

		// Build the page navigation list
		$data = $this->_buildDataObject();

		$list = array();

		$list['pages'] = array(); //make sure it exists
		foreach ($data->pages as $i => $page)
		{
			if ($page->base !== null) {
				$list['pages'][$i]['active'] = true;
				$list['pages'][$i]['data'] = $this->_item_active($page);
			} else {
				$list['pages'][$i]['active'] = false;
				$list['pages'][$i]['data'] = $this->_item_inactive($page);
			}
		}

		if ($data->end->base !== null) {
			$list['end']['active'] = true;
			$list['end']['data'] = $this->_item_active($data->end);
		} else {
			$list['end']['active'] = false;
			$list['end']['data'] = $this->_item_inactive($data->end);
		}

		if ($this->total > $this->limit) {
			return $this->_list_render($list);
		} else {
			return '';
		}
	}

	public function _list_render($list)
	{
		// Initialize variables
		$lang =& JFactory::getLanguage();
		$html = null;

		// Reverse output rendering for right-to-left display
		if ($lang->isRTL()) {
			$list['pages'] = array_reverse( $list['pages'] );
			foreach ( $list['pages'] as $page ) 
			{
				$html .= $page['data'];
			}
			$html .= $list['end']['data'];
		} else {
			foreach ( $list['pages'] as $page ) 
			{
				$html .= $page['data'].' ';
			}
			$html .= $list['end']['data'];
		}
		return $html;
	}

	public function _item_active(&$item)
	{
		return '<a title="'.$item->text.'" href="'.$item->link.'" class="pagenav">'.$item->text.'</a>';
	}

	public function _item_inactive(&$item)
	{
		return '<span class="pagenav">'.$item->text.'</span>';
	}

	/**
	 * Create and return the pagination data object
	 *
	 * @access	public
	 * @return	object	Pagination data object
	 * @since	1.5
	 */
	public function _buildDataObject()
	{
		// Initialize variables
		$data = new stdClass();

		// Set the next and end data objects
		$data->end	= new XForumPaginationObject(JText::_('End'));

		if ($this->get('pages.current') < $this->get('pages.total'))
		{
			$end  = ($this->get('pages.total') -1) * $this->limit;

			$data->end->base	= $end;
			$data->end->link	= JRoute::_('&topic='.$this->forum.'&limitstart='.$end);
		}

		$data->pages = array();
		$stop = $this->get('pages.stop');
		for ($i = $this->get('pages.start'); $i <= $stop; $i ++)
		{
			$offset = ($i -1) * $this->limit;

			$offset = $offset == 0 ? '' : $offset;  //set the empty for removal from route

			$data->pages[$i] = new XForumPaginationObject($i);
			//if ($i != $this->get('pages.current') || $this->_viewall)
			//{
				$data->pages[$i]->base	= $offset;
				$data->pages[$i]->link	= JRoute::_('&topic='.$this->forum.'&limitstart='.$offset);
			//}
		}
		return $data;
	}
}

//----------------------------------------------------------
// XForum Pagination object representing a particular item in the pagination lists
//----------------------------------------------------------

class XForumPaginationObject extends JObject
{
	var $text;
	var $base;
	var $link;

	public function __construct($text, $base=null, $link=null)
	{
		$this->text = $text;
		$this->base = $base;
		$this->link = $link;
	}
}
?>