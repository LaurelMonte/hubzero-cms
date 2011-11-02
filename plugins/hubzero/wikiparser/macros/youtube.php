<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'YoutubeMacro'
 * 
 * Long description (if any) ...
 */
class YoutubeMacro extends WikiMacro
{

	/**
	 * Short description for 'description'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     mixed Return description (if any) ...
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = "Embeds a Youtube Video into the Page";
		$txt['html'] = '<p>Embeds a Youtube Video into the Page. Accepts either full Youtube video URL or just Youtube Video ID (highlighted below).</p>
						<p><strong>Youtube URL:</strong> http://www.youtube.com/watch?v=<span class="highlight">FgfGOEpZEOw</span></p>
						<p>Examples:</p>
						<ul>
							<li><code>[[Youtube(FgfGOEpZEOw)]]</code></li>
							<li><code>[[Youtube(http://www.youtube.com/watch?v=FgfGOEpZEOw)]]</code></li>
						</ul>
						<p>Displays:</p>
						<iframe src="http://youtube.com/embed/FgfGOEpZEOw" width="640px" height="390px" border="0"></iframe>';

		return $txt['html'];
	}

	/**
	 * Short description for 'render'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     string Return description (if any) ...
	 */
	public function render()
	{
		//get the args passed in
		$content = $this->args;

		//declare the partial youtube embed url
		$youtube_url = "https://www.youtube.com/embed/";

		// args will be null if the macro is called without parenthesis.
		if (!$content) {
			return '';
		}

		//check is user entered full youtube url or just Video Id
		if (strstr($content,'http')) {
			//split the string into two parts 
			//uri and query string
			$full_url_parts = explode("?",$content);

			//split apart any key=>value pairs in query string
			$query_string_parts = explode("%26%2338%3B",urlencode($full_url_parts[1]));

			//foreach query string parts
			//explode at equals sign
			//check to see if v is the first part and if it is set the second part to the video id
			foreach($query_string_parts as $qsp) {
				$pairs_parts = explode("%3D",$qsp);
				if($pairs_parts[0] == 'v') {
					$video_id = $pairs_parts[1];
					break;
				}
			}
		} else {
			$video_id = $content;
		}

		//append to the youtube url
		$youtube_url .= $video_id;

		//return the emdeded youtube video
		return "<iframe src=\"{$youtube_url}\" width=\"640\" height=\"380\"></iframe>";
	}
}
?>