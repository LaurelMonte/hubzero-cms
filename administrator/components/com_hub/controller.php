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

ximport('Hubzero_Controller');

class HubController extends Hubzero_Controller
{
	public function execute()
	{
		$this->_task = Jrequest::getVar( 'task', '' );
		
		switch ($this->_task) 
		{
			case 'save':       $this->save();      break;
			case 'remove':     $this->delete();    break;
			case 'new':        $this->edit();      break;
			case 'add':        $this->edit();      break;
			case 'edit':       $this->edit();      break;
			case 'cancel':     $this->cancel();    break;
			case 'misc':       $this->misc();      break;
			
			case 'components':   $this->components();          break;
			case 'savecom':      $this->savecom();             break;
			
			case 'registration': $this->settings();            break;
			case 'savereg':      $this->saveReg(); break;
			case 'databases':    $this->settings();            break;
			case 'savedb':       $this->_save('databases');    break;
			case 'site':         $this->settings();            break;
			case 'savesite':     $this->_save('site');         break;
			
			case 'addorg': $this->addorg(); break;
			case 'editorg': $this->editorg(); break;
			case 'removeorg': $this->removeorg(); break;
			case 'saveorg': $this->saveorg(); break;
			case 'cancelorg': $this->cancelorg(); break;
			case 'orgs': $this->orgs(); break;
			
			default: $this->settings(); break;
		}
		
		// Load the component
		/*$component = new JTableComponent( $this->database );
		$component->loadByOption( $this->_option );
		
		$this->database->setQuery( "SELECT COUNT(*) FROM #__components WHERE `option`='".$component->option."' AND parent=".$component->id );
		$menuitems = $this->database->loadResult();
		if (!$menuitems) {
			$menusite = new JTableComponent( $this->database );
			$menusite->name = 'Site';
			$menusite->parent = $component->id;
			$menusite->admin_menu_link = 'option='.$this->_option.'&task=site';
			$menusite->admin_menu_alt = 'Site';
			$menusite->option = $this->_option;
			$menusite->ordering = 1;
			$menusite->store();
			
			$menureg = new JTableComponent( $this->database );
			$menureg->name = 'Registration';
			$menureg->parent = $component->id;
			$menureg->admin_menu_link = 'option='.$this->_option.'&task=registration';
			$menureg->admin_menu_alt = 'Registration';
			$menureg->option = $this->_option;
			$menureg->ordering = 2;
			$menureg->store();
			
			$menudat = new JTableComponent( $this->database );
			$menudat->name = 'Databases';
			$menudat->parent = $component->id;
			$menudat->admin_menu_link = 'option='.$this->_option.'&task=databases';
			$menudat->admin_menu_alt = 'Databases';
			$menudat->option = $this->_option;
			$menudat->ordering = 3;
			$menudat->store();
			
			$menumis = new JTableComponent( $this->database );
			$menumis->name = 'Misc. Settings';
			$menumis->parent = $component->id;
			$menumis->admin_menu_link = 'option='.$this->_option.'&task=misc';
			$menumis->admin_menu_alt = 'Misc. Settings';
			$menumis->option = $this->_option;
			$menumis->ordering = 4;
			$menumis->store();
			
			$menucom = new JTableComponent( $this->database );
			$menucom->name = 'Components';
			$menucom->parent = $component->id;
			$menucom->admin_menu_link = 'option='.$this->_option.'&task=components';
			$menucom->admin_menu_alt = 'Components';
			$menucom->option = $this->_option;
			$menucom->ordering = 5;
			$menucom->store();
			
			$menucom = new JTableComponent( $this->database );
			$menucom->name = 'Organizations';
			$menucom->parent = $component->id;
			$menucom->admin_menu_link = 'option='.$this->_option.'&task=orgs';
			$menucom->admin_menu_alt = 'Organizations';
			$menucom->option = $this->_option;
			$menucom->ordering = 6;
			$menucom->store();
		}*/
	}
	
	//----------------------------------------------------------
	// Config functions
	//----------------------------------------------------------
	
