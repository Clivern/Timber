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

namespace Timber\Configs;

/**
 * Base App Configurations
 *
 * @since 1.0
 */
class App {

      /**
       * Application name
       *
       * @since 1.0
       * @access private
       * @var string $this->app_name
       */
      private $app_name = 'Timber';

      /**
       * Application version
       *
       * @since 1.0
       * @access private
       * @var string $this->app_version
       */
      private $app_version = TIMBER_CURRENT_VERSION;

      /**
       * Application author
       *
       * @since 1.0
       * @access private
       * @var string $this->app_author
       */
      private $app_author = 'Clivern';

      /**
       * Application author url
       *
       * @since 1.0
       * @access private
       * @var string $this->app_author_url
       */
      private $app_author_url = 'http://clivern.com';

      /**
       * Application author email
       *
       * @since 1.0
       * @access private
       * @var string $this->app_author_email
       */
      private $app_author_email = 'support@clivern.com';

      /**
       * Application latest update
       *
       * @since 1.0
       * @access private
       * @var string $this->last_update
       */
      private $last_update = '16.4.2015';

      /**
       * Application base bath
       *
       * @since 1.0
       * @access private
       * @var string $this->timber_root
       */
      private $timber_root = TIMBER_ROOT;

      /**
       * Application url schema
       *
       * @since 1.0
       * @access private
       * @var http|https $this->url_schema
       */
      private $url_schema = 'http';

      /**
       * Application home url
       *
       * @since 1.0
       * @access private
       * @var string $this->home_url
       */
      private $home_url = TIMBER_HOME_URL;

      /**
       * Application debug mode
       *
       * @since 1.0
       * @access private
       * @var boolean $this->debug
       */
      private $debug = TIMBER_DEBUG_MODE;

      /**
       * A list of cookies keys used over the app
       *
       * @since 1.0
       * @access private
       * @var array $this->cookie_keys
       */
      private $cookie_keys = array(
            'cookie_check' => '_timber_ck_check',
            'auth_staff' => '_timber_auth_staff',
            'auth_client' => '_timber_auth_client',
            'auth_admin' => '_timber_auth_admin',
      );

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
       * Configure application
       *
       * @since 1.0
       * @access public
       * @return object
       */
      public function configureApp()
      {
            $this->home_url = rtrim($this->home_url, '/index.php');

            $this->timber->setName($this->app_name);
            $this->timber->config(array(
                  # Bind App Author
                  'app_name' => $this->app_name,
                  'app_author' => $this->app_author,
                  'app_author_url' => $this->app_author_url,
                  'app_author_email' => $this->app_author_email,
                  'last_update' => $this->last_update,
                  # Bind Paths and URLs
                  'timber_root' => $this->timber_root,
                  'url_schema' => (strpos($this->home_url, 'ttps://') > 0) ? 'https' : 'http',
                  'home_url' => rtrim($this->home_url, '/'),
                  'request_url' => (strpos($this->home_url, 'tp://example.co') > 0) ? rtrim($this->timber->request->getScheme() . '://' . $this->timber->request->getHost() . str_replace( 'index.php', '', $this->timber->request->getRootUri()) , '/') : rtrim($this->home_url, '/'),
                  # Bind Cookies
                  'cookie_check' => $this->cookie_keys['cookie_check'],
                  'auth_staff' => $this->cookie_keys['auth_staff'],
                  'auth_client' => $this->cookie_keys['auth_client'],
                  'auth_admin' => $this->cookie_keys['auth_admin'],
                  # Bind Mode
                  'mode' => ($this->debug) ? 'development' : 'production',
                  'debug' => $this->debug,
                  # Bind Logger
                  'log.writer' => \Timber\Libraries\Logger::instance(),
                  'view' => \Timber\Libraries\Twig::instance(),
            ));

            $this->timber->config('slim.url_scheme', $this->timber->config('url_schema'));

            //Mod rewrite is active
            if( !TIMBER_MOD_REWRITE ){
                  $this->timber->config('request_url', $this->timber->config('request_url') . '/index.php');
                  $this->timber->config('home_url', $this->timber->config('home_url') . '/index.php');
            }

            return $this;
      }

      /**
       * Configure orm
       *
       * @since 1.0
       * @access public
       * @return object
       */
      public function configureOrm()
      {
            # Get Fresh Connection
            $db_connect = $this->newConnection();
            # Config Connection
            if( ($db_connect === false) && !(is_object($db_connect)) ){
                  $this->timber->config('db_connection', false);
                  return $this;
            }
            # Config Results Set
            \ORM::configure('return_result_sets', true);
            \ORM::configure('logging', TIMBER_DEBUG_MODE);
            # Config Tables IDs
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
            # Bind PDO Object
            \ORM::set_db($db_connect);
            $this->timber->config('db_connection', true);
            return $this;
      }

      /**
       * Connect to DB
       *
       * Issue found if user by mistake added faulty db configs
       * idiorm will through uncaught exception and this is too bad
       * so i set pdo connection outside and give it to idiorm to work on
       *
       * The code that causes the issue
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