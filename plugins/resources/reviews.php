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

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_resources_reviews' );
	
//-----------

class plgResourcesReviews extends JPlugin
{
	public function plgResourcesReviews(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'resources', 'reviews' );
		$this->_params = new JParameter( $this->_plugin->params );
		
		
		$this->infolink = '/kb/points/';
		$upconfig =& JComponentHelper::getParams( 'com_userpoints' );
		$this->banking = $upconfig->get('bankAccounts');
	}
	
	//-----------
	
	public function &onResourcesAreas( $resource )
	{
		$areas = array(
			'reviews' => JText::_('PLG_RESOURCES_REVIEWS')
		);
		return $areas;
	}
	
	//-----------

	public function onResourcesRateItem( $option )
	{
		$id = JRequest::getInt( 'rid', 0 );
		
		$arr = array(
			'html'=>'',
			'metadata'=>''
		);
		
		ximport('Hubzero_View_Helper_Html');
		ximport('Hubzero_Plugin_View');
		
		$database =& JFactory::getDBO();
		$resource = new ResourcesResource($database);
		$resource->load( $id );
		
		$h = new PlgResourcesReviewsHelper();
		$h->resource = $resource;
		$h->option = $option;
		$h->_option = $option;
		$h->execute();
		
		return $arr;
	}

	//-----------

	public function onResources( $resource, $option, $areas, $rtrn='all' )
	{
		$arr = array(
			'html'=>'',
			'metadata'=>''
		);
			
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) {
			if (!array_intersect( $areas, $this->onResourcesAreas( $resource ) ) 
			&& !array_intersect( $areas, array_keys( $this->onResourcesAreas( $resource ) ) )) {
				$rtrn = 'metadata';
			}
		}

		ximport('Hubzero_View_Helper_Html');
		ximport('Hubzero_Plugin_View');

		// Instantiate a helper object and perform any needed actions
		$h = new PlgResourcesReviewsHelper();
		$h->resource = $resource;
		$h->option = $option;
		$h->_option = $option;
		$h->execute();

		// Get reviews for this resource
		$database =& JFactory::getDBO();
		$r = new ResourcesReview( $database );
		$reviews = $r->getRatings( $resource->id );

		// Are we returning any HTML?
		if ($rtrn == 'all' || $rtrn == 'html') {
			// Did they perform an action?
			// If so, they need to be logged in first.
			if (!$h->loggedin) {
				// Instantiate a view
				$view = new Hubzero_Plugin_View(
					array(
						'folder'=>'resources',
						'element'=>'reviews',
						'name'=>'login'
					)
				);
			} else {
				// Instantiate a view
				$view = new Hubzero_Plugin_View(
					array(
						'folder'=>'resources',
						'element'=>'reviews',
						'name'=>'browse'
					)
				);
			}
			
			// Thumbs voting CSS & JS
			$voting = $this->_params->get('voting');
			if ($voting) {
				ximport('xdocument');
				XDocument::addComponentStylesheet('com_answers', 'vote.css');
			}
			
			// Pass the view some info
			$view->option = $option;
			$view->resource = $resource;
			$view->reviews = $reviews;
			$view->voting = $voting;
			$view->h = $h;
			$view->banking = $this->banking;
			$view->infolink = $this->infolink;
			$view->voting = $voting;
			if ($h->getError()) {
				$view->setError( $h->getError() );
			}

			// Return the output
			$arr['html'] = $view->loadTemplate();
		}
		
		// Build the HTML meant for the "about" tab's metadata overview
		if ($rtrn == 'all' || $rtrn == 'metadata') {
			if ($resource->alias) {
				$url = JRoute::_('index.php?option='.$option.'&alias='.$resource->alias.'&active=reviews');
				$url2 = JRoute::_('index.php?option='.$option.'&alias='.$resource->alias.'&active=reviews&action=addreview#reviewform');
			} else {
				$url = JRoute::_('index.php?option='.$option.'&id='.$resource->id.'&active=reviews');
				$url2 = JRoute::_('index.php?option='.$option.'&id='.$resource->id.'&active=reviews&action=addreview#reviewform');
			}
			
			$arr['metadata']  = '<p class="review"><a href="'.$url.'">'.JText::sprintf('PLG_RESOURCES_REVIEWS_NUM_REVIEWS',count($reviews)).'</a> (<a href="'.$url2.'">'.JText::_('PLG_RESOURCES_REVIEWS_REVIEW_THIS').'</a>)</p>';
		}

		return $arr;
	}
	
	//-----------
	
	public function getComments($item, $category, $level, $abuse=false)
	{
		$database =& JFactory::getDBO();
		
		$level++;

		$hc = new XComment( $database );
		$comments = $hc->getResults( array('id'=>$item->id, 'category'=>$category) );
		
		if ($comments) {
			foreach ($comments as $comment) 
			{
				$comment->replies = plgResourcesReviews::getComments($comment, 'reviewcomment', $level, $abuse);
				if ($abuse) {
					$comment->abuse_reports = plgResourcesReviews::getAbuseReports($comment->id, 'reviewcomment');
				}
			}
		}
		return $comments;
	}
	
	//-----------
	
	public function getAbuseReports($item, $category)
	{
		$database =& JFactory::getDBO();

		$ra = new ReportAbuse( $database );
		return $ra->getCount( array('id'=>$item, 'category'=>$category) );
	}
}

