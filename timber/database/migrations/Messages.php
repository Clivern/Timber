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
 * Create Messages Table
 *
 * @since 1.0
 */
class Messages {

	/**
     * Instance of orm
     *
     * @since 1.0
     * @access private
     * @var object $this->orm
     */
	private $orm;

	/**
	 * Messages table name
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
		$this->table = TIMBER_DB_PREFIX . TIMBER_DB_MESSAGES_TABLE;
		$this->charset = TIMBER_DB_CHARSET;
		$this->orm = \ORM::get_db();
	}

	/**
	 * Create Messages table
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
	 * Dump messages table
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
	 * Get messages table up query
	 *
	 * @since 1.0
	 * @access private
	 */
	private function upQuery()
	{
		$query = "CREATE TABLE IF NOT EXISTS `{$this->table}` (
   				`ms_id` int(11) not null auto_increment,
   				`sender_id` int(11) not null,
   				`receiver_id` int(11) not null,
   				`parent_id` int(11) not null,
   				`subject` varchar(150) not null,
   				`rece_cat` varchar(15) not null,
   				`send_cat` varchar(15) not null,
   				`rece_hide` enum('on','off') not null,
   				`send_hide` enum('on','off') not null,
   				`content` text not null,
   				`attach` enum('on','off') not null,
   				`created_at` datetime not null,
   				`updated_at` datetime not null,
   				`sent_at` datetime not null,
   				PRIMARY KEY (`ms_id`),
   				KEY `ms_id` (`ms_id`),
   				KEY `sender_id` (`sender_id`),
   				KEY `receiver_id` (`receiver_id`),
   				KEY `parent_id` (`parent_id`),
   				KEY `rece_cat` (`rece_cat`),
   				KEY `send_cat` (`send_cat`),
   				KEY `rece_hide` (`rece_hide`),
   				KEY `send_hide` (`send_hide`)
			) ENGINE=InnoDB DEFAULT CHARSET={$this->charset} AUTO_INCREMENT=1;";
		return $query;
	}

	/**
	 * Get messages table down query
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