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
 * Debug APP installation and server
 *
 * @since 1.0
 */
class Debug {

	/**
	 * App debug report data
	 *
	 * @since 1.0
	 * @access private
	 * @var array $this->data
	 */
	private $data;

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
		//silence is golden
	}

	/**
	 * Get final report
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function getReport()
	{
		$this->serverInfo();
		$this->clientFile();
		$this->dbConnection();
		$this->dbTables();
		$this->appInstalled();
		$this->tablesData();
		return $this->data;
	}

	/**
	 * Set Server Info
	 *
	 * @since 1.0
	 * @access private
	 */
	private function serverInfo()
	{
		$this->data['php_version'] = (function_exists('phpversion')) ? phpversion() : '?';
		$this->data['loaded_extensions'] = (function_exists('get_loaded_extensions')) ? get_loaded_extensions() : array();

		$this->data['required_extensions'] = array(
			array( 'key' => 'PDO', 'value' => (in_array('PDO', $this->data['loaded_extensions']) ? 'on' : 'off')),
			array( 'key' => 'pdo_mysql', 'value' => (in_array('pdo_mysql', $this->data['loaded_extensions']) ? 'on' : 'off')),
			array( 'key' => 'mysql', 'value' => (in_array('mysql', $this->data['loaded_extensions']) ? 'on' : 'off')),
			array( 'key' => 'mysqli', 'value' => (in_array('mysqli', $this->data['loaded_extensions']) ? 'on' : 'off')),
			array( 'key' => 'mbstring', 'value' => (in_array('mbstring', $this->data['loaded_extensions']) ? 'on' : 'off')),
			array( 'key' => 'dom', 'value' => (in_array('dom', $this->data['loaded_extensions']) ? 'on' : 'off')),
			array( 'key' => 'mcrypt', 'value' => (in_array('mcrypt', $this->data['loaded_extensions']) ? 'on' : 'off')),
			array( 'key' => 'gd', 'value' => (in_array('gd', $this->data['loaded_extensions']) ? 'on' : 'off')),
			array( 'key' => 'curl', 'value' => (in_array('curl', $this->data['loaded_extensions']) ? 'on' : 'off')),
			array( 'key' => 'zlib', 'value' => (in_array('zlib', $this->data['loaded_extensions']) ? 'on' : 'off')),
		);
	}

	/**
	 * Set Client File Status
	 *
	 * @since 1.0
	 * @access private
	 */
	private function clientFile()
	{
		$client_file = TIMBER_ROOT . '/timber/client.php';
		if( !(is_file($client_file)) || !(file_exists($client_file)) ){
			$this->data['client_file'] = false;
		}else{
			$this->data['client_file'] = true;
		}
	}

	/**
	 * Set DB Connection Status
	 *
	 * @since 1.0
	 * @access private
	 */
	private function dbConnection()
	{
		$this->data['db_connection'] = (boolean) $this->timber->config('db_connection');
	}

	/**
	 * Set DB Tables Status
	 *
	 * @since 1.0
	 * @access private
	 */
	private function dbTables()
	{
		//since it is used during installation, Take Precautions!
		$this->data['db_tables'] = false;
		if( !($this->data['db_connection']) ){
			return false;
		}

		$tables = \ORM::get_db()->query('SHOW TABLES');
		$tables_list = array();
		while($result = $tables->fetch()) {
     			$tables_list[] = $result[0];
		}
		if((is_array($tables_list)) && !(count($tables_list) > 0)){
			return false;
		}

		$status  = (boolean) (in_array(TIMBER_DB_PREFIX . TIMBER_DB_FILES_TABLE, $tables_list));
		$status &= (boolean) (in_array(TIMBER_DB_PREFIX . TIMBER_DB_INVOICES_TABLE, $tables_list));
		$status &= (boolean) (in_array(TIMBER_DB_PREFIX . TIMBER_DB_ITEMS_TABLE, $tables_list));
		$status &= (boolean) (in_array(TIMBER_DB_PREFIX . TIMBER_DB_MESSAGES_TABLE, $tables_list));
		$status &= (boolean) (in_array(TIMBER_DB_PREFIX . TIMBER_DB_METAS_TABLE, $tables_list));
		$status &= (boolean) (in_array(TIMBER_DB_PREFIX . TIMBER_DB_MILESTONES_TABLE, $tables_list));
		$status &= (boolean) (in_array(TIMBER_DB_PREFIX . TIMBER_DB_OPTIONS_TABLE, $tables_list));
		$status &= (boolean) (in_array(TIMBER_DB_PREFIX . TIMBER_DB_PROJECTS_TABLE, $tables_list));
		$status &= (boolean) (in_array(TIMBER_DB_PREFIX . TIMBER_DB_PROJECTS_META_TABLE, $tables_list));
		$status &= (boolean) (in_array(TIMBER_DB_PREFIX . TIMBER_DB_QUOTATIONS_TABLE, $tables_list));
		$status &= (boolean) (in_array(TIMBER_DB_PREFIX . TIMBER_DB_SUBSCRIPTIONS_TABLE, $tables_list));
		$status &= (boolean) (in_array(TIMBER_DB_PREFIX . TIMBER_DB_TASKS_TABLE, $tables_list));
		$status &= (boolean) (in_array(TIMBER_DB_PREFIX . TIMBER_DB_TICKETS_TABLE, $tables_list));
		$status &= (boolean) (in_array(TIMBER_DB_PREFIX . TIMBER_DB_USERS_TABLE, $tables_list));
		$status &= (boolean) (in_array(TIMBER_DB_PREFIX . TIMBER_DB_USERS_META_TABLE, $tables_list));

		$this->data['db_tables'] = (boolean) $status;
	}

	/**
	 * Set whether app installed or not
	 *
	 * @since 1.0
	 * @access private
	 */
	private function appInstalled()
	{
		//since it is used during installation, Take Precautions!
		$this->data['app_installed'] = false;
		if( !($this->data['db_connection']) || !($this->data['db_tables']) ){
			return false;
		}

		$site_init = $this->timber->option_model->getOptionByKey('_timber_install');

		if(is_object($site_init)){
			$site_init = $site_init->as_array();
		}else{
			$this->data['app_installed'] = false;
		}

		if( (is_array($site_init)) && (count($site_init) > 0) && ($site_init['op_value'] == 'over') ){
			$this->data['app_installed'] = true;
		}else{
			$this->data['app_installed'] = false;
		}
	}

	/**
	 * Set Min Table Data Status
	 *
	 * @since 1.0
	 * @access private
	 */
	private function tablesData()
	{
		$this->data['options_count_status'] = false;
		$this->data['users_count_status'] = false;
		$this->data['users_meta_count_status'] = false;

		if( !($this->data['db_connection']) || !($this->data['db_tables']) ){
			return false;
		}

		$this->data['options_count'] = $this->timber->option_model->countOptions();
		$this->data['users_count'] = $this->timber->user_model->countUsers();
		$this->data['users_meta_count'] = $this->timber->user_meta_model->countMetas();
		$this->data['options_count_status'] = (boolean) ((int) $this->data['options_count'] >= 68);
		$this->data['users_count_status'] = (boolean) ((int) $this->data['users_count'] >= 1);
		$this->data['users_meta_count_status'] = (boolean) ((int)$this->data['users_meta_count'] >= 1);
	}
}