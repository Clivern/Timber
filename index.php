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
die("TEST");
/**
 * App root dir full path
 *
 * @since 1.0
 * @var string
 */
define( 'TIMBER_ROOT', dirname(__FILE__) );

/**
 * Load slim framework
 */
require_once TIMBER_ROOT . '/vendor/autoload.php';

/**
 * whether client file exist
 *
 * @since 1.0
 * @var boolean
 */
if( is_file( TIMBER_ROOT . '/timber/client.php' ) ){
	//file exist
	define( 'APP_CLIENT_FILE_EXIST', true );
}else{
	//file not exist
	define( 'APP_CLIENT_FILE_EXIST', false );
}

/**
 * Include client configs if file exist
 */
if( APP_CLIENT_FILE_EXIST ){
	//include client file
	require_once TIMBER_ROOT . '/timber/client.php';
}

/**
 * get default client configurations
 */
require_once TIMBER_ROOT . '/timber/client-default.php';


/**
 * Enable debugging on development mode
 */
if( TIMBER_DEBUG_MODE ){
	@ini_set('display_errors', 1);
	@ini_set('log_errors', 1);
	@error_reporting(E_ALL);
}

/**
 * Create Instance Of Slim Framework
 *
 * @since 1.0
 * @var object
 */
$timber = new \Slim\Slim();

/**
 * Configure Slim Instance
 *
 * @since 1.0
 * @var object
 */
$app = \Timber\Configs\App::instance()->setDepen($timber)->configureApp()->configureOrm();

/**
 * Inject Libraries to Slim Instance
 *
 * @since 1.0
 * @var object
 */
$container = \Timber\Configs\Container::instance()->setDepen($timber)->bindAll();

/**
 * Define Routes for Both Backend And Frontend
 *
 * @since 1.0
 * @var object
 */
$router = \Timber\Configs\Router::instance()->setDepen($timber)->defineRoutes();

/**
 * Show The Awesomeness
 */
$timber->run();