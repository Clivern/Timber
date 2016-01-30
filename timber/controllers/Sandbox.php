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
 * Sandbox Controller
 *
 * @since 1.0
 */
class Sandbox {

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
	}

	/**
	 * Render
	 *
	 * @since 1.0
	 * @access public
	 */
	public function render($plugin)
	{
		if( (empty($plugin)) || !(in_array($plugin, $this->timber->plugins->getActivePlugins())) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}
		$this->timber->applyHook('timber.render_filters', $plugin, $this->timber, $this->services);
		return $this->timber->render('sandbox', $this->getData($plugin));
	}

	/**
	 * Bind and get data
	 *
	 * @since 1.0
	 * @access private
	 * @return array
	 */
	private function getData($plugin)
	{
		$data = array(
			'plugin' => $plugin
		);

		$data = array_merge($data,
			$this->services->DashboardData->currentUserData(),
			$this->services->Common->subPageName( ucfirst($plugin) . " | " ),
			$this->services->Common->runtimeScripts( 'dashboard' )
		);

		return $data;
	}
}