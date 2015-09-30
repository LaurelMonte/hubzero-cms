<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Developer\Models\Api;

use Hubzero\Base\Model;
use Lang;
use Date;

require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'api' . DS . 'refreshtoken.php';

/**
 * Refresh token model
 */
class RefreshToken extends Model
{
	/**
	 * Table name
	 *
	 * @var  string
	 */
	protected $_tbl_name = '\\Components\\Developer\\Tables\\Api\\RefreshToken';

	/**
	 * Return Instance of application for token
	 * 
	 * @return  object
	 */
	public function application()
	{
		return new Application($this->get('application_id'));
	}

	/**
	 * Load code details by code
	 * 
	 * @return  void
	 */
	public function loadByToken($refreshToken)
	{
		$token = $this->_tbl->find(array(
			'refresh_token' => $refreshToken,
			'limit'         => 1
		));

		return (!empty($token)) ? new self($token[0]) : null;
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $as  What format to return
	 * @return  string
	 */
	public function created($as='')
	{
		switch (strtolower($as))
		{
			case 'date':
				return Date::of($this->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return Date::of($this->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
			break;

			case 'relative':
				return Date::of($this->get('created'))->relative();
			break;

			default:
				if ($as)
				{
					return Date::of($this->get('created'))->toLocal($as);
				}
				return $this->get('created');
			break;
		}
	}

	/** 
	 * Expire token
	 * 
	 * @return  void
	 */
	public function expire()
	{
		$this->set('state', 2);
		$this->set('expires', Date::of('now')->toSql());
		if (!$this->store())
		{
			return false;
		}
		return true;
	}
}