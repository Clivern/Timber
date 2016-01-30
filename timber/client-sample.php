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

/**
 * App Home URL
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_HOME_URL', 'TIMBER_HOME_URL__');

/**
 * App database driver
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_DB_DRIVER', 'TIMBER_DB_DRIVER__');

/**
 * App database host
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_DB_HOST', 'TIMBER_DB_HOST__');

/**
 * App database name
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_DB_NAME', 'TIMBER_DB_NAME__');

/**
 * App database user
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_DB_USER', 'TIMBER_DB_USER__');

/**
 * App database password
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_DB_PWD', 'TIMBER_DB_PWD__');

/**
 * App database charset
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_DB_CHARSET', 'TIMBER_DB_CHARSET__');

/**
 * App database tables names prefix
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_DB_PREFIX', 'TIMBER_DB_PREFIX__');

/**
 * App database port
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_DB_PORT', 'TIMBER_DB_PORT__');

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
define('ENCRYPT_LEVEL', ENCRYPT_LEVEL__);

/**
 * Used to encrypt users auth data
 *
 * Timber will set value during installation
 *
 * @since 1.0
 * @var string
 */
define('STAFF_AUTH_SALT', 'STAFF_AUTH_SALT__');

/**
 * Used to encrypt admins auth data
 *
 * Timber will set value during installation
 *
 * @since 1.0
 * @var string
 */
define('ADMINS_AUTH_SALT', 'ADMINS_AUTH_SALT__');

/**
 * Used to encrypt clients auth data
 *
 * Timber will set value during installation
 *
 * @since 1.0
 * @var string
 */
define('CLIENTS_AUTH_SALT', 'CLIENTS_AUTH_SALT__');

/**
 * Used to encrypt public cookie data
 *
 * Timber will set value during installation
 *
 * @since 1.0
 * @var string
 */
define('APP_PUB_SALT', 'APP_PUB_SALT__');

/**
 * Random Hash
 *
 * Timber will set value during installation
 *
 * @since 1.0
 * @var string
 */
define('RANDOM_HASH', 'RANDOM_HASH__');

/**
 * Admin Auth Iden.
 *
 * Timber will set value during installation
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_ADMIN_IDEN', 'TIMBER_ADMIN_IDEN__');

/**
 * User Auth Iden.
 *
 * Timber will set value during installation
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_STAFF_IDEN', 'TIMBER_STAFF_IDEN__');

/**
 * Client Auth Iden.
 *
 * Timber will set value during installation
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_CLIENT_IDEN', 'TIMBER_CLIENT_IDEN__');

/**
 * Timber Backups DIR
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_BACKUPS_DIR', '/backups');

/**
 * Timber Cache DIR
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_CACHE_DIR', '/cache');

/**
 * Timber Storage DIR
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_STORAGE_DIR', '/storage');

/**
 * Timber Public DIR
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_THEMES_DIR', '/themes');

/**
 * Timber Logs DIR
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_LOGS_DIR', '/logs');

/**
 * Timber Plugins DIR
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_PLUGINS_DIR', '/plugins');

/**
 * Timber Langs DIR
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_LANGS_DIR', '/langs');

/**
 * Debug mode
 *
 * @since 1.0
 * @var boolean
 */
define('TIMBER_DEBUG_MODE', false);

/**
 * Mod Rewrite
 *
 * @since 1.0
 * @var boolean
 */
define('TIMBER_MOD_REWRITE', false);