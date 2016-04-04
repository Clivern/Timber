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
 *
 * ====== Please Note That ======
 * - You must not edit this file.
 * - All these configs can be overriden from client.php file
 * - Don't change or define `TIMBER_CURRENT_VERSION`.
 */

/**
 * Timber current version
 *
 * This used all over the app core so be careful and don't change.
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_CURRENT_VERSION', '1.0');

/**
 * App Home URL
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_HOME_URL')){
	define('TIMBER_HOME_URL', 'http://example.com');
}

/**
 * App database driver
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_DB_DRIVER')){
	define('TIMBER_DB_DRIVER', 'mysql');
}

/**
 * App database host
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_DB_HOST')){
	define('TIMBER_DB_HOST', '');
}

/**
 * App database name
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_DB_NAME')){
	define('TIMBER_DB_NAME', '');
}

/**
 * App database user
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_DB_USER')){
	define('TIMBER_DB_USER', '');
}

/**
 * App database password
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_DB_PWD')){
	define('TIMBER_DB_PWD', '');
}

/**
 * App database charset
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_DB_CHARSET')){
	define('TIMBER_DB_CHARSET', 'utf8');
}

/**
 * App database tables names prefix
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_DB_PREFIX')){
	define('TIMBER_DB_PREFIX', 'timber_');
}

/**
 * App database port
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_DB_PORT')){
	define('TIMBER_DB_PORT', '');
}

/**
 * App files table name
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_DB_FILES_TABLE')){
	define('TIMBER_DB_FILES_TABLE', 'files');
}

/**
 * App invoices table name
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_DB_INVOICES_TABLE')){
	define('TIMBER_DB_INVOICES_TABLE', 'invoices');
}

/**
 * App items table name
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_DB_ITEMS_TABLE')){
	define('TIMBER_DB_ITEMS_TABLE', 'items');
}

/**
 * App messages table name
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_DB_MESSAGES_TABLE')){
	define('TIMBER_DB_MESSAGES_TABLE', 'messages');
}

/**
 * App metas table name
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_DB_METAS_TABLE')){
	define('TIMBER_DB_METAS_TABLE', 'metas');
}

/**
 * App milestones table name
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_DB_MILESTONES_TABLE')){
	define('TIMBER_DB_MILESTONES_TABLE', 'milestones');
}

/**
 * App options table name
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_DB_OPTIONS_TABLE')){
	define('TIMBER_DB_OPTIONS_TABLE', 'options');
}

/**
 * App projects table name
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_DB_PROJECTS_TABLE')){
	define('TIMBER_DB_PROJECTS_TABLE', 'projects');
}

/**
 * App projects meta table name
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_DB_PROJECTS_META_TABLE')){
	define('TIMBER_DB_PROJECTS_META_TABLE', 'projects_meta');
}

/**
 * App quotations table name
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_DB_QUOTATIONS_TABLE')){
	define('TIMBER_DB_QUOTATIONS_TABLE', 'quotations');
}

/**
 * App subscriptions table name
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_DB_SUBSCRIPTIONS_TABLE')){
	define('TIMBER_DB_SUBSCRIPTIONS_TABLE', 'subscriptions');
}

/**
 * App tasks table name
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_DB_TASKS_TABLE')){
	define('TIMBER_DB_TASKS_TABLE', 'tasks');
}

/**
 * App tickets meta table name
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_DB_TICKETS_TABLE')){
	define('TIMBER_DB_TICKETS_TABLE', 'tickets');
}

/**
 * App users table name
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_DB_USERS_TABLE')){
	define('TIMBER_DB_USERS_TABLE', 'users');
}

/**
 * App users meta table name
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_DB_USERS_META_TABLE')){
	define('TIMBER_DB_USERS_META_TABLE', 'users_meta');
}

/**
 * Encrypt level
 *
 * During installation app check if your server supports mcrypt and if so it
 * sets encrypt level to 2 otherwise it sets it to 1
 *
 * Level 1 uses simple encrypt mechanism but don't worry it is also hard
 * Level 2 uses mcrypt if your server support mcrypt change to level 2
 *
 * @since 1.0
 * @var integer
 */
