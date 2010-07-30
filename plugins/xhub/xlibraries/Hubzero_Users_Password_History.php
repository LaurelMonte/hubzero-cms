<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2010 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GPLv3
 *
 * Copyright 2010 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 3 as 
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Hubzero_Users_Password_History
{
    private $user_id;

    private function logDebug($msg)
    {
        $xlog = &XFactory::getLogger();
        $xlog->logDebug($msg);
    }

    public function getInstance($instance)
    {
        $db = &JFactory::getDBO();

        if (empty($db))
        {
            return false;
        }

		$hzph = new Hubzero_Users_Password_History();

		if (is_numeric($instance) && $instance > 0)
			$hzph->user_id = $instance;
		else
		{
			$query = "SELECT id FROM #__users WHERE username=" .
				$db->Quote($instance) . ";";
			$db->setQuery($query);
			$result = $db->loadResult();
			if (is_numeric($result) && $result > 0)
				$hzph->user_id = $result;
		}

		if (empty($hzph->user_id))
			return false;

		return $hzph;
    }

    public function add($passhash = null, $invalidated = null)
    {
        $db = &JFactory::getDBO();

        if (empty($db))
        {
            return false;
        }

		if (empty($passhash))
			$passhash = null;

		if (empty($invalidated))
			$invalidated = "NOW()";
		else
			$invalidated = $db->Quote($invalidated);

		$user_id = $this->user_id;

		$query = "INSERT INTO #__users_password_history(user_id," .
			"passhash,invalidated)" . 
			" VALUES ( " .
			$db->Quote($user_id) . "," . 
			$db->Quote($passhash) . "," .
			$invalidated . 
			");"; 

		$db->setQuery($query);

        $result = $db->query();

        if ($result !== false || $db->getErrorNum() == 1062)
        {
                return true;
        }

        return false;
    }

    public function exists($passhash = null, $since =  null)
    {
        $db = JFactory::getDBO();

        if (empty($db))
        {
            return false;
        }

        $query = "SELECT 1 FROM #__users_password_history WHERE " .
			"user_id=" . $db->Quote($this->user_id) . " AND " .
			"passhash=" . $db->Quote($passhash);

		if (!empty($since))
			$query .= " AND invalidated >= " . $db->Quote($since);

		$query .= ";";

		$db->setQuery($query);

		$result = $db->loadResult();

		if ($result == '1')
			return true;

		return false;
    }

    public function remove($passhash, $timestamp)
    {
        if ($this->user_id <= 0)
        {
            return false;
        }

        $db = JFactory::getDBO();

        if (empty($db))
        {
            return false;
        }

        $db->setQuery("DELETE FROM #__users_password_history WHERE user_id= " . 
			$db->Quote($this->user_id) . " AND passhash = " .
			$db->Quote($passhash) . " AND invalidated = " .
			$db->Quote($timestamp) . ";");

        if (!$db->query())
        {
            return false;
        }

        return true;
    }

	public function addPassword($passhash, $user = null)
	{
		$hzuph = self::getInstance($user);

		$hzuph->add($passhash);

		return true;
	}
}

