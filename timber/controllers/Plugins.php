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
 * Plugins Controller
 *
 * @since 1.0
 */
class Plugins {

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
		$this->services->Common->renderFilter(array('admin'), '/admin/plugins');
	}

	/**
	 * Render Plugins Page
	 *
	 * @since 1.0
	 * @access public
	 */
	public function render()
	{
		return $this->timber->render( 'plugins', $this->getData() );
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
		# $this->timber->bench->start();

		$this->services->PluginsRequests->setRequest($form);
		$this->services->PluginsRequests->processRequest(
			array(
				'activate' => array('admin'),
				'deactivate' => array('admin'),
				'delete' => array('admin'),
			),
			array(
				'activate' => array('real_admin'),
				'deactivate' => array('real_admin'),
				'delete' => array('real_admin'),
			),
			$form,
			$this->services->PluginsRequests,
			array(
				'activate' => 'activatePlugin',
				'deactivate' => 'deactivatePlugin',
				'delete' => 'deletePlugin',
			)
		);
		$this->services->PluginsRequests->getResponse();

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
			$this->services->PluginsData->currentUserData(),
			$this->services->PluginsData->pluginsData(),
			$this->services->Common->subPageName( $this->timber->translator->trans('Plugins') . " | " ),
			$this->services->Common->runtimeScripts( 'plugins' )
		);

		return $data;
	}
}