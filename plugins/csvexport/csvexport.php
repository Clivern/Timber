<?php
/**
 * CSV Export - Export Records to CSV
 *
 * @author      Clivern <support@clivern.com>
 * @copyright   2016 Clivern
 * @link        http://clivern.com
 * @license     http://codecanyon.com/license
 * @version     1.0
 * @package     CSV Export
 */

/**
 * If this file is called directly, abort.
 */
if ( !defined('TIMBER_ROOT') )
{
    die;
}

/**
 * CSV Export Root Dir Path
 *
 * @since 1.0
 */
define('CLIVERN_CSVEXPORT_ROOT_DIR', dirname(__FILE__));

/**
 * load plugin core functions
 */
include_once CLIVERN_CSVEXPORT_ROOT_DIR . '/writer.php';
include_once CLIVERN_CSVEXPORT_ROOT_DIR . '/core.php';