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
 * Error Controller
 *
 * @since 1.0
 */
class Error {

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
		return $this;
	}

	/**
	 * Render 500
	 *
	 * @since 1.0
	 * @access public
	 */
	public function run500()
	{
		return $this->timber->render('500', array('site_sub_page' => $this->timber->translator->trans('500')));
	}

	/**
	 * Render 404
	 *
	 * @since 1.0
	 * @access public
	 */
	public function run404()
	{
		return $this->timber->render('404', array('site_sub_page' => $this->timber->translator->trans('404') . ' | '));
	}

	/**
	 * Render Maintenance
	 *
	 * @since 1.0
	 * @access public
	 */
	public function runMaintenance()
	{
		return $this->timber->render('maintenance', array('site_sub_page' => $this->timber->translator->trans('Maintenance') . ' | '));
	}

	/**
	 * 500 filter
	 *
	 * @since 1.0
	 * @access public
	 */
	public function filters500()
	{
		$this->timber->filter->issueRun();
		$this->timber->filter->configLibs();
	}

	/**
	 * 404 filters
	 *
	 * @since 1.0
	 * @access public
	 */
	public function filters404()
	{
		$this->timber->filter->issueDetect();
		$this->timber->filter->configLibs();
	}

	/**
	 * Maintenance filters
	 *
	 * @since 1.0
	 * @access public
	 */
	public function filtersMaintenance()
	{
		$this->timber->filter->issueDetect();
		$this->timber->filter->configLibs();
	}
}