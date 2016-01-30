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
define('TIMBER_HOME_URL', 'http://timber.com');

/**
 * App database driver
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_DB_DRIVER', 'mysql');

/**
 * App database host
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_DB_HOST', 'localhost');

/**
 * App database name
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_DB_NAME', 'timber');

/**
 * App database user
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_DB_USER', 'root');

/**
 * App database password
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_DB_PWD', '');

/**
 * App database charset
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_DB_CHARSET', 'utf8');

/**
 * App database tables names prefix
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_DB_PREFIX', 'timber_');

/**
 * App database port
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_DB_PORT', '');

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
define('ENCRYPT_LEVEL', 2);

/**
 * Used to encrypt users auth data
 *
 * Timber will set value during installation
 *
 * @since 1.0
 * @var string
 */
define('STAFF_AUTH_SALT', '$2a$08$CRFRzkL6zTT');

/**
 * Used to encrypt admins auth data
 *
 * Timber will set value during installation
 *
 * @since 1.0
 * @var string
 */
define('ADMINS_AUTH_SALT', '$2a$08$2rd2DNhuKNQ');

/**
 * Used to encrypt clients auth data
 *
 * Timber will set value during installation
 *
 * @since 1.0
 * @var string
 */
define('CLIENTS_AUTH_SALT', '$2a$08$Zy/HZe6vJH.');

/**
 * Used to encrypt public cookie data
 *
 * Timber will set value during installation
 *
 * @since 1.0
 * @var string
 */
define('APP_PUB_SALT', '$2a$08$YDATSrcJD/f');

/**
 * Random Hash
 *
 * Timber will set value during installation
 *
 * @since 1.0
 * @var string
 */
define('RANDOM_HASH', '$2a$08$BCUZKLu/ERy');

/**
 * Admin Auth Iden.
 *
 * Timber will set value during installation
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_ADMIN_IDEN', '54d7');

/**
 * User Auth Iden.
 *
 * Timber will set value during installation
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_STAFF_IDEN', '8dfa');

/**
 * Client Auth Iden.
 *
 * Timber will set value during installation
 *
 * @since 1.0
 * @var string
 */
define('TIMBER_CLIENT_IDEN', 'f6ad');

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
define('TIMBER_DEBUG_MODE', true);

/**
 * Mod Rewrite
 *
 * @since 1.0
 * @var boolean
 */
define('TIMBER_MOD_REWRITE', true);

/**
 * Whether timber installed
 *
 * @since 1.0
 * @var string
 */
 define('TIMBER_INSTALLED', true);