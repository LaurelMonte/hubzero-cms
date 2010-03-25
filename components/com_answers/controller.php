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

class AnswersController extends JObject
{	
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;
	private $note	= NULL;

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
	
	public function execute()
	{
		$upconfig =& JComponentHelper::getParams('com_userpoints');
		$this->banking = $upconfig->get('bankAccounts');
		
		if ($this->banking) {
			ximport('bankaccount');
		}
		
		// Get the component parameters
		$this->config = new AnswersConfig( $this->_option );
		$this->infolink = (isset($this->config->parameters['infolink'])) ? $this->config->parameters['infolink'] : '/kb/points/';
		$this->showcomments = (isset($this->config->parameters['showcomments'])) ? $this->config->parameters['showcomments'] : '1';
		
		$task = JRequest::getVar( 'task', '' );
		$this->_task = $task;
	
		switch ( $this->_task ) 
		{
			case 'new':         $this->create();      break;
			case 'savea':       $this->savea();       break;
			case 'saveq':       $this->saveq();       break;
			case 'answer':      $this->answer();      break;
			case 'tag':         $this->tag();         break;
			case 'question':    $this->question();    break;
			case 'accept':      $this->accept();      break;
			case 'myquestions': $this->myquestions(); break;
			case 'search':      $this->search();      break;
			//case 'start':       $this->start();       break;
			case 'delete':      $this->answer();      break;
			case 'delete_q':    $this->delete_q();    break;
			case 'rateitem':   	$this->rateitem();    break;
			case 'savereply':   $this->savereply();   break;
			case 'reply':      	$this->reply();  	  break;
			case 'math':      	$this->answer();  	  break;
			
			default: $this->search(); break;
		}
	}

