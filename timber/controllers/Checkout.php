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
 * Checkout Controller
 *
 * @since 1.0
 */
class Checkout {

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
	 * @param string $page
	 * @return boolean
	 */
	public function renderFilters()
	{
		$this->timber->filter->issueDetect();
		$this->timber->filter->configLibs();
		$this->timber->security->cookieCheck();
		$this->services->Common->renderFilter(array('client'), '/admin/invoices');
	}

	/**
	 * Payment Request
	 *
	 * @since 1.0
	 * @access public
	 * @param paypal|stripe $provider
	 */
	public function paymentRequest( $provider = 'paypal' )
	{

		if( $this->timber->demo->demoActive() ){
			$this->timber->redirect( $this->timber->config('request_url') . '/admin/invoices/checkout?return_code=8e' );
		}

		if( 'paypal' == $provider )
		{
			$this->timber->cachier->payPaypalExpress();
		}
		if( 'stripe' == $provider )
		{
			$this->timber->cachier->payStripe();
		}
	}

	/**
	 * Success Payment Request
	 *
	 * @since 1.0
	 * @access public
	 * @param paypal $provider
	 */
	public function successPayment( $provider = 'paypal' )
	{
		if( 'paypal' == $provider )
		{
			$this->timber->cachier->successPaypalExpress();
		}
	}

	/**
	 * Cancel Payment Request
	 *
	 * @since 1.0
	 * @access public
	 * @param paypal $provider
	 */
	public function cancelPayment( $provider = 'paypal' )
	{
		if( 'paypal' == $provider )
		{
			$this->timber->cachier->errorPaypalExpress();
		}
	}
}