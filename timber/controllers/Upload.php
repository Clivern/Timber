<?php
/**
 * Timber - Ultimate Freelancer Platform
 *
 * @author      Clivern <support@clivern.com>
 * @copyright   2015 Clivern
 * @link        http://clivern.com
 * @license     http://codecanyon.com/license
 * @version     1.1
 * @package     Timber
 */

namespace Timber\Controllers;

/**
 * Upload Controller
 *
 * @since 1.0
 */
class Upload {

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
		# $this->timber->bench->start();

		$this->services->FilesRequests->setRequest($form);
		$this->services->FilesRequests->processRequest(
			array(
				'site_logo' => array('client', 'staff', 'admin'),
				'profile_avatar' => array('client', 'staff', 'admin'),
				'record_attachments' => array('client', 'staff', 'admin'),
				'dump_file' => array('client', 'staff', 'admin')
			),
			array(
				'site_logo' => array('real_client', 'real_staff', 'real_admin'),
				'profile_avatar' => array('real_client', 'real_staff', 'real_admin'),
				'record_attachments' => array('real_client', 'real_staff', 'real_admin'),
				'dump_file' => array('real_client', 'real_staff', 'real_admin')
			),
			$form,
			$this->services->FilesRequests,
			array(
				'site_logo' => 'uploadSiteLogo',
				'profile_avatar' => 'uploadProfileAvatar',
				'record_attachments' => 'uploadRecordAttachments',
				'dump_file' => 'dumpFile',
			)
		);
		$this->services->FilesRequests->getResponse();

		# $this->timber->bench->end();
		# $this->timber->bench->log();
	}
}