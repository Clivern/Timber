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
 * Create Tasks Table
 *
 * @since 1.0
 */
class Tasks {

	/**
     * Instance of orm
     *
     * @since 1.0
     * @access private
     * @var object $this->orm
     */
	private $orm;

	/**
	 * Tasks table name
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
		$this->table = TIMBER_DB_PREFIX . TIMBER_DB_TASKS_TABLE;
		$this->charset = TIMBER_DB_CHARSET;
		$this->orm = \ORM::get_db();
	}

	/**
	 * Create tasks table
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
	 * Dump tasks table
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
	 * Get tasks table up query
	 *
	 * @since 1.0
	 * @access private
	 */
	private function upQuery()
	{
		$query = "CREATE TABLE IF NOT EXISTS `{$this->table}` (
   				`ta_id` int(11) not null auto_increment,
   				`mi_id` int(11) not null,
   				`pr_id` int(11) not null,
   				`owner_id` int(11) not null,
   				`assign_to` int(11) not null,
   				`title` varchar(150) not null,
   				`description` varchar(250) not null,
   				`status` enum('1','2','3','4','5','6','7','8','9') not null,
   				`priority` enum('1','2','3','4','5','6','7','8','9') not null,
   				`start_at` date not null,
   				`end_at` date not null,
   				`created_at` datetime not null,
   				`updated_at` datetime not null,
   				PRIMARY KEY (`ta_id`),
   				KEY `ta_id` (`ta_id`),
   				KEY `mi_id` (`mi_id`),
   				KEY `pr_id` (`pr_id`),
   				KEY `assign_to` (`assign_to`),
   				KEY `status` (`status`)
			) ENGINE=InnoDB DEFAULT CHARSET={$this->charset} AUTO_INCREMENT=1;";
		return $query;
	}

	/**
	 * Get tasks table down query
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