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
 * Backup App Database
 *
 * Used to perform db backups (compressed or not)
 *
 * @since 1.0
 */
class Backup {

    /**
     * Backup settings
     *
     * @since 1.0
     * @access private
     * @var array $this->backup_settings
     */
    private $backup_settings;

    /**
     * A list of performed backups
     *
     * @since 1.0
     * @access private
     * @var array $this->performed_backups
     */
    private $performed_backups;

    /**
     * Newline character based on server
     *
     * @since 1.0
     * @access private
     * @var string $this->newline
     */
    private $newline = PHP_EOL;

    /**
     * App name added to file header
     *
     * @since 1.0
     * @access private
     * @var string $this->app_name
     */
    private $app_name = 'TIMBER';

    /**
     * Inline comment separator
     *
     * @since 1.0
     * @access private
     * @var string $this->separator
     */
    private $separator = '/*sep*/';

    /**
     * File name format
     *
     * @since 1.0
     * @access private
     * @var string $this->fname_format
     */
    private $fname_format = 'Y_m_d__H_i_s';

    /**
     * Final backup file name
     *
     * @since 1.0
     * @access private
     * @var string $this->fname
     */
    private $fname = '';

    /**
     * Whether to add header to backup file
     *
     * @since 1.0
     * @access private
     * @var boolean $this->file_header
     */
    private $file_header = true;

    /**
     * Whether to add tables drop to backup file
     *
     * @since 1.0
     * @access private
     * @var boolean $this->tables_drop
     */
    private $tables_drop = true;

    /**
     * Whether to add table structure to backup file
     *
     * @since 1.0
     * @access private
     * @var boolean $this->tables_structure
     */
    private $tables_structure = true;

