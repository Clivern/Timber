<?php
/**
 * Timber - Ultimate Freelancer Platform
 *
 * @author      Clivern <support@clivern.com>
 * @copyright   2015 Clivern
 * @link        http://clivern.com
 * @license     http://codecanyon.com/license
 * @version     1.0
 * @package     Timber
 */

namespace Timber\Services;

/**
 * Realtime Requests Services
 *
 * @since 1.0
 */
class RealtimeRequests extends \Timber\Services\Base {

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 * @access public
	 * @param object $timber
	 */
	public function __construct($timber)
	{
		parent::__construct($timber);
	}

	public function getMembersList(){}
	public function getProjectsList(){}
}