//-----------

class PlgResourcesReviewsHelper extends JObject
{
	private $_data  = array();
	
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

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message, $this->_messageType );
		}
	}
	
	//-----------
	
	public function execute()
	{
		// Incoming action
		$action = JRequest::getVar( 'action', '' );

		$this->loggedin = true;
		
		if ($action) {
			// Check the user's logged-in status
			$juser =& JFactory::getUser();
			if ($juser->get('guest')) {
				$this->loggedin = false;
				return;
			}
		}
		
		// Perform an action
		switch ( $action ) 
		{
			case 'addreview':    $this->editreview();   break;
			case 'editreview':   $this->editreview();   break;
			case 'savereview':   $this->savereview();   break;
			case 'deletereview': $this->deletereview(); break;		
			case 'savereply': 	 $this->savereply(); 	break;
			case 'deletereply':  $this->deletereply();  break;	
			case 'rateitem':   	 $this->rateitem();  	break;
		}
	}
	
	//-----------
	
	private function savereply()
	{	
		$juser =& JFactory::getUser();
		
		// Is the user logged in?
		if ($juser->get('guest')) {
			$this->setError( JText::_('PLG_RESOURCES_REVIEWS_LOGIN_NOTICE') );
			return;
		}
		
		// Incoming
		$id       = JRequest::getInt('referenceid', 0 );
		$rid      = JRequest::getInt('rid', 0 );
		$category = JRequest::getVar('category', '' );
		$when     = date( 'Y-m-d H:i:s');
		
		// Trim and addslashes all posted items
		$_POST = array_map('trim',$_POST);
		
		if (!$id) {
			// Cannot proceed
			$this->setError( JText::_('PLG_RESOURCES_REVIEWS_COMMENT_ERROR_NO_REFERENCE_ID') );
			return;
		}
		
		if (!$category) {
			// Cannot proceed
			$this->setError( JText::_('PLG_RESOURCES_REVIEWS_COMMENT_ERROR_NO_CATEGORY') );
			return;
		}
		
		$database =& JFactory::getDBO();
		ximport( 'xcomment' );
			
		$row = new XComment( $database );
		if (!$row->bind( $_POST )) {
			$this->setError( $row->getError() );
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
			$this->setError( $row->getError() );
			return;
		}
		// Save the data
		if (!$row->store()) {
			$this->setError( $row->getError() );
			return;
		}
	}
	
	//-----------
	
	public function deletereply()
	{
		$database =& JFactory::getDBO();
		$resource =& $this->resource;
		
		// Incoming
		$replyid = JRequest::getInt( 'refid', 0 );
		
		// Do we have a review ID?
		if (!$replyid) {
			$this->setError( JText::_('PLG_RESOURCES_REVIEWS_COMMENT_ERROR_NO_REFERENCE_ID') );
			return;
		}
		
		// Do we have a resource ID?
		if (!$resource->id) {
			$this->setError( JText::_('PLG_RESOURCES_REVIEWS_NO_RESOURCE_ID') );
			return;
		}
		
		// Delete the review
		ximport( 'xcomment' );
		$reply = new XComment( $database );
		
		$comments = $reply->getResults( array('id'=>$replyid, 'category'=>'reviewcomment') );
		if (count($comments) > 0) {
			foreach ($comments as $comment) 
			{
				$reply->delete( $comment->id );
			}
		}
		$reply->delete( $replyid );
	}
	
	//-----------
	
	public function rateitem()
	{		
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
				
		$id   = JRequest::getInt( 'refid', 0 );
		$ajax = JRequest::getInt( 'ajax', 0 );
		$cat  = JRequest::getVar( 'category', 'review' );
		$vote = JRequest::getVar( 'vote', '' );
		$ip   = $this->_ipAddress();
		$rid  = JRequest::getInt( 'id', 0 );
				
		if (!$id) {
			// Cannot proceed		
			return;
		}
	
		// Is the user logged in?
		if ($juser->get('guest')) {
			$this->login( JText::_('PLG_RESOURCES_REVIEWS_PLEASE_LOGIN_TO_VOTE') );
			return;
		} else {
			// Load answer
			$rev = new ResourcesReview( $database );
			$rev->load($id );
			$voted = $rev->getVote ($id, $cat, $juser->get('id'));
	
			if (!$voted && $vote && $rev->user_id != $juser->get('id')) {
				require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'vote.class.php' );
				$v = new Vote( $database );
				$v->referenceid = $id;
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
						
			// update display
			if ($ajax) {
				$response = $rev->getRating( $id, $juser->get('id'));
				//echo plgResourcesReviews::rateitem($response[0], $juser, $this->_option, $rid);
				$view = new Hubzero_Plugin_View(
					array(
						'folder'=>'resources',
						'element'=>'reviews',
						'name'=>'browse',
						'layout'=>'rateitem'
					)
				);
				$view->option = $this->_option;
				$view->item = $response[0];
				$view->display();
			} else {				
				$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=reviews&id='.$rid);
			}
		}
	}
	
	//-----------
	
	public function editreview()
	{
		// Is the user logged in?
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->setError( JText::_('PLG_RESOURCES_REVIEWS_LOGIN_NOTICE') );
			return;
		}
		
		$resource =& $this->resource;
		
		// Do we have an ID?
		if (!$resource->id) {
			// No - fail! Can't do anything else without an ID
			$this->setError( JText::_('PLG_RESOURCES_REVIEWS_NO_RESOURCE_ID') );
			return;
		}
		
		// Incoming
		$myr = JRequest::getInt( 'myrating', 0 );

		$database =& JFactory::getDBO();
		
		$review = new ResourcesReview( $database );
		$review->loadUserReview( $resource->id, $juser->get('id') );
		if (!$review->id) {
			// New review, get the user's ID
			$review->user_id = $juser->get('id');
			$review->resource_id = $resource->id;
			$review->tags = '';
		} else {
			// Editing a review, do some prep work
			$review->comment = str_replace('<br />','',$review->comment);

			$RE = new ResourceExtended($resource->id, $database);
			$RE->getTagsForEditing($review->user_id);
			$review->tags = ($RE->tagsForEditing) ? $RE->tagsForEditing : '';
		}
		$review->rating = ($myr) ? $myr : $review->rating;

		// Store the object in our registry
		$this->myreview = $review;
		return;
	}
	
	//-----------

	public function savereview()
	{
		// Incoming
		$resource_id = JRequest::getInt( 'resource_id', 0 );
		
		// Do we have a resource ID?
		if (!$resource_id) {
			// No ID - fail! Can't do anything else without an ID
			$this->setError( JText::_('PLG_RESOURCES_REVIEWS_NO_RESOURCE_ID') );
			return;
		}
		
		$database =& JFactory::getDBO();
		
		// Bind the form data to our object
		$row = new ResourcesReview( $database );
		if (!$row->bind( $_POST )) {
			$this->setError( $row->getError() );
			return;
		}
		
		// Perform some text cleaning, etc.
		$row->id        = JRequest::getInt( 'reviewid', 0 );
		$row->comment   = Hubzero_View_Helper_Html::purifyText($row->comment);
		$row->comment   = nl2br($row->comment);
		$row->anonymous = ($row->anonymous == 1 || $row->anonymous == '1') ? $row->anonymous : 0;
		$row->created   = ($row->created) ? $row->created : date( "Y-m-d H:i:s" );
		
		// Check for missing (required) fields
		if (!$row->check()) {
			$this->setError( $row->getError() );
			return;
		}
		// Save the data
		if (!$row->store()) {
			$this->setError( $row->getError() );
			return;
		}
		
		// Calculate the new average rating for the parent resource
		$resource =& $this->resource;
		$resource->calculateRating();
		$resource->updateRating();
		
		// Process tags
		$tags = trim(JRequest::getVar( 'review_tags', '' ));
		if ($tags) {
			$rt = new ResourcesTags( $database );
			$rt->tag_object($row->user_id, $resource_id, $tags, 1, 0);
		}
		
		// Instantiate a helper object and get all the contributor IDs
		$helper = new ResourcesHelper( $resource->id, $database );
		$helper->getContributorIDs();
		$users = $helper->contributorIDs;
		
		// Get the HUB configuration
		$jconfig =& JFactory::getConfig();
		
		// Build the subject
		$subject = $jconfig->getValue('config.sitename').' '.JText::_('PLG_RESOURCES_REVIEWS_CONTRIBUTIONS');
		
		// Message
		$juser =& JFactory::getUser();
		$eview = new Hubzero_Plugin_View(
			array(
				'folder'=>'resources',
				'element'=>'reviews',
				'name'=>'emails'
			)
		);
		$eview->option = $this->_option;
		$eview->juser = $juser;
		$eview->resource = $resource;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);
		
		// Build the "from" data for the e-mail
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_('PLG_RESOURCES_REVIEWS_CONTRIBUTIONS');
		$from['email'] = $jconfig->getValue('config.mailfrom');
		
		// Send message
		JPluginHelper::importPlugin( 'xmessage' );
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger( 'onSendMessage', array( 'resources_new_comment', $subject, $message, $from, $users, $this->_option ))) {
			$this->setError( JText::_('PLG_RESOURCES_REVIEWS_FAILED_TO_MESSAGE') );
		}
	}

	//-----------
	
	public function deletereview()
	{
		$database =& JFactory::getDBO();
		$resource =& $this->resource;
		
		// Incoming
		$reviewid = JRequest::getInt( 'reviewid', 0 );
		
		// Do we have a review ID?
		if (!$reviewid) {
			$this->setError( JText::_('PLG_RESOURCES_REVIEWS_NO_ID') );
			return;
		}
		
		// Do we have a resource ID?
		if (!$resource->id) {
			$this->setError( JText::_('PLG_RESOURCES_REVIEWS_NO_RESOURCE_ID') );
			return;
		}
		
		$review = new ResourcesReview( $database );
		
		// Delete the review's comments
		ximport( 'xcomment' );
		$reply = new XComment( $database );
		
		$comments1 = $reply->getResults( array('id'=>$reviewid, 'category'=>'review') );
		if (count($comments1) > 0) {
			foreach ($comments1 as $comment1) 
			{
				$comments2 = $reply->getResults( array('id'=>$comment1->id, 'category'=>'reviewcomment') );
				if (count($comments2) > 0) {
					foreach ($comments2 as $comment2) 
					{
						$comments3 = $reply->getResults( array('id'=>$comment2->id, 'category'=>'reviewcomment') );
						if (count($comments3) > 0) {
							foreach ($comments3 as $comment3) 
							{
								$reply->delete( $comment3->id );
							}
						}
						$reply->delete( $comment2->id );
					}
				}
				$reply->delete( $comment1->id );
			}
		}
		
		// Delete the review
		$review->delete( $reviewid );

		// Recalculate the average rating for the parent resource
		$resource->calculateRating();
		$resource->updateRating();
	}
	
	//-----------
	
	private function _validIp($ip)
	{
		return (!preg_match( "/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $ip)) ? FALSE : TRUE;
	}
	
	//-----------

	private function _ipAddress()
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
		
		if (!$this->_validIp($ip_address)) {
			$ip_address = '0.0.0.0';
		}
				
		return $ip_address;
	}
	
	//-----------
	
	private function _server($index = '')
	{		
		if (!isset($_SERVER[$index])) {
			return FALSE;
		}
		
		return $_SERVER[$index];
	}
}
