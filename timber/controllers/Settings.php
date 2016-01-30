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
 * Settings Controller
 *
 * @since 1.0
 */
class Settings {

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
		$this->services->Common->renderFilter(array('admin'), '/admin/settings');
	}

	/**
	 * Render Settings Page
	 *
	 * @since 1.0
	 * @access public
	 */
	public function render()
	{
		return $this->timber->render( 'settings', $this->getData() );
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

		$this->services->SettingsRequests->setRequest($form);
		$this->services->SettingsRequests->processRequest(
			array(
				'company' => array('admin'),
				'appearance' => array('admin'),
				'rules' => array('admin'),
				'backups' => array('admin'),
				'templates' => array('admin'),
				'payments' => array('admin'),
				'actions' => array('admin'),
			),
			array(
				'company' => array('real_admin'),
				'appearance' => array('real_admin'),
				'rules' => array('real_admin'),
				'backups' => array('real_admin'),
				'templates' => array('real_admin'),
				'payments' => array('real_admin'),
				'actions' => array('real_admin'),
			),
			$form,
			$this->services->SettingsRequests,
			array(
				'company' => 'updateCompanyData',
				'appearance' => 'updateAppearanceData',
				'rules' => 'updateRulesData',
				'backups' => 'updateBackupsData',
				'templates' => 'updateTemplatesData',
				'payments' => 'updatePaymentsData',
				'actions' => 'performActions',
			)
		);
		$this->services->SettingsRequests->getResponse();

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

		$data['countries_list'] = $this->timber->helpers->getCountries();

		$data = array_merge($data,
			$this->services->SettingsData->currentUserData(),
			$this->services->SettingsData->companyData(),
			$this->services->SettingsData->appearanceData(),
			$this->services->SettingsData->rulesData(),
			$this->services->SettingsData->paymentsData(),
			$this->services->SettingsData->templatesData(),
			$this->services->SettingsData->backupsData(),
			$this->services->SettingsData->aboutData(),
			$this->services->SettingsData->uploaderInfo(),
			$this->services->Common->subPageName( $this->timber->translator->trans('Settings') . " | " ),
			$this->services->Common->runtimeScripts( 'settings' )
		);

		return $data;
	}
}