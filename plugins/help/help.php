<?php
/**
 * Help - Timber Help Plugin
 *
 * @author      Clivern <support@clivern.com>
 * @copyright   2015 Clivern
 * @link        http://clivern.com
 * @license     http://codecanyon.com/license
 * @version     1.1
 * @package     Help
 */

/**
 * If this file is called directly, abort.
 */
if ( !defined('TIMBER_ROOT') )
{
    die;
}

/**
 * Help Root Dir Path
 *
 * @since 1.0
 */
define('CLIVERN_HELP_ROOT_DIR', dirname(__FILE__));

/**
 * load plugin core functions
 */
include_once CLIVERN_HELP_ROOT_DIR . '/core.php';