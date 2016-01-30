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

namespace Timber\Libraries;

/**
 * Check Latest Version of App to Inform Admins
 *
 * The lastest version files hosted on clivern.com
 *
 * @since 1.0
 */
class Remote {

	/**
	 * Timber current version (old version)
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->current_version
	 */
	private $current_version = TIMBER_CURRENT_VERSION;

	/**
       * Application updates url
       *
       * <code>
	 *   {
	 *       "version": "1.1",
	 *   }
       * </code>
       * @since 1.0
       * @access private
       * @var string $this->remote_updates_url
       */
	private $remote_updates_url = 'http://clivern.com/wp-content/api/timber-update-notifier.json';

	/**
	 * Remote response
	 *
       * @since 1.0
       * @access private
       * @var boolean|array $this->remote_response
	 */
	private $remote_response = false;

	/**
	 * Cached response
	 *
       * @since 1.0
       * @access private
       * @var boolean|array $this->cached_response
	 */
	private $cached_response = false;

	/**
	 * Check interval in seconds
	 *
       * @since 1.0
       * @access private
       * @var integer $this->ckeck_interval
	 */
	private $ckeck_interval = 43200;

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
		return $this;
	}

	/**
	 * Set cached response
	 *
	 * @since 1.0
	 * @access public
	 */
	public function config()
	{
		$this->cached_response = unserialize($this->timber->config('_site_updates_settings'));
		$this->check();
	}

	/**
	 * Check if update needed from cached response
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function needUpdate()
	{
		return (boolean) ((string) $this->cached_response['version'] > (string) $this->current_version);
	}

	/**
	 * Get latest version from cached response
	 *
	 * @since 1.0
	 * @access public
	 * @return string
	 */
	public function getLatestVersion()
	{
		return $this->cached_response['version'];
	}

	/**
	 * Get remote response
	 *
	 * If remote response value not false save to db
	 *
	 * @since 1.0
	 * @access public
	 * @return  boolean|array
	 */
	public function getRemoteResponse()
	{
		return $this->remote_response;
	}

	/**
	 * Fetch latest version and changelog from remote
	 *
	 * @since 1.0
	 * @access private
	 * @return boolean
	 */
	private function fetch()
	{
		// check if cURL extension exist
		if ( function_exists('curl_init') ) {
			$ch = curl_init($this->remote_updates_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			$response = @curl_exec($ch);
			curl_close($ch);
		}else{
			$response = @file_get_contents($this->remote_updates_url);
		}
		//check if fetch succeed
		if( (strrpos($response, "version") > 0) ){
			$this->remote_response = json_decode($response, true);
			$this->remote_response['time'] = time();
			return (boolean) $this->timber->option_model->updateOptionByKey(array('op_key' => '_site_updates_settings', 'op_value' => serialize($this->remote_response) ));
		}
		return false;
	}

	/**
	 * Run check for updates
	 *
	 * @since 1.0
	 * @access private
	 * @return boolean
	 */
	private function check()
	{
		$now = time();
        if ( ($now - $this->cached_response['time']) < $this->ckeck_interval ) {
            return true;
        }
        if( $this->fetch() ){
        	return true;
        }
        return false;
	}
}