    /**
     * Whether to add tables inserts to backup file
     *
     * @since 1.0
     * @access private
     * @var boolean $this->tables_inserts
     */
    private $tables_inserts = true;

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
        //silence
    }

    /**
     * Execute scheduled backup
     *
     * @since 1.0
     * @access public
     * @param boolean $force
     * @return boolean
     */
    public function executeSchedule($force = false)
    {
        $backup_settings = $this->timber->option_model->getOptionByKey('_site_backup_settings');
        if( (false === $backup_settings) || !(is_object($backup_settings)) ){
            return false;
        }
        $backup_settings = $backup_settings->as_array();
        $this->backup_settings = unserialize($backup_settings['op_value']);

        $performed_backups = $this->timber->option_model->getOptionByKey('_site_backup_performed');
        if( (false === $performed_backups) || !(is_object($performed_backups)) ){
            return false;
        }
        $performed_backups = $performed_backups->as_array();
        $this->performed_backups = unserialize($performed_backups['op_value']);
        $this->performed_backups = array_values($this->performed_backups);

        if( ((time() - $this->backup_settings['run']) < ($this->backup_settings['interval'] * 24 * 60 * 60)) && !($force) ){
            return true;
        }
        //check if schedule is active
        if( ($this->backup_settings['status'] == 'off') && !($force) ){
            return true;
        }

        $compress = ($this->backup_settings['compress'] == 'on') ? true : false;
        $status = $this->execute($compress);
        if( !$status ){
            return false;
        }

        while (count($this->performed_backups) > $this->backup_settings['store']) {
            $this->dumpOldBackups();
            array_shift($this->performed_backups);
        }
        //set time and update option
        $this->backup_settings['run'] = time();
        $update_status = true;
        $update_status &= $this->timber->option_model->updateOptionByKey(array(
            'op_key' => '_site_backup_settings',
            'op_value' => serialize($this->backup_settings),
        ));
        $update_status &= $this->timber->option_model->updateOptionByKey(array(
            'op_key' => '_site_backup_performed',
            'op_value' => serialize($this->performed_backups),
        ));
        return (boolean)($update_status);
    }

    /**
     * Dump old backup files
     *
     * @since 1.0
     * @return boolean
     */
    private function dumpOldBackups()
    {
        //delete file
        $first_backup = TIMBER_ROOT . TIMBER_BACKUPS_DIR . '/' . $this->performed_backups[0];
        if( !(is_file($first_backup)) || !(file_exists($first_backup)) ){
            return false;
        }
        return (boolean) unlink($first_backup);
    }

    /**
     * Execute backup
     *
     * @since 1.0
     * @access private
     * @param boolean $compress
     * @return boolean
     */
    private function execute($compress = false)
    {
        if( empty($this->fname) ){
            $file_name = $this->timber->time->getCurrentDate(false, $this->fname_format);
            $this->fname = TIMBER_ROOT . TIMBER_BACKUPS_DIR . '/' . $this->timber->time->getCurrentDate(false, $this->fname_format);
            $this->fname .= ($compress) ? '.sql.gz' : '.sql';
            $file_name .= ($compress) ? '.sql.gz' : '.sql';
        }
        $this->performed_backups[] = $file_name;
        $file_contents = '';
        $file_contents .= ($this->file_header) ? $this->addHeader() : '';
        $file_contents .= ($this->tables_drop) ? $this->addDumpTables() : '';
        $file_contents .= ($this->tables_structure) ? $this->addCreateTables() : '';
        $file_contents .= ($this->tables_inserts) ? $this->addInserts() : '';
        if($compress){
            $status = $this->writeGzip($file_contents);
        }else{
            $status = $this->writeFile($file_contents);
        }
        return $status;
    }

    /**
     * Add header to backup
     *
     * @since 1.0
     * @access public
     * @return string
     */
    private function addHeader()
    {
        $php_version = (function_exists('phpversion')) ? phpversion() : '?';

        $value = '';
        $value .= '#' . $this->newline;
        $value .= '# MySQL database dump' . $this->newline;
        $value .= '# Created by ' . $this->app_name . ', Version. ' . TIMBER_CURRENT_VERSION . $this->newline;
        $value .= '#' . $this->newline;
        $value .= '# Host: ' . TIMBER_DB_HOST . $this->newline;
        $value .= '# Generated: ' . date('Y-m-d H:i:s') . $this->newline;
        //issue rise and need connection, crap!
        $value .= '# PHP version: ' . $php_version . $this->newline;
        $value .= '#' . $this->newline;
        $value .= '# Database: `' . TIMBER_DB_NAME . '`' . $this->newline;
        $value .= '#' . $this->newline . $this->newline . $this->newline;
        return $value;
    }

    /**
     * Add dump tables to backup
     *
     * @since 1.0
     * @access public
     * @return string
     */
    private function addDumpTables()
    {
        $value = '';

        # Files Table
        $value .= '/* DROP '. TIMBER_DB_PREFIX . TIMBER_DB_FILES_TABLE .' TABLE */'. $this->newline;
        $value .= 'DROP TABLE IF EXISTS `' . TIMBER_DB_PREFIX . TIMBER_DB_FILES_TABLE . '`;' . $this->newline;

        # Invoices Table
        $value .= '/* DROP '. TIMBER_DB_PREFIX . TIMBER_DB_INVOICES_TABLE .' TABLE */'. $this->newline;
        $value .= 'DROP TABLE IF EXISTS `' . TIMBER_DB_PREFIX . TIMBER_DB_INVOICES_TABLE . '`;' . $this->newline;

        # Items Table
        $value .= '/* DROP '. TIMBER_DB_PREFIX . TIMBER_DB_ITEMS_TABLE .' TABLE */'. $this->newline;
        $value .= 'DROP TABLE IF EXISTS `' . TIMBER_DB_PREFIX . TIMBER_DB_ITEMS_TABLE . '`;' . $this->newline;

        # Messages Table
        $value .= '/* DROP '. TIMBER_DB_PREFIX . TIMBER_DB_MESSAGES_TABLE .' TABLE */'. $this->newline;
        $value .= 'DROP TABLE IF EXISTS `' . TIMBER_DB_PREFIX . TIMBER_DB_MESSAGES_TABLE . '`;' . $this->newline;

        # Metas Table
        $value .= '/* DROP '. TIMBER_DB_PREFIX . TIMBER_DB_METAS_TABLE .' TABLE */'. $this->newline;
        $value .= 'DROP TABLE IF EXISTS `' . TIMBER_DB_PREFIX . TIMBER_DB_METAS_TABLE . '`;' . $this->newline;

        # Milestones Table
        $value .= '/* DROP '. TIMBER_DB_PREFIX . TIMBER_DB_MILESTONES_TABLE .' TABLE */'. $this->newline;
        $value .= 'DROP TABLE IF EXISTS `' . TIMBER_DB_PREFIX . TIMBER_DB_MILESTONES_TABLE . '`;' . $this->newline;

        # Options Table
        $value .= '/* DROP '. TIMBER_DB_PREFIX . TIMBER_DB_OPTIONS_TABLE .' TABLE */'. $this->newline;
        $value .= 'DROP TABLE IF EXISTS `' . TIMBER_DB_PREFIX . TIMBER_DB_OPTIONS_TABLE . '`;' . $this->newline;

        # Projects Table
        $value .= '/* DROP '. TIMBER_DB_PREFIX . TIMBER_DB_PROJECTS_TABLE .' TABLE */'. $this->newline;
        $value .= 'DROP TABLE IF EXISTS `' . TIMBER_DB_PREFIX . TIMBER_DB_PROJECTS_TABLE . '`;' . $this->newline;

        # Projects Meta Table
        $value .= '/* DROP '. TIMBER_DB_PREFIX . TIMBER_DB_PROJECTS_META_TABLE .' TABLE */'. $this->newline;
        $value .= 'DROP TABLE IF EXISTS `' . TIMBER_DB_PREFIX . TIMBER_DB_PROJECTS_META_TABLE . '`;' . $this->newline;

        # Quotations Table
        $value .= '/* DROP '. TIMBER_DB_PREFIX . TIMBER_DB_QUOTATIONS_TABLE .' TABLE */'. $this->newline;
        $value .= 'DROP TABLE IF EXISTS `' . TIMBER_DB_PREFIX . TIMBER_DB_QUOTATIONS_TABLE . '`;' . $this->newline;

        # Subscriptions Table
        $value .= '/* DROP '. TIMBER_DB_PREFIX . TIMBER_DB_SUBSCRIPTIONS_TABLE .' TABLE */'. $this->newline;
        $value .= 'DROP TABLE IF EXISTS `' . TIMBER_DB_PREFIX . TIMBER_DB_SUBSCRIPTIONS_TABLE . '`;' . $this->newline;

        # Tasks Table
        $value .= '/* DROP '. TIMBER_DB_PREFIX . TIMBER_DB_TASKS_TABLE .' TABLE */'. $this->newline;
        $value .= 'DROP TABLE IF EXISTS `' . TIMBER_DB_PREFIX . TIMBER_DB_TASKS_TABLE . '`;' . $this->newline;

        # Tickets Table
        $value .= '/* DROP '. TIMBER_DB_PREFIX . TIMBER_DB_TICKETS_TABLE .' TABLE */'. $this->newline;
        $value .= 'DROP TABLE IF EXISTS `' . TIMBER_DB_PREFIX . TIMBER_DB_TICKETS_TABLE . '`;' . $this->newline;

        # Users Table
        $value .= '/* DROP '. TIMBER_DB_PREFIX . TIMBER_DB_USERS_TABLE .' TABLE */'. $this->newline;
        $value .= 'DROP TABLE IF EXISTS `' . TIMBER_DB_PREFIX . TIMBER_DB_USERS_TABLE . '`;' . $this->newline;

        # Users Meta Table
        $value .= '/* DROP '. TIMBER_DB_PREFIX . TIMBER_DB_USERS_META_TABLE .' TABLE */'. $this->newline;
        $value .= 'DROP TABLE IF EXISTS `' . TIMBER_DB_PREFIX . TIMBER_DB_USERS_META_TABLE . '`;' . $this->newline;

        return $value;
    }

    /**
     * Add tables creation to backup
     *
     * @since 1.0
     * @access public
     * @return string
     */
    private function addCreateTables()
    {
        $value = '';

        # Files Table
        $value .= '/* CREATE '. TIMBER_DB_PREFIX . TIMBER_DB_FILES_TABLE .' TABLE */'. $this->newline;
        $value .= 'CREATE TABLE IF NOT EXISTS `'. TIMBER_DB_PREFIX . TIMBER_DB_FILES_TABLE .'` (
                `fi_id` int(11) not null auto_increment,
                `title` varchar(100) not null,
                `hash` varchar(150) not null,
                `owner_id` int(11) not null,
                `description` varchar(150) not null,
                `storage` enum(\'1\',\'2\',\'3\',\'4\',\'5\',\'6\',\'7\',\'8\',\'9\') not null,
                `type` varchar(50) not null,
                `uploaded_at` datetime not null,
                PRIMARY KEY (`fi_id`),
                KEY `fi_id` (`fi_id`),
                KEY `hash` (`hash`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . TIMBER_DB_CHARSET . ' AUTO_INCREMENT=1;' . $this->newline;

        # Invoices Table
        $value .= '/* CREATE '. TIMBER_DB_PREFIX . TIMBER_DB_INVOICES_TABLE .' TABLE */'. $this->newline;
        $value .= 'CREATE TABLE IF NOT EXISTS `'. TIMBER_DB_PREFIX . TIMBER_DB_INVOICES_TABLE .'` (
                `in_id` int(11) not null auto_increment,
                `reference` varchar(50) not null,
                `owner_id` int(11) not null,
                `client_id` int(11) not null,
                `status` enum(\'1\',\'2\',\'3\',\'4\',\'5\',\'6\',\'7\',\'8\',\'9\') not null,
                `type` enum(\'1\',\'2\',\'3\',\'4\',\'5\',\'6\',\'7\',\'8\',\'9\') not null,
                `terms` text not null,
                `tax` varchar(20) not null,
                `discount` varchar(20) not null,
                `total` varchar(20) not null,
                `attach` enum(\'on\',\'off\') not null,
                `rec_type` varchar(20) not null,
                `rec_id` int(11) not null,
                `due_date` date not null,
                `issue_date` date not null,
                `created_at` datetime not null,
                `updated_at` datetime not null,
                PRIMARY KEY (`in_id`),
                KEY `in_id` (`in_id`),
                KEY `client_id` (`client_id`),
                KEY `status` (`status`),
                KEY `type` (`type`),
                KEY `rec_type` (`rec_type`),
                KEY `rec_id` (`rec_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . TIMBER_DB_CHARSET . ' AUTO_INCREMENT=1;' . $this->newline;


        # Items Table
        $value .= '/* CREATE '. TIMBER_DB_PREFIX . TIMBER_DB_ITEMS_TABLE .' TABLE */'. $this->newline;
        $value .= 'CREATE TABLE IF NOT EXISTS `'. TIMBER_DB_PREFIX . TIMBER_DB_ITEMS_TABLE .'` (
                `it_id` int(11) not null auto_increment,
                `title` varchar(100) not null,
                `owner_id` int(11) not null,
                `description` varchar(250) not null,
                `cost` varchar(20) not null,
                `created_at` datetime not null,
                `updated_at` datetime not null,
                PRIMARY KEY (`it_id`),
                KEY `it_id` (`it_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . TIMBER_DB_CHARSET . ' AUTO_INCREMENT=1;' . $this->newline;

        # Messages Table
        $value .= '/* CREATE '. TIMBER_DB_PREFIX . TIMBER_DB_MESSAGES_TABLE .' TABLE */'. $this->newline;
        $value .= 'CREATE TABLE IF NOT EXISTS `'. TIMBER_DB_PREFIX . TIMBER_DB_MESSAGES_TABLE .'` (
                `ms_id` int(11) not null auto_increment,
                `sender_id` int(11) not null,
                `receiver_id` int(11) not null,
                `parent_id` int(11) not null,
                `subject` varchar(150) not null,
                `rece_cat` varchar(15) not null,
                `send_cat` varchar(15) not null,
                `rece_hide` enum(\'on\',\'off\') not null,
                `send_hide` enum(\'on\',\'off\') not null,
                `content` text not null,
                `attach` enum(\'on\',\'off\') not null,
                `created_at` datetime not null,
                `updated_at` datetime not null,
                `sent_at` datetime not null,
                PRIMARY KEY (`ms_id`),
                KEY `ms_id` (`ms_id`),
                KEY `sender_id` (`sender_id`),
                KEY `receiver_id` (`receiver_id`),
                KEY `parent_id` (`parent_id`),
                KEY `rece_cat` (`rece_cat`),
                KEY `send_cat` (`send_cat`),
                KEY `rece_hide` (`rece_hide`),
                KEY `send_hide` (`send_hide`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . TIMBER_DB_CHARSET . ' AUTO_INCREMENT=1;' . $this->newline;


        # Metas Table
        $value .= '/* CREATE '. TIMBER_DB_PREFIX . TIMBER_DB_METAS_TABLE .' TABLE */'. $this->newline;
        $value .= 'CREATE TABLE IF NOT EXISTS `'. TIMBER_DB_PREFIX . TIMBER_DB_METAS_TABLE .'` (
                `me_id` int(11) not null auto_increment,
                `rec_id` int(11) not null,
                `rec_type` varchar(20) not null,
                `me_key` varchar(60) not null,
                `me_value` text not null,
                PRIMARY KEY (`me_id`),
                KEY `me_id` (`me_id`),
                KEY `rec_id` (`rec_id`),
                KEY `rec_type` (`rec_type`),
                KEY `me_key` (`me_key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . TIMBER_DB_CHARSET . ' AUTO_INCREMENT=1;' . $this->newline;

        # Milestones Table
        $value .= '/* CREATE '. TIMBER_DB_PREFIX . TIMBER_DB_MILESTONES_TABLE .' TABLE */'. $this->newline;
        $value .= 'CREATE TABLE IF NOT EXISTS `'. TIMBER_DB_PREFIX . TIMBER_DB_MILESTONES_TABLE .'` (
                `mi_id` int(11) not null auto_increment,
                `pr_id` int(11) not null,
                `owner_id` int(11) not null,
                `title` varchar(150) not null,
                `description` varchar(250) not null,
                `status` enum(\'1\',\'2\',\'3\',\'4\',\'5\',\'6\',\'7\',\'8\',\'9\') not null,
                `priority` enum(\'1\',\'2\',\'3\',\'4\',\'5\',\'6\',\'7\',\'8\',\'9\') not null,
                `start_at` date not null,
                `end_at` date not null,
                `created_at` datetime not null,
                `updated_at` datetime not null,
                PRIMARY KEY (`mi_id`),
                KEY `mi_id` (`mi_id`),
                KEY `pr_id` (`pr_id`),
                KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . TIMBER_DB_CHARSET . ' AUTO_INCREMENT=1;' . $this->newline;


        # Options Table
        $value .= '/* CREATE '. TIMBER_DB_PREFIX . TIMBER_DB_OPTIONS_TABLE .' TABLE */'. $this->newline;
        $value .= 'CREATE TABLE IF NOT EXISTS `'. TIMBER_DB_PREFIX . TIMBER_DB_OPTIONS_TABLE .'` (
                `op_id` int(11) not null auto_increment,
                `op_key` varchar(60) not null,
                `op_value` text not null,
                `autoload` enum(\'on\',\'off\') not null,
                PRIMARY KEY (`op_id`),
                KEY `op_id` (`op_id`),
                KEY `op_key` (`op_key`),
                KEY `autoload` (`autoload`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . TIMBER_DB_CHARSET . ' AUTO_INCREMENT=1;' . $this->newline;

        # Projects Table
        $value .= '/* CREATE '. TIMBER_DB_PREFIX . TIMBER_DB_PROJECTS_TABLE .' TABLE */'. $this->newline;
        $value .= 'CREATE TABLE IF NOT EXISTS `'. TIMBER_DB_PREFIX . TIMBER_DB_PROJECTS_TABLE .'` (
                `pr_id` int(11) not null auto_increment,
                `title` varchar(60) not null,
                `reference` varchar(60) not null,
                `description` text not null,
                `version` varchar(20) not null,
                `progress` varchar(20) not null,
                `budget` varchar(20) not null,
                `status` enum(\'1\',\'2\',\'3\',\'4\',\'5\',\'6\',\'7\',\'8\',\'9\') not null,
                `owner_id` int(11) not null,
                `tax` varchar(20) not null,
                `discount` varchar(20) not null,
                `attach` enum(\'on\',\'off\') not null,
                `created_at` datetime not null,
                `updated_at` datetime not null,
                `start_at` date not null,
                `end_at` date not null,
                PRIMARY KEY (`pr_id`),
                KEY `pr_id` (`pr_id`),
                KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . TIMBER_DB_CHARSET . ' AUTO_INCREMENT=1;' . $this->newline;

        # Projects Meta Table
        $value .= '/* CREATE '. TIMBER_DB_PREFIX . TIMBER_DB_PROJECTS_META_TABLE .' TABLE */'. $this->newline;
        $value .= 'CREATE TABLE IF NOT EXISTS `'. TIMBER_DB_PREFIX . TIMBER_DB_PROJECTS_META_TABLE .'` (
                `me_id` int(11) not null auto_increment,
                `pr_id` int(11) not null,
                `me_key` varchar(60) not null,
                `me_value` text not null,
                PRIMARY KEY (`me_id`),
                KEY `me_id` (`me_id`),
                KEY `pr_id` (`pr_id`),
                KEY `me_key` (`me_key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . TIMBER_DB_CHARSET . ' AUTO_INCREMENT=1;' . $this->newline;

        # Quotations Table
        $value .= '/* CREATE '. TIMBER_DB_PREFIX . TIMBER_DB_QUOTATIONS_TABLE .' TABLE */'. $this->newline;
        $value .= 'CREATE TABLE IF NOT EXISTS `'. TIMBER_DB_PREFIX . TIMBER_DB_QUOTATIONS_TABLE .'` (
                `qu_id` int(11) not null auto_increment,
                `title` varchar(50) not null,
                `reference` varchar(50) not null,
                `owner_id` int(11) not null,
                `terms` text not null,
                `created_at` datetime not null,
                `updated_at` datetime not null,
                PRIMARY KEY (`qu_id`),
                KEY `qu_id` (`qu_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . TIMBER_DB_CHARSET . ' AUTO_INCREMENT=1;' . $this->newline;

        # Suscriptions Table
        $value .= '/* CREATE '. TIMBER_DB_PREFIX . TIMBER_DB_SUBSCRIPTIONS_TABLE .' TABLE */'. $this->newline;
        $value .= 'CREATE TABLE IF NOT EXISTS `'. TIMBER_DB_PREFIX . TIMBER_DB_SUBSCRIPTIONS_TABLE .'` (
                `su_id` int(11) not null auto_increment,
                `reference` varchar(50) not null,
                `owner_id` int(11) not null,
                `client_id` int(11) not null,
                `status` enum(\'1\',\'2\',\'3\',\'4\',\'5\',\'6\',\'7\',\'8\',\'9\') not null,
                `frequency` varchar(20) not null,
                `terms` text not null,
                `tax` varchar(20) not null,
                `discount` varchar(20) not null,
                `total` varchar(20) not null,
                `attach` enum(\'on\',\'off\') not null,
                `begin_at` date not null,
                `end_at` date not null,
                `created_at` datetime not null,
                `updated_at` datetime not null,
                PRIMARY KEY (`su_id`),
                KEY `su_id` (`su_id`),
                KEY `client_id` (`client_id`),
                KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . TIMBER_DB_CHARSET . ' AUTO_INCREMENT=1;' . $this->newline;


        # Tasks Table
        $value .= '/* CREATE '. TIMBER_DB_PREFIX . TIMBER_DB_TASKS_TABLE .' TABLE */'. $this->newline;
        $value .= 'CREATE TABLE IF NOT EXISTS `'. TIMBER_DB_PREFIX . TIMBER_DB_TASKS_TABLE .'` (
                `ta_id` int(11) not null auto_increment,
                `mi_id` int(11) not null,
                `pr_id` int(11) not null,
                `owner_id` int(11) not null,
                `assign_to` int(11) not null,
                `title` varchar(150) not null,
                `description` varchar(250) not null,
                `status` enum(\'1\',\'2\',\'3\',\'4\',\'5\',\'6\',\'7\',\'8\',\'9\') not null,
                `priority` enum(\'1\',\'2\',\'3\',\'4\',\'5\',\'6\',\'7\',\'8\',\'9\') not null,
                `start_at` date not null,
                `end_at` date not null,
                `created_at` datetime not null,
                `updated_at` datetime not null,
                PRIMARY KEY (`ta_id`),
                KEY `ta_id` (`ta_id`),
                KEY `mi_id` (`mi_id`),
                KEY `pr_id` (`pr_id`),
                KEY `assign_to` (`assign_to`),
                KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . TIMBER_DB_CHARSET . ' AUTO_INCREMENT=1;' . $this->newline;

        # Tickets Table
        $value .= '/* CREATE '. TIMBER_DB_PREFIX . TIMBER_DB_TICKETS_TABLE .' TABLE */'. $this->newline;
        $value .= 'CREATE TABLE IF NOT EXISTS `'. TIMBER_DB_PREFIX . TIMBER_DB_TICKETS_TABLE .'` (
                `ti_id` int(11) not null auto_increment,
                `pr_id` int(11) not null,
                `parent_id` int(11) not null,
                `reference` varchar(50) not null,
                `owner_id` int(11) not null,
                `status` enum(\'1\',\'2\',\'3\',\'4\',\'5\',\'6\',\'7\',\'8\',\'9\') not null,
                `type` enum(\'1\',\'2\',\'3\',\'4\',\'5\',\'6\',\'7\',\'8\',\'9\') not null,
                `depth` enum(\'1\',\'2\',\'3\',\'4\',\'5\',\'6\',\'7\',\'8\',\'9\') not null,
                `subject` varchar(150) not null,
                `content` text not null,
                `attach` enum(\'on\',\'off\') not null,
                `created_at` datetime not null,
                `updated_at` datetime not null,
                PRIMARY KEY (`ti_id`),
                KEY `ti_id` (`ti_id`),
                KEY `pr_id` (`pr_id`),
                KEY `parent_id` (`parent_id`),
                KEY `owner_id` (`owner_id`),
                KEY `status` (`status`),
                KEY `type` (`type`),
                KEY `depth` (`depth`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . TIMBER_DB_CHARSET . ' AUTO_INCREMENT=1;' . $this->newline;

        # Users Table
        $value .= '/* CREATE '. TIMBER_DB_PREFIX . TIMBER_DB_USERS_TABLE .' TABLE */'. $this->newline;
        $value .= 'CREATE TABLE IF NOT EXISTS `'. TIMBER_DB_PREFIX . TIMBER_DB_USERS_TABLE .'` (
                `us_id` int(11) not null auto_increment,
                `user_name` varchar(60) not null,
                `first_name` varchar(60) not null,
                `last_name` varchar(60) not null,
                `company` varchar(60) not null,
                `email` varchar(100) not null,
                `website` varchar(150) not null,
                `phone_num` varchar(60) not null,
                `zip_code` varchar(60) not null,
                `vat_nubmer` varchar(60) not null,
                `language` varchar(20) not null,
                `job` varchar(60) not null,
                `grav_id` int(11) not null,
                `country` varchar(20) not null,
                `city` varchar(60) not null,
                `address1` varchar(60) not null,
                `address2` varchar(60) not null,
                `password` varchar(250) not null,
                `sec_hash` varchar(100) not null,
                `identifier` varchar(250) not null,
                `auth_by` enum(\'1\',\'2\',\'3\',\'4\',\'5\',\'6\',\'7\',\'8\',\'9\') not null,
                `access_rule` enum(\'1\',\'2\',\'3\',\'4\',\'5\',\'6\',\'7\',\'8\',\'9\') not null,
                `status` enum(\'1\',\'2\',\'3\',\'4\',\'5\',\'6\',\'7\',\'8\',\'9\') not null,
                `created_at` datetime not null,
                `updated_at` datetime not null,
                PRIMARY KEY (`us_id`),
                KEY `us_id` (`us_id`),
                KEY `user_name` (`user_name`),
                KEY `email` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . TIMBER_DB_CHARSET . ' AUTO_INCREMENT=1;' . $this->newline;

        # Users Meta Table
        $value .= '/* CREATE '. TIMBER_DB_PREFIX . TIMBER_DB_USERS_META_TABLE .' TABLE */'. $this->newline;
        $value .= 'CREATE TABLE IF NOT EXISTS `'. TIMBER_DB_PREFIX . TIMBER_DB_USERS_META_TABLE .'` (
                `me_id` int(11) not null auto_increment,
                `us_id` int(11) not null,
                `me_key` varchar(60) not null,
                `me_value` text not null,
                PRIMARY KEY (`me_id`),
                KEY `me_id` (`me_id`),
                KEY `us_id` (`us_id`),
                KEY `me_key` (`me_key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . TIMBER_DB_CHARSET . ' AUTO_INCREMENT=1;' . $this->newline;

        return $value;
    }

    /**
     * Return all tables inserts
     *
     * @since 1.0
     * @access private
     * @return string
     */
    private function addInserts()
    {
        $inserts = '';

        $inserts .= $this->filesInserts();
        $inserts .= $this->invoicesInserts();
        $inserts .= $this->itemsInserts();
        $inserts .= $this->messagesInserts();
        $inserts .= $this->metasInserts();
        $inserts .= $this->milestonesInserts();
        $inserts .= $this->optionsInserts();
        $inserts .= $this->projectsInserts();
        $inserts .= $this->projectsMetaInserts();
        $inserts .= $this->quotationsInserts();
        $inserts .= $this->subscriptionsInserts();
        $inserts .= $this->tasksInserts();
        $inserts .= $this->ticketsInserts();
        $inserts .= $this->usersInserts();
        $inserts .= $this->usersMetaInserts();

        return $inserts;
    }

    /**
     * Return files table
     *
     * @since 1.0
     * @access private
     * @return string
     */
    private function filesInserts()
    {
        $value = '';
        $value .= '/* INSERT VALUES IN '. TIMBER_DB_PREFIX . TIMBER_DB_FILES_TABLE .' TABLE */'. $this->newline;
        $inserts = \ORM::for_table(TIMBER_DB_PREFIX . TIMBER_DB_FILES_TABLE)->find_array();
        if( (is_array($inserts)) && (count($inserts) > 0) ){
            foreach ($inserts as $key => $insert) {
                $insert_values = array_values($insert);
                $escaped_insert_values = array();
                foreach ($insert_values as $single_value) {
                    $escaped_insert_values[] = addslashes($single_value);
                }
                $value .= 'INSERT INTO ' . TIMBER_DB_PREFIX . TIMBER_DB_FILES_TABLE . ' VALUES (\'' . implode("', '", $escaped_insert_values) . '\');' . $this->newline;
            }

        }
        return $value;
    }

    /**
     * Return invoices table
     *
     * @since 1.0
     * @access private
     * @return string
     */
    private function invoicesInserts()
    {
        $value = '';
        $value .= '/* INSERT VALUES IN '. TIMBER_DB_PREFIX . TIMBER_DB_INVOICES_TABLE .' TABLE */'. $this->newline;
        $inserts = \ORM::for_table(TIMBER_DB_PREFIX . TIMBER_DB_INVOICES_TABLE)->find_array();
        if( (is_array($inserts)) && (count($inserts) > 0) ){
            foreach ($inserts as $key => $insert) {
                $insert_values = array_values($insert);
                $escaped_insert_values = array();
                foreach ($insert_values as $single_value) {
                    $escaped_insert_values[] = addslashes($single_value);
                }
                $value .= 'INSERT INTO ' . TIMBER_DB_PREFIX . TIMBER_DB_INVOICES_TABLE . ' VALUES (\'' . implode("', '", $escaped_insert_values) . '\');' . $this->newline;
            }

        }
        return $value;
    }

    /**
     * Return items table
     *
     * @since 1.0
     * @access private
     * @return string
     */
    private function itemsInserts()
    {
        $value = '';
        $value .= '/* INSERT VALUES IN '. TIMBER_DB_PREFIX . TIMBER_DB_ITEMS_TABLE .' TABLE */'. $this->newline;
        $inserts = \ORM::for_table(TIMBER_DB_PREFIX . TIMBER_DB_ITEMS_TABLE)->find_array();
        if( (is_array($inserts)) && (count($inserts) > 0) ){
            foreach ($inserts as $key => $insert) {
                $insert_values = array_values($insert);
                $escaped_insert_values = array();
                foreach ($insert_values as $single_value) {
                    $escaped_insert_values[] = addslashes($single_value);
                }
                $value .= 'INSERT INTO ' . TIMBER_DB_PREFIX . TIMBER_DB_ITEMS_TABLE . ' VALUES (\'' . implode("', '", $escaped_insert_values) . '\');' . $this->newline;
            }

        }
        return $value;
    }

    /**
     * Return messages table
     *
     * @since 1.0
     * @access private
     * @return string
     */
    private function messagesInserts()
    {
        $value = '';
        $value .= '/* INSERT VALUES IN '. TIMBER_DB_PREFIX . TIMBER_DB_MESSAGES_TABLE .' TABLE */'. $this->newline;
        $inserts = \ORM::for_table(TIMBER_DB_PREFIX . TIMBER_DB_MESSAGES_TABLE)->find_array();
        if( (is_array($inserts)) && (count($inserts) > 0) ){
            foreach ($inserts as $key => $insert) {
                $insert_values = array_values($insert);
                $escaped_insert_values = array();
                foreach ($insert_values as $single_value) {
                    $escaped_insert_values[] = addslashes($single_value);
                }
                $value .= 'INSERT INTO ' . TIMBER_DB_PREFIX . TIMBER_DB_MESSAGES_TABLE . ' VALUES (\'' . implode("', '", $escaped_insert_values) . '\');' . $this->newline;
            }

        }
        return $value;
    }

    /**
     * Return metas table
     *
     * @since 1.0
     * @access private
     * @return string
     */
    private function metasInserts()
    {
        $value = '';
        $value .= '/* INSERT VALUES IN '. TIMBER_DB_PREFIX . TIMBER_DB_METAS_TABLE .' TABLE */'. $this->newline;
        $inserts = \ORM::for_table(TIMBER_DB_PREFIX . TIMBER_DB_METAS_TABLE)->find_array();
        if( (is_array($inserts)) && (count($inserts) > 0) ){
            foreach ($inserts as $key => $insert) {
                $insert_values = array_values($insert);
                $escaped_insert_values = array();
                foreach ($insert_values as $single_value) {
                    $escaped_insert_values[] = addslashes($single_value);
                }
                $value .= 'INSERT INTO ' . TIMBER_DB_PREFIX . TIMBER_DB_METAS_TABLE . ' VALUES (\'' . implode("', '", $escaped_insert_values) . '\');' . $this->newline;
            }

        }
        return $value;
    }

    /**
     * Return milestones table
     *
     * @since 1.0
     * @access private
     * @return string
     */
    private function milestonesInserts()
    {
        $value = '';
        $value .= '/* INSERT VALUES IN '. TIMBER_DB_PREFIX . TIMBER_DB_MILESTONES_TABLE .' TABLE */'. $this->newline;
        $inserts = \ORM::for_table(TIMBER_DB_PREFIX . TIMBER_DB_MILESTONES_TABLE)->find_array();
        if( (is_array($inserts)) && (count($inserts) > 0) ){
            foreach ($inserts as $key => $insert) {
                $insert_values = array_values($insert);
                $escaped_insert_values = array();
                foreach ($insert_values as $single_value) {
                    $escaped_insert_values[] = addslashes($single_value);
                }
                $value .= 'INSERT INTO ' . TIMBER_DB_PREFIX . TIMBER_DB_MILESTONES_TABLE . ' VALUES (\'' . implode("', '", $escaped_insert_values) . '\');' . $this->newline;
            }

        }
        return $value;
    }

    /**
     * Return options table
     *
     * @since 1.0
     * @access private
     * @return string
     */
    private function optionsInserts()
    {
        $value = '';
        $value .= '/* INSERT VALUES IN '. TIMBER_DB_PREFIX . TIMBER_DB_OPTIONS_TABLE .' TABLE */'. $this->newline;
        $inserts = \ORM::for_table(TIMBER_DB_PREFIX . TIMBER_DB_OPTIONS_TABLE)->find_array();
        if( (is_array($inserts)) && (count($inserts) > 0) ){
            foreach ($inserts as $key => $insert) {
                $insert_values = array_values($insert);
                $escaped_insert_values = array();
                foreach ($insert_values as $single_value) {
                    $escaped_insert_values[] = addslashes($single_value);
                }
                $value .= 'INSERT INTO ' . TIMBER_DB_PREFIX . TIMBER_DB_OPTIONS_TABLE . ' VALUES (\'' . implode("', '", $escaped_insert_values) . '\');' . $this->newline;
            }

        }
        return $value;
    }

    /**
     * Return projects table
     *
     * @since 1.0
     * @access private
     * @return string
     */
    private function projectsInserts()
    {
        $value = '';
        $value .= '/* INSERT VALUES IN '. TIMBER_DB_PREFIX . TIMBER_DB_PROJECTS_TABLE .' TABLE */'. $this->newline;
        $inserts = \ORM::for_table(TIMBER_DB_PREFIX . TIMBER_DB_PROJECTS_TABLE)->find_array();
        if( (is_array($inserts)) && (count($inserts) > 0) ){
            foreach ($inserts as $key => $insert) {
                $insert_values = array_values($insert);
                $escaped_insert_values = array();
                foreach ($insert_values as $single_value) {
                    $escaped_insert_values[] = addslashes($single_value);
                }
                $value .= 'INSERT INTO ' . TIMBER_DB_PREFIX . TIMBER_DB_PROJECTS_TABLE . ' VALUES (\'' . implode("', '", $escaped_insert_values) . '\');' . $this->newline;
            }

        }
        return $value;
    }

    /**
     * Return projects meta table
     *
     * @since 1.0
     * @access private
     * @return string
     */
    private function projectsMetaInserts()
    {
        $value = '';
        $value .= '/* INSERT VALUES IN '. TIMBER_DB_PREFIX . TIMBER_DB_PROJECTS_META_TABLE .' TABLE */'. $this->newline;
        $inserts = \ORM::for_table(TIMBER_DB_PREFIX . TIMBER_DB_PROJECTS_META_TABLE)->find_array();
        if( (is_array($inserts)) && (count($inserts) > 0) ){
            foreach ($inserts as $key => $insert) {
                $insert_values = array_values($insert);
                $escaped_insert_values = array();
                foreach ($insert_values as $single_value) {
                    $escaped_insert_values[] = addslashes($single_value);
                }
                $value .= 'INSERT INTO ' . TIMBER_DB_PREFIX . TIMBER_DB_PROJECTS_META_TABLE . ' VALUES (\'' . implode("', '", $escaped_insert_values) . '\');' . $this->newline;
            }

        }
        return $value;
    }

    /**
     * Return quotations table
     *
     * @since 1.0
     * @access private
     * @return string
     */
    private function quotationsInserts()
    {
        $value = '';
        $value .= '/* INSERT VALUES IN '. TIMBER_DB_PREFIX . TIMBER_DB_QUOTATIONS_TABLE .' TABLE */'. $this->newline;
        $inserts = \ORM::for_table(TIMBER_DB_PREFIX . TIMBER_DB_QUOTATIONS_TABLE)->find_array();
        if( (is_array($inserts)) && (count($inserts) > 0) ){
            foreach ($inserts as $key => $insert) {
                $insert_values = array_values($insert);
                $escaped_insert_values = array();
                foreach ($insert_values as $single_value) {
                    $escaped_insert_values[] = addslashes($single_value);
                }
                $value .= 'INSERT INTO ' . TIMBER_DB_PREFIX . TIMBER_DB_QUOTATIONS_TABLE . ' VALUES (\'' . implode("', '", $escaped_insert_values) . '\');' . $this->newline;
            }

        }
        return $value;
    }

    /**
     * Return subscriptions table
     *
     * @since 1.0
     * @access private
     * @return string
     */
    private function subscriptionsInserts()
    {
        $value = '';
        $value .= '/* INSERT VALUES IN '. TIMBER_DB_PREFIX . TIMBER_DB_SUBSCRIPTIONS_TABLE .' TABLE */'. $this->newline;
        $inserts = \ORM::for_table(TIMBER_DB_PREFIX . TIMBER_DB_SUBSCRIPTIONS_TABLE)->find_array();
        if( (is_array($inserts)) && (count($inserts) > 0) ){
            foreach ($inserts as $key => $insert) {
                $insert_values = array_values($insert);
                $escaped_insert_values = array();
                foreach ($insert_values as $single_value) {
                    $escaped_insert_values[] = addslashes($single_value);
                }
                $value .= 'INSERT INTO ' . TIMBER_DB_PREFIX . TIMBER_DB_SUBSCRIPTIONS_TABLE . ' VALUES (\'' . implode("', '", $escaped_insert_values) . '\');' . $this->newline;
            }

        }
        return $value;
    }

    /**
     * Return tasks table
     *
     * @since 1.0
     * @access private
     * @return string
     */
    private function tasksInserts()
    {
        $value = '';
        $value .= '/* INSERT VALUES IN '. TIMBER_DB_PREFIX . TIMBER_DB_TASKS_TABLE .' TABLE */'. $this->newline;
        $inserts = \ORM::for_table(TIMBER_DB_PREFIX . TIMBER_DB_TASKS_TABLE)->find_array();
        if( (is_array($inserts)) && (count($inserts) > 0) ){
            foreach ($inserts as $key => $insert) {
                $insert_values = array_values($insert);
                $escaped_insert_values = array();
                foreach ($insert_values as $single_value) {
                    $escaped_insert_values[] = addslashes($single_value);
                }
                $value .= 'INSERT INTO ' . TIMBER_DB_PREFIX . TIMBER_DB_TASKS_TABLE . ' VALUES (\'' . implode("', '", $escaped_insert_values) . '\');' . $this->newline;
            }

        }
        return $value;
    }

    /**
     * Return tickets table
     *
     * @since 1.0
     * @access private
     * @return string
     */
    private function ticketsInserts()
    {
        $value = '';
        $value .= '/* INSERT VALUES IN '. TIMBER_DB_PREFIX . TIMBER_DB_TICKETS_TABLE .' TABLE */'. $this->newline;
        $inserts = \ORM::for_table(TIMBER_DB_PREFIX . TIMBER_DB_TICKETS_TABLE)->find_array();
        if( (is_array($inserts)) && (count($inserts) > 0) ){
            foreach ($inserts as $key => $insert) {
                $insert_values = array_values($insert);
                $escaped_insert_values = array();
                foreach ($insert_values as $single_value) {
                    $escaped_insert_values[] = addslashes($single_value);
                }
                $value .= 'INSERT INTO ' . TIMBER_DB_PREFIX . TIMBER_DB_TICKETS_TABLE . ' VALUES (\'' . implode("', '", $escaped_insert_values) . '\');' . $this->newline;
            }

        }
        return $value;
    }

    /**
     * Return users table
     *
     * @since 1.0
     * @access private
     * @return string
     */
    private function usersInserts()
    {
        $value = '';
        $value .= '/* INSERT VALUES IN '. TIMBER_DB_PREFIX . TIMBER_DB_USERS_TABLE .' TABLE */'. $this->newline;
        $inserts = \ORM::for_table(TIMBER_DB_PREFIX . TIMBER_DB_USERS_TABLE)->find_array();
        if( (is_array($inserts)) && (count($inserts) > 0) ){
            foreach ($inserts as $key => $insert) {
                $insert_values = array_values($insert);
                $escaped_insert_values = array();
                foreach ($insert_values as $single_value) {
                    $escaped_insert_values[] = addslashes($single_value);
                }
                $value .= 'INSERT INTO ' . TIMBER_DB_PREFIX . TIMBER_DB_USERS_TABLE . ' VALUES (\'' . implode("', '", $escaped_insert_values) . '\');' . $this->newline;
            }

        }
        return $value;
    }

    /**
     * Return users meta table
     *
     * @since 1.0
     * @access private
     * @return string
     */
    private function usersMetaInserts()
    {
        $value = '';
        $value .= '/* INSERT VALUES IN '. TIMBER_DB_PREFIX . TIMBER_DB_USERS_META_TABLE .' TABLE */'. $this->newline;
        $inserts = \ORM::for_table(TIMBER_DB_PREFIX . TIMBER_DB_USERS_META_TABLE)->find_array();
        if( (is_array($inserts)) && (count($inserts) > 0) ){
            foreach ($inserts as $key => $insert) {
                $insert_values = array_values($insert);
                $escaped_insert_values = array();
                foreach ($insert_values as $single_value) {
                    $escaped_insert_values[] = addslashes($single_value);
                }
                $value .= 'INSERT INTO ' . TIMBER_DB_PREFIX . TIMBER_DB_USERS_META_TABLE . ' VALUES (\'' . implode("', '", $escaped_insert_values) . '\');' . $this->newline;
            }

        }
        return $value;
    }

    /**
     * Read file content
     *
     * @since 1.0
     * @access private
     * @return string|boolean
     */
    private function readFile($path)
    {
        if ( !(is_file($path)) || !(file_exists($path)) ) {
            return false;
        }
        if( !(is_readable($path)) ){
            @chmod($path, 0755);
        }
        $content = @file_get_contents($path);
        if( ($content === false) || (empty($content)) ){
            return false;
        }
        return $content;
    }

    /**
     * Write to txt file
     *
     * @since 1.0
     * @access private
     * @param string $content
     * @return boolean
     */
    private function writeFile($content)
    {
        if (  (is_file($this->fname)) || (file_exists($this->fname)) ) {
            return false;
        }

        $handle = @fopen($this->fname, 'w');
        @fwrite($handle, $content);
        @fclose($handle);
        return (boolean)(file_exists($this->fname));
    }

    /**
     * Write to .gz file
     *
     * @since 1.0
     * @access private
     * @param string $content
     * @return boolean
     */
    private function writeGzip($content)
    {
        if (  (is_file($this->fname)) || (file_exists($this->fname)) ) {
            return false;
        }

        $handle = @gzopen($this->fname, 'w9');
        @gzwrite($handle, $content);
        @gzclose($handle);
        return (boolean)(file_exists($this->fname));
    }
}