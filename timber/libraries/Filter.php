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
 * Filter Library Perform All Pre-Render Process
 *
 * It configure other application libraries according to
 * application state (if issue found configure app according
 * to defaults otherwise configure app according to user defined values)
 *
 * @since 1.0
 */
class Filter {

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
	 * @param string $route
	 */
	public function config($route = '')
	{
		//fix modes during installation process
		if( $route == 'installer' ){
			$this->modesFix();
		}
	}

	/**
	 * Configure libraries
	 *
	 * configuring libraries include passing app instance and fire config method of each library
	 *
	 * @since 1.0
	 * @access public
	 */
	public function configLibs()
	{
		$this->timber->backup->setDepen($this->timber)->config();
		$this->timber->bench->setDepen($this->timber)->config();
		$this->timber->biller->setDepen($this->timber)->config();
		$this->timber->cookie->setDepen($this->timber)->config();
		$this->timber->debug->setDepen($this->timber)->config();
		$this->timber->encrypter->setDepen($this->timber)->config();
		$this->timber->faker->setDepen($this->timber)->config();
		$this->timber->cachier->setDepen($this->timber)->config();
		$this->timber->gravatar->setDepen($this->timber)->config();
		$this->timber->hasher->setDepen($this->timber)->config(8, false);
		$this->timber->helpers->setDepen($this->timber)->config();
		$this->timber->logger->setDepen($this->timber)->config();
		$this->timber->mailer->setDepen($this->timber)->config();
		$this->timber->plugins->setDepen($this->timber)->config();
		$this->timber->storage->setDepen($this->timber)->config();
		$this->timber->remote->setDepen($this->timber)->config();
		$this->timber->security->setDepen($this->timber)->config();
		$this->timber->access->setDepen($this->timber)->config();
        $this->timber->notify->setDepen($this->timber)->config();
		$this->timber->time->setDepen($this->timber)->config();
		$this->timber->translator->setDepen($this->timber)->config();
		$this->timber->twigext->setDepen($this->timber)->config();
		$this->timber->twig->setDepen($this->timber)->config();
		$this->timber->validator->setDepen($this->timber)->config();
		$this->timber->upgrade->setDepen($this->timber)->config();
		# Init Plugins
		$this->timber->applyHook('timber.init', $this->timber);
		return $this;
	}

	/**
	 * Detect if db not connected or app not correctly installed
	 * and send to issues page
	 *
	 * Issues page shouldn't have any dynamic content.
	 *
	 * @since 1.0
	 * @access public
	 * @return  boolean
	 */
	public function issueDetect()
	{
		if( !($this->clientExist()) ){
			if( TIMBER_INSTALLED ){
				$this->timber->redirect( $this->timber->config('request_url') . '/500' );
			}else{
				//$this->htaccess();
				$this->timber->redirect( $this->timber->config('request_url') . '/install' );
			}
		}

		if( !($this->dbConnected()) ){
			if( TIMBER_INSTALLED ){
				$this->timber->redirect( $this->timber->config('request_url') . '/500' );
			}else{
				//$this->htaccess();
				$this->timber->redirect( $this->timber->config('request_url') . '/install' );
			}
		}

		if( !($this->tablesExist()) ){
			if( TIMBER_INSTALLED ){
				$this->timber->redirect( $this->timber->config('request_url') . '/500' );
			}else{
				//$this->htaccess();
				$this->timber->redirect( $this->timber->config('request_url') . '/install' );
			}
		}

		if( !($this->appInstalled()) ){
			if( TIMBER_INSTALLED ){
				$this->timber->redirect( $this->timber->config('request_url') . '/500' );
			}else{
				//$this->htaccess();
				$this->timber->redirect( $this->timber->config('request_url') . '/install' );
			}
		}

		if( !($this->getAutoloadOptions()) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/500' );
		}

		//app is ready to run
		return true;
	}