	protected function &loadConfiguration()
	{
		$arr = array();
		
		if (!is_readable(JPATH_CONFIGURATION.DS.'hubconfiguration.php')) {
			return $arr;
		}
		
		require_once(JPATH_CONFIGURATION.DS.'hubconfiguration.php');
		
		$object = new HubConfig();
		
		if (is_object( $object )) {
			foreach (get_object_vars($object) as $k => $v) 
			{
				if (substr($k, 0,1) != '_' || $k == '_name') {
					$arr[$k] = $v;
				}
			}
		}
		
		return $arr;
	}
	
	//-----------
	
	protected function saveConfiguration(&$arr)
	{
		$handle = fopen(JPATH_CONFIGURATION.DS.'hubconfiguration.php', "wb");
		fwrite($handle, "<?php\nclass HubConfig {\n");
		foreach ($arr as $key => $value ) 
		{
			if (strstr($value, "'")) {
				$value = addslashes($value);
			}
			fwrite($handle, '    var $' . $key . " = '" . $value . "';\n");
		}
		fwrite($handle, "}\n?>\n");
		fclose($handle);
	}

	//-----------
	
	protected function settings() 
	{
		$arr =& $this->loadConfiguration();
		
		switch ($this->_task) 
		{
			case 'registration': 
				$a = array();
				$component =& JComponentHelper::getComponent( $this->_option );
				$params = trim($component->params);
				if ($params) {
					$params = explode("\n", $params);
					foreach ($params as $p) 
					{
						$b = explode("=",$p);
						$a[$b[0]] = trim(end($b));
					}
				} else {
					$a = array();
				    $a['registrationUsername'] = 'RRUU';
				    $a['registrationPassword'] = 'RRUU';
				    $a['registrationConfirmPassword'] = 'RRUU';
				    $a['registrationFullname'] = 'RRUU';
				    $a['registrationEmail'] = 'RRUU';
				    $a['registrationConfirmEmail'] = 'RRUU';
				    $a['registrationURL'] = 'OHHO';
				    $a['registrationPhone'] = 'OHHO';
				    $a['registrationEmployment'] = 'RORO';
				    $a['registrationOrganization'] = 'OOOO';
				    $a['registrationCitizenship'] = 'RHRH';
				    $a['registrationResidency'] = 'RHRH';
				    $a['registrationSex'] = 'RHRH';
				    $a['registrationDisability'] = 'RHRH';
				    $a['registrationHispanic'] = 'RHRH';
				    $a['registrationRace'] = 'OHHH';
				    $a['registrationInterests'] = 'OOOO';
				    $a['registrationReason'] = 'OOOO';
				    $a['registrationOptIn'] = 'OOUU';
				    $a['registrationTOU'] = 'RHRH';
				}
				
				$view = new JView( array('name'=>'registration') );
				$view->a = $a;
			break;
			case 'databases': 
				$view = new JView( array('name'=>'databases') );
			break;
			case 'site':
			default: 
				$view = new JView( array('name'=>'site') );
			break;
		}

		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->arr = $arr;
		
		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}
	
	//-----------
	
	protected function _save( $task='' ) 
	{
		if ($task == 'registration') {
			$this->saveReg();
			return;
		}
		
		$settings = JRequest::getVar( 'settings', array(), 'post' );
		
		if (!is_array($settings) || empty($settings)) {
			$this->_redirect = 'index.php?option='.$this->_option.'&task='.$task;
			return;
		}
		
		$arr =& $this->loadConfiguration();
		
		foreach ($settings as $name=>$value) 
		{
			if ($task == 'registration') {
				$r = $value['create'].$value['proxy'].$value['update'].$value['edit'];

				$arr['registration'.$name] = $r;
			} else {
				$arr[$name] = $value;
			}
		}
		
		$this->saveConfiguration($arr);
		
		$this->_redirect = 'index.php?option='.$this->_option.'&task='.$task;
		$this->_message = JText::_('Configuration saved');
	}
	
	//-----------
	
	protected function saveReg() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$settings = JRequest::getVar( 'settings', array(), 'post' );

		if (!is_array($settings) || empty($settings)) {
			$this->_redirect = 'index.php?option='.$this->_option.'&task=registration';
			return;
		}

		$arr = array();

		$database =& JFactory::getDBO();
		
