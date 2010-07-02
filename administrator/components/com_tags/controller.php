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

ximport('Hubzero_Controller');

class TagsController extends Hubzero_Controller
{
	public function execute()
	{
		$this->_task = JRequest::getVar( 'task', '' );
		
		switch ($this->_task) 
		{
			case 'new':    $this->add();    break;
			case 'add':    $this->add();    break;
			case 'edit':   $this->edit();   break;
			case 'cancel': $this->cancel(); break;
			case 'save':   $this->save();   break;
			case 'remove': $this->remove(); break;
			case 'merge':  $this->merge();  break;
			case 'pierce': $this->pierce();  break;
			case 'browse': $this->browse(); break;
			
			default: $this->browse(); break;
		}
	}

	//----------------------------------------------------------
	// Tag functions
	//----------------------------------------------------------

	protected function browse()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'tags') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();
		
		// Incoming
		$view->filters = array();
		$view->filters['limit']  = $app->getUserStateFromRequest($this->_option.'.browse.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start']  = $app->getUserStateFromRequest($this->_option.'.browse.limitstart', 'limitstart', 0, 'int');
		$view->filters['search'] = urldecode(trim($app->getUserStateFromRequest($this->_option.'.browse.search','search', '')));
		$view->filters['by']     = trim($app->getUserStateFromRequest($this->_option.'.browse.by', 'filterby', 'all'));
		
		$t = new TagsTag( $this->database );

		// Record count
		$view->total = $t->getCount( $view->filters );
		
		$view->filters['limit'] = ($view->filters['limit'] == 0) ? 'all' : $view->filters['limit'];
		
		// Get records
		$view->rows = $t->getRecords( $view->filters );

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}

	//-----------
	
	protected function add() 
	{
		$this->edit();
	}

	//-----------

	protected function edit($tag=NULL)
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'tag') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		
		// Incoming
		$id = JRequest::getInt( 'id', 0, 'request' );
	
		// Load a tag object if one doesn't already exist
		if (!$tag) {
			$tag = new TagsTag( $this->database );
			$tag->load( $id );
		}
		
		$view->tag = $tag;
		
		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}

	//-----------

	protected function cancel()
	{
		$this->_redirect = 'index.php?option='.$this->_option;
	}

	//-----------
	
	protected function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$row = new TagsTag( $this->database );
		if (!$row->bind( $_POST )) {
			$this->setError( $row->getError() );
			$this->edit($row);
			return;
		}
		
		$row->admin = JRequest::getInt('admin', 0);
		$row->raw_tag = trim($row->raw_tag);
		
		$t = new Tags();
		$row->tag = $t->normalize_tag($row->raw_tag);

		// Check content
		if (!$row->check()) {
			$this->setError( $row->getError() );
			$this->edit($row);
			return;
		}

		// Make sure the tag doesn't already exist
		if (!$row->id) {
			if ($row->checkExistence()) {
				$this->setError( JText::_('TAG_EXIST') );
				$this->edit($row);
				return;
			}
		}

		// Store new content
		if (!$row->store()) {
			$this->setError( $row->getError() );
			$this->edit($row);
			return;
		}
	
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_( 'TAG_SAVED' );
	}

	//-----------

	protected function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$ids = JRequest::getVar('id', array());
		if (!is_array( $ids )) {
			$ids = array();
		}
		
		// Make sure we have an ID
		if (empty($ids)) {
			$this->_redirect = 'index.php?option='.$this->_option;
			return;
		}
		
		// Get Tags plugins
		JPluginHelper::importPlugin('tags');
		$dispatcher =& JDispatcher::getInstance();
		
		foreach ($ids as $id) 
		{
			// Remove references to the tag
			$dispatcher->trigger( 'onTagDelete', array($id) );
			
			// Remove the tag
			$tag = new TagsTag( $this->database );
			$tag->delete( $id );
		}
	
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_( 'TAG_REMOVED' );
	}
	
	//-----------

	protected function merge()
	{
		// Incoming
		$ids  = JRequest::getVar('id', array());
		$step = JRequest::getInt('step', 1);
		$step = ($step) ? $step : 1;
		
		if (!is_array($ids)) {
			$ids = array(0);
		}
		
		// Make sure we have some IDs to work with
		if ($step == 1 && (!$ids || count($ids) < 1)) {
			$this->_redirect = 'index.php?option='.$this->_option;
			return;
		}
		
		$idstr = implode(',',$ids);
		
		switch ($step)
		{
			case 1:
				// Instantiate a new view
				$view = new JView( array('name'=>'merge') );
				$view->option = $this->_option;
				$view->task = $this->_task;
				$view->step = 2;
				$view->idstr = $idstr;
				$view->tags = array();
				$to = new TagsObject( $this->database );
				
				// Loop through the IDs of the tags we want to merge
				foreach ($ids as $id) 
				{
					// Load the tag's info
					$tag = new TagsTag( $this->database );
					$tag->load( $id );
					
					// Get the total number of items associated with this tag
					$tag->total = $to->getCount( $id );
					
					// Add the tag object to an array
					$view->tags[] = $tag;
				}
				
				// Get all tags
				$t = new TagsTag( $this->database );
				$view->rows = $t->getAllTags(true);
				
				// Set any errors
				if ($this->getError()) {
					$view->setError( $this->getError() );
				}

				// Output the HTML
				$view->display();
			break;
			
			case 2:
				// Check for request forgeries
				JRequest::checkToken() or jexit( 'Invalid Token' );
			
				// Get the string of tag IDs we plan to merge
				$ind = JRequest::getVar('ids', '', 'post');
				if ($ind) {
					$ids = explode(',',$ind);
				} else {
					$ids = array();
				}
				
				// Incoming
				$tag_exist = JRequest::getInt('existingtag', 0, 'post');
				$tag_new   = JRequest::getVar('newtag', '', 'post');
				
				// Are we merging tags into a totally new tag?
				if ($tag_new) {
					// Yes, we are
					$_POST['raw_tag'] = $tag_new;
					$_POST['alias'] = '';
					$_POST['description'] = '';
					
					$this->save(0);
					
					$tagging = new Tags( $this->database );
					$mtag = $tagging->get_raw_tag_id($tag_new);
				} else {
					// No, we're merging into an existing tag
					$mtag = $tag_exist;
				}
				
				$to = new TagsObject( $this->database );

				foreach ($ids as $id)
				{
					if ($mtag != $id) {
						// Get all the associations to this tag
						// Loop through the associations and link them to a different tag
						$to->moveObjects($id, $mtag);
						
						// Delete the tag
						$tag = new TagsTag( $this->database );
						$tag->delete( $id );
					}
				}
				
				$this->_redirect = 'index.php?option='.$this->_option;
				$this->_message = JText::_( 'TAGS_MERGED' );
			break;
		}
	}
	
	//-----------

	protected function pierce()
	{
		// Incoming
		$ids  = JRequest::getVar('id', array());
		$step = JRequest::getInt('step', 1);
		$step = ($step) ? $step : 1;
		
		if (!is_array($ids)) {
			$ids = array(0);
		}
		
		// Make sure we have some IDs to work with
		if ($step == 1 && (!$ids || count($ids) < 1)) {
			$this->_redirect = 'index.php?option='.$this->_option;
			return;
		}
		
		$idstr = implode(',',$ids);
		
		switch ($step)
		{
			case 1:
				// Instantiate a new view
				$view = new JView( array('name'=>'pierce') );
				$view->option = $this->_option;
				$view->task = $this->_task;
				$view->step = 2;
				$view->idstr = $idstr;
				$view->tags = array();
				$to = new TagsObject( $this->database );
				// Loop through the IDs of the tags we want to merge
				foreach ($ids as $id) 
				{
					// Load the tag's info
					$tag = new TagsTag( $this->database );
					$tag->load( $id );
					
					// Get the total number of items associated with this tag
					$tag->total = $to->getCount( $id );
					
					// Add the tag object to an array
					$view->tags[] = $tag;
				}
				
				// Get all tags
				$t = new TagsTag( $this->database );
				$view->rows = $t->getAllTags(true);
				
				// Set any errors
				if ($this->getError()) {
					$view->setError( $this->getError() );
				}

				// Output the HTML
				$view->display();
			break;
			
			case 2:
				// Check for request forgeries
				JRequest::checkToken() or jexit( 'Invalid Token' );
				
				// Get the string of tag IDs we plan to merge
				$ind = JRequest::getVar('ids', '', 'post');
				if ($ind) {
					$ids = explode(',',$ind);
				} else {
					$ids = array();
				}
				
				// Incoming
				$tag_exist = JRequest::getInt('existingtag', 0, 'post');
				$tag_new   = JRequest::getVar('newtag', '', 'post');
				
				// Are we merging tags into a totally new tag?
				if ($tag_new) {
					// Yes, we are
					$_POST['raw_tag'] = $tag_new;
					$_POST['alias'] = '';
					$_POST['description'] = '';
					
					$this->save(0);
					
					$tagging = new Tags( $this->database );
					$mtag = $tagging->get_raw_tag_id($tag_new);
				} else {
					// No, we're merging into an existing tag
					$mtag = $tag_exist;
				}
				
				$to = new TagsObject( $this->database );
				
				foreach ($ids as $id)
				{
					if ($mtag != $id) {
						// Get all the associations to this tag
						// Loop through the associations and link them to a different tag
						$to->copyObjects($id, $mtag);
					}
				}
				
				$this->_redirect = 'index.php?option='.$this->_option;
				$this->_message = JText::_( 'TAGS_COPIED' );
			break;
		}
	}
}