if(!defined('ENCRYPT_LEVEL')){
	define('ENCRYPT_LEVEL', 1);
}

/**
 * Used to encrypt users auth data
 *
 * Timber will set value during installation
 *
 * @since 1.0
 * @var string
 */
if(!defined('STAFF_AUTH_SALT')){
	define('STAFF_AUTH_SALT', 'R*b~fLe7VryHfRi=ej');
}

/**
 * Used to encrypt admins auth data
 *
 * Timber will set value during installation
 *
 * @since 1.0
 * @var string
 */
if(!defined('ADMINS_AUTH_SALT')){
	define('ADMINS_AUTH_SALT', 'a9JhxN%4mwYDi1z8Z_');
}

/**
 * Used to encrypt clients auth data
 *
 * Timber will set value during installation
 *
 * @since 1.0
 * @var string
 */
if(!defined('CLIENTS_AUTH_SALT')){
	define('CLIENTS_AUTH_SALT', 'a9JhxN%4mwYDi1z8Z_');
}

/**
 * Used to encrypt public cookie data
 *
 * Timber will set value during installation
 *
 * @since 1.0
 * @var string
 */
if(!defined('APP_PUB_SALT')){
	define('APP_PUB_SALT', 'amJnmfslqym4nO4cz*');
}

/**
 * Random Hash
 *
 * Timber will set value during installation
 *
 * @since 1.0
 * @var string
 */
if(!defined('RANDOM_HASH')){
	define('RANDOM_HASH', 'GBhC02Op=t6kqag~A');
}

/**
 * Admin Auth Iden.
 *
 * Timber will set value during installation
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_ADMIN_IDEN')){
	define('TIMBER_ADMIN_IDEN', 'uty');
}

/**
 * User Auth Iden.
 *
 * Timber will set value during installation
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_STAFF_IDEN')){
	define('TIMBER_STAFF_IDEN', 'klw');
}

/**
 * Client Auth Iden.
 *
 * Timber will set value during installation
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_CLIENT_IDEN')){
	define('TIMBER_CLIENT_IDEN', 'fgy');
}

/**
 * Timber Backups DIR
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_BACKUPS_DIR')){
	define('TIMBER_BACKUPS_DIR', '/backups');
}

/**
 * Timber Cache DIR
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_CACHE_DIR')){
	define('TIMBER_CACHE_DIR', '/cache');
}

/**
 * Timber Storage DIR
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_STORAGE_DIR')){
	define('TIMBER_STORAGE_DIR', '/storage');
}

/**
 * Timber Public DIR
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_THEMES_DIR')){
	define('TIMBER_THEMES_DIR', '/themes');
}

/**
 * Timber Logs DIR
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_LOGS_DIR')){
	define('TIMBER_LOGS_DIR', '/logs');
}

/**
 * Timber Langs DIR
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_LANGS_DIR')){
	define('TIMBER_LANGS_DIR', '/langs');
}

/**
 * Timber Plugins DIR
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_PLUGINS_DIR')){
	define('TIMBER_PLUGINS_DIR', '/plugins');
}

/**
 * Debug mode
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_DEBUG_MODE')){
	define('TIMBER_DEBUG_MODE', true);
}

/**
 * Whether Timber installed
 *
 * @since 1.0
 * @var string
 */
if(!defined('TIMBER_INSTALLED')){
	define('TIMBER_INSTALLED', false);
}

/**
 * Mod Rewrite
 *
 * @since 1.0
 * @var boolean
 */
if(!defined('TIMBER_MOD_REWRITE')){
	define('TIMBER_MOD_REWRITE', true);
}

/**
 * Mod Rewrite
 *
 * @since 1.0
 * @var boolean
 */
if(!defined('TIMBER_DEMO_MODE')){
	define('TIMBER_DEMO_MODE', false);
}