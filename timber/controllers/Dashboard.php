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
 * Dashboard Controller
 *
 * @since 1.0
 */
class Dashboard {

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
	 * Run common tasks before rendering
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function renderFilters()
	{
		$this->timber->filter->issueDetect();
		$this->timber->filter->configLibs();
		$this->timber->security->cookieCheck();
		$this->services->Common->renderFilter(array('client', 'staff', 'admin'), '/admin/dashboard');
	}

	/**
	 * Render
	 *
	 * @since 1.0
	 * @access public
	 */
	public function render()
	{
		return $this->timber->render( 'dashboard', $this->getData() );
	}

	/**
	 *  Run common tasks before requests
	 *
	 * @since 1.0
	 * @access public
	 */
	public function requestFilters()
	{
		# $this->services->Common->ajaxCheck();
		# $this->timber->filter->issueDetect();
		# $this->timber->filter->configLibs();
	}

	/**
	 * Process requests and respond
	 *
	 * @since 1.0
	 * @access public
	 * @return string
	 */
	public function requests()
	{
		# $this->timber->bench->start();

		# $this->services->DashboardRequests->setRequest($form);
		# $this->services->DashboardRequests->processRequest(
		# 	array('client', 'staff', 'admin'),
		# 	array('real_client', 'real_staff', 'real_admin'),
		# 	$form,
		# 	$this->services->DashboardRequests,
		# 	array(
		# 		'request' => 'requestMethod',
		# 	)
		# );
		# $this->services->DashboardRequests->getResponse();

		# $this->timber->bench->end();
		# $this->timber->bench->log();
	}

	/**
	 * Bind and get data
	 *
	 * @since 1.0
	 * @access private
	 * @return array
	 */
	private function getData()
	{
		$data = array();

		$data = array_merge($data,
			$this->services->DashboardData->getOverallStatistics(),
			$this->services->DashboardData->currentUserData(),
			$this->services->Common->subPageName( $this->timber->translator->trans('Dashboard') . " | " ),
			$this->services->Common->runtimeScripts( 'dashboard' )
		);

		return $data;
	}
}