		$component = new JTableComponent( $database );
		$component->loadByOption( $this->_option );
		//$component->params = $params;
		$params = trim($component->params);
		if ($params) {
			$params = explode("\n", $params);
			foreach ($params as $p) 
			{
				$b = explode("=",$p);
				$arr[$b[0]] = trim(end($b));
			}
		}
		foreach ($settings as $name=>$value) 
		{
			$r = $value['create'].$value['proxy'].$value['update'].$value['edit'];

			//$arr['registration'.$name] = $r;
			$arr['registration'.trim($name)] = trim($r);
		}
		$a = array();
		foreach ($arr as $k=>$v) 
		{
			$a[] = $k.'='.$v;
		}
		$component->params = implode("\n",$a);
		$component->store();
		
		$this->_redirect = 'index.php?option='.$this->_option.'&task=registration';
		$this->_message = JText::_('Configuration saved');
	}
	
	//-----------

	protected function misc()
	{
		$view = new JView( array('name'=>'misc') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		$f = array('hubShortName','hubShortURL','hubLongURL','hubSupportEmail','hubMonitorEmail','hubHomeDir');
		
		$arr =& $this->loadConfiguration();
		$view->rows = array();
		foreach ($arr as $field => $value) 
		{
			if ((substr($field, 0, strlen('registration')) != 'registration')
			 && (substr($field, 0, strlen('hubLDAP')) != 'hubLDAP')
			 && (substr($field, 0, strlen('forge')) != 'forge')
			 && (substr($field, 0, strlen('mwDB')) != 'mwDB')
			 && (substr($field, 0, strlen('ipDB')) != 'ipDB')
			 && (substr($field, 0, strlen('hubFocusArea')) != 'hubFocusArea')
			 && (substr($field, 0, strlen('hubLoginReturn')) != 'hubLoginReturn')
			 && !in_array($field,$f)) {
				$view->rows[$field] = $value;
			}
		}
		
		// Get Joomla configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();
		
		// Get paging variables
		$limit = $app->getUserStateFromRequest($this->_option.'.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$start = JRequest::getInt('limitstart', 0);
		
		$total = count($view->rows);

		// initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $total, $start, $limit );
		
		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	//-----------

	protected function edit()
	{
		$arr =& $this->loadConfiguration();
		
		$view = new JView( array('name'=>'configuration') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		
		$name = JRequest::getVar( 'name', 0 );

		if (empty($name)) {
			$ids = JRequest::getVar( 'id', array(0) );
			
			if (is_array($ids)) {
				foreach ($ids as $id) 
				{
					if (array_key_exists($id,$arr)) {
						$name = $id;
						break;
					}
				}
			}
		}
		
		if (empty($name)) {
			$view->name = null;
			$view->value = null;
		} else {
			if (!array_key_exists($name, $arr)) {
				$arr[$name] = null;
			}
			
			$view->name = $name;
			$view->value = $arr[$name];
		}
		
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

	protected function save( $redirect=1 )
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$arr =& $this->loadConfiguration();
		
		$name = JRequest::getVar( 'editname', 0, 'post' );
		
		$editsave = !empty($name);
		
		if (!$editsave) {
			$name = JRequest::getVar( 'name', 0, 'post' );
		}
		$value = JRequest::getVar( 'value', 0, 'post' );

        if (!$editsave && array_key_exists($name, $arr)) {
            $this->_redirect = 'index.php?option='.$this->_option.'&task=misc';
            $this->_message = JText::_('Variable already exists');
		} else {
			$arr[$name] = $value;
			
			$this->saveConfiguration($arr);
			
			if ($redirect) {
				$this->_redirect = 'index.php?option='.$this->_option.'&task=misc';
				$this->_message = JText::_('Configuration variable saved');
			}
		}
	}

	//-----------

	protected function delete()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$modified = false;

		$arr =& $this->loadConfiguration();

		$ids = JRequest::getVar( 'id', array(0) );

		if (is_array($ids)) {
			foreach ($ids as $id)
			{
				if (array_key_exists($id,$arr)) {
					unset( $arr[$id] );
					$modified = true;
				}
			}
			
			if ($modified) {
				$this->saveConfiguration($arr);
			}
        }

		$this->_redirect = 'index.php?option='.$this->_option.'&task=misc';
		$this->_message = JText::_('Configuration variable deleted');
	}
	
	//-----------
	
	protected function components() 
	{
		// Get the list of components
		$arr =& $this->loadConfiguration();
		
		$components = (isset($arr['hubComponentList'])) ? $arr['hubComponentList'] : 'com_answers,com_blog,com_citations,com_contribtool,com_events,com_feedback,com_groups,com_meetings,com_members,com_resources,com_support,com_tags,com_tools,com_userpoints,com_wishlist';
		$components = explode(',',$components);
		$components = array_map('trim',$components);

		sort($components);
		
		$view = new JView( array('name'=>'components') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->components = $components;
		
		// Get the active component
		$com = JRequest::getVar( 'component', '' );
		if (!$com) {
			$com = $components[0];
		}
		
		// Load the component
		$view->component = new JTableComponent( $this->database );
		$view->component->loadByOption( $com );
		
		$view->msg = $this->_message;
		
		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}
	
	//-----------
	
	protected function savecom()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		// Incoming component ID
		$id = JRequest::getInt( 'id', 0, 'post' );
		
		// Load the component
		$component = new JTableComponent( $this->database );
		$component->load( $id );
		
		// Incoming parameters
		$params = JRequest::getVar( 'params', array(), 'post' );
		if (is_array( $params )) {
			$txt = array();
			foreach ( $params as $k=>$v) 
			{
				$txt[] = "$k=$v";
			}
			
			$component->params = implode( "\n", $txt );
			
			// Save the changes
			if (!$component->store()) {
				$this->setError( $component->getError() );
			}
			
			$this->_message = JText::_('Configuration successfully saved.');
		}
		
		// Push through to the components view
		$this->components();
	}
	
	//----------------------------------------------------------
	//  Organizations
	//----------------------------------------------------------
	
	protected function orgs()
	{
		$view = new JView( array('name'=>'organizations') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();
		
		// Get filters
		$view->filters = array();
		$view->filters['search'] = urldecode($app->getUserStateFromRequest($this->_option.'.orgsearch', 'search', ''));
		$view->filters['show']   = '';
		
		// Get paging variables
		$view->filters['limit'] = $app->getUserStateFromRequest($this->_option.'.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start'] = JRequest::getInt('limitstart', 0);

		$obj = new XOrganization( $this->database );

		// Get a record count
		$view->total = $obj->getCount( $view->filters );
		
		// Get records
		$view->rows = $obj->getRecords( $view->filters );

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

	protected function addorg()
	{
		$this->editorg();
	}

	//-----------

	protected function editorg() 
	{
		$view = new JView( array('name'=>'organization') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		
		// Incoming
		$ids = JRequest::getVar( 'id', array() );

		// Get the single ID we're working with
		if (is_array($ids)) {
			$id = (!empty($ids)) ? $ids[0] : 0;
		} else {
			$id = 0;
		}
		
		// Initiate database class and load info
		$view->org = new XOrganization( $this->database );
		$view->org->load( $id );
		
		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}
	
	//-----------
	
	protected function saveorg() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		// Load the tag object and bind the incoming data to it
		$row = new XOrganization( $this->database );
		if (!$row->bind( $_POST )) {
			JError::raiseError( 500, $row->getError() );
			return;
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
	
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=orgs';
		$this->_message = JText::_( 'HUB_ORG_SAVED' );
	}
	
	//-----------

	protected function removeorg() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		// Incoming
		$ids = JRequest::getVar( 'id', array() );

		// Get the single ID we're working with
		if (!is_array($ids)) {
			$ids = array();
		}

		// Do we have any IDs?
		if (!empty($ids)) {
			$org = new XOrganization( $this->database );
			
			// Loop through each ID and delete the necessary items
			foreach ($ids as $id) 
			{
				// Remove the organization
				$org->delete( $id );
			}
		}
		
		// Output messsage and redirect
		$this->_redirect = 'index.php?option='.$this->_option.'&task=orgs';
		$this->_message = JText::_('HUB_ORG_REMOVED');
	}
	
	//-----------

	protected function cancelorg()
	{
		$this->_redirect = 'index.php?option='.$this->_option.'&task=orgs';
	}
}
