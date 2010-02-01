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

class ContributeController extends JObject
{	
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;

	//-----------
	
	public function __construct( $config=array() )
	{
		$this->_redirect = NULL;
		$this->_message = NULL;
		$this->_messageType = 'message';
		
		// Set the controller name
		if (empty( $this->_name )) {
			if (isset($config['name'])) {
				$this->_name = $config['name'];
			} else {
				$r = null;
				if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
					echo "Controller::__construct() : Can't get or parse class name.";
				}
				$this->_name = strtolower( $r[1] );
			}
		}
		
		// Set the component name
		$this->_option = 'com_'.$this->_name;
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
	
	private function getTask()
	{
		$juser =& JFactory::getUser();

		$task = JRequest::getVar( 'task', '' );
		$step = JRequest::getInt( 'step', 0 );
		if ($step && !$task) {
			$task = 'start';
		}
		if ($juser->get('guest')) {
			$task = ($task) ? 'login':'';
		}
		$this->_task = $task;
		$this->step = $step;
		
		return $task;
	}
	
	//-----------
	
	public function execute()
	{
		$this->steps = array('Type','Compose','Attach','Authors','Tags','Review');
		
		// Load the com_resources component config
		$config =& JComponentHelper::getParams( 'com_resources' );
		$this->config = $config;

		// Push some styles and scrips to the template
		$this->getStyles();
		$this->getScripts();

		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}

		switch ( $this->getTask() ) 
		{
			case 'rename':       $this->attach_rename();  break;
			case 'saveattach':   $this->attach_save();    break;
			case 'deleteattach': $this->attach_delete();  break;
			case 'attach':       $this->attachments();    break;
			case 'orderupa':     $this->reorder_attach(); break;
			case 'orderdowna':   $this->reorder_attach(); break;
			
			case 'saveauthor':   $this->author_save();    break;
			case 'removeauthor': $this->author_remove();  break;
			case 'authors':      $this->authors();        break;
			case 'orderupc':     $this->reorder_author(); break;
			case 'orderdownc':   $this->reorder_author(); break;
			
			case 'new':     $this->edit();   break;
			case 'edit':    $this->edit();   break;
			case 'save':    $this->save();   break;
			case 'submit':  $this->submit(); break;
			case 'delete':  $this->delete(); break;
			case 'cancel':  $this->delete(); break;
			case 'discard': $this->delete(); break;
			
			case 'start':   $this->steps();  break;
			case 'login':   $this->login();  break;

			default: $this->intro(); break;
		}
	}
	
	//-----------

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message );
		}
	}

	//-----------
	
	private function getStyles( $option='' ) 
	{
		$option = ($option) ? $option : $this->_option;
		ximport('xdocument');
		XDocument::addComponentStylesheet($option);
	}
	
	//-----------
	
	private function getScripts()
	{
		$document =& JFactory::getDocument();
		if (is_file(JPATH_ROOT.DS.'components'.DS.$this->_option.DS.$this->_name.'.js')) {
			$document->addScript('components'.DS.$this->_option.DS.$this->_name.'.js');
		}
	}
	
	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	protected function login()
	{
		// Build the page title
		$title = JText::_(strtoupper($this->_name)).': '.JText::_('LOGIN');
		
		// Write title
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// Output HTML
		echo ContributeHtml::div( ContributeHtml::hed(2, $title), 'full', 'content-header').n;
		echo '<div class="main section">'.n;
		echo ContributeHtml::warning( JText::_('CONTRIBUTE_NOT_LOGGEDIN') );
		ximport('xmodule');
		XModuleHelper::displayModules('force_mod');
		echo '</div><!-- / .main section -->'.n;
	}

	//-----------

	protected function intro()
	{
		// Build the page title
		$title = JText::_(strtoupper($this->_name));
		
		// Write title
		$document =& JFactory::getDocument();
		$document->setTitle( $title );

		jimport( 'joomla.application.component.view');

		// Output HTML
		$view = new JView( array('name'=>'summary') );
		$view->title = $title;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------
	
	protected function check_progress($id)
	{
		$steps = $this->steps;
		$laststep = (count($steps) - 1);
		$stepchecks = array();
		
		$progress['submitted'] = 0;
		for ($i=1, $n=count( $steps ); $i < $n; $i++) 
		{
			$check = 'step_'.$steps[$i].'_check';
			$stepchecks[$steps[$i]] = $this->$check( $id );

			if ($stepchecks[$steps[$i]]) {
				$progress[$steps[$i]] = 1;
				if ($i == $laststep) {
					$progress['submitted'] = 1;
				}
			} else {
				$progress[$steps[$i]] = 0;
			}
		}
		$this->progress = $progress;
	}

	//-----------

	protected function steps() 
	{
		$steps = $this->steps;
		$step  = $this->step;
		if ($step > count($steps)) {
			$step = count($steps);
		}
		
		$pre = ($step > 0) ? $step - 1 : 0;
		$preprocess = 'step_'.strtolower($steps[$pre]).'_process';
		$activestep = 'step_'.strtolower($steps[$step]);
		
		if (isset($_POST['step'])) {
			$this->$preprocess();
		}
		
		$id = JRequest::getInt( 'id', 0 );
		
		$this->check_progress($id);
		
		$this->$activestep();
	}
	
	//----------------------------------------------------------
	// Steps
	//----------------------------------------------------------
	
	protected function step_type()
	{
		$step = $this->step;
		$step++;
		
		// Build the page title
		$title = JText::_(strtoupper($this->_name)).': '.JText::_('GETTING_STARTED');
		
		// Write title
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		$database =& JFactory::getDBO();
		$rt = new ResourcesType( $database );
		$types = $rt->getMajorTypes();
		
		// Output HTML
		echo ContributeHtml::div( ContributeHtml::hed(2, $title), 'full', 'content-header').n;
		echo '<div class="main section">'.n;
		ContributeHtml::stepType( $this->_option, $step, $types );
		echo '</div><!-- / .main section -->'.n;
	}
	
	//-----------
	
	protected function step_compose()
	{
		$step = $this->step;
		$next_step = $step+1;
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		
		// Instantiate a new resource object
		$database =& JFactory::getDBO();
		$row = new ResourcesResource( $database );
		if ($id) {
			// Load the resource
			$row->load( $id );
		} else {
			// Load the type and set the state
			$row->type = JRequest::getVar( 'type', '' );
			$row->published = 2;
		}
		
		// Build the page title
		$title = JText::_(strtoupper($this->_name)).': '.JText::sprintf('STEP_NUMBER', $step).': '.JText::_('STEP_'.strtoupper($this->steps[$step]));
		
		// Write title
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// Output HTML
		echo ContributeHtml::div( ContributeHtml::hed(2, $title), 'full', 'content-header').n;
		echo '<div class="main section">'.n;
		ContributeHtml::writeSteps( $this->steps, $this->progress, $this->_option, $step, $id );
		ContributeHtml::stepCompose( $database, $this->_option, 'start', $row, $this->config, $next_step );
		echo '</div><!-- / .main section -->'.n;
	}
	
	//-----------
	
	protected function step_attach()
	{
		$step = $this->step;
		$next_step = $step+1;
		
		// Build the page title
		$title = JText::_(strtoupper($this->_name)).': '.JText::sprintf('STEP_NUMBER', $step).': '.JText::_('STEP_'.strtoupper($this->steps[$step]));
		
		// Write title
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		
		// Ensure we have an ID to work with
		if (!$id) {
			echo ContributeHtml::div( ContributeHtml::hed(2, $title), 'full', 'content-header').n;
			echo '<div class="main section">'.n;
			echo ContributeHtml::error( JText::_('CONTRIBUTE_NO_ID') );
			echo '</div><!-- / .main section -->'.n;
			return;
		}
		
		// Load the resource
		$database =& JFactory::getDBO();
		$row = new ResourcesResource( $database );
		$row->load( $id );
		
		// Output HTML
		echo ContributeHtml::div( ContributeHtml::hed(2, $title), 'full', 'content-header').n;
		echo '<div class="main section">'.n;
		ContributeHtml::writeSteps( $this->steps, $this->progress, $this->_option, $step, $id );
		ContributeHtml::stepAttach( $this->_option, 'start', $id, $row->type, $next_step );
		echo '</div><!-- / .main section -->'.n;
	}

	//-----------

	protected function step_authors()
	{
		$step = $this->_data['step'];
		$next_step = $step+1;
		
		// Build the page title
		$title = JText::_(strtoupper($this->_name)).': '.JText::sprintf('STEP_NUMBER', $step).': '.JText::_('STEP_'.strtoupper($this->steps[$step]));
		
		// Write title
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		
		// Ensure we have an ID to work with
		if (!$id) {
			echo ContributeHtml::div( ContributeHtml::hed(2, $title), 'full', 'content-header').n;
			echo '<div class="main section">'.n;
			echo ContributeHtml::error( JText::_('CONTRIBUTE_NO_ID') );
			echo '</div><!-- / .main section -->'.n;
			return;
		}
		
		// Load the resource
		$database =& JFactory::getDBO();
		$row = new ResourcesResource( $database );
		$row->load( $id );
		
		$accesses = array('Public','Registered','Special','Protected','Private');
		
		$lists = array();
		$lists['access'] = ContributeHtml::selectAccess($accesses, $row->access);
		
		// Get groups
		$juser =& JFactory::getUser();

		ximport('xuserhelper');
			
		$groups = XUserHelper::getGroups( $juser->get('id'), 'members' );
		
		// build <select> of groups
		$lists['groups'] = ContributeHtml::selectGroup($groups, $row->group_owner);

		echo ContributeHtml::div( ContributeHtml::hed(2, $title), 'full', 'content-header').n;
		echo '<div class="main section">'.n;
		ContributeHtml::writeSteps( $this->steps, $this->progress, $this->_option, $step, $id );
		ContributeHtml::stepAuthors( $this->_option, 'start', $id, $next_step, $lists );
		echo '</div><!-- / .main section -->'.n;
	}

	//-----------

	protected function step_tags()
	{
		$step = $this->step;
		$next_step = $step+1;
		
		// Build the page title
		$title = JText::_(strtoupper($this->_name)).': '.JText::sprintf('STEP_NUMBER', $step).': '.JText::_('STEP_'.strtoupper($this->steps[$step]));
		
		// Write title
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		
		// Ensure we have an ID to work with
		if (!$id) {
			echo ContributeHtml::div( ContributeHtml::hed(2, $title), 'full', 'content-header').n;
			echo '<div class="main section">'.n;
			echo ContributeHtml::error( JText::_('CONTRIBUTE_NO_ID') );
			echo '</div><!-- / .main section -->'.n;
			return;
		}
		
		// Get any HUB focus areas
		// These are used where any resource is required to have one of these tags
		$tconfig =& JComponentHelper::getParams( 'com_tags' );
		$fa1 = $tconfig->get('focus_area_01');
		$fa2 = $tconfig->get('focus_area_02');
		$fa3 = $tconfig->get('focus_area_03');
		$fa4 = $tconfig->get('focus_area_04');
		$fa5 = $tconfig->get('focus_area_05');
		$fa6 = $tconfig->get('focus_area_06');
		$fa7 = $tconfig->get('focus_area_07');
		$fa8 = $tconfig->get('focus_area_08');
		$fa9 = $tconfig->get('focus_area_09');
		$fa10 = $tconfig->get('focus_area_10');
		
		// Instantiate our tag object
		$database =& JFactory::getDBO();
		$tagcloud = new ResourcesTags($database);

		// Normalize the focus areas
		$tagfa1 = $tagcloud->normalize_tag($fa1);
		$tagfa2 = $tagcloud->normalize_tag($fa2);
		$tagfa3 = $tagcloud->normalize_tag($fa3);
		$tagfa4 = $tagcloud->normalize_tag($fa4);
		$tagfa5 = $tagcloud->normalize_tag($fa5);
		$tagfa6 = $tagcloud->normalize_tag($fa6);
		$tagfa7 = $tagcloud->normalize_tag($fa7);
		$tagfa8 = $tagcloud->normalize_tag($fa8);
		$tagfa9 = $tagcloud->normalize_tag($fa9);
		$tagfa10 = $tagcloud->normalize_tag($fa10);
		
		// Get all the tags on this resource
		$tags_men = $tagcloud->get_tags_on_object($id, 0, 0, 0, 0);
		$mytagarray = array();
		$tagfa = '';

		$fas = array($tagfa1,$tagfa2,$tagfa3,$tagfa4,$tagfa5,$tagfa6,$tagfa7,$tagfa8,$tagfa9,$tagfa10);
		$fats = array();
		if ($fa1) {
			$fats[$fa1] = $tagfa1;
		}
		if ($fa2) {
			$fats[$fa2] = $tagfa2;
		}
		if ($fa3) {
			$fats[$fa3] = $tagfa3;
		}
		if ($fa4) {
			$fats[$fa4] = $tagfa4;
		}
		if ($fa5) {
			$fats[$fa5] = $tagfa5;
		}
		if ($fa6) {
			$fats[$fa6] = $tagfa6;
		}
		if ($fa7) {
			$fats[$fa7] = $tagfa7;
		}
		if ($fa8) {
			$fats[$fa8] = $tagfa8;
		}
		if ($fa9) {
			$fats[$fa9] = $tagfa9;
		}
		if ($fa10) {
			$fats[$fa10] = $tagfa10;
		}

		// Loop through all the tags and pull out the focus areas - those will be displayed differently
		foreach ($tags_men as $tag_men)
		{
			if (in_array($tag_men['tag'],$fas)) {
				$tagfa = $tag_men['tag'];
			} else {
				$mytagarray[] = $tag_men['raw_tag'];
			}
		}
		$tags = implode( ', ', $mytagarray );

		// Output HTML
		echo ContributeHtml::div( ContributeHtml::hed(2, $title), 'full', 'content-header').n;
		echo '<div class="main section">'.n;
		ContributeHtml::writeSteps( $this->steps, $this->progress, $this->_option, $step, $id );
		ContributeHtml::stepTags( $this->_option, 'start', $id, $tags, '', $next_step, $tagfa, $fats );
		echo '</div><!-- / .main section -->'.n;
	}

	//-----------

	protected function step_review()
	{
		$step = $this->step;
		$next_step = $step+1;
		
		// Build the page title
		$title = JText::_(strtoupper($this->_name)).': '.JText::sprintf('STEP_NUMBER', $step).': '.JText::_('STEP_'.strtoupper($this->steps[$step]));
		
		// Write title
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		
		// Ensure we have an ID to work with
		if (!$id) {
			echo ContributeHtml::div( ContributeHtml::hed(2, $title), 'full', 'content-header').n;
			echo '<div class="main section">'.n;
			echo ContributeHtml::error( JText::_('CONTRIBUTE_NO_ID') );
			echo '</div><!-- / .main section -->'.n;
			return;
		}
		
		// Push some needed styles to the tmeplate
		$this->getStyles('com_resources');
		
		// Get some needed libraries
		include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'resources.html.php' );

		// Load resource info
		$database =& JFactory::getDBO();
		$resource = new ResourcesResource( $database );
		$resource->load( $id );
		
		$juser =& JFactory::getUser();
		if (!$juser->get('guest')) {
			ximport('xuserhelper');
			$xgroups = XUserHelper::getGroups($juser->get('id'), 'all');
			// Get the groups the user has access to
			$usersgroups = $this->_getUsersGroups($xgroups);
		} else {
			$usersgroups = array();
		}

		// Output HTML
		echo ContributeHtml::div( ContributeHtml::hed(2, $title), 'full', 'content-header').n;
		echo '<div class="main section">'.n;
		ContributeHtml::writeSteps( $this->steps, $this->progress, $this->_option, $step, $id );
		if ($this->getError()) {
			echo ContributeHtml::error($this->getError());
		}
		ContributeHtml::stepReview( $database, $this->_option, $this->progress, 'submit', $id, $resource, $next_step, $this->config, $usersgroups );
		echo '</div><!-- / .main section -->'.n;
	}
	
	//-----------

	private function _getUsersGroups($groups)
	{
		$arr = array();
		if (!empty($groups)) {
			foreach ($groups as $group)
			{
				if ($group->regconfirmed) {
					$arr[] = $group->cn;
				}
			}
		}
		return $arr;
	}

	//----------------------------------------------------------
	//  Pre Processing
	//----------------------------------------------------------
	
	protected function step_type_process()
	{
		// do nothing
	}
	
	//-----------
	
	protected function step_compose_process() 
	{
	    $juser =& JFactory::getUser();
		$database =& JFactory::getDBO();

		// Initiate extended database class
		$row = new ResourcesResource( $database );
		if (!$row->bind( $_POST )) {
			echo ContributeHtml::alert( $row->getError() );
			exit();
		}
		$isNew = $row->id < 1;

		$row->created = ($row->created) ? $row->created : date( 'Y-m-d H:i:s' );
		$row->created_by = ($row->created_by) ? $row->created_by : $juser->get('id');

		// Set status to "composing"
		if ($isNew) {
			$row->published = 2;
		} else {
			$row->published = ($row->published) ? $row->published : 2;
		}
		$row->publish_up = ($row->publish_up) ? $row->publish_up : date( 'Y-m-d H:i:s' );
		$row->publish_down = '0000-00-00 00:00:00';
		$row->modified = date( 'Y-m-d H:i:s' );
		$row->modified_by = $juser->get('id');

		// Get custom areas, add wrapper tags, and compile into fulltext
		/*$nbtag = $_POST['nbtag'];
		$nbtag = array_map('trim',$nbtag);
		foreach ($nbtag as $tagname=>$tagcontent)
		{
			if ($tagcontent != '') {
				$row->fulltext .= '<nb:'.$tagname.'>'.$tagcontent.'</nb:'.$tagname.'>';
			}
		}*/
		$type = new ResourcesType( $database );
		$type->load( $row->type );
		
		$fields = array();
		if (trim($type->customFields) != '') {
			$fs = explode("\n", trim($type->customFields));
			foreach ($fs as $f) 
			{
				$fields[] = explode('=', $f);
			}
		} else {
			if ($row->type == 7) {
				$flds = $this->config->get('tagstool');
			} else {
				$flds = $this->config->get('tagsothr');
			}
			$flds = explode(',',$flds);
			foreach ($flds as $fld) 
			{
				$fields[] = array($fld, $fld, 'textarea', 0);
			}
		}
		
		$nbtag = $_POST['nbtag'];
		$nbtag = array_map('trim',$nbtag);
		foreach ($nbtag as $tagname=>$tagcontent)
		{
			if ($tagcontent != '') {
				$row->fulltext .= n.'<nb:'.$tagname.'>'.$tagcontent.'</nb:'.$tagname.'>'.n;
			} else {
				foreach ($fields as $f) 
				{
					if ($f[0] == $tagname && end($f) == 1) {
						echo ContributeHtml::alert( JText::sprintf('CONTRIBUTE_REQUIRED_FIELD_CHECK', $f[1]) );
						exit();
					}
				}
			}
		}

		// Strip any scripting there may be
		$row->fulltext   = $this->txt_clean($row->fulltext);
		$row->fulltext   = $this->txt_autop($row->fulltext,1);
		$row->footertext = $this->txt_clean($row->footertext);
		$row->introtext  = $this->txt_shorten($row->fulltext);

		// Check content
		if (!$row->check()) {
			echo ContributeHtml::alert( $row->getError() );
			exit();
		}

		// Store new content
		if (!$row->store()) {
			echo ContributeHtml::alert( $row->getError() );
			exit();
		}
		
		// Checkin the resource
		$row->checkin();
	
		// Is it a new resource?
		if ($isNew) {
			// Get the resource ID
			if (!$row->id) {
				$row->id = $row->insertid();
			}
			
			// Automatically attach this user as the first author
			$_REQUEST['pid'] = $row->id;
			$_POST['authid'] = $juser->get('id');
			$_REQUEST['id'] = $row->id;
			
			$this->author_save(0);
		}
	}

	//-----------

	protected function step_attach_process()
	{
		// do nothing
	}

	//-----------

	protected function step_authors_process()
	{
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		
		// Ensure we have an ID to work with
		if (!$id) {
			return;
		}
		
		// Load the resource
		$database =& JFactory::getDBO();
		$row = new ResourcesResource( $database );
		$row->load( $id );
		
		// Set the group and access level
		$row->group_owner = JRequest::getVar( 'group_owner', '' );
		$row->access = JRequest::getInt( 'access', 0 );
		
		// Check content
		if (!$row->check()) {
			echo ContributeHtml::alert( $row->getError() );
			exit();
		}

		// Store new content
		if (!$row->store()) {
			echo ContributeHtml::alert( $row->getError() );
			exit();
		}
	}

	//-----------

	protected function step_tags_process()
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		// Incoming
		$id    = JRequest::getInt( 'id', 0, 'post' );
		$tags  = JRequest::getVar( 'tags', '', 'post' );
		$tagfa = JRequest::getVar( 'tagfa', '', 'post' );
		
		if ($tags) {
			$tags = $tagfa.', '.$tags;
		} else {
			$tags = $tagfa;
		}

		// Tag the resource
		$rt = new ResourcesTags($database);
		$rt->tag_object($juser->get('id'), $id, $tags, 1, 0);
	}

	//----------------------------------------------------------
	// Final submission
	//----------------------------------------------------------

	protected function submit()
	{
		// Build the page title
		$title = JText::_(strtoupper($this->_name)).': '.JText::_('SUBMIT');

		// Write title
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		
		// Ensure we have an ID to work with
		if (!$id) {
			echo ContributeHtml::div( ContributeHtml::hed(2, $title), 'full', 'content-header').n;
			echo '<div class="main section">'.n;
			echo ContributeHtml::error( JText::_('CONTRIBUTE_NO_ID') );
			echo '</div><!-- / .main section -->'.n;
			return;
		}
	
		// Load resource info
		$database =& JFactory::getDBO();
		$resource = new ResourcesResource( $database );
		$resource->load( $id );

		// Set a flag for if the resource was already published or not
		$published = 0;
		if ($resource->published != 2) {
			$published = 1;
		}
	
		// Check if a newly submitted resource was authorized to be published
		$authorized = JRequest::getInt( 'authorization', 0 );
		if (!$authorized && !$published) {
			$this->setError( JText::_('CONTRIBUTION_NOT_AUTHORIZED') );
			$this->check_progress($id);
			$this->step_review();
			return;
		}
		
		// Is this a newly submitted resource?
		if (!$published) {
			// 0 = unpublished, 1 = published, 2 = composing, 3 = pending (submitted), 4 = deleted
			// Are submissions auto-approved?
			if ($this->config->get('autoapprove') == 1) {
				// Set status to published
				$resource->published = 1;
			} else {
				$apu = $this->config->get('autoapproved_users');
				$apu = explode(',', $apu);
				$apu = array_map('trim',$apu);
				
				$juser =& JFactory::getUser();
				if (in_array($juser->get('username'),$apu)) {
					// Set status to published
					$resource->published = 1;
				} else {
					// Set status to pending review (submitted)
					$resource->published = 3;
				}
			}
			
			// Get the resource's contributors
			$helper = new ResourcesHelper( $id, $database );
			$helper->getCons();

			$contributors = $helper->_contributors;

			if (!$contributors || count($contributors) <= 0) {
				$this->setError( JText::_('CONTRIBUTION_HAS_NO_AUTHORS') );
				$this->check_progress($id);
				$this->step_review();
				return;
			}
		}
		
		// Is this resource licensed under Creative Commons?
		if ($this->config->get('cc_license')) {
			$license = JRequest::getInt( 'license', 0 );
			if ($license == 1) {
				$params = explode("\n",$resource->params);
				$newparams = array();
				$flag = 0;

				// Loop through the params and check if a license param exist
				foreach ($params as $param)
				{
					$p = explode('=',$param);
					if ($p[0] == 'license') {
						$flag = 1;
						$p[1] = 'cc3';
					}
					$param = implode('=',$p);
					$newparams[] = $param;
				}

				// No license param so add it
				if ($flag == 0) {
					$newparams[] = 'license=cc3';
				}

				// Overwrite the resource's params with the new params
				$resource->params = implode("\n",$newparams);
			}
		}
		
		// Save and checkin the resource
		$resource->store();
		$resource->checkin();
		
		// If a previously published resource, redirect to the resource page
		if ($published == 1) {
			if ($resource->alias) {
				$url = JRoute::_('index.php?option=com_resources&alias='.$resource->alias);
			} else {
				$url = JRoute::_('index.php?option=com_resources&id='.$resource->id);
			}
			$this->_redirect = $url;
			return;
		}

		$jconfig =& JFactory::getConfig();
		
		// E-mail "from" info
		$from = array();
		$from['email'] = $jconfig->getValue('config.mailfrom');
		$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_('SUBMISSIONS');
		
		// E-mail subject
		$subject = $jconfig->getValue('config.sitename').' '.JText::_('EMAIL_SUBJECT');
		
		// E-mail message
		$message  = JText::sprintf('EMAIL_MESSAGE', $jconfig->getValue('config.live_site'))."\r\n";
		$message .= JRoute::_('index.php?option=com_resources&id='.$id);

		// Send e-mail
		foreach ($contributors as $contributor)
		{
			$xuser = JUser::getInstance( $contributor->id );
			if (is_object($xuser)) {
				if ($xuser->get('email')) {
					//$this->send_email($from, $email, $subject, $message);
				}
			}
		}
		
		// Output HTML
		echo ContributeHtml::div( ContributeHtml::hed(2, JText::_('SUBMIT')), 'full', 'content-header').n;
		echo '<div class="main section">'.n;
		ContributeHtml::thanks( $this->_option, $this->config, $resource );
		echo '</div><!-- / .main section -->'.n;
	}

	//-----------

	protected function delete() 
	{
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		
		// Ensure we have an ID to work with
		if (!$id) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			return;
		}

		// Incoming step
		$step = JRequest::getVar( 'step', 1 );
		
		// Perform step
		switch ($step) 
		{
			case 1:
				$steps = $this->steps;
				
				$progress = array();
				$progress['submitted'] = 0;
				for ($i = 1, $n = count( $steps ); $i < $n; $i++) 
				{
					$progress[$steps[$i]] = 0;
				}
				
				$database =& JFactory::getDBO();
				
				// Load the resource
				$row = new ResourcesResource( $database );
				$row->load( $id );
				$row->typetitle = $row->getTypeTitle(0);

				// Build the page title
				$title = JText::_(strtoupper($this->_name)).': '.JText::_('DELETE');

				// Write title
				$document =& JFactory::getDocument();
				$document->setTitle( $title );

				// Output HTML
				echo ContributeHtml::div( ContributeHtml::hed(2, $title), 'full', 'content-header').n;
				echo '<div class="main section">'.n;
				ContributeHtml::writeSteps( $steps, $progress, $this->_option, 'discard', $id );
				ContributeHtml::delete( $row, $this->_option );
				echo '</div><!-- / .main section -->'.n;
			break;
			
			case 2:
				// Incoming confirmation flag
				$confirm = JRequest::getVar( 'confirm', '', 'post' );
				
				// Did they confirm the deletion?
				if ($confirm != 'confirmed') {
					$this->redirect = JRoute::_('index.php?option='.$this->_option);
					return;
				}
				
				$database =& JFactory::getDBO();
				
				// Load the resource
				$resource = new ResourcesResource( $database );
				$resource->load( $id );
				
				// Check if the resource was "published"
				if ($resource->published == 1) {
					// It was, so we can only mark it as "deleted"
					if (!$this->markRemovedContribution( $id )) {
						echo ContributeHtml::error( $this->getError() );
						return;
					}
				} else {
					// It wasn't. Attempt to delete the resource
					if (!$this->deleteContribution( $id )) {
						echo ContributeHtml::error( $this->getError() );
						return;
					}
				}
				
				// Redirect to the start page
				$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			break;
		}
	}
	
	//-----------

	protected function markRemovedContribution( $id )
	{
		$database =& JFactory::getDBO();
		
		// Make sure we have a record to pull
		if (!$id) {
			$this->setError( JText::_('CONTRIBUTE_NO_ID') );
			return false;
		}
		
		// Load resource info
		$row = new ResourcesResource( $database );
		$row->load( $id );
		
		// Mark resource as deleted
		$row->published = 4;
		if (!$row->store()) {
			$this->setError( $row->getError() );
			return false;
		}

		// Return success
		return true;
	}

	//-----------

	protected function deleteContribution( $id )
	{
		// Make sure we have a record to pull
		if (!$id) {
			$this->setError( JText::_('CONTRIBUTE_NO_ID') );
			return false;
		}
		
		jimport('joomla.filesystem.folder');
		
		$database =& JFactory::getDBO();
		
		// Load resource info
		$row = new ResourcesResource( $database );
		$row->load( $id );
		
		// Get the resource's children
		$helper = new ResourcesHelper( $id, $database );
		$helper->getChildren();
		$children = $helper->children;
		
		// Were there any children?
		if ($children) {
			// Loop through each child and delete its files and associations
			foreach ($children as $child) 
			{
				// Skip standalone children
				if ($child->standalone == 1) {
					continue;
				}
				
				// Get path and delete directories
				if ($child->path != '') {
					$listdir = $child->path;
				} else {
					// No stored path, derive from created date		
					$listdir = ContributeHtml::build_path( $child->created, $child->id, '' );
				}

				// Build the path
				$path = $this->_buildUploadPath( $listdir, '' );

				// Check if the folder even exists
				if (!is_dir($path) or !$path) { 
					$this->setError( JText::_('DIRECTORY_NOT_FOUND') ); 
				} else {
					// Attempt to delete the folder
					if (!JFolder::delete($path)) {
						$this->setError( JText::_('UNABLE_TO_DELETE_DIRECTORY') );
					}
				}

				// Delete associations to the resource
				$row->deleteExistence( $child->id );

				// Delete the resource
				$row->delete( $child->id );
			}
		}
		
		// Get path and delete directories
		if ($row->path != '') {
			$listdir = $row->path;
		} else {
			// No stored path, derive from created date		
			$listdir = ContributeHtml::build_path( $row->created, $id, '' );
		}
		
		// Build the path
		$path = $this->_buildUploadPath( $listdir, '' );

		// Check if the folder even exists
		if (!is_dir($path) or !$path) { 
			$this->setError( JText::_('DIRECTORY_NOT_FOUND') ); 
		} else {
			// Attempt to delete the folder
			if (!JFolder::delete($path)) {
				$this->setError( JText::_('UNABLE_TO_DELETE_DIRECTORY') );
			}
		}
		
		// Delete associations to the resource
		$row->deleteExistence();
		
		// Delete the resource
		$row->delete();

		// Return success (null)
		return true;
	}

	//----------------------------------------------------------
	// Attachments
	//----------------------------------------------------------
	
	protected function attach_rename()
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return false;
		}
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		$name = trim(JRequest::getVar( 'name', '' ));

		// Ensure we have everything we need
		if ($id && $name != '') {
			$database =& JFactory::getDBO();
			
			$r = new ResourcesResource( $database );
			$r->load( $id );
			$r->title = $name;
			$r->store();
		}
		
		// Echo the name
		echo $name;
	}

	//-----------

	protected function attach_save()
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return false;
		}
		$database =& JFactory::getDBO();

		// Incoming
		$pid = JRequest::getInt( 'pid', 0 );
		if (!$pid) {
			$this->setError( JText::_('CONTRIBUTE_NO_ID') );
			$this->attachments( $pid );
		}

		// Incoming file
		$file = JRequest::getVar( 'upload', '', 'files', 'array' );
		if (!$file['name']) {
			$this->setError( JText::_('CONTRIBUTE_NO_FILE') );
			$this->attachments( $pid );
			return;
		}
		
		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ','_',$file['name']);

		// Instantiate a new resource object
		$row = new ResourcesResource( $database );
		if (!$row->bind( $_POST )) {
			$this->setError( $row->getError() );
			$this->attachments( $pid );
			return;
		}
		$row->title = ($row->title) ? $row->title : $file['name'];
		$row->introtext = $row->title;
		$row->created = date( 'Y-m-d H:i:s' );
		$row->created_by = $juser->get('id');
		$row->published = 1;
		$row->publish_up = date( 'Y-m-d H:i:s' );
		$row->publish_down = '0000-00-00 00:00:00';
		$row->standalone = 0;

		// Check content
		if (!$row->check()) {
			$this->setError( $row->getError() );
			$this->attachments( $pid );
			return;
		}
		// Store new content
		if (!$row->store()) {
			$this->setError( $row->getError() );
			$this->attachments( $pid );
			return;
		}
		
		if (!$row->id) {
			$row->id = $row->insertid();
		}
		
		// Build the path
		$listdir = ContributeHtml::build_path( $row->created, $row->id, '' );
		$path = $this->_buildUploadPath( $listdir, '' );

		// Make sure the upload path exist
		if (!is_dir( $path )) {
			jimport('joomla.filesystem.folder');
			if (!JFolder::create( $path, 0777 )) {
				$this->setError( JText::_('UNABLE_TO_CREATE_UPLOAD_PATH') );
				$this->attachments( $pid );
				return;
			}
		}

		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $path.DS.$file['name'])) {
			$this->setError( JText::_('ERROR_UPLOADING') );
		} else {
			// File was uploaded
			
			// Check the file type
			$row->type = $this->_getChildType($file['name']);

			// If it's a package (ZIP, etc) ...
			if ($row->type == 38) {
				/*jimport('joomla.filesystem.archive');
				
				// Extract the files
				if (!JArchive::extract( $file_to_unzip, $path )) {
					$this->setError( JText::_('Could not extract package.') );
				}*/
				require_once( JPATH_ROOT.DS.'administrator'.DS.'includes'.DS.'pcl'.DS.'pclzip.lib.php' );
		
				if (!extension_loaded('zlib')) {
					$this->setError( JText::_('ZLIB_PACKAGE_REQUIRED') );
				} else {
					// Check the table of contents and look for a Breeze viewer.swf file
					$isbreeze = 0;
					
					$zip = new PclZip( $path.DS.$file['name'] );
						
					$file_to_unzip = preg_replace('/(.+)\..*$/', '$1', $path.DS.$file['name']);
					
					if (($list = $zip->listContent()) == 0) {
						die('Error: '.$zip->errorInfo(true));
					}
					
					for ($i=0; $i<sizeof($list); $i++) 
					{
						if (substr($list[$i]['filename'], strlen($list[$i]['filename']) - 10, strlen($list[$i]['filename'])) == 'viewer.swf') {
							$isbreeze = $list[$i]['filename'];
							break;
						}
						//$this->setError( substr($list[$i]['filename'], strlen($list[$i]['filename']), -4).' '.substr($file['name'], strlen($file['name']), -4) );
					}
					if (!$isbreeze) {
						for ($i=0; $i<sizeof($list); $i++) 
						{
							if (strtolower(substr($list[$i]['filename'], -3)) == 'swf' 
							 && substr($list[$i]['filename'], strlen($list[$i]['filename']), -4) == substr($file['name'], strlen($file['name']), -4)) {
								$isbreeze = $list[$i]['filename'];
								break;
							}
							//$this->setError( substr($list[$i]['filename'], strlen($list[$i]['filename']), -4).' '.substr($file['name'], strlen($file['name']), -4) );
						}
					}

					// It IS a breeze presentation
					if ($isbreeze) {
						// unzip the file
						$do = $zip->extract($path);
						if (!$do) {
							$this->setError( JText::_( 'UNABLE_TO_EXTRACT_PACKAGE' ) );
						} else {
							$row->path = $listdir.DS.$isbreeze;

							@unlink( $path.DS.$file['name'] );
						}
						$row->type = $this->_getChildType($row->path);
						$row->title = $isbreeze;
					}
				}
			}
		}
		
		if (!$row->path) {
			$row->path = $listdir.DS.$file['name'];
		}
		if (substr($row->path, 0, 1) == DS) {
			$row->path = substr($row->path, 1, strlen($row->path));
		}

		// Store new content
		if (!$row->store()) {
			$this->setError( $row->getError() );
			$this->attachments( $pid );
			return;
		}
		
		// Instantiate a ResourcesAssoc object
		$assoc = new ResourcesAssoc( $database );

		// Get the last child in the ordering
		$order = $assoc->getLastOrder( $pid );
		$order = ($order) ? $order : 0;
		
		// Increase the ordering - new items are always last
		$order = $order + 1;
		
		// Create new parent/child association
		$assoc->parent_id = $pid;
		$assoc->child_id = $row->id;
		$assoc->ordering = $order;
		$assoc->grouping = 0;
		if (!$assoc->check()) {
			$this->setError( $assoc->getError() );
		}
		if (!$assoc->store(true)) {
			$this->setError( $assoc->getError() );
		}

		// Push through to the attachments view
		$this->attachments( $pid );
	}

	//-----------

	protected function attach_delete() 
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return false;
		}
		
		$database =& JFactory::getDBO();
		
		// Incoming parent ID
		$pid = JRequest::getInt( 'pid', 0 );
		if (!$pid) {
			$this->setError( JText::_('CONTRIBUTE_NO_ID') );
			$this->attachments( $pid );
		}
		
		// Incoming child ID
		$id = JRequest::getInt( 'id', 0 );
		if (!$id) {
			$this->setError( JText::_('CONTRIBUTE_NO_CHILD_ID') );
			$this->attachments( $pid );
		}
		
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
	
		// Load resource info
		$row = new ResourcesResource( $database );
		$row->load( $id );
		
		// Get path and delete directories
		if ($row->path != '') {
			$listdir = $row->path;
		} else {
			// No stored path, derive from created date		
			$listdir = ContributeHtml::build_path( $row->created, $id, '' );
		}
		
		// Build the path
		$path = $this->_buildUploadPath( $listdir, '' );

		// Check if the file even exists
		if (!is_file($path) or !$path) { 
			$this->setError( JText::_('FILE_NOT_FOUND') ); 
		} else {
			// Attempt to delete the file
			if (!JFile::delete($path)) {
				$this->setError( JText::_('UNABLE_TO_DELETE_FILE') );
			}
		}
		
		if (!$this->getError()) {
			$file = basename($path);
			$path = substr($path, 0, (strlen($path) - strlen($file)));
			$year = substr(trim($row->created), 0, 4);
			$month = substr(trim($row->created), 5, 2);
			$path = str_replace(JPATH_ROOT,'',$path);
			$path = str_replace($this->config->get('uploadpath'),'',$path);
			$bits = explode('/', $path);
			$p = array();
			$b = '';
			$g = array_pop($bits);
			foreach ($bits as $bit) 
			{
				if ($bit == '/' || $bit == $year || $bit == $month || $bit == Contributehtml::niceidformat($id)) {
					$b .= ($bit != '/') ? DS.$bit : '';
				} else if ($bit != '/') {
					$p[] = $bit;
				}
			}
			if (count($p) > 1) {
				$p = array_reverse($p);
				foreach ($p as $v) 
				{
					$npath = JPATH_ROOT.$this->config->get('uploadpath').$b.DS.$v;

					// Check if the folder even exists
					if (!is_dir($npath) or !$npath) { 
						$this->setError( JText::_('DIRECTORY_NOT_FOUND') ); 
					} else {
						// Attempt to delete the folder
						if (!JFolder::delete($npath)) {
						//if (!$this->delete_dir($npath)) {
							$this->setError( JText::_('UNABLE_TO_DELETE_DIRECTORY') );
						}
					}
				}
			}
		}
		
		if (!$this->getError()) {
			// Delete associations to the resource
			$row->deleteExistence();

			// Delete resource
			$row->delete();
		}

		// Push through to the attachments view
		$this->attachments( $pid );
	}

	//-----------

	protected function attachments( $id=null ) 
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return false;
		}
		
		// Incoming
		if (!$id) {
			$id = JRequest::getInt( 'id', 0 );
		}
		
		// Ensure we have an ID to work with
		if (!$id) {
			echo ContributeHtml::error( JText::_('CONTRIBUTE_NO_ID') );
			return;
		}
		
		// Initiate a resource helper class
		$database =& JFactory::getDBO();
		
		$helper = new ResourcesHelper( $id, $database );
		$helper->getChildren();
		
		// Get the app
		$app =& JFactory::getApplication();
		
		// Output HTML
		ContributeHtml::pageTop( $this->_option, $app );
		ContributeHtml::attachments( $this->_option, $id, '', $helper->children, $this->config, $this->getError() );
		ContributeHtml::pageBottom();
	}

	//-----------
	
	private function _buildUploadPath( $listdir, $subdir='' ) 
	{
		if ($subdir) {
			// Make sure the path doesn't end with a slash
			if (substr($subdir, -1) == DS) { 
				$subdir = substr($subdir, 0, strlen($subdir) - 1);
			}
			// Ensure the path starts with a slash
			if (substr($subdir, 0, 1) != DS) { 
				$subdir = DS.$subdir;
			}
		}
		
		// Get the configured upload path
		$base_path = $this->config->get('uploadpath');
		if ($base_path) {
			// Make sure the path doesn't end with a slash
			if (substr($base_path, -1) == DS) { 
				$base_path = substr($base_path, 0, strlen($base_path) - 1);
			}
			// Ensure the path starts with a slash
			if (substr($base_path, 0, 1) != DS) { 
				$base_path = DS.$base_path;
			}
		}
		
		// Make sure the path doesn't end with a slash
		if (substr($listdir, -1) == DS) { 
			$listdir = substr($listdir, 0, strlen($listdir) - 1);
		}
		// Ensure the path starts with a slash
		if (substr($listdir, 0, 1) != DS) { 
			$listdir = DS.$listdir;
		}
		// Does the beginning of the $listdir match the config path?
		if (substr($listdir, 0, strlen($base_path)) == $base_path) {
			// Yes - ... this really shouldn't happen
		} else {
			// No - append it
			$listdir = $base_path.$listdir;
		}

		// Build the path
		return JPATH_ROOT.$listdir.$subdir;
	}

	//-----------

	private function _getChildType($filename)
	{
		$filename_arr = explode('.',$filename);
		$ftype = end($filename_arr);
		$ftype = (strlen($ftype) > 3) ? substr($ftype, 0, 3) : $ftype;
		$ftype = strtolower($ftype);
	
		switch ($ftype) 
		{
			case 'mov': $type = 15; break;
			case 'swf': $type = 32; break;
			case 'ppt': $type = 35; break;
			case 'asf': $type = 37; break;
			case 'asx': $type = 37; break;
			case 'wmv': $type = 37; break;
			case 'zip': $type = 38; break;
			case 'tar': $type = 38; break;
			case 'pdf': $type = 33; break;
			default:    $type = 13; break;
		}
	
		return $type;
	}
	
	//-----------

	protected function reorder_attach() 
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		$pid = JRequest::getInt( 'pid', 0 );

		// Ensure we have an ID to work with
		if (!$id) {
			$this->setError( JText::_('CONTRIBUTE_NO_CHILD_ID') );
			$this->attachments( $pid );
			return;
		}
		
		// Ensure we have a parent ID to work with
		if (!$pid) {
			$this->setError( JText::_('CONTRIBUTE_NO_ID') );
			$this->attachments( $pid );
			return;
		}

		$move = substr($this->_task, 0, (strlen($this->_task) - 1));

		// Get the element moving down - item 1
		$resource1 = new ResourcesAssoc( $database );
		$resource1->loadAssoc( $pid, $id );

		// Get the element directly after it in ordering - item 2
		$resource2 = clone( $resource1 );
		$resource2->getNeighbor( $move );

		switch ($move) 
		{
			case 'orderup':				
				// Switch places: give item 1 the position of item 2, vice versa
				$orderup = $resource2->ordering;
				$orderdn = $resource1->ordering;
				
				$resource1->ordering = $orderup;
				$resource2->ordering = $orderdn;
				break;
			
			case 'orderdown':
				// Switch places: give item 1 the position of item 2, vice versa
				$orderup = $resource1->ordering;
				$orderdn = $resource2->ordering;
				
				$resource1->ordering = $orderdn;
				$resource2->ordering = $orderup;
				break;
		}
		
		// Save changes
		$resource1->store();
		$resource2->store();
		
		// Push through to the attachments view
		$this->attachments( $pid );
	}

	//----------------------------------------------------------
	// contributors manager
	//----------------------------------------------------------

	protected function author_save($show=1)
	{
		// Incoming resource ID
		$id = JRequest::getInt( 'pid', 0 );
		if (!$id) {
			$this->setError( JText::_('CONTRIBUTE_NO_ID') );
			$this->authors( $id );
			return;
		}
		
		ximport('xprofile');
		
		$database =& JFactory::getDBO();
		
		// Incoming authors
		$authid = JRequest::getInt( 'authid', 0, 'post' );
		$authorsNewstr = JRequest::getVar( 'new_authors', '', 'post' );

		// Instantiate a resource/contributor association object
		$rc = new ResourcesContributor( $database );
		$rc->subtable = 'resources';
		$rc->subid = $id;
		
		// Get the last child in the ordering
		$order = $rc->getLastOrder( $id, 'resources' );
		$order = $order + 1; // new items are always last
		
		// Was there an ID? (this will come from the author <select>)
		if ($authid) {
			// Check if they're already linked to this resource
			$rc->loadAssociation( $authid, $id, 'resources' );
			if ($rc->authorid) {
				$this->setError( JText::sprintf('USER_IS_ALREADY_AUTHOR', $authid) );
			} else {
				// Perform a check to see if they have a contributors page. If not, we'll need to make one
				//$xuser =& JUser::getInstance( $authid );
				$xuser = new XProfile();
				$xuser->load( $authid );
				if ($xuser) {
					$this->_author_check($authid);

					// New record
					$rc->authorid = $authid;
					$rc->ordering = $order;
					$rc->name = $xuser->get('name');
					$rc->organization = $xuser->get('organization');
					$rc->createAssociation();

					$order++;
				}
			}
		}
		$xuser = null;
		// Do we have new authors?
		if ($authorsNewstr) {
			// Turn the string into an array of usernames
			$authorsNew = split(',',$authorsNewstr);
			
			jimport('joomla.user.helper');
			
			// loop through each one
			for ($i=0, $n=count( $authorsNew ); $i < $n; $i++)
			{
				$cid = strtolower(trim($authorsNew[$i]));
			
				// Find the user's account info
				$uid = JUserHelper::getUserId($cid);
				if (!$uid) {
					$this->setError( JText::sprintf('UNABLE_TO_FIND_USER_ACCOUNT', $cid) );
					continue;
				}
				
				$xuser =& JUser::getInstance( $uid );
				if (!is_object($xuser)) {
					$this->setError( JText::sprintf('UNABLE_TO_FIND_USER_ACCOUNT', $cid) );
					continue;
				}

				$uid = $xuser->get('id');
		
				if (!$uid) {
					$this->setError( JText::sprintf('UNABLE_TO_FIND_USER_ACCOUNT', $cid) );
					continue;
				}
				
				// Check if they're already linked to this resource
				$rcc = new ResourcesContributor( $database );
				$rcc->loadAssociation( $uid, $id, 'resources' );
				if ($rcc->authorid) {
					$this->setError( JText::sprintf('USER_IS_ALREADY_AUTHOR', $cid) );
					continue;
				}
				
				$this->_author_check($xuser->get('id'));
				
				// New record
				$xprofile = XProfile::getInstance($xuser->get('id'));
				$rcc->subtable = 'resources';
				$rcc->subid = $id;
				$rcc->authorid = $uid;
				$rcc->ordering = $order;
				$rcc->name = $xprofile->get('name');
				$rcc->organization = $xprofile->get('organization');
				$rcc->createAssociation();
				
				$order++;
			}
		}

		if ($show) {
			// Push through to the authors view
			$this->authors( $id );
		}
	}

	//-----------

	private function _author_check($id)
	{
		$xprofile = XProfile::getInstance($id);
		if ($xprofile->get('givenName') == '' && $xprofile->get('middleName') == '' && $xprofile->get('surname') == '') {
			$bits = explode(' ', $xuser->get('name'));
			$xprofile->set('surname', array_pop($bits));
			if (count($bits) >= 1) {
				$xprofile->set('givenName', array_shift($bits));
			}
			if (count($bits) >= 1) {
				$xprofile->set('middleName', implode(' ',$bits));
			}
		}
	}

	//-----------

	protected function author_remove()
	{
		// Incoming
		$id  = JRequest::getInt( 'id', 0 );
		$pid = JRequest::getInt( 'pid', 0 );
		
		// Ensure we have a resource ID ($pid) to work with
		if (!$pid) {
			$this->setError( JText::_('CONTRIBUTE_NO_ID') );
			$this->authors();
			return;
		}
		
		// Ensure we have the contributor's ID ($id)
		if ($id) {
			$database =& JFactory::getDBO();
			
			$rc = new ResourcesContributor( $database );
			if (!$rc->deleteAssociation( $id, $pid, 'resources' )) {
				$this->setError( $rc->getError() );
			}
		}
		
		// Push through to the authors view
		$this->authors( $pid );
	}

	//-----------

	protected function reorder_author() 
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		$pid = JRequest::getInt( 'pid', 0 );

		// Ensure we have an ID to work with
		if (!$id) {
			$this->setError( JText::_('CONTRIBUTE_NO_CHILD_ID') );
			$this->authors( $pid );
			return;
		}
		
		// Ensure we have a parent ID to work with
		if (!$pid) {
			$this->setError( JText::_('CONTRIBUTE_NO_ID') );
			$this->authors( $pid );
			return;
		}

		$move = substr($this->_task, 0, (strlen($this->_task) - 1));

		// Get the element moving down - item 1
		$author1 = new ResourcesContributor( $database );
		$author1->loadAssociation( $id, $pid, 'resources' );

		// Get the element directly after it in ordering - item 2
		$author2 = clone( $author1 );
		$author2->getNeighbor( $move );

		switch ($move) 
		{
			case 'orderup':				
				// Switch places: give item 1 the position of item 2, vice versa
				$orderup = $author2->ordering;
				$orderdn = $author1->ordering;
				
				$author1->ordering = $orderup;
				$author2->ordering = $orderdn;
				break;
			
			case 'orderdown':
				// Switch places: give item 1 the position of item 2, vice versa
				$orderup = $author1->ordering;
				$orderdn = $author2->ordering;
				
				$author1->ordering = $orderdn;
				$author2->ordering = $orderup;
				break;
		}
		
		// Save changes
		$author1->updateAssociation();
		$author2->updateAssociation();
		
		// Push through to the attachments view
		$this->authors( $pid );
	}

	//-----------

	protected function authors( $id=null ) 
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		if (!$id) {
			$id = JRequest::getInt( 'id', 0 );
		}
		
		// Ensure we have an ID to work with
		if (!$id) {
			echo ContributeHtml::error( JText::_('No resource ID found') );
			return;
		}
		
		// Initiate a resource helper class
		$database =& JFactory::getDBO();
		
		// Get all contributors of this resource
		$helper = new ResourcesHelper( $id, $database );
		$helper->getCons();
		
		// Get a list of all existing contributors
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_members'.DS.'members.class.php' );
		
		// Initiate a members object
		$mp = new MembersProfile( $database );
		
		$filters = array();
		$filters['search'] = '';
		$filters['show']   = '';
		$filters['index']  = '';
		$filters['limit']  = 'all';
		$filters['sortby'] = 'surname';
		$filters['authorized'] = false;
		
		// Get all members
		$rows = $mp->getRecords( $filters, false );

		// Get the app
		$app =& JFactory::getApplication();
	
		// Output HTML
		ContributeHtml::pageTop( $this->_option, $app );
		ContributeHtml::contributors( $id, $rows, $helper->_contributors, $this->_option, $this->getError() );
		ContributeHtml::pageBottom();
	}

	//----------------------------------------------------------
	// Checks
	//----------------------------------------------------------
	
	protected function step_type_check( $id )
	{
		// do nothing
	}
	
	//-----------
	
	protected function step_compose_check( $id )
	{
		return $id;
	}

	//-----------
	
	protected function step_attach_check( $id )
	{
		if ($id) {
			$database =& JFactory::getDBO();
			$ra = new ResourcesAssoc( $database );
			$total = $ra->getCount( $id );
		} else {
			$total = 0;
		}
		return $total;
	}

	//-----------
	
	protected function step_authors_check( $id )
	{
		if ($id) {
			$database =& JFactory::getDBO();
			$rc = new ResourcesContributor( $database );
			$contributors = $rc->getCount( $id, 'resources' );
		} else {
			$contributors = 0;
		}

		return $contributors;
	}
	
	//-----------
	
	protected function step_tags_check( $id )
	{
		$database =& JFactory::getDBO();

		$rt = new ResourcesTags( $database );
		$tags = $rt->getTags( $id );

		if (count($tags) > 0) {
			return 1;
		} else {
			return 0;
		}
	}
	
	//-----------

	protected function step_review_check( $id ) 
	{
		$database =& JFactory::getDBO();
		
		$row = new ResourcesResource( $database );
		$row->load( $id );
	
		if ($row->published == 1) {
			return 1;
		} else {
			return 0;
		}
	}
	
	//----------------------------------------------------------
	// Misc
	//----------------------------------------------------------

	private function send_email($from, $email, $subject, $message) 
	{
		if ($from) {
		    	$xhub = &XFactory::getHub();
			$contact_email = $from['email'];
			$contact_name  = $from['name'];

			$args = "-f '" . $contact_email . "'";
			$headers  = "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/plain; charset=utf-8\n";
			$headers .= 'From: ' . $contact_name .' <'. $contact_email . ">\n";
			$headers .= 'Reply-To: ' . $contact_name .' <'. $contact_email . ">\n";
			$headers .= "X-Priority: 3\n";
			$headers .= "X-MSMail-Priority: High\n";
			$headers .= 'X-Mailer: ' . $xhub->getCfg('hubShortName') . "\n";
			if (mail($email, $subject, $message, $headers, $args)) {
				return(1);
			}
		}
		return(0);
	}

	//-----------

	private function formSelect($name, $array, $value='', $class='')
	{
		$out  = '<select name="'.$name.'" id="'.$name.'"';
		$out .= ($class) ? ' class="'.$class.'">'.n : '>'.n;
		foreach ($array as $avalue => $alabel) 
		{
			$selected = ($avalue == $value || $alabel == $value)
					  ? ' selected="selected"'
					  : '';
			$out .= t.' <option value="'.$avalue.'"'.$selected.'>'.$alabel.'</option>'.n;
		}
		$out .= '</select>'.n;
		return $out;
	}

	//-----------

	public function txt_shorten($text, $chars=500) 
	{
		$text = strip_tags($text);
		$text = trim($text);
		
		if (strlen($text) > $chars) {
			$text = $text.' ';
			$text = substr($text,0,$chars);
			$text = substr($text,0,strrpos($text,' '));
			$text = $text.' ...';
		}

		return $text;
	}

	//-----------

	public function txt_clean( &$text ) 
	{
		$text = preg_replace( '/{kl_php}(.*?){\/kl_php}/s', '', $text );
		$text = preg_replace( '/{.+?}/', '', $text );
		$text = preg_replace( "'<style[^>]*>.*?</style>'si", '', $text );
		$text = preg_replace( "'<script[^>]*>.*?</script>'si", '', $text );
		//$text = preg_replace( '/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2', $text );
		$text = preg_replace( '/<!--.+?-->/', '', $text );
		//$text = nl2br( $text );
		//$text = str_replace( '<br>', '<br />', $text );
		return $text;
	}

	//-----------

	public function txt_autop($pee, $br = 1) 
	{
		// converts paragraphs of text into xhtml
		$pee = $pee . "\n"; // just to make things a little easier, pad the end
		$pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
		$pee = preg_replace('!(<(?:table|ul|ol|li|pre|form|blockquote|h[1-6])[^>]*>)!', "\n$1", $pee); // Space things out a little
		$pee = preg_replace('!(</(?:table|ul|ol|li|pre|form|blockquote|h[1-6])>)!', "$1\n", $pee); // Space things out a little
		$pee = preg_replace("/(\r\n|\r)/", "\n", $pee); // cross-platform newlines 
		$pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
		$pee = preg_replace('/\n?(.+?)(?:\n\s*\n|\z)/s', "\t<p>$1</p>\n", $pee); // make paragraphs, including one at the end 
		$pee = preg_replace('|<p>\s*?</p>|', '', $pee); // under certain strange conditions it could create a P of entirely whitespace 
		$pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); // problem with nested lists
		$pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
		$pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
		$pee = preg_replace('!<p>\s*(</?(?:table|tr|td|th|div|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)!', "$1", $pee);
		$pee = preg_replace('!(</?(?:table|tr|td|th|div|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)\s*</p>!', "$1", $pee); 
		if ($br) $pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee); // optionally make line breaks
		$pee = preg_replace('!(</?(?:table|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)\s*<br />!', "$1", $pee);
		$pee = preg_replace('!<br />(\s*</?(?:p|li|div|th|pre|td|ul|ol)>)!', '$1', $pee);
		$pee = preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $pee);
		
		return $pee; 
	}

	//-----------
	
	public function txt_unpee($pee)
	{
		$pee = str_replace("\t", '', $pee);
		$pee = str_replace('</p><p>', '', $pee);
		$pee = str_replace('<p>', '', $pee);
		$pee = str_replace('</p>', "\n", $pee);
		$pee = str_replace('<br />', '', $pee);
		$pee = trim($pee);
		return $pee; 
	}
}
?>
