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
 * Crons Controller
 *
 * @since 1.0
 */
class Crons {

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
	 * Cron filters
	 *
	 * @since 1.0
	 * @access public
	 */
	public function filters()
	{
		$this->timber->filter->issueDetect();
		$this->timber->filter->configLibs();
		$this->timber->security->cookieCheck();
	}

	/**
	 * Fire Cron Jobs
	 *
	 * @since 1.0
	 * @access public
	 * @param string $key
	 * @return string
	 */
	public function render($key = '')
	{
		if($key === ''){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		$cron_key = $this->timber->option_model->getOptionByKey('_cron_key');

		if( (false === $cron_key) || !(is_object($cron_key)) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		$cron_key = $cron_key->as_array();

		if( $cron_key['op_value'] !== $key ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		$this->scheduledBackup();
		$this->emailAlerts();
		$this->systemTasks();
		$this->dbRestore();
		echo ":)";
	}

	/**
	 * Check and run scheduled backups
	 *
	 * @since 1.0
	 * @access private
	 */
	private function scheduledBackup()
	{
		$this->timber->backup->executeSchedule(false);
	}

	/**
	 * Email Alerts
	 *
	 * @since 1.0
	 * @access private
	 */
	private function emailAlerts()
	{
		# Send Email Alerts
        $this->timber->notify->fireMailerCrons();
	}

	/**
	 * System Tasks
	 *
	 * @since 1.0
	 * @access private
	 */
	private function systemTasks()
	{
		# System Tasks
		$this->timber->storage->unStoreFiles();
		$this->timber->storage->dumpStoredFiles();
	}

	/**
	 * DB Restore
	 *
	 * @since 1.0
	 * @access public
	 */
	private function dbRestore()
	{
		$db_dump_file = TIMBER_ROOT . TIMBER_BACKUPS_DIR . '/timber_db_dump.sql';

		if( ($this->timber->demo->demoActive()) && (is_file($db_dump_file)) && (file_exists($db_dump_file)) ){
			$orm = \ORM::get_db();
			$orm->exec(file_get_contents($db_dump_file));
			echo "Resetted";
		}
	}
}