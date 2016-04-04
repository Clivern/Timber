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
 * Logout Controller
 *
 * @since 1.0
 */
class Logout {

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
	 * Perform logout request
	 *
	 * @since 1.0
	 * @access public
	 * @param string $nonce
	 */
	public function request($nonce = '')
	{
		if( !($this->verifyNonce($nonce)) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		if( $this->timber->cookie->exist( $this->timber->config('auth_staff')) ){
			$this->timber->cookie->delete( $this->timber->config('auth_staff'), true);
		}
		if( $this->timber->cookie->exist( $this->timber->config('auth_client')) ){
			$this->timber->cookie->delete( $this->timber->config('auth_client'), true);
		}
		if( $this->timber->cookie->exist($this->timber->config('auth_admin')) ){
			$this->timber->cookie->delete( $this->timber->config('auth_admin'), true);
		}

		$this->timber->redirect( $this->timber->config('request_url') );
	}

	/**
	 * Logout filters
	 *
	 * @since 1.0
	 * @access public
	 */
	public function requestFilter()
	{
		$this->timber->filter->issueDetect();
		$this->timber->filter->configLibs();
	}

	/**
	 * Verify if nonce is valid
	 *
	 * @since 1.0
	 * @access private
	 * @param string $nonce
	 * @return boolean
	 */
	private function verifyNonce($nonce)
	{
		if( $this->timber->security->isAuth() && ($this->timber->security->isAdmin() || $this->timber->security->isStaff() || $this->timber->security->isClient()) ){
			return (boolean) ( $this->timber->security->getNonce() == $nonce );
		}
		return false;
	}
}