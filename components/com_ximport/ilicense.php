<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2008-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2008-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
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

function _comparelicenses()
{
               $xhub = &XFactory::getHub();
               $conn = &XFactory::getPLDC();
			$db   = &JFactory::getDBO();

               $hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

               $dn = 'ou=licenses,' . $hubLDAPBaseDN;
               $filter = '(&(objectclass=*)(hasSubordinates=FALSE))';

               $sr = @ldap_search($conn, $dn, $filter, array("*","+")); //, $attributes, 0, 0, 0);

               if ($sr === false)
                    return false;

               $count = @ldap_count_entries($conn, $sr);

               if ($count === false)
                    return false;

               $entry = @ldap_first_entry($conn, $sr);

			echo "<table>";

               do
               {	
                    $attributes = ldap_get_attributes($conn, $entry);
				$rowhtml = '';
					$showrow = false;

				for($i = 0; $i < $attributes['count']; $i++)
				{
					$key = $attributes[$i];
					$value = $attributes[$key];
	
					for($j = 0; $j < $value['count']; $j++)
					{
						if (in_array($key,array('objectClass','structuralObjectClass','entryUUID','entryCSN','creatorsName','modifiersName','entryDN','subschemaSubentry','hasSubordinates')))
							continue; // don't care about these
					
						//if (in_array($key,array('member')))
						//	continue; // don't care about these for the moment

						$query = "SELECT * FROM #__licenses WHERE alias=" . $db->Quote( $attributes['license'][0] );
						$db->setQuery($query);
						$result = $db->loadObject();

						if (!is_object($result))
						{
							echo "<tr><td>Unable to load database record for " . $attributes['license'][0] . "</td></tr>";
						}
						else if ($key == 'license')
						{
							// nothing to do for this
						}
						else if ($key == 'member')
						{
							$license_id = $result->id;
                                   $myvalue = $value[$j];
							$result = preg_match('/^tool=([^,.]*),/', $myvalue, $matches);
							$showrow = true;
							$rowhtml = "<tr>";
							if ($result)
							{
								$alias = $matches[1];

								$rowhtml = "<td>$license_id</td><td>$alias</td>";

								$query = "SELECT * FROM #__tool_version WHERE instance=" . $db->Quote( $alias );
								$db->setQuery($query);
								$result = $db->loadObject();
							}

							if (!is_object($result))
							{
								$rowhtml .=  "<td>Unable to load database record for tool $alias</td>";
							}
							else
							{
								$tool_id = $result->id;
								$query = "SELECT * FROM #__licenses_tools WHERE license_id=" . $db->Quote($license_id) . " AND tool_id = " . $db->Quote($result->id);

								$db->setQuery($query);
								$result = $db->loadObject();
								
								if (!is_object($result))
								{
									$query = "INSERT INTO #__licenses_tools (license_id,tool_id) VALUES (" . $db->Quote($license_id) . "," . $db->Quote($tool_id) . ");";
									$rowhtml .= "<td>$query</td>";
									$result = $db->execute($query);

									if ($result)
										$rowhtml .=  "<td>FIXED</td>";
									else
										$rowhtml .= "<td>FIX FAILED</td>";
								}
								else
								{
									$showrow = false;
									$rowhtml .= "<td>Already exists</td>";
								}
								
							}
							$rowhtml .= "</tr>";
						}
						else if ($key == 'createTimestamp')
                              {
                                   $ddate = $result->created;
                                   $myvalue = $value[$j];
                                   $ldate = strftime("%F %T",strtotime($myvalue));
                                   $dts = strtotime($ddate);
                                   $lts = strtotime($ldate);


                                   if (($ddate == "0000-00-00 00:00:00") || ($lts < $dts))
                                   {
                                   	$showrow = true;
                                   	$rowhtml .= "<tr><td>" . $attributes['license'][0] . "</td>";
                                   	$rowhtml .= "<td>$key</td><td>DD:" . $ddate . "</td>" . "<td>LV:" . $value[$j] . "</td>";
                                   	$rowhtml .= "<td>DD:" . $ddate . "</td>" . "<td>LD:" . $ldate . "</td>";
                                   	$rowhtml .= "<td>DTS:" . $dts . "</td>" . "<td>LTS:" . $lts . "</td>";
                                   	//$rowhtml .= "<td>LDAP CREATED EARLIER!</td></tr>";

								$query = "UPDATE #__licenses SET created=" . $db->Quote($ldate) . " WHERE alias=" . $db->Quote( $attributes['license'][0] );
								$result = $db->execute($query);
                                   	
								if ($result)
									$rowhtml .= "<td>FIXED</td></tr>";
								else
									$rowhtml .= "<td>FIX FAILED</td></tr>";
                                   }

                              }
						else if ($key == 'modifyTimestamp')
                              {
                                   $ddate = $result->modified;
                                   $myvalue = $value[$j];
                                   $ldate = strftime("%F %T",strtotime($myvalue));
                                   $dts = strtotime($ddate);
                                   $lts = strtotime($ldate);


                                   if (($ddate == "0000-00-00 00:00:00") || ($lts > $dts))
                                   {
                                   	$showrow = true;
                                   	$rowhtml .= "<tr><td>" . $attributes['license'][0] . "</td>";
                                   	$rowhtml .= "<td>$key</td><td>DD:" . $ddate . "</td>" . "<td>LV:" . $value[$j] . "</td>";
                                   	$rowhtml .= "<td>DD:" . $ddate . "</td>" . "<td>LD:" . $ldate . "</td>";
                                   	$rowhtml .= "<td>DTS:" . $dts . "</td>" . "<td>LTS:" . $lts . "</td>";
                                   	//$rowhtml .= "<td>LDAP CREATED EARLIER!</td></tr>";

								$query = "UPDATE #__licenses SET modified=" . $db->Quote($ldate) . " WHERE alias=" . $db->Quote( $attributes['license'][0] );
								$result = $db->execute($query);
                                   	
								if ($result)
									$rowhtml .= "<td>FIXED</td></tr>";
								else
									$rowhtml .= "<td>FIX FAILED</td></tr>";
                                   }

                              }
						else
							echo "$key: " . $value[$j] . "<br>";

						if ($showrow) echo $rowhtml;
					}

				}

                    $entry = @ldap_next_entry($conn, $entry);
               }
               while($entry !== false);

			echo "</table>";
}

?>
