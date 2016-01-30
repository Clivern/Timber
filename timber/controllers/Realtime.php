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

namespace Timber\Controllers;

/**
 * Realtime Controller
 *
 * @since 1.0
 */
class Realtime {

	/**
	 * Current used services
	 *
	 * @since 1.0
	 * @access private
	 * @var object
	 */
	private $services;

	/**
	 * Instance of timber app
	 *
	 * @since 1.0
	 * @access private
	 * @var object $this->timber
	 */
	private $timber;

	/**
	 * Holds an instance of this class
	 *
	 * @since 1.0
	 * @access private
	 * @var object self::$instance
	 */
	private static $instance;

	/**
	 * Create instance of this class or return existing instance
	 *
	 * @since 1.0
	 * @access public
	 * @return object an instance of this class
	 */
	public static function instance()
	{
		if ( !isset(self::$instance) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Set class dependencies
	 *
	 * @since 1.0
	 * @access public
	 * @param object $timber
	 * @return object
	 */
	public function setDepen($timber)
	{
		$this->timber = $timber;
		$this->timber->filter->setDepen($timber)->config();
		$this->services = new \Timber\Services\Base($this->timber);
		return $this;
	}

	/**
	 *  Run common tasks before requests
	 *
	 * @since 1.0
	 * @access public
	 */
	public function requestFilters()
	{
		$this->services->Common->ajaxCheck();
		$this->timber->filter->issueDetect();
		$this->timber->filter->configLibs();
	}

	/**
	 * Process requests and respond
	 *
	 * @since 1.0
	 * @access public
	 * @param string $form
	 * @return string
	 */
	public function requests($form = '')
	{
		if( !in_array($form, array('members_list', 'projects_list')) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		return $this->timber->render( 'realtime', $this->getData($form) );
	}

	/**
	 * Bind and get data
	 *
	 * @since 1.0
	 * @access private
	 * @param string $form
	 * @return array
	 */
	private function getData($form = 'members_list')
	{
		$data = array();

		if( 'members_list' == $form ){

			$data = array_merge($data,
				$this->services->MembersData->currentUserData(),
				$this->services->MembersData->listData()
			);

		}elseif( 'projects_list' == $form ){

			$data = array_merge($data,
				$this->services->ProjectsData->currentUserData(),
				$this->services->ProjectsData->listData()
			);

		}

		return $data;
	}

}