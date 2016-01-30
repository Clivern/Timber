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

namespace Timber\Database\Migrations;

/**
 * Create Users Table
 *
 * @since 1.0
 */
class Users {

	/**
     * Instance of orm
     *
     * @since 1.0
     * @access private
     * @var object $this->orm
     */
	private $orm;

	/**
	 * Users table name
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->table
	 */
	private $table;

	/**
	 * Table charset
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->charset
	 */
	private $charset;

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
	 * Class constructor
	 *
	 * @since 1.0
	 * @access public
	 */
	public function __construct()
	{
		//prefix table name
		$this->table = TIMBER_DB_PREFIX . TIMBER_DB_USERS_TABLE;
		$this->charset = TIMBER_DB_CHARSET;
		$this->orm = \ORM::get_db();
	}

	/**
	 * Create users table
	 *
	 * @since 1.0
	 * @access public
	 * @return object
	 */
	public function run()
	{
		$this->orm->exec($this->upQuery());
		return $this;
	}

	/**
	 * Dump users table
	 *
	 * @since 1.0
	 * @access public
	 * @return object
	 */
	public function dump()
	{
		$this->orm->exec($this->downQuery());
		return $this;
	}

	/**
	 * Get users table up query
	 *
	 * @since 1.0
	 * @access private
	 */
	private function upQuery()
	{
		$query = "CREATE TABLE IF NOT EXISTS `{$this->table}` (
   				`us_id` int(11) not null auto_increment,
   				`user_name` varchar(60) not null,
   				`first_name` varchar(60) not null,
   				`last_name` varchar(60) not null,
   				`company` varchar(60) not null,
   				`email` varchar(100) not null,
   				`website` varchar(150) not null,
   				`phone_num` varchar(60) not null,
   				`zip_code` varchar(60) not null,
   				`vat_nubmer` varchar(60) not null,
   				`language` varchar(20) not null,
   				`job` varchar(60) not null,
   				`grav_id` int(11) not null,
   				`country` varchar(20) not null,
   				`city` varchar(60) not null,
   				`address1` varchar(60) not null,
   				`address2` varchar(60) not null,
   				`password` varchar(250) not null,
   				`sec_hash` varchar(100) not null,
   				`identifier` varchar(250) not null,
   				`auth_by` enum('1','2','3','4','5','6','7','8','9') not null,
   				`access_rule` enum('1','2','3','4','5','6','7','8','9') not null,
   				`status` enum('1','2','3','4','5','6','7','8','9') not null,
   				`created_at` datetime not null,
   				`updated_at` datetime not null,
   				PRIMARY KEY (`us_id`),
   				KEY `us_id` (`us_id`),
   				KEY `user_name` (`user_name`),
   				KEY `email` (`email`)
			) ENGINE=InnoDB DEFAULT CHARSET={$this->charset} AUTO_INCREMENT=1;";
		return $query;
	}

	/**
	 * Get users table down query
	 *
	 * @since 1.0
	 * @access private
	 */
	private function downQuery()
	{
		$query = "DROP TABLE IF EXISTS `{$this->table}`";
		return $query;
	}
}