	//-----------

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message, $this->_messageType );
		}
	}
	
	//-----------

	private function _getStyles($option='', $css='')
	{
		ximport('xdocument');
		if ($option) {
			XDocument::addComponentStylesheet($option, $css);
		} else {
			XDocument::addComponentStylesheet($this->_option);
		}
	}

	//-----------

	private function _buildPathway($question=null) 
	{
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option='.$this->_option
			);
		}
		if ($this->_task && $this->_task != 'view') {
			$pathway->addItem(
				JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task)),
				'index.php?option='.$this->_option.'&task='.$this->_task
			);
		}
		if (is_object($question)) {
			$pathway->addItem( 
				Hubzero_View_Helper_Html::shortenText(stripslashes($question->subject), 50, 0), 
				'index.php?option='.$this->_option.'&task=question&id='.$question->id 
			);
		}
	}
	
	//-----------
	
	private function _buildTitle($question=null) 
	{
		$this->_title = JText::_(strtoupper($this->_option));
		if ($this->_task && $this->_task != 'view') {
			$this->_title .= ': '.JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task));
		}
		if (is_object($question)) {
			$this->_title .= ': '.Hubzero_View_Helper_Html::shortenText(stripslashes($question->subject), 50, 0);
		}
		$document =& JFactory::getDocument();
		$document->setTitle( $this->_title );
	}
	
	//-----------
	
	private function _getScripts($option='',$name='')
	{
		$document =& JFactory::getDocument();
		
		if ($option) {
			$name = ($name) ? $name : $option;
			if (is_file(JPATH_ROOT.DS.'components'.DS.$option.DS.$name.'.js')) {
				$document->addScript('components'.DS.$option.DS.$name.'.js');
			}
		} else {
			if (is_file(JPATH_ROOT.DS.'components'.DS.$this->_option.DS.$this->_name.'.js')) {
				$document->addScript('components'.DS.$this->_option.DS.$this->_name.'.js');
			}
		}
	}
	
	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	protected function login() 
	{
		// Set the page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();
		
		// Instantiate a view
		$view = new JView( array('name'=>'login') );
		$view->title = JText::_(strtoupper($this->_option)).': '.JText::_('COM_ANSWERS_LOGIN');
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//-----------

	protected function start() 
	{		
		// Instantiate a new view
		$view = new JView( array('name'=>'start') );
		$view->option = $this->_option;
		$view->infolink = $this->infolink;
		$view->banking = $this->banking;
		
		// Incoming
		$view->filters = array();
		$view->filters['limit']    = JRequest::getInt( 'limit', 25 );
		$view->filters['start']    = JRequest::getInt( 'limitstart', 0 );
		$view->filters['tag']      = JRequest::getVar( 'tag', '' );
		$view->filters['q']        = JRequest::getVar( 'q', '' );
		$view->filters['filterby'] = JRequest::getVar( 'filterby', '' );
		$view->filters['sortby']   = JRequest::getVar( 'sortby', 'rewards' );

		$database =& JFactory::getDBO();
		
		$aq = new AnswersQuestion( $database );
		//$BT = new BankTransaction( $database );
		
		// Get a record count
		$view->total = $aq->getCount( $filters );
		
		// Get records
		$view->results = $aq->getResults( $filters );
		
		// Did we get any results?
		if (count($view->results) > 0) {
			// Do some processing on the results
			for ($i=0; $i < count($view->results); $i++) 
			{
				$row =& $view->results[$i];
				$row->created = Hubzero_View_Helper_Html::mkt($row->created);
				$row->when = Hubzero_View_Helper_Html::timeAgo($row->created);
				$row->points = $row->points ? $row->points : 0;
				$row->reports = $this->_getReports($row->id, 'question');
	
				// Get tags on this question
				$tagging = new AnswersTags( $database );
				$row->tags = $tagging->get_tags_on_object($row->id, 0, 0, 0);
			}
		}
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );

		// Add the CSS to the template
		$this->_getStyles();
		
		// Set the page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();

		// Output HTML
		$view->title = $this->_title;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------
	
	private function savereply()
	{
		// Is the user logged in?
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->setError( JText::_('COM_ANSWERS_LOGIN_TO_COMMENT') );
			$this->login();
			return;
		}

		// Incoming
		$id       = JRequest::getInt( 'referenceid', 0 );
		$rid      = JRequest::getInt( 'rid', 0 );
		$ajax     = JRequest::getInt( 'ajax', 0 );
		$category = JRequest::getVar( 'category', '' );
		$when     = date( 'Y-m-d H:i:s');
		
		// Trim and addslashes all posted items
		$_POST = array_map('trim',$_POST);
		
		if (!$id && !$ajax) {
			JError::raiseError( 500, JText::_('COM_ANSWERS_ERROR_QUESTION_ID_NOT_FOUND') );
			return;
		}
		
		if ($id && $category) {
			$database =& JFactory::getDBO();
			
			$row = new XComment( $database );
			if (!$row->bind( $_POST )) {
				JError::raiseError( 500, $row->getError() );
				return;
			}
			
			// Perform some text cleaning, etc.
			$row->comment   = Hubzero_View_Helper_Html::purifyText($row->comment);
			$row->comment   = nl2br($row->comment);
			$row->anonymous = ($row->anonymous == 1 || $row->anonymous == '1') ? $row->anonymous : 0;
			$row->added   	= $when;
			$row->state     = 0;
			$row->added_by 	= $juser->get('id');
			
			// Check for missing (required) fields
			if (!$row->check()) {
				JError::raiseError( 500, $row->getError() );
				return;
			}
			// Save the data
			if (!$row->store()) {
				JError::raiseError( 500, $row->getError() );
				return;
			}
		}
	
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$rid);
	}
	
	//-----------
	
	private function reply()
	{
		// Is the user logged in?
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->setError( JText::_('COM_ANSWERS_LOGIN_TO_COMMENT') );
			$this->login();
			return;
		}
		
		// Retrieve a review or comment ID and category
		$id    = JRequest::getInt( 'id', 0 );
		$refid = JRequest::getInt( 'refid', 0 );
		$cat   = JRequest::getVar( 'category', '' );
		
		// Do we have an ID?
		if (!$id) {
			// Cannot proceed
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			return;
		}
		
		// Do we have a category?
		if (!$cat) {
			// Cannot proceed
			$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$id);
			return;
		}
		
		// Store the comment object in our registry
		$this->category = $cat;
		$this->referenceid = $refid;
		$this->qid = $id;
		$this->question();	
	}
	
	//-----------
	
	private function rateitem()
	{
		// Is the user logged in?
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login( JText::_('COM_ANSWERS_PLEASE_LOGIN_TO_VOTE') );
			return;
		}
		
		// Incoming
		$id   = JRequest::getInt( 'refid', 0 );
		$ajax = JRequest::getInt( 'ajax', 0 );
		$cat  = JRequest::getVar( 'category', '' );
		$vote = JRequest::getVar( 'vote', '' );
		$ip   = $this->_ip_address();
		
		if (!$id) {
			// cannot proceed		
			return;
		}
		
		$database =& JFactory::getDBO();
		
		// load answer
		$row = new AnswersResponse( $database );
		$row->load( $id );
		$qid = $row->qid;
			
		$al = new AnswersLog( $database );
		$voted = $al->checkVote($id, $ip);
	
		if (!$voted && $vote && $row->created_by != $juser->get('username')) {
			// record if it was helpful or not
			if ($vote == 'yes') {
				$row->helpful++;
			} elseif ($vote == 'no') {
				$row->nothelpful++;
			}
				
			if (!$row->store()) {
				$this->setError( $row->getError() );
				return;
			}
				
			// Record user's vote (old way)
			$al->rid = $row->id;
			$al->ip = $ip;
			$al->helpful = $vote;
			if (!$al->check()) {
				$this->setError( $al->getError() );
				return;
			}
			if (!$al->store()) {
				$this->setError( $al->getError() );
				return;
			}
				
			// Record user's vote (new way)
			if ($cat) {
				require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$this->_option.DS.'vote.class.php' );
				$v = new Vote( $database );
				$v->referenceid = $row->id;
				$v->category = $cat;
				$v->voter = $juser->get('id');
				$v->ip = $ip;
				$v->voted = date( 'Y-m-d H:i:s', time() );
				$v->helpful = $vote;
				if (!$v->check()) {
					$this->setError( $v->getError() );
					return;
				}
				if (!$v->store()) {
					$this->setError( $v->getError() );
					return;
				}
			}
		}
						
		// update display
		if ($ajax) {
			$response = $row->getResponse($id, $ip);

			$view = new JView( array('name'=>'rateitem') );
			$view->option = $this->_option;
			$view->item = $response[0];
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			$view->display();
		} else {				
			$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$qid);
		}
	}
	
	//-----------

	protected function myquestions() 
	{
		// Is the user logged in?
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->setError( JText::_('COM_ANSWERS_LOGIN_TO_VIEW_QUESTIONS') );
			$this->login();
			return;
		}
		
		// Instantiate a new view
		$view = new JView( array('name'=>'search') );
		$view->option = $this->_option;
		$view->infolink = $this->infolink;
		$view->banking = $this->banking;
		$view->task = $this->_task;
		
		// Incoming
		$view->filters = array();
		$view->filters['limit']    = JRequest::getInt( 'limit', 25 );
		$view->filters['start']    = JRequest::getInt( 'limitstart', 0 );
		$view->filters['tag']      = JRequest::getVar( 'tag', '' );
		$view->filters['q']        = JRequest::getVar( 'q', '' );
		$view->filters['filterby'] = JRequest::getVar( 'filterby', 'all' );
		$view->filters['sortby']   = JRequest::getVar( 'sortby', 'rewards' );
		$view->filters['interest'] = JRequest::getVar( 'interest', 0 );
		$view->filters['assigned'] = JRequest::getVar( 'assigned', 0 );
		$view->filters['interest'] = ($view->filters['assigned'] == 1) ? 0 : $view->filters['interest']; 
		
		$database =& JFactory::getDBO();
			
		// Get questions of interest
		if ($view->filters['interest']) {
			require_once( JPATH_ROOT.DS.'components'.DS.'com_members'.DS.'members.tags.php' );
			
			// Get tags of interest
			$mt = new MembersTags( $database );
			$mytags  = $mt->get_tag_string( $juser->get('id') );

			$view->filters['tag'] = ($view->filters['tag']) ? $view->filters['tag'] : $mytags;
			
			if (!$view->filters['tag']) {
				$view->filters['filterby']   = 'none';
			}		
			$view->filters['mine'] = 0;
		} 
		
		// Get assigned questions
		if ($view->filters['assigned']) {
			require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.author.php' );
			
			// What tools did this user contribute?
			$TA = new ToolAuthor($database); 
			$tools = $TA->getToolContributions($juser->get('id'));
			$mytooltags = '';
			if ($tools) {
				foreach ($tools as $tool) 
				{
					$mytooltags .= 'tool'.$tool->toolname.',';
				}
			}
			
			$view->filters['tag'] = ($view->filters['tag']) ? $view->filters['tag'] : $mytooltags;
			if (!$view->filters['tag']) {
				$view->filters['filterby'] = 'none';
			}
				
			$view->filters['mine'] = 0;
		}
		 
		if (!$view->filters['assigned'] && !$view->filters['interest']) {
			$view->filters['mine'] = 1;
		}
		$view->filters['mine'] = 1;

		$aq = new AnswersQuestion( $database );

		// Get a record count
		$view->total = $aq->getCount( $view->filters );
		
		// Get records
		$view->results = $aq->getResults( $view->filters );
		
		// Did we get any results?
		if (count($view->results) > 0) {
			// Do some processing on the results
			for ($i=0; $i < count($view->results); $i++) 
			{
				$row =& $view->results[$i];
				$row->created = Hubzero_View_Helper_Html::mkt($row->created);
				$row->when = Hubzero_View_Helper_Html::timeAgo($row->created);
				$row->points = $row->points ? $row->points : 0;
				$row->reports = $this->_getReports($row->id, 'question');
	
				// Get tags on this question
				$tagging = new AnswersTags( $database );
				$row->tags = $tagging->get_tags_on_object($row->id, 0, 0, 0);
			}
		}
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );

		// Add the CSS to the template
		$this->_getStyles();
		
		// Set the page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();

		// Output HTML
		$view->title = $this->_title;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//-----------

	protected function search()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'search') );
		$view->option = $this->_option;
		$view->infolink = $this->infolink;
		$view->banking = $this->banking;
		$view->task = $this->_task;
		
		// Incoming
		$view->filters = array();
		$view->filters['limit']    = JRequest::getInt( 'limit', 25 );
		$view->filters['start']    = JRequest::getInt( 'limitstart', 0 );
		$view->filters['tag']      = JRequest::getVar( 'tags', '' );
		$view->filters['tag']      = ($view->filters['tag']) ? $view->filters['tag'] : JRequest::getVar( 'tag', '' );
		$view->filters['q']        = JRequest::getVar( 'q', '' );
		$view->filters['filterby'] = JRequest::getVar( 'filterby', '' );
		$view->filters['sortby']   = JRequest::getVar( 'sortby', 'rewards' );
		
		// Instantiate a Questions object
		$database =& JFactory::getDBO();
		$aq = new AnswersQuestion( $database );
		
		// Get a record count
		$view->total = $aq->getCount( $view->filters );
		
		// Get records
		$view->results = $aq->getResults( $view->filters );
		
		// Did we get any results?
		if (count($view->results) > 0) {
			// Do some processing on the results
			for ($i=0; $i < count($view->results); $i++) 
			{
				$row =& $view->results[$i];
				$row->created = Hubzero_View_Helper_Html::mkt($row->created);
				$row->when = Hubzero_View_Helper_Html::timeAgo($row->created);
				$row->points = ($row->points) ? $row->points : 0;
				$row->reports = $this->_getReports($row->id, 'question');
	
				// Get tags on this question
				$tagging = new AnswersTags( $database );
				$row->tags = $tagging->get_tags_on_object($row->id, 0, 0, 0);
			}
		}
		
		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );

		// Add the CSS to the template
		$this->_getStyles();
		
		// Set the page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();

		// Output HTML
		$view->title = $this->_title;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------

	protected function question()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'question') );
		$view->option = $this->_option;
		$view->infolink = $this->infolink;
		$view->banking = $this->banking;
		
		// Incoming
		$id   = JRequest::getInt( 'id', 0 );
		$note = $this->_note(JRequest::getInt( 'note', 0));
		$vote = JRequest::getVar( 'vote', 0 );
		
		if (isset($this->qid)) {
			$id = $this->qid;
		}
				
		// Ensure we have an ID to work with
		if (!$id) {
			JError::raiseError( 404, JText::_('COM_ANSWERS_ERROR_QUESTION_ID_NOT_FOUND') );
			return;
		}
		
		$juser =& JFactory::getUser();
		$database =& JFactory::getDBO();
		
		if ($this->_task == 'reply') {
			$addcomment = new XComment( $database );
			$addcomment->referenceid = $this->referenceid;
			$addcomment->category = $this->category;
		} else {
			$addcomment = NULL;
		}

		// Did they vote for the question?
		if ($vote) {
			// Login required
			if ($juser->get('guest')) {
				$this->setError( JText::_('COM_ANSWERS_LOGIN_TO_RECOMMEND_QUESTION') );
				$this->login();
				return;
			} else {
				$this->vote($database, $id);
			}
		}
		
		// Load the question
		$question = new AnswersQuestion($database);
		$question->load($id);
		
		// Check if question with this ID exists
		if (!$question->check()) {
			$id = 0;
		}
		
		// Get tags on this question
		$tagging = new AnswersTags( $database );
		$tags = $tagging->get_tags_on_object($id, 0, 0, 0);
			
		// Check reward value of the question 
		$reward = 0;
		if ($this->banking) {
			$BT = new BankTransaction($database);
			$reward = $BT->getAmount( 'answers', 'hold', $id );
		}
		
		// Check if person voted
		$voted = 0;
		$ip = '';
		if (!$juser->get('guest')) {
			$voted = $this->_getVote($id);
			$ip = $this->_ip_address();
		}
		
		// Check for abuse reports
		$question->reports = $this->_getReports($id, 'question');
				
		// Get responses
		$ar = new AnswersResponse( $database );
		$responses = $ar->getRecords( array('ip'=>$ip,'qid'=>$id) );
		
		// Calculate max award
		if ($this->banking) {
			$AE = new AnswersEconomy( $database );
			$question->marketvalue = round($AE->calculate_marketvalue($id, 'maxaward'));
			$question->maxaward = round(2* $question->marketvalue/3 + $reward);
		}
		
		// Determines if we're using abuse reports or not
		$abuse = false;
		if (is_file(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'support.reportabuse.php')) {
			$abuse = true;
		}
				
		// Determines if we're allowing comments
		$reply = false;
		if (is_file(JPATH_ROOT.DS.'plugins'.DS.'xhub'.DS.'xlibraries'.DS.'xcomment.php')) {
			$reply = true;
		}
		
		if ($responses && $reply && $abuse) {
			foreach ($responses as $response) 
			{
				$response->replies = $this->_getComments($response, 'answer', 0);
				$response->reports = $this->_getReports($response->id, 'answer');
			}
		}
		
		// Add the CSS to the template
		$this->_getStyles();
		$this->_getStyles($this->_option, 'vote.css');
		
		// Add the Javascript to the template
		$this->_getScripts();
		$this->_getScripts($this->_option, 'vote');
		
		// Set the page title
		$this->_buildTitle($question);
		
		// Set the pathway
		$this->_buildPathway($question);

		// Output HTML
		$view->title = $this->_title;
		$view->juser = $juser;
		$view->question = $question;
		$view->responses = $responses;
		$view->id = $id;
		$view->tags = $tags;
		$view->responding = 0;
		$view->reward = $reward;
		$view->voted = $voted;
		$view->note = $note;
		$view->addcomment = $addcomment;
		$view->showcomments = $this->showcomments;
		$view->abuse = $abuse;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//-----------

	protected function answer()
	{
		$juser =& JFactory::getUser();
		
		$responding = ($this->_task == 'delete')   ? 4 : 1;		
		if ($this->_task == 'math') { 
			$responding= 6;
		}
		$note = $this->_note(JRequest::getInt( 'note', 0));
		$ip = (!$juser->get('guest')) ? $this->_ip_address() : '';
		$id = JRequest::getInt( 'id', 0 );
		
		// Login required
		if ($juser->get('guest') && $this->_task != 'math') {
			if ($responding != 4) {
				$this->setError( JText::_('COM_ANSWERS_PLEASE_LOGIN_TO_ANSWER') );
			}
			$this->login();
			return;
		}
		
		$database =& JFactory::getDBO();
		
		// Load the question
		$question = new AnswersQuestion( $database );
		$BT = new BankTransaction( $database );
		$question->load( $id );
		
		// check if question with this id exists
		if (!$question->check()) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option);
			return;
		}

		// check if user is attempting to answer his own answer
		if ($question->created_by == $juser->get('username') && $responding==1) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$id.'&note=6');
			return;
		} else if ($question->created_by != $juser->get('username') && $responding==4) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$id.'&note=7');
			return;
		}
		
		// Get tags on this question
		$tagging = new AnswersTags( $database );
		$tags = $tagging->get_tags_on_object($id, 0, 0, 0);

		// Check reward value of the question 
		if ($this->banking) {
			$reward = $BT->getAmount( 'answers', 'hold', $id );
		}
		$reward = $reward ? $reward : 0;
	
		// Check number of votes
		$voted = $this->_getVote($id);
			
		// Check for abuse reports
		$question->reports = $this->_getReports($id, 'question');	
		
		// Get responses
		$ar = new AnswersResponse( $database );
		$responses = $ar->getRecords( array('ip'=>$ip,'qid'=>$id) );
		
		// Determines if we're using abuse reports or not
		$abuse = false;
		if (is_file(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'support.reportabuse.php')) {
			$abuse = true;
		}
				
		// Determines if we're allowing comments
		$reply = false;
		if (is_file(JPATH_ROOT.DS.'plugins'.DS.'xhub'.DS.'xlibraries'.DS.'xcomment.php')) {
			$reply = true;
		}
		
		if ($responses && $reply && $abuse) {
			foreach ($responses as $response) 
			{
				$response->replies = $this->_getComments($response, 'answer', 0);
				$response->reports = $this->_getReports($response->id, 'answer');
			}
		}
		
		// Calculate max award
		if ($this->banking) {
			$AE = new AnswersEconomy( $database );
			$question->marketvalue = round($AE->calculate_marketvalue($id, 'maxaward'));
			$question->maxaward = round(2* $question->marketvalue/3 + $reward);
		}
		
		if (isset($this->comment)) {
			$addcomment =& $this->comment;
		} else {
			$addcomment = NULL;
		}
		
		if ($question->state != 0) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$id);
			return;
		}
			
		// Add the CSS to the template
		$this->_getStyles();
		$this->_getStyles($this->_option, 'vote.css');

		// Add the Javascript to the template
		$this->_getScripts();
		$this->_getScripts($this->_option, 'vote');

		// Set the page title
		$this->_buildTitle($question);

		// Set the pathway
		$this->_buildPathway($question);
		
		// Instantiate a new view
		$view = new JView( array('name'=>'question') );
		$view->option = $this->_option;
		$view->infolink = $this->infolink;
		$view->banking = $this->banking;
		$view->title = $this->_title;
		$view->juser = $juser;
		$view->responses = $responses;
		$view->question = $question;
		$view->id = $id;
		$view->tags = $tags;
		$view->reward = $reward;
		$view->voted = $voted;
		$view->note = $note;
		$view->showcomments = $this->showcomments;
		$view->responding = $responding;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//-----------

	protected function create()
	{
		// Login required
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login();
			return;
		}
		
		// Instantiate a new view
		$view = new JView( array('name'=>'create') );
		$view->option = $this->_option;
		$view->infolink = $this->infolink;
		$view->banking = $this->banking;
		$view->task = $this->_task;
		
		// Incoming
		$view->tag = JRequest::getVar( 'tag', '' );
		
		// Is banking turned on?
		$view->funds = 0;
		if ($this->banking) {
			$database =& JFactory::getDBO();
			
			$BTL = new BankTeller( $database, $juser->get('id') );
			$balance = $BTL->summary();
			$credit  = $BTL->credit_summary();
			$funds   = $balance - $credit;			
			$view->funds = ($funds > 0) ? $funds : 0;
		} 
		
		// Add the CSS to the template
		$this->_getStyles();
		
		// Set the page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();

		// Output HTML
		$view->title = $this->_title;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//----------------------------------------------------------
	// Retrievers
	//----------------------------------------------------------

	private function _getVote($id)
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		// Get the user's IP address
		$ip = $this->_ip_address();
				
		// See if a person from this IP has already voted in the last week
		$aql = new AnswersQuestionsLog( $database );
		$voted = $aql->checkVote($id, $ip, $juser->get('id'));
	
		return $voted;
	}
	
	//-----------
	
	private function _getReports($id, $cat)
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$filters = array();
		$filters['id']  = $id;
		$filters['category']  = $cat;
		$filters['state']  = 0;
		
		// Check for abuse reports on an item
		$ra = new ReportAbuse( $database );
		
		return $ra->getCount( $filters );
	}
	
	//-----------
	
	private function _getComments($item, $category, $level, $abuse=true)
	{
		$database =& JFactory::getDBO();
		
		$level++;

		$hc = new XComment( $database );
		$comments = $hc->getResults( array('id'=>$item->id, 'category'=>$category) );
		
		if ($comments) {
			foreach ($comments as $comment) 
			{
				$comment->replies = $this->_getComments($comment, 'answercomment', $level, $abuse);
				if ($abuse) {
					$comment->reports = $this->_getReports($comment->id, 'answercomment');
				}
			}
		}
		return $comments;
	}

	//----------------------------------------------------------
	// Processors
	//----------------------------------------------------------
	
	protected function saveq()
	{
		// Login required
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login();
			return;
		}

		// trim and addslashes all posted items
		$_POST = array_map('trim',$_POST);
		
		// Incoming
		$tags  = JRequest::getVar( 'tags', '' );
		$funds = JRequest::getVar( 'funds', '0' );
		$reward = JRequest::getVar( 'reward', '0' );
		
		// If offering a reward, do some checks
		if ($reward) {
			// Is it an actual number?
			if (!is_numeric($reward)) {
				JError::raiseError( 500, JText::_('COM_ANSWERS_REWARD_MUST_BE_NUMERIC') );
				return;
			}
			// Are they offering more than they can afford?
			if ($reward > $funds) {
				JError::raiseError( 500, JText::_('COM_ANSWERS_INSUFFICIENT_FUNDS') );
				return;
			}
		}
		
		// Ensure the user added a tag
		if (!$tags) {
			JError::raiseError( 500, JText::_('COM_ANSWERS_QUESTION_MUST_HAVE_TAG') );
			return;
		}
		
		// Initiate class and bind posted items to database fields
		$database =& JFactory::getDBO();
		$row = new AnswersQuestion( $database );
		if (!$row->bind( $_POST )) {
			JError::raiseError( 500, $row->getError() );
			return;
		}
		
		$row->subject    = TextFilter::cleanXss($row->subject);
		$row->question   = TextFilter::cleanXss($row->question);
		$row->question   = nl2br($row->question);
		$row->created    = date( 'Y-m-d H:i:s', time() );
		$row->created_by = $juser->get('username');
		$row->state      = 0;
		$row->email      = 1; // force notification
		if ($reward && $this->banking) {
			$row->reward = 1;
		}
		
		// Check content
		if (!$row->check()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		// Store new content
		if (!$row->store()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		// Hold the reward for this question if we're banking
		if ($reward && $this->banking) {
			$BTL = new BankTeller( $database, $juser->get('id') );
			$BTL->hold($reward, JText::_('COM_ANSWERS_HOLD_REWARD_FOR_BEST_ANSWER'), 'answers', $row->id);	
		}
		
		// Add the tags
		$tagging = new AnswersTags( $database );
		$tagging->tag_object($juser->get('id'), $row->id, $tags, 1, 0);
		
		// Redirect to the question
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$row->id.'&note=5');
	}
	
	//-----------
	
	protected function savea()
	{
		// Login required
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login();
			return;
		}
		
		// Incoming
		$id = JRequest::getInt( 'qid', 0 );
		
		// Trim and addslashes all posted items
		$_POST = array_map('trim',$_POST);
	
		// Initiate class and bind posted items to database fields
		$database =& JFactory::getDBO();
		$row = new AnswersResponse( $database );
		if (!$row->bind( $_POST )) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		$row->answer     = TextFilter::cleanXss($row->answer);
		$row->answer     = nl2br($row->answer);
		$row->created_by = $juser->get('username');
		$row->created    = date( 'Y-m-d H:i:s', time() );
		$row->state      = 0;

		// Check content
		if (!$row->check()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}

		// Store new content
		if (!$row->store()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}
		
		// Load the question
		$question = new AnswersQuestion( $database );
		$question->load( $id );
		
		$jconfig =& JFactory::getConfig();
		
		// Build the "from" info
		$hub = array(
			'email' => $jconfig->getValue('config.mailfrom'), 
			'name' => $jconfig->getValue('config.sitename').' '.JText::_('COM_ANSWERS_ANSWERS')
		);
		
		// Build the message subject
		$subject = $jconfig->getValue('config.sitename').' '.JText::_('COM_ANSWERS_ANSWERS').', '.JText::_('COM_ANSWERS_QUESTION').' #'.$question->id.' '.JText::_('COM_ANSWERS_RESPONSE');
		
		// Build the message	
		$eview = new JView( array('name'=>'emails','layout'=>'response') );
		$eview->option = $this->_option;
		$eview->hubShortName = $jconfig->getValue('config.sitename');
		$eview->juser = $juser;
		$eview->question = $question;
		$eview->row = $row;
		$eview->id = $id;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);

		$user =& JUser::getInstance( $question->created_by );
		
		// Send the message
		JPluginHelper::importPlugin( 'xmessage' );
		$dispatcher =& JDispatcher::getInstance();
		/*if (!$dispatcher->trigger( 'onSendMessage', array( 'answers_reply_submitted', $subject, $message, $hub, array($user->get('id')), $this->_option, $question->id, JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$id)))) {
			$this->setError( JText::_('COM_ANSWERS_MESSAGE_FAILED') );
		}*/
		
		// Redirect to the question
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$id.'&note=4');
	}
	
	//-----------
	
	protected function delete_q()
	{
		// Login required
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login();
			return;
		}
		
		$database =& JFactory::getDBO();
		
		// Incoming
		$id = JRequest::getInt( 'qid', 0 );
		$ip = (!$juser->get('guest')) ? $this->_ip_address() : '';

		$BT = new BankTransaction( $database );
		$reward = $BT->getAmount( 'answers', 'hold', $id );
		$email = 0;
		
		$question = new AnswersQuestion( $database );
		$question->load( $id );
		
		// Check if user is authorized to delete
		if ($question->created_by != $juser->get('username')) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$id.'&note=3');
			return;
		} else if ($question->state == 1) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$id.'&note=2');
			return;
		}
		
		$question->state = 2;  // Deleted by user
		$question->reward = 0;
			
		// Store new content
		if (!$question->store()) {
			JError::raiseError( 500, $row->getError() );
			return;
		}
		
		// Get all the answers for this question
		$ar = new AnswersResponse( $database );
		$responses = $ar->getRecords( array('ip'=>$ip,'qid'=>$id) );
		
		if ($reward && $this->banking) {
			if ($responses) {
				$jconfig =& JFactory::getConfig();
				
				$users = array();
				foreach ($responses as $r) 
				{
					$user =& JUser::getInstance( $r->created_by );
					if (!is_object($user))  {
						continue;
					}
					$users[] = $user->get('id');
				}
				
				// Build the "from" info
				$hub = array(
					'email' => $jconfig->getValue('config.mailfrom'), 
					'name' => $jconfig->getValue('config.sitename').' '.JText::_('COM_ANSWERS_ANSWERS')
				);
				
				// Build the message subject
				$subject = $jconfig->getValue('config.sitename').' '.JText::_('COM_ANSWERS_ANSWERS').', '.JText::_('COM_ANSWERS_QUESTION').' #'.$id.' '.JText::_('COM_ANSWERS_WAS_REMOVED');
				
				// Build the message	
				$eview = new JView( array('name'=>'emails','layout'=>'removed') );
				$eview->option = $this->_option;
				$eview->hubShortName = $jconfig->getValue('config.sitename');
				$eview->juser = $juser;
				$eview->question = $question;
				$eview->id = $id;
				$message = $eview->loadTemplate();
				$message = str_replace("\n", "\r\n", $message);
				
				// Send the message
				JPluginHelper::importPlugin( 'xmessage' );
				$dispatcher =& JDispatcher::getInstance();
				/*if (!$dispatcher->trigger( 'onSendMessage', array( 'answers_question_deleted', $subject, $message, $hub, $users, $this->_option ))) {
					$this->setError( JText::_('COM_ANSWERS_MESSAGE_FAILED') );
				}*/
			}
			
			// Remove hold
			$BT->deleteRecords( 'answers', 'hold', $id );
					
			// Make credit adjustment
			$BTL_Q = new BankTeller( $database, $juser->get('id') );
			$credit = $BTL_Q->credit_summary();
			$adjusted = $credit - $reward;
			$BTL_Q->credit_adjustment($adjusted);
		}
		
		// Delete all tag associations	
		$tagging = new AnswersTags( $database );
		$tagging->remove_all_tags($id);
		
		// Get all the answers for this question		
		if ($responses) {
			$al = new AnswersLog( $database );
			foreach ($responses as $answer)
			{
				// Delete votes
				$al->deleteLog( $answer->id );
				
				// Delete response
				$ar->deleteResponse( $answer->id );
			}
		}
		
		// Redirect to the question
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$id.'&note=1');	
	}

	//-----------
	
	protected function accept()
	{
		// Login required
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login();
			return;
		}
		
		// Incoming
		$id  = JRequest::getInt( 'id', 0 );
		$rid = JRequest::getInt( 'rid', 0 );
		
		$database =& JFactory::getDBO();
		
		// Load and mark the answer as THE accepted answer
		$answer = new AnswersResponse( $database );
		$answer->load( $rid );
		$answer->state = 1;

		// Check changes
		if (!$answer->check()) {
			$this->setError( $answer->getError() );
		}

		// Save changes
		if (!$answer->store()) {
			$this->setError( $answer->getError() );
		}
		
		// Load and mark the question as closed
		$question = new AnswersQuestion( $database );
		$question->load( $id );
		$question->state = 1;
		$question->reward = 0; // Uncheck reward label
		
		$user =& JUser::getInstance( $question->created_by );
		
		// Check changes
		if (!$question->check()) {
			$this->setError( $question->getError() );
		}

		// Save changes
		if (!$question->store()) {
			$this->setError( $question->getError() );
		}
		
		if ($this->banking) {
			// Calculate and distribute earned points
			$AE = new AnswersEconomy( $database );			
			$AE->distribute_points($id, $question->created_by, $answer->created_by, 'closure');
		}
		
		// Load the plugins
		JPluginHelper::importPlugin( 'xmessage' );
		$dispatcher =& JDispatcher::getInstance();
		
		// Call the plugin
		/*if (!$dispatcher->trigger( 'onTakeAction', array( 'answers_reply_submitted', array($user->get('id')), $this->_option, $question->id ))) {
			$this->setError( JText::_('COM_ANSWERS_ACTION_FAILED')  );
		}*/

		// Redirect to the question
		$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$id.'&note=10');	
	}
	
	//-----------
	
	protected function vote($database, $id)
	{
		$ip = $this->_ip_address();
		
		// Login required
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->setError( JText::_('COM_ANSWERS_PLEASE_LOGIN_TO_VOTE') );
			$this->login();
			return;
		}
			
		// See if a person from this IP has already voted
		$al = new AnswersQuestionsLog( $database );
		$voted = $al->checkVote( $id, $ip );
	
		if ($voted) {	
			$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$id.'&note=8');
			return;
		}
				
		// load the resource
		$row = new AnswersQuestion( $database );
		$row->load( $id );
		$this->qid = $id;
		
		// check if user is rating his own question
		if ($row->created_by == $juser->get('username')) {
			$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=question&id='.$id.'&note=9');
			return;
		}
		
		// record vote
		$row->helpful++;
		
		if (!$row->store()) {
			$this->setError( $row->getError() );
			return;
		}
		
		$expires = time() + (7 * 24 * 60 * 60); // in a week
		$expires = date( 'Y-m-d H:i:s', $expires );
		
		// Record user's vote
		$al->qid = $id;
		$al->ip = $ip;
		$al->voter = $juser->get('id');
		$al->expires = $expires;
		if (!$al->check()) {
			$this->setError( $al->getError() );
			return;
		}
		if (!$al->store()) {
			$this->setError( $al->getError() );
			return;
		}
	}
	
	//----------------------------------------------------------
	// Misc Functions
	//----------------------------------------------------------

	private function _server($index = '')
	{		
		if (!isset($_SERVER[$index])) {
			return FALSE;
		}
		
		return $_SERVER[$index];
	}
	
	//-----------
	
	private function _valid_ip($ip)
	{
		return (!preg_match( "/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $ip)) ? FALSE : TRUE;
	}
	
	//-----------

	private function _ip_address()
	{
		if ($this->_server('REMOTE_ADDR') AND $this->_server('HTTP_CLIENT_IP')) {
			$ip_address = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ($this->_server('REMOTE_ADDR')) {
			$ip_address = $_SERVER['REMOTE_ADDR'];
		} elseif ($this->_server('HTTP_CLIENT_IP')) {
			$ip_address = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ($this->_server('HTTP_X_FORWARDED_FOR')) {
			$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		
		if ($ip_address === FALSE) {
			$ip_address = '0.0.0.0';
			return $ip_address;
		}
		
		if (strstr($ip_address, ',')) {
			$x = explode(',', $ip_address);
			$ip_address = end($x);
		}
		
		if (!$this->_valid_ip($ip_address)) {
			$ip_address = '0.0.0.0';
		}
				
		return $ip_address;
	}
	
	//-----------
	
	private function _authorize()
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return false;
		}
		
		// Check if they're a site admin (from Joomla)
		if ($juser->authorize($this->_option, 'manage')) {
			return 'admin';
		}

		return false;
	}
	
	//-----------
	
	private function _note($type, $note=array('msg'=>'','class'=>'warning')) 
	{
		switch ($type) 
		{
			case '1' :  // question was removed
				$note['msg'] = JText::_('COM_ANSWERS_NOTICE_QUESTION_REMOVED');
				$note['class'] = 'info';
			break;
			case '2' : // can't delete a closed question
				$note['msg'] = JText::_('COM_ANSWERS_WARNING_CANT_DELETE_CLOSED');
			break;
			case '3' : // not authorized to delete question
				$note['msg'] = JText::_('COM_ANSWERS_WARNING_CANT_DELETE');
			break;
			case '4' : // answer posted
				$note['msg'] = JText::_('COM_ANSWERS_NOTICE_POSTED_THANKS');
				$note['class'] = 'passed';
			break;
			case '5' : // question posted
				$note['msg'] = JText::_('COM_ANSWERS_NOTICE_QUESTION_POSTED_THANKS');
				$note['class'] = 'passed';
			break;
			case '6' : // can't answer own question
				$note['msg'] = JText::_('COM_ANSWERS_NOTICE_CANT_ANSWER_OWN_QUESTION');
			break;
			case '7' : // can't delete question
				$note['msg'] = JText::_('COM_ANSWERS_NOTICE_CANNOT_DELETE');
			break;
			case '8' : // can't vote again
				$note['msg'] = JText::_('COM_ANSWERS_NOTICE_ALREADY_VOTED_FOR_QUESTION');
			break;
			case '9' : // can't vote for own question
				$note['msg'] = JText::_('COM_ANSWERS_NOTICE_RECOMMEND_OWN_QUESTION');
			break;
			case '10' : // answer accepted
				$note['msg'] = JText::_('COM_ANSWERS_NOTICE_QUESTION_CLOSED');
			break;
		}
		return $note;
	}
}
?>