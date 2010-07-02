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

class FeedbackController extends JObject
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
	
	public function execute()
	{
		// Load the component config
		$config =& JComponentHelper::getParams( $this->_option );
		$this->config = $config;
		
		$this->_task = JRequest::getVar( 'task', '', 'post' );
		if (!$this->_task) {
			$this->_task = JRequest::getVar( 'task', '', 'get' );
		}
		
		switch ($this->_task) 
		{
			// Image management
			case 'upload':          $this->upload();          break;
			case 'img':             $this->img();             break;
			case 'delete':          $this->delete();          break;
			
			// Processors
			case 'sendsuggestions': $this->sendsuggestions(); break;
			case 'sendstory':       $this->sendstory();       break;
			case 'sendreport':      $this->sendreport();      break;
			
			// Views
			case 'suggestions':     $this->suggestions();     break;
			case 'success_story':   $this->success_story();   break;
			case 'report_problems': $this->report_problems(); break;
			case 'poll':            $this->poll();            break;
			case 'main':            $this->main();            break;

			default: $this->main(); break;
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
	
	private function _buildPathway() 
	{
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option='.$this->_option
			);
		}
		if ($this->_task) {
			$pathway->addItem(
				JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task)),
				'index.php?option='.$this->_option.'&task='.$this->_task
			);
		}
	}
	
	//-----------
	
	private function _buildTitle() 
	{
		$this->_title = JText::_(strtoupper($this->_option));
		if ($this->_task) {
			$this->_title .= ': '.JText::_(strtoupper($this->_option).'_'.strtoupper($this->_task));
		}
		$document =& JFactory::getDocument();
		$document->setTitle( $this->_title );
	}
	
	//-----------
	
	private function _getStyles() 
	{
		ximport('xdocument');
		XDocument::addComponentStylesheet($this->_option);
	}

	//-----------
	
	private function _getScripts()
	{
		$document =& JFactory::getDocument();
		if (is_file('components'.DS.$this->_option.DS.$this->_name.'.js')) {
			$document->addScript('components'.DS.$this->_option.DS.$this->_name.'.js');
		}
	}

	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------
	
	public function login() 
	{
		$view = new JView( array('name'=>'login') );
		$view->title = $this->_title;
		$view->msg   = $this->_msg;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
	
	//-----------
	
	protected function main() 
	{
		$database =& JFactory::getDBO();
		
		// Check if wishlist component entry is there
		$database->setQuery( "SELECT c.id FROM #__components as c WHERE c.option='com_wishlist' AND enabled=1" );
		$wishlist = $database->loadResult();
		$wishlist = $wishlist ? 1 : 0;
		
		// Check if xpoll component entry is there
		$database->setQuery( "SELECT c.id FROM #__components as c WHERE c.option='com_xpoll' AND enabled=1" );
		$xpoll = $database->loadResult();
		$xpoll = $xpoll ? 1 : 0;

		// Set page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();
		
		// Push some styles to the template
		$this->_getStyles();
		
		// Output HTML
		$view = new JView( array('name'=>'main') );
		$view->title = $this->_title;
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->wishlist = $wishlist;
		$view->xpoll = $xpoll;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//-----------

	protected function success_story()
	{
		// Logged-in user?
		$juser =& JFactory::getUser();
		
		// Incoming
		$quote = array();
		$quote['long'] = JRequest::getVar('quote', '', 'post');
		$quote['short'] = JRequest::getVar('short_quote', '', 'post');
		
		// Generate a CAPTCHA
		$captcha = array();
		$captcha['operand1'] = rand(0,10);
		$captcha['operand2'] = rand(0,10);
		$captcha['sum'] = $captcha['operand1'] + $captcha['operand2'];
		$captcha['key'] = $this->_generate_hash($captcha['sum'],date('j'));
		
		// Set page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();
		
		// Push some styles to the template
		$this->_getStyles();
		
		if($juser->get('guest')) {
			$this->_msg = JText::_('To submit a success story, you need to be logged in. Please login using the form below:');
			$this->login();
			return;
		}
		
		// Output HTML
		$view = new JView( array('name'=>'story') );
		$view->title = $this->_title;
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->user = $this->_getUser();
		$view->quote = $quote;
		$view->captcha = $captcha;
		$view->verified = ($juser->get('guest')) ? 0 : 1;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//-----------

	protected function poll()
	{
		// Set page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();
		
		// Push some styles to the template
		$this->_getStyles();
		
		// Output HTML
		$view = new JView( array('name'=>'poll') );
		$view->title = $this->_title;
		$view->option = $this->_option;
		$view->task = $this->_task;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//-----------

	protected function suggestions() 
	{
		// Logged-in user?
		$juser =& JFactory::getUser();
		
		// Incoming
		$suggestion = array();
		$suggestion['for'] = JRequest::getVar( 'for', '' );
		$suggestion['idea'] = '';
	
		// Generate a CAPTCHA
		$suggestion['operand1'] = rand(0,10);
		$suggestion['operand2'] = rand(0,10);
		$suggestion['sum'] = $suggestion['operand1'] + $suggestion['operand2'];
		$suggestion['key'] = $this->_generate_hash($suggestion['sum'],date('j'));

		// Set page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();
		
		// Push some styles to the template
		$this->_getStyles();
		
		// Output HTML
		$view = new JView( array('name'=>'suggestions') );
		$view->title = $this->_title;
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->user = $this->_getUser();
		$view->suggestion = $suggestion;
		$view->verified = ($juser->get('guest')) ? 0 : 1;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//-----------

	protected function report_problems() 
	{
		// Logged-in user?
		$juser =& JFactory::getUser();
	
		// Get browser info
		ximport('Hubzero_Browser');
		$browser = new Hubzero_Browser();
		
		$problem = array(
			'os' => $browser->getOs(), 
			'osver' => $browser->getOsVersion(), 
			'browser' => $browser->getBrowser(), 
			'browserver' => $browser->getBrowserVersion(), 
			'topic' => '',
			'short' => '', 
			'long' => '', 
			'referer' => JRequest::getVar( 'HTTP_REFERER', NULL, 'server' ), 
			'tool' => JRequest::getVar( 'tool', '' )
		);
					 
		// Generate a CAPTCHA
		$problem['operand1'] = rand(0,10);
		$problem['operand2'] = rand(0,10);
		$problem['sum'] = $problem['operand1'] + $problem['operand2'];
		$problem['key'] = $this->_generate_hash($problem['sum'],date('j'));

		// Set page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();
		
		// Push some styles to the template
		$this->_getStyles();

		// Output HTML
		$view = new JView( array('name'=>'report') );
		$view->title = $this->_title;
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->reporter = $this->_getUser();
		$view->problem = $problem;
		$view->verified = ($juser->get('guest')) ? 0 : 1;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//----------------------------------------------------------
	// Processors
	//----------------------------------------------------------

	public function sendstory() 
	{
		// Set page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();
	
		// Push some styles to the template
		$this->_getStyles();
	
		$database =& JFactory::getDBO();
	
		// Initiate class and bind posted items to database fields
		$row = new FeedbackQuotes( $database );
		if (!$row->bind( $_POST )) {
			JError::raiseError( 500, $row->getError() );
			return;
		}
		
		$user = array('uid'=>$row->userid, 'name'=>$row->fullname, 'org'=>$row->org, 'email'=>$row->useremail );
		
		// Check that a story was entered
		if (!$row->quote) {
			$this->setError(JText::_('COM_FEEDBACK_ERROR_MISSING_STORY'));
			
			// Generate a CAPTCHA
			$captcha = array();
			$captcha['operand1'] = rand(0,10);
			$captcha['operand2'] = rand(0,10);
			$captcha['sum'] = $captcha['operand1'] + $captcha['operand2'];
			$captcha['key'] = $this->_generate_hash($captcha['sum'],date('j'));
			
			// Output HTML
			$view = new JView( array('name'=>'story') );
			$view->title = $this->_title;
			$view->option = $this->_option;
			$view->task = $this->_task;
			$view->user = $user;
			$view->quote = $row->quote;
			$view->captcha = $captcha;
			$view->verified = JRequest::getInt( 'verified', 0 );
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			$view->display();
			return;
		}
			
		// Code cleaner for xhtml transitional compliance
		$row->quote = Hubzero_View_Helper_Html::purifyText($row->quote);
		$row->quote = str_replace( '<br>', '<br />', $row->quote );
		$row->date  = date( 'Y-m-d H:i:s', time() );
		$row->picture = basename($row->picture);
		
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
		
		// Output HTML
		$view = new JView( array('name'=>'story', 'layout'=>'thanks') );
		$view->title = $this->_title;
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->user = $user;
		$view->quote = $row->quote;
		$view->picture = $row->picture;
		$view->config = $this->config;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//-----------

	protected function sendsuggestions() 
	{
		// Set page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();
		
		// Push some styles to the template
		$this->_getStyles();
		
		// Incoming
		$suggester  = array_map('trim', $_POST['suggester']);
		$suggestion = array_map('trim', $_POST['suggestion']);
		$suggester  = array_map(array('Hubzero_View_Helper_Html','purifyText'), $suggester);
		$suggestion = array_map(array('Hubzero_View_Helper_Html','purifyText'), $suggestion);
	
		// Make sure email address is valid
		$validemail = $this->_check_validEmail($suggester['email']);
	
		// Prep a new math question and hash in case any form validation fails
		$suggestion['operand1'] = rand(0,10);
		$suggestion['operand2'] = rand(0,10);
		$sum = $suggestion['operand1'] + $suggestion['operand2'];
		$suggestion['key'] = $this->_generate_hash($sum,date('j'));
		
		$juser =& JFactory::getUser();
	
		if ($suggester['name'] && $suggestion['for'] && $suggestion['idea'] && $validemail) {			
			// Are the logged in?
			if ($juser->get('guest')) {
				// No - don't trust user
				// Check CAPTCHA
				$key = JRequest::getInt( 'krhash', 0 );
				$answer = JRequest::getInt( 'answer', 0 );
				$answer = $this->_generate_hash($answer,date('j'));

				if ($answer != $key) {
					$view = new JView( array('name'=>'suggestions') );
					$view->title = $this->_title;
					$view->option = $this->_option;
					$view->task = $this->_task;
					$view->user = $suggester;
					$view->suggestion = $suggestion;
					$view->verified = ($juser->get('guest')) ? 0 : 1;
					$view->setError(3);
					$view->display();
					return;
				}
			}
			
			// Get user's IP and domain
			$ip = $this->_ip_address();
			$hostname = gethostbyaddr(JRequest::getVar('REMOTE_ADDR','','server'));
			
			// Quick spam filter
			$spam = $this->_detect_spam($suggestion['idea'], $ip);
			if ($spam) {
				// Output form with error messages
				$view = new JView( array('name'=>'suggestions') );
				$view->title = $this->_title;
				$view->option = $this->_option;
				$view->task = $this->_task;
				$view->user = $suggester;
				$view->suggestion = $suggestion;
				$view->verified = ($juser->get('guest')) ? 0 : 1;
				$view->setError(1);
				$view->display();
				return;
			}
			
			// Get some email settings
			$jconfig =& JFactory::getConfig();
			
			$admin   = $jconfig->getValue('config.mailfrom');
			$subject = $jconfig->getValue('config.sitename').' '.JText::_('COM_FEEDBACK_SUGGESTIONS');
			$from    = $jconfig->getValue('config.sitename').' '.JText::_('COM_FEEDBACK_SUGGESTIONS_FORM');
			$hub     = array('email' => $suggester['email'], 'name' => $from);
			
			// Generate e-mail message
			$message  = (!$juser->get('guest')) ? JText::_('COM_FEEDBACK_VERIFIED_USER')."\r\n" : '';
			$message .= ($suggester['login']) ? JText::_('COM_FEEDBACK_USERNAME').': '. $suggester['login'] ."\r\n" : '';
			$message .= JText::_('COM_FEEDBACK_NAME').': '. $suggester['name'] ."\r\n";
			$message .= JText::_('COM_FEEDBACK_AFFILIATION').': '. $suggester['org'] ."\r\n";
			$message .= JText::_('COM_FEEDBACK_EMAIL').': '. $suggester['email'] ."\r\n";
			$message .= JText::_('COM_FEEDBACK_FOR').': '. $suggestion['for'] ."\r\n";
			$message .= JText::_('COM_FEEDBACK_IDEA').': '. $suggestion['idea'] ."\r\n";
	
			// Send e-mail
			ximport('xhubhelper');
			XHubHelper::send_email($admin, $subject, $message);
			
			// Get their browser and OS
			list( $os, $os_version, $browser, $browser_ver ) = $this->_browsercheck(JRequest::getVar('HTTP_USER_AGENT','','server'));
		
			// Create new support ticket
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'tables'.DS.'ticket.php' );
			
			$data = array();
			$data['id']        = NULL;
			$data['status']    = 0;
			$data['created']   = date( "Y-m-d H:i:s" );
			$data['login']     = $suggester['login'];
			$data['severity']  = 'normal';
			$data['owner']     = NULL;
			$data['category']  = 'Suggestion';
			$data['summary']   = $suggestion['for'];
			$data['report']    = $suggestion['idea'];
			$data['resolved']  = NULL;
			$data['email']     = $suggester['email'];
			$data['name']      = $suggester['name'];
			$data['os']        = $os .' '. $os_version;
			$data['browser']   = $browser .' '. $browser_ver;
			$data['ip']        = $ip;
			$data['hostname']  = $hostname;
			$data['uas']       = JRequest::getVar('HTTP_USER_AGENT','','server');
			$data['referrer']  = NULL;
			$data['cookies']   = (JRequest::getVar('sessioncookie','','cookie')) ? 1 : 0;
			$data['instances'] = 1;
			$data['section']   = 1;
			
			$database =& JFactory::getDBO();
			
			$row = new SupportTicket( $database );
			if (!$row->bind( $data )) {
				$this->setError( $row->getError() );
			}
			if (!$row->check()) {
				$this->setError( $row->getError() );
			}
			if (!$row->store()) {
				$this->setError( $row->getError() );
			}

			// Output Thank You message
			$view = new JView( array('name'=>'suggestions', 'layout'=>'thanks') );
			$view->title = $this->_title;
			$view->option = $this->_option;
			$view->task = $this->_task;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			$view->display();
		} else {
			// Error
			$this->setError(1);
			if ($validemail == 0) {
				$this->setError(2);
			}
		
			// Output form with error messages
			$view = new JView( array('name'=>'suggestions') );
			$view->title = $this->_title;
			$view->option = $this->_option;
			$view->task = $this->_task;
			$view->user = $suggester;
			$view->suggestion = $suggestion;
			$view->verified = ($juser->get('guest')) ? 0 : 1;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			$view->display();
		}
	}

	//-----------

	protected function sendreport() 
	{
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'tables'.DS.'attachment.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'tables'.DS.'ticket.php' );

		// Incoming
		$no_html  = JRequest::getInt( 'no_html', 0 );
		$reporter = array_map('trim', $_POST['reporter']);
		$problem  = array_map('trim', $_POST['problem']);
		$reporter = array_map(array('Hubzero_View_Helper_Html','purifyText'), $reporter);
		//$problem  = array_map(array('Hubzero_View_Helper_Html','purifyText'), $problem);
	
		// Make sure email address is valid
		$validemail = $this->_check_validEmail($reporter['email']);

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Push some styles to the template
		$this->_getStyles();
	
		// Prep a new math question and hash in case any form validation fails
		$problem['operand1'] = rand(0,10);
		$problem['operand2'] = rand(0,10);
		$sum = $problem['operand1'] + $problem['operand2'];
		$problem['key'] = $this->_generate_hash($sum,date('j'));
	
		$juser =& JFactory::getUser();

		// Check for some required fields
		if (!$reporter['name'] || !$reporter['email'] || !$validemail || !$problem['long']) {
			// Output form with error messages
			$view = new JView( array('name'=>'report') );
			$view->title = $this->_title;
			$view->option = $this->_option;
			$view->task = 'report_problems';
			$view->reporter = $reporter;
			$view->problem = $problem;
			$view->verified = ($juser->get('guest')) ? 0 : 1;
			$view->setError(2);
			$view->display();
			return;
		}
		
		// Get the user's IP
		$ip = $this->_ip_address();
		$hostname = gethostbyaddr(JRequest::getVar('REMOTE_ADDR','','server'));
			
		// Are the logged in?
		if ($juser->get('guest')) {
			// No - don't trust user
			// Check CAPTCHA
			$key = JRequest::getVar( 'krhash', 0 );
			$answer = JRequest::getInt( 'answer', 0 );
			$answer = $this->_generate_hash($answer,date('j'));
				
			// Quick spam filter
			$spam = $this->_detect_spam($problem['long'], $ip);

			if ($answer != $key || $spam) {
				if ($no_html) {
					// Output error messages (AJAX)
					$view = new JView( array('name'=>'report', 'layout'=>'error') );
					if ($this->getError()) {
						$view->setError( $this->getError() );
					}
					$view->display();
					return;
				} else {
					// Output form with error messages
					$view = new JView( array('name'=>'report') );
					$view->title = $this->_title;
					$view->option = $this->_option;
					$view->task = 'report_problems';
					$view->reporter = $reporter;
					$view->problem = $problem;
					$view->verified = ($juser->get('guest')) ? 0 : 1;
					$view->setError(3);
					$view->display();
					return;
				}
			}
		}

		// Get user's city, region and location based on ip
		$source_city    = 'unknown';
		$source_region  = 'unknown';
		$source_country = 'unknown';
		
		ximport('xgeoutils');
		$gdb =& GeoUtils::getGODBO();
		if (is_object($gdb)) {
			$gdb->setQuery( "SELECT countrySHORT, countryLONG, ipREGION, ipCITY FROM ipcitylatlong WHERE INET_ATON('$ip') BETWEEN ipFROM and ipTO" );
			$rows = $gdb->loadObjectList();
			if ($rows && count($rows) > 0) {
				$source_city    = $rows[0]->ipCITY;
				$source_region  = $rows[0]->ipREGION;
				$source_country = $rows[0]->countryLONG;
			}
		}
		
		// Cut suggestion at 70 characters
		if (!$problem['short'] && $problem['long']) {
			$problem['short'] = substr($problem['long'], 0, 70);
			if (strlen($problem['short']) >=70 ) {
				$problem['short'] .= '...';
			}
		}
		
		$tool = $this->_getTool( $problem['referer'] );
		if ($tool) {
			$group = $this->_getTicketGroup( trim($tool) );
		} else {
			$group = '';
		}
		
		// Build an array of ticket data
		$data = array();
		$data['id']        = NULL;
		$data['status']    = 0;
		$data['created']   = date( "Y-m-d H:i:s" );
		$data['login']     = $reporter['login'];
		$data['severity']  = 'normal';
		$data['owner']     = NULL;
		$data['category']  = (isset($problem['topic'])) ? $problem['topic'] : '';
		$data['summary']   = htmlentities($problem['short'], ENT_COMPAT, 'UTF-8');
		$data['report']    = htmlentities($problem['long'], ENT_COMPAT, 'UTF-8');
		$data['resolved']  = NULL;
		$data['email']     = $reporter['email'];
		$data['name']      = $reporter['name'];
		$data['os']        = $problem['os'] .' '. $problem['osver'];
		$data['browser']   = $problem['browser'] .' '. $problem['browserver'];
		$data['ip']        = $ip;
		$data['hostname']  = $hostname;
		$data['uas']       = JRequest::getVar('HTTP_USER_AGENT','','server');
		$data['referrer']  = $problem['referer'];
		$data['cookies']   = (JRequest::getVar('sessioncookie','','cookie')) ? 1 : 0;
		$data['instances'] = 1;
		$data['section']   = 1;
		$data['group']     = $group;
		
		// Initiate class and bind data to database fields
		$database =& JFactory::getDBO();
		$row = new SupportTicket( $database );
		if (!$row->bind( $data )) {
			$this->setError( $row->getError() );
		}
		// Check the data
		if (!$row->check()) {
			$this->setError( $row->getError() );
		}
		// Save the data
		if (!$row->store()) {
			$this->setError( $row->getError() );
		}
		// Retrieve the ticket ID
		if (!$row->id) {
			$row->getId();
		}
		
		$sconfig = JComponentHelper::getParams( 'com_support' );
		$attachment = $this->_uploadAttachment( $row->id, $sconfig );
		$row->report .= ($attachment) ? "\n\n".$attachment : '';
		
		// Save the data
		if (!$row->store()) {
			$this->setError( $row->getError() );
		}
		
		// Get some email settings
		$jconfig =& JFactory::getConfig();
		$admin   = $jconfig->getValue('config.mailfrom');
		$subject = $jconfig->getValue('config.sitename').' '.JText::_('COM_FEEDBACK_SUPPORT').', '.JText::sprintf('COM_FEEDBACK_TICKET_NUMBER',$row->id);
		$from    = $jconfig->getValue('config.sitename').' web-robot';
		$hub     = array('email' => $reporter['email'], 'name' => $from);
		
		// Parse comments for attachments
		$xhub =& XFactory::getHub();
		$attach = new SupportAttachment( $database );
		$attach->webpath = $xhub->getCfg('hubLongURL').$sconfig->get('webpath').DS.$row->id;
		$attach->uppath  = JPATH_ROOT.$sconfig->get('webpath').DS.$row->id;
		$attach->output  = 'email';
		
		// Generate e-mail message
		$message  = (!$juser->get('guest')) ? JText::_('COM_FEEDBACK_VERIFIED_USER')."\r\n\r\n" : '';
		$message .= ($reporter['login']) ? JText::_('COM_FEEDBACK_USERNAME').': '. $reporter['login'] ."\r\n" : '';
		$message .= JText::_('COM_FEEDBACK_NAME').': '. $reporter['name'] ."\r\n";
		$message .= JText::_('COM_FEEDBACK_AFFILIATION').': '. $reporter['org'] ."\r\n";
		$message .= JText::_('COM_FEEDBACK_EMAIL').': '. $reporter['email'] ."\r\n";
		$message .= JText::_('COM_FEEDBACK_IP_HOSTNAME').': '. $ip .' ('.$hostname.')' ."\r\n";
		$message .= JText::_('COM_FEEDBACK_REGION').': '.$source_city.', '.$source_region.', '.$source_country ."\r\n\r\n";
		$message .= JText::_('COM_FEEDBACK_OS').': '. $problem['os'] .' '. $problem['osver'] ."\r\n";
		$message .= JText::_('COM_FEEDBACK_BROWSER').': '. $problem['browser'] .' '. $problem['browserver'] ."\r\n";
		$message .= JText::_('COM_FEEDBACK_UAS').': '. JRequest::getVar('HTTP_USER_AGENT','','server') ."\r\n";
		$message .= JText::_('COM_FEEDBACK_COOKIES').': ';
		$message .= (JRequest::getVar('sessioncookie','','cookie')) ? JText::_('COM_FEEDBACK_COOKIES_ENABLED')."\r\n" : JText::_('COM_FEEDBACK_COOKIES_DISABLED')."\r\n";
		$message .= JText::_('COM_FEEDBACK_REFERRER').': '. $problem['referer'] ."\r\n";
		$message .= ($problem['tool']) ? JText::_('COM_FEEDBACK_TOOL').': '. $problem['tool'] ."\r\n\r\n" : "\r\n";
		$message .= JText::_('COM_FEEDBACK_PROBLEM_DETAILS').': '. $attach->parse(stripslashes($row->report)) ."\r\n";

		// Send e-mail
		ximport('xhubhelper');
		XHubHelper::send_email($admin, $subject, $message);

		// Output Thank You message
		$view = new JView( array('name'=>'report', 'layout'=>'thanks') );
		$view->title = $this->_title;
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->ticket = $row->id;
		$view->no_html = $no_html;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	private function _uploadAttachment( $listdir, $sconfig )
	{
		if (!$listdir) {
			$this->setError( JText::_('SUPPORT_NO_UPLOAD_DIRECTORY') );
			return '';
		}
		
		// Incoming file
		$file = JRequest::getVar( 'upload', '', 'files', 'array' );
		if (!$file['name']) {
			return '';
		}
		
		// Incoming
		$description = ''; //JRequest::getVar( 'description', '' );
		
		// Construct our file path
		$path = JPATH_ROOT.$sconfig->get('webpath').DS.$listdir;
		
		// Build the path if it doesn't exist
		if (!is_dir( $path )) {
			jimport('joomla.filesystem.folder');
			if (!JFolder::create( $path, 0777 )) {
				$this->setError( JText::_('UNABLE_TO_CREATE_UPLOAD_PATH') );
				return '';
			}
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ','_',$file['name']);
		$ext = strtolower(JFile::getExt($file['name']));
		if (!in_array($ext, array('jpg','jpeg','jpe','png','bmp','gif'))) {
			return '';
		}

		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $path.DS.$file['name'])) {
			$this->setError( JText::_('ERROR_UPLOADING') );
			return '';
		} else {
			// File was uploaded
			// Create database entry
			$description = htmlspecialchars($description);
			
			$database =& JFactory::getDBO();
			$row = new SupportAttachment( $database );
			$row->bind( array('id'=>0,'ticket'=>$listdir,'filename'=>$file['name'],'description'=>$description) );
			if (!$row->check()) {
				$this->setError( $row->getError() );
			}
			if (!$row->store()) {
				$this->setError( $row->getError() );
			}
			if (!$row->id) {
				$row->getID();
			}
			
			return '{attachment#'.$row->id.'}';
		}
	}

	//-----------

	private function _getTool( $referrer ) 
	{
		$tool = '';
		
		if (!$referrer) {
			return $tool;
		}
		
		if (substr($referrer,0,3) == '/mw') {
			$bits = explode('/', $referrer);
			if ($bits[2] == 'invoke') {
				$longbits = explode('?',$bits[3]);
				if (is_array($longbits)) {
					$tool = trim($longbits[0]);
				} else {
					$tool = trim($bits[3]);
				}
			} else if ($bits[2] == 'view') {
				$longbits = explode('=',$bits[3]);
				if (is_array($longbits)) {
					$tool = trim(end($longbits));
				} else {
					$tool = trim($bits[3]);
				}
			}
			if (strstr($tool,'_r')) {
				$version = strrchr($tool,'_r');
				$tool = str_replace($version, '', $tool);
			}
			if (strstr($tool,'_dev')) {
				$version = strrchr($tool,'_dev');
				$tool = str_replace($version, '', $tool);
			}
		} else if (substr($referrer,0,6) == '/tools' || substr($referrer,0,10) == '/resources') {
			$bits = explode('/', $referrer);
			$tool = (isset($bits[2])) ? trim($bits[2]) : '';
		} else if (substr($referrer,0,4) == 'http') {
			$bits = explode('/', $referrer);
			$tool = (isset($bits[4])) ? trim($bits[4]) : '';
		}

		return $tool;
	}
	
	//-----------

	private function _getTicketGroup($tool) 
	{
		// Do we have a tool?
		if (!$tool) {
			return '';
		}
		
		$database =& JFactory::getDBO();
		
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.resource.php');
		$resource = new ResourcesResource( $database );
		$tool = str_replace(':','-',$tool);
		$resource->loadAlias( $tool );
		
		if (!$resource || $resource->type != 7) {
			return '';
		}
			
		// Get tags on the tools
		include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'resources.tags.php' );
		$rt = new ResourcesTags($database);
		$tags = $rt->getTags( $resource->id, 0, 0, 1 );

		if (!$tags) {
			return 'app-'.$tool;
		}

		// Get tag/group associations
		include_once( JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'tags.tag.php' );
		$tt = new TagsGroup( $database );
		$tgas = $tt->getRecords();
			
		if (!$tgas) {
			return 'app-'.$tool;
		}

		// Loop through the tags and make a flat array so we can search quickly
		$ts = array();
		foreach ($tags as $tag) 
		{
			$ts[] = $tag->tag;
		}
		// Loop through the tag/group array and see if one of them is in the tags list
		foreach ($tgas as $tga) 
		{
			if (in_array($tga->tag, $ts)) {
				// We found one! So set the group
				return $tga->cn;
				break;
			}
		}
		return 'app-'.$tool;
	}

	//----------------------------------------------------------
	//  Image handling
	//----------------------------------------------------------

	protected function upload()
	{
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->setError( JText::_('COM_FEEDBACK_NOTAUTH') );
			$this->img( '', 0 );
			return;
		}
		
		// Incoming
		$id = JRequest::getInt( 'id', 0 );
		if (!$id) {
			$this->setError( JText::_('COM_FEEDBACK_NO_ID') );
			$this->img( '', $id );
			return;
		}
		
		// Incoming file
		$file = JRequest::getVar( 'upload', '', 'files', 'array' );
		if (!$file['name']) {
			$this->setError( JText::_('COM_FEEDBACK_NO_FILE') );
			$this->img( '', $id );
			return;
		}

		// Build upload path
		ximport('fileuploadutils');
		$dir  = FileUploadUtils::niceidformat( $id );
		$path = JPATH_ROOT;
		if (substr($this->config->get('uploadpath'), 0, 1) != DS) {
			$path .= DS;
		}
		if (substr($this->config->get('uploadpath'), -1, 1) == DS) {
			$path = substr($this->config->get('uploadpath'), 0, (strlen($this->config->get('uploadpath')) - 1));
		}
		$path .= $this->config->get('uploadpath').DS.$dir;
		
		if (!is_dir( $path )) {
			jimport('joomla.filesystem.folder');
			if (!JFolder::create( $path, 0777 )) {
				$this->setError( JText::_('COM_FEEDBACK_UNABLE_TO_CREATE_UPLOAD_PATH') );
				$this->img( '', $id );
				return;
			}
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ','_',$file['name']);
		
		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $path.DS.$file['name'])) {
			$this->setError( JText::_('COM_FEEDBACK_ERROR_UPLOADING') );
			$file = $curfile;
		} else {
			// Do we have an old file we're replacing?
			$curfile = JRequest::getVar( 'currentfile', '' );
			
			if ($curfile != '') {
				// Yes - remove it
				if (file_exists($path.DS.$curfile)) {
					if (!JFile::delete($path.DS.$curfile)) {
						$this->setError( JText::_('COM_FEEDBACK_UNABLE_TO_DELETE_FILE') );
						$this->img( $file['name'], $id );
						return;
					}
				}
			}

			$file = $file['name'];
		}

		// Push through to the image view
		$this->img( $file, $id );
	}

	//-----------

	protected function delete()
	{
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->setError( JText::_('COM_FEEDBACK_NOTAUTH') );
			$this->img( '', 0 );
			return;
		}
		
		// Incoming member ID
		$id = JRequest::getInt( 'id', 0 );
		if (!$id) {
			$this->setError( JText::_('COM_FEEDBACK_NO_ID') );
			$this->img( '', $id );
			return;
		}
		
		if ($juser->get('id') != $id) {
			$this->setError( JText::_('COM_FEEDBACK_NOTAUTH') );
			$this->img( '', $juser->get('id') );
			return;
		}
		
		// Incoming file
		$file = JRequest::getVar( 'file', '' );
		if (!$file) {
			$this->setError( JText::_('COM_FEEDBACK_NO_FILE') );
			$this->img( '', $id );
			return;
		}
		
		$file = basename($file);
		
		// Build the file path
		ximport('fileuploadutils');
		$dir  = FileUploadUtils::niceidformat( $id );
		$path = JPATH_ROOT;
		if (substr($this->config->get('uploadpath'), 0, 1) != DS) {
			$path .= DS;
		}
		if (substr($this->config->get('uploadpath'), -1, 1) == DS) {
			$path = substr($this->config->get('uploadpath'), 0, (strlen($this->config->get('uploadpath')) - 1));
		}
		$path .= $this->config->get('uploadpath').DS.$dir;

		if (!file_exists($path.DS.$file) or !$file) { 
			$this->setError( JText::_('COM_FEEDBACK_FILE_NOT_FOUND') ); 
		} else {
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFile::delete($path.DS.$file)) {
				$this->setError( JText::_('COM_FEEDBACK_UNABLE_TO_DELETE_FILE') );
				$this->img( $file, $id );
				return;
			}

			$file = '';
		}
	
		// Push through to the image view
		$this->img( $file, $id );
	}

	//-----------

	protected function img( $file='', $id=0 )
	{
		// Do have an ID or do we need to get one?
		if (!$id) {
			$id = JRequest::getInt( 'id', 0 );
		}
		ximport('fileuploadutils');
		$dir = FileUploadUtils::niceidformat( $id );
		
		// Do we have a file or do we need to get one?
		$file = ($file) 
			  ? $file 
			  : JRequest::getVar( 'file', '' );
			  
		// Build the directory path
		$path = $this->config->get('uploadpath').DS.$dir;

		// Output form with error messages
		$view = new JView( array('name'=>'story', 'layout'=>'picture') );
		$view->title = $this->_title;
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->webpath = $this->config->get('uploadpath');
		$view->default_picture = $this->config->get('defaultpic');
		$view->path = $dir;
		$view->file = $file;
		$view->file_path = $path;
		$view->id = $id;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//----------------------------------------------------------
	// Private functions
	//----------------------------------------------------------

	private function _getUser() 
	{
		$juser =& JFactory::getUser();
		
		$user = array();
		$user['login'] = '';
		$user['name']  = '';
		$user['org']   = '';
		$user['email'] = '';
		$user['uid']   = '';

		if (!$juser->get('guest')) {
			ximport('xprofile');
			
			$profile = new XProfile();
			$profile->load( $juser->get('id') );
			
			if (is_object($profile)) {
				$user['login'] = $profile->get('username');
				$user['name']  = $profile->get('name');
				$user['org']   = $profile->get('organization');
				$user['email'] = $profile->get('email');
				$user['uid']   = $profile->get('uidNumber');
			}
		}
		return $user;
	}
	
	//-----------

	private function _detect_spam($text, $ip)
	{
		// Spammer IPs (banned)
		$ips = $this->config->get('blacklist');
		if ($ips) {
			$bl = explode(',',$ips);
			array_map('trim',$bl);
		} else {
			$bl = array();
		}
		
		// Bad words
		$words = $this->config->get('badwords');
		if ($words) {
			$badwords = explode(',', $words);
			array_map('trim',$badwords);
		} else {
			$badwords = array();
		}

		// Build an array of patterns to check againts
		$patterns = array('/\[url=(.*?)\](.*?)\[\/url\]/s', '/\[url=(.*?)\[\/url\]/s');
		foreach ($badwords as $badword)
		{
			if (!empty($badword))
			    	$patterns[] = '/(.*?)'.trim($badword).'(.*?)/s';
		}
	
		// Set the splam flag
		$spam = false;
	
		// Check the text against bad words
		foreach ($patterns as $pattern)
		{
			preg_match_all( $pattern, $text, $matches );
			if (count($matches[0]) >=1) {
				$spam = true;
			}
		}
		
		// Check the number of links in the text
		// Very unusual to have 5 or more - usually only spammers
		if (!$spam) {
			$num = substr_count($text, 'http://');
			if ($num >= 5) { // too many links
        	    $spam = true;
			}
		}
		
		// Check the user's IP against the blacklist
		if (in_array($ip, $bl)) {
			$spam = true;
		}
		
		return $spam;
	}

	//-----------

	private function _generate_hash($input, $day)
	{
		// Add date:
		$input .= $day . date('ny');

		// Get MD5 and reverse it
		$enc = strrev(md5($input));
	
		// Get only a few chars out of the string
		$enc = substr($enc, 26, 1) . substr($enc, 10, 1) . substr($enc, 23, 1) . substr($enc, 3, 1) . substr($enc, 19, 1);
	
		return $enc;
	}

	//-----------

	private function _check_validLogin($login) 
	{
		if (eregi("^[_0-9a-zA-Z]+$", $login)) {
			return(1);
		} else {
			return(0);
		}
	}

	//-----------

	private function _check_validEmail($email) 
	{
		if (eregi("^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$", $email)) {
			return(1);
		} else {
			return(0);
		}
	}

	//-----------

	private function _server($index = '')
	{		
		if (!isset($_SERVER[$index])) {
			return FALSE;
		}
		
		return TRUE;
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
			 $ip_address = JRequest::getVar('HTTP_CLIENT_IP','','server');
		} elseif ($this->_server('REMOTE_ADDR')) {
			 $ip_address = JRequest::getVar('REMOTE_ADDR','','server');
		} elseif ($this->_server('HTTP_CLIENT_IP')) {
			 $ip_address = JRequest::getVar('HTTP_CLIENT_IP','','server');
		} elseif ($this->_server('HTTP_X_FORWARDED_FOR')) {
			 $ip_address = JRequest::getVar('HTTP_X_FORWARDED_FOR','','server');
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
}
?>