	/**
	 * Load default options in case app face problem to run (mostly in install and 500 controllers)
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function issueRun()
	{
		$this->getAutoloadOptionsDefaults();
		return true;
	}

	/**
	 * Fix files and folders modes
	 *
	 * @since 1.0
	 * @access private
	 */
	private function modesFix()
	{
		$dirs = array(
			TIMBER_ROOT => 0755,
			TIMBER_BACKUPS_DIR => 0755,
			TIMBER_CACHE_DIR => 0755,
			TIMBER_THEMES_DIR => 0755,
			TIMBER_STORAGE_DIR => 0755,
			TIMBER_LANGS_DIR => 0755,
			TIMBER_LOGS_DIR => 0755,
			TIMBER_PLUGINS_DIR => 0755,
		);
		$files = array(
			'/timber/client-sample.php' => 0755,
			'/timber/client.php' => 0755,
			'/timber/client-default.php' => 0755,
		);

		foreach ($dirs as $dir => $mode) {
			if( !(is_dir( TIMBER_ROOT . $dir )) ){ continue; }
			@chmod( TIMBER_ROOT . $dir, $mode );
		}

		foreach ($files as $file => $mode) {
			if( !(is_file( TIMBER_ROOT . $file )) || !(file_exists( TIMBER_ROOT . $file )) ){ continue; }
			@chmod( TIMBER_ROOT . $file, $mode );
		}
	}

	/**
	 * Check if htaccess file exist otherwise create it
	 *
	 *
	 * # BEGIN Timber
	 * <IfModule mod_rewrite.c>
	 * RewriteEngine On
	 * RewriteBase /timber/
	 * RewriteRule ^index\.php$ - [L]
	 * RewriteCond %{REQUEST_FILENAME} !-f
	 * RewriteCond %{REQUEST_FILENAME} !-d
	 * RewriteRule . /timber/index.php [L]
	 * </IfModule>
	 * # END Timber
	 *
	 * @since 1.0
	 * @access private
	 * @return boolean
	 */
	private function htaccess()
	{
		$htaccess_file = TIMBER_ROOT . '/.htaccess';
		if( (is_file($htaccess_file)) && (file_exists($htaccess_file)) ){
			return true;
		}
		// should be /project/ for sub-dir
		// or / and for base dir
		$Uri = $this->timber->request->getRootUri();
		$Uri = str_replace( 'index.php', '', $Uri );
		$Uri = ( empty($Uri) || ($Uri == '/') ) ? '/' : '/' . trim( $Uri, '/' ) . '/';
		$file_content = "# BEGIN Timber\n<IfModule mod_rewrite.c>\nRewriteEngine On\nRewriteBase {$Uri}\nRewriteRule ^index\.php$ - [L]\nRewriteCond %{REQUEST_FILENAME} !-f\nRewriteCond %{REQUEST_FILENAME} !-d\nRewriteRule . {$Uri}index.php [L]\n</IfModule>\n# END Timber";

		$handle = @fopen( TIMBER_ROOT . '/.htaccess' , 'w');
		@fwrite($handle, $file_content);
		@fclose($handle);
		$this->timber->redirect( $this->timber->config('request_url') );
	}

	/**
	 * Check if client file exist
	 *
	 * @since 1.0
	 * @access private
	 * @return boolean
	 */
	private function clientExist()
	{
		$client_file = TIMBER_ROOT . '/timber/client.php';
		if( !(is_file($client_file)) || !(file_exists($client_file)) ){
			return false;
		}
		return true;
	}

	/**
	 * Check if db connected
	 *
	 * @since 1.0
	 * @access private
	 * @return boolean
	 */
	private function dbConnected()
	{
		return (boolean) $this->timber->config('db_connection');
	}

	/**
	 * Check if app tables exists
	 *
	 * @since 1.0
	 * @access private
	 * @return boolean
	 */
	private function tablesExist()
	{
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

		return (boolean) $status;
	}

	/**
	 * Check if Timber Installed
	 *
	 * Used to Predict any Issues that may Result from false configs
	 *
	 * @since 1.0
	 * @access private
	 * @return boolean
	 */
	private function appInstalled()
	{
		$options_count = $this->timber->option_model->countOptions();
		$users_count = $this->timber->user_model->countUsers();
		$users_meta_count = $this->timber->user_meta_model->countMetas();
		$options_count_status = (boolean) ((int) $options_count >= 40);
		$users_count_status = (boolean) ((int) $users_count >= 1);
		$users_meta_count_status = (boolean) ((int) $users_meta_count >= 1);

		if( (false === $options_count_status) || (false === $users_count_status) || (false === $users_meta_count_status) ){
			return false;
		}

		# Check if Site Installation Complete
		$site_init = $this->timber->option_model->getOptionByKey('_timber_install');

		if( is_object($site_init) ){
			$site_init = $site_init->as_array();
		}else{
			return false;
		}
		if( (is_array($site_init)) && (count($site_init) > 0) && ($site_init['op_value'] == 'over') ){
			return true;
		}
		return false;
	}

	/**
	 * Get autoload options and push to app
	 *
	 * @since 1.0
	 * @access private
	 * @return boolean
	 */
	private function getAutoloadOptions()
	{
		$options = $this->timber->option_model->getOptions('on');
		if( !(is_array($options)) || !(count($options) > 0) ){
			return false;
		}
		foreach ($options as $key => $option) {
			$this->timber->config($option['op_key'], $option['op_value']);
		}
		return true;
	}

	/**
	 * Get default options and push to app
	 *
	 * Set default to used options in init
	 *
	 * @since 1.0
	 * @access private
	 * @return boolean
	 */
	private function getAutoloadOptionsDefaults()
	{
		$options = array(
			'_site_title' => '',
			'_site_description' => '',
			'_site_logo' => '0',
			'_site_country' => '',
			'_site_city' => '',
			'_site_address_line1' => '',
			'_site_address_line2' => '',
			'_site_vat_number' => '',
			'_site_phone' => '',

			'_site_currency' => 'USD',
			'_site_currency_symbol' => '$',
			'_site_tax_rates' => serialize(array()),
			'_site_lang' => 'en_US',
			'_site_timezone' => 'America/New_York',
			'_site_email' => 'hello@example.com',
			'_site_emails_sender' => 'no_reply@timber.com',
			'_site_maintainance_mode' => 'off',

			'_site_theme' => 'default',
			'_site_skin' => 'default',
			'_google_font' => 'Montserrat',
			'_default_gravatar' => 'grav1',
			'_gravatar_platform' => 'gravatar',
			'_site_custom_styles' => '',
			'_site_custom_scripts' => '',
			'_site_tracking_codes' => '',

			'_site_caching' => serialize(array('status' => 'off', 'purge_each' => '7', 'last_run' => time() )),
			'_site_updates_settings' => serialize( array('version' => TIMBER_CURRENT_VERSION, 'time' => time()) ),
			'_site_backup_settings' => serialize(array('status' => 'off','run' => time(), 'interval' => '7' , 'compress' => 'off', 'store' => 10)),
			'_site_backup_performed' => serialize(array()),

			'_site_date_format' => 'Y-m-d',
			'_site_time_format' => 'H:i:s',
			'_max_upload_size' => '2',
			'_allowed_upload_extensions' => serialize(array()),

			'_client_permissions' => serialize(array()),
			'_staff_permissions' => serialize(array()),

			// '_mailer_smtp_server'
			// '_fpwd_tpl'
			// '_login_info_tpl'
			// '_register_invite_tpl'
			// '_message_notify_tpl'
			// '_reply_notify_tpl'

			// '_cron_key' => '',
			// '_db_reset_key' => '',

			'_themes_data' => serialize(array()),
			'_plugins_data' => serialize(array()),
			'_active_plugins' => serialize(array()),

			'_bank_transfer_status' => 'off',
			'_bank_transfer_details' => '',
			'_2checkout_status' => 'off',
			'_2checkout_details' => '',
			'_paypal_status' => 'off',
			'_paypal_details' => '',
			'_stripe_status' => 'off',
			'_stripe_details' => '',

			'_google_login_status' => 'off',
			'_google_login_app_key' => '',
			'_google_login_app_secret' => '',
			'_twitter_login_status' => 'off',
			'_twitter_login_app_key' => '',
			'_twitter_login_app_secret' => '',
			'_facebook_login_status' => 'off',
			'_facebook_login_app_id' => '',
			'_facebook_login_app_secret' => ''
		);
		foreach ($options as $key => $value) {
			$this->timber->config($key, $value);
		}
		return true;
	}
}