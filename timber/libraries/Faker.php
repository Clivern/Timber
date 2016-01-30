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
 * Create hashes and salts
 *
 * @since 1.0
 */
class Faker {

	/**
	 * Holds an instance of hasher class
	 *
	 * @since 1.0
	 * @access private
	 * @var object $this->hasher
	 */
	private $hasher;

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
	 * Configure class
	 *
	 * @since 1.0
	 * @access public
	 */
	public function config()
	{
		$this->hasher = $this->timber->hasher;
	}

	/**
	 * Get random hash alphanumeric value used for nonce and hash
	 *
	 * Nonces are 10 letters long
	 * Hashes are 20 letters long
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $lenght
	 * @return string
	 */
	public function randHash($length)
	{
		return substr(md5(uniqid(mt_rand(), true)), 0, $length);
	}

	/**
	 * Get salt and it may contain special characters
	 *
	 * Salts are 18 letters long
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $lenght
	 * @return string
	 */
	public function randSalt($lenght)
	{
		return substr($this->hasher->HashPassword($this->randHash(11)), 0, $lenght);
	}
}