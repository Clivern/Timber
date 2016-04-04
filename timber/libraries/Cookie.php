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

namespace Timber\Libraries;

/**
 * Create cookies for app not for eating
 *
 * @since 1.0
 */
class Cookie {

	/**
	 * Instance of timber app
	 *
	 * @since 1.0
	 * @access private
	 * @var object $this->timber
	 */
	private $timber;

	/**
	 * Cookie domain
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->domain
	 */
	private $domain;

	/**
	 * Cookie path
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->path
	 */
	private $path;

	/**
	 * Site SSL Support
	 *
	 * @since 1.0
	 * @access private
	 * @var boolean $this->ssl_support
	 */
	private $ssl_support;

	/**
	 * Cookie will be made accessible only through the HTTP protocol
	 *
	 * @since 1.0
	 * @access private
	 * @var boolean $this->httponly
	 */
	private $httponly = true;

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
		return $this;
	}

	/**
	 * Config class properties
	 *
	 * @since 1.0
	 * @access public
	 */
	public function config()
	{
		$home_url = rtrim( TIMBER_HOME_URL, '/index.php' );
		$this->domain = parse_url( $home_url, PHP_URL_HOST );
		$this->domain = ($this->domain != 'localhost') ? $this->domain : false;
		$this->path = parse_url( $home_url, PHP_URL_PATH );
		if( ($this->path != '/') && ($this->path == null) ){
			$this->path = '/';
		}
		if( $this->path != '/' ){
			$this->path = trim($this->path, '/');
			$this->path = '/'. $this->path .'/';
		}
		$this->ssl_support = ($this->timber->config('url_schema') == 'https') ? true : false;
	}

  	/**
  	 * Returns true if there is a cookie with this key.
  	 *
	 * @since 1.0
	 * @access public
  	 * @param string $key
  	 * @return boolean
  	 */
	public function exist($key)
	{
		return (boolean) ( (isset($_COOKIE[$key])) && !(empty($_COOKIE[$key])) );
	}

	/**
	 * Get cookie value with key
	 *
	 * @since 1.0
	 * @access public
	 * @param string $key
	 * @param string $default
	 * @return string
	 */
	public function get($key, $default = '')
	{
		return ( ($this->exist($key) ) ? $_COOKIE[$key] : $default );
	}

	/**
	 * Set cookie
	 *
	 * @since 1.0
	 * @access public
	 * @param string $key
	 * @param string $value
	 * @param integer|null $lifetime null will create sessions
	 * @return boolean
	 */
	public function set($key, $value, $lifetime = null)
	{
    		if ( headers_sent() ){
    			return false;
    		}
    		if($lifetime != null){
    			$lifetime = time() + ($lifetime * 24 * 60 * 60);
    		}
    		$status = @setcookie( $key, $value, $lifetime, $this->path, $this->domain, $this->ssl_support, $this->httponly );
    		return (boolean) $status;
	}

	/**
	 * Delete cookie
	 *
	 * @since 1.0
	 * @access public
	 * @param string $key
	 * @param boolean $global
	 * @return boolean
	 */
	public function delete($key, $global = true)
	{
    		if ( headers_sent() ){
    			return false;
    		}
    		$status = @setcookie( $key, '', time() - 3600, $this->path, $this->domain, $this->ssl_support, $this->httponly );
    		if( ($global) && (isset($_COOKIE[$key])) ){
    			unset($_COOKIE[$key]);
    		}
    		return (boolean) $status;
	}
}