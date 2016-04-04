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
 * Run common tasks from command line
 *
 * Just type [php artisan]
 *
 * Perform common and effective tasks from command line
 * Added only for Absolute geeks :)
 *
 * @since 1.0
 */
class Artisan {

	/**
	 * Holds command arguments
	 *
	 * @since 1.0
	 * @access private
	 * @var array $this->args
	 */
	private $args;

	/**
	 * Whether there is any active db connection
	 *
	 * @since 1.0
	 * @access private
	 * @var boolean $this->db_connect
	 */
	private $db_connect;

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
	 * Set command line passed arguments
	 *
	 * @since 1.0
	 * @access public
	 * @param array $args
	 */
	public function setArgs($args)
	{
		$this->args = $args;
		$this->configureOrm();
		return $this;
	}

	/**
	 * Execute commands
	 *
	 * @since 1.0
	 * @access public
	 * @return string
	 */
	public function exec()
	{
		if (count($this->args) <= 1) {
			$this->help();
		} else {
			switch ($this->args[1]) {
				case "test":
					//run test
					$this->test();
					break;
				case "help":
					$this->help();
					break;
				default:
					$this->help();
					break;
			}
		}
	}

	/**
	 * Run migrations
	 *
	 * Note: This Method Is deactivated during production.
	 *
	 * @since 1.0
	 * @access private
	 */
	private function migrate()
	{
		if( !($this->db_connect) ){
			echo "Error Connect DB";
			die();
		}

		$Files = \Timber\Database\Migrations\Files::instance()->dump()->run();
		echo "------- Files Table Created -------\n";
		$Invoices = \Timber\Database\Migrations\Invoices::instance()->dump()->run();
		echo "------- Invoices Table Created -------\n";
		$Items = \Timber\Database\Migrations\Items::instance()->dump()->run();
		echo "------- Items Table Created -------\n";
		$Messages = \Timber\Database\Migrations\Messages::instance()->dump()->run();
		echo "------- Messages Table Created -------\n";
		$Metas = \Timber\Database\Migrations\Metas::instance()->dump()->run();
		echo "------- Metas Table Created -------\n";
		$Milestones = \Timber\Database\Migrations\Milestones::instance()->dump()->run();
		echo "------- Milestones Table Created -------\n";
		$Options = \Timber\Database\Migrations\Options::instance()->dump()->run();
		echo "------- Options Table Created -------\n";
		$Projects = \Timber\Database\Migrations\Projects::instance()->dump()->run();
		echo "------- Projects Table Created -------\n";
		$ProjectsMeta = \Timber\Database\Migrations\ProjectsMeta::instance()->dump()->run();
		echo "------- ProjectsMeta Table Created -------\n";
		$Quotations = \Timber\Database\Migrations\Quotations::instance()->dump()->run();
		echo "------- Quotations Table Created -------\n";
		$Subscriptions = \Timber\Database\Migrations\Subscriptions::instance()->dump()->run();
		echo "------- Subscriptions Table Created -------\n";
		$Tasks = \Timber\Database\Migrations\Tasks::instance()->dump()->run();
		echo "------- Tasks Table Created -------\n";
		$Tickets = \Timber\Database\Migrations\Tickets::instance()->dump()->run();
		echo "------- Tickets Table Created -------\n";
		$Users = \Timber\Database\Migrations\Users::instance()->dump()->run();
		echo "------- Users Table Created -------\n";
		$UsersMeta = \Timber\Database\Migrations\UsersMeta::instance()->dump()->run();
	}

	/**
	 * Test Method
	 *
	 * @since 1.0
	 * @access private
	 */
	private function test()
	{
		echo "TIMBER IS YOURS! MAKE IT AWESOME";
	}

	/**
	 * Output help text
	 *
	 * @since 1.0
	 * @access private
	 * @return string
	 */
	private function help()
	{
		echo "use: php artisan <command> [<args>]\n\n";
		echo "Some of the commands :\n";
		echo "----- help : [get help]\n";
		echo "----- test : [get surprise]\n";
	}

      /**
       * Configure ORM
       *
       * @since 1.0
       * @access private
       * @return object
       */
      private function configureOrm()
      {
            # Connect To DB
            $db_connect = $this->newConnection();
            # Check If Connection Exist
            if(($db_connect === false) && !(is_object($db_connect))){
                  $this->db_connect = false;
                  return $this;
            }
            # Config Results Set
            \ORM::configure('return_result_sets', true);
            # Bind Tables IDs
            \ORM::configure('id_column_overrides', array(
            	TIMBER_DB_PREFIX . TIMBER_DB_FILES_TABLE => 'fi_id',
            	TIMBER_DB_PREFIX . TIMBER_DB_INVOICES_TABLE => 'in_id',
            	TIMBER_DB_PREFIX . TIMBER_DB_ITEMS_TABLE => 'it_id',
            	TIMBER_DB_PREFIX . TIMBER_DB_MESSAGES_TABLE => 'ms_id',
            	TIMBER_DB_PREFIX . TIMBER_DB_METAS_TABLE => 'me_id',
            	TIMBER_DB_PREFIX . TIMBER_DB_MILESTONES_TABLE => 'mi_id',
            	TIMBER_DB_PREFIX . TIMBER_DB_OPTIONS_TABLE => 'op_id',
            	TIMBER_DB_PREFIX . TIMBER_DB_PROJECTS_TABLE => 'pr_id',
            	TIMBER_DB_PREFIX . TIMBER_DB_PROJECTS_META_TABLE => 'me_id',
            	TIMBER_DB_PREFIX . TIMBER_DB_QUOTATIONS_TABLE => 'qu_id',
            	TIMBER_DB_PREFIX . TIMBER_DB_SUBSCRIPTIONS_TABLE => 'su_id',
            	TIMBER_DB_PREFIX . TIMBER_DB_TASKS_TABLE => 'ta_id',
            	TIMBER_DB_PREFIX . TIMBER_DB_TICKETS_TABLE => 'ti_id',
            	TIMBER_DB_PREFIX . TIMBER_DB_USERS_TABLE => 'us_id',
            	TIMBER_DB_PREFIX . TIMBER_DB_USERS_META_TABLE => 'me_id'
            ));
            \ORM::set_db($db_connect);
            $this->db_connect = true;
            return $this;
      }

      /**
       * Connect to DB
       *
       * Issue found if user by mistake added faulty db configs
       * idiorm will through uncaught exception and this is too bad
       * so i set pdo connection outside and give it to idiorm to work on
       *
       * Here is the old code that causes the issue
       * <code>
       *   \ORM::configure(array(
       *          'connection_string' => TIMBER_DB_DRIVER . ':host=' . TIMBER_DB_HOST . ';dbname=' . TIMBER_DB_NAME,
       *          'username' => TIMBER_DB_USER,
       *          'password' => TIMBER_DB_PWD
       *   ));
       * </code>
       *
       * @since 1.0
       * @access private
       * @return object|boolean
       */
      private function newConnection()
      {
            # Check if Client File Still not Exist
            if( (TIMBER_DB_HOST == '') && (TIMBER_DB_NAME == '') && (TIMBER_DB_USER == '') && (TIMBER_DB_PWD == '') ){
                  # Not Installed Yet
                  return false;
            }
            # Try To Connect
            try {
                  $db = @new \PDO(
                        TIMBER_DB_DRIVER . ':host=' . TIMBER_DB_HOST . ';dbname=' . TIMBER_DB_NAME,
                        TIMBER_DB_USER,
                        TIMBER_DB_PWD
                  );
                  $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
                  return $db;
            } catch (\PDOException $e) {
                  return false;
            }
      }

}