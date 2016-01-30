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
 * Install Controller
 *
 * @since 1.0
 */
class Install {

    /**
     * Data to be used in template
     *
     * @since 1.0
     * @access private
     * @var array $this->data
     */
    private $data = array();

    /**
     * A list of app configs
     *
     * @since 1.0
     * @access private
     * @var array $this->configs
     */
    private $configs = array(
        'TIMBER_HOME_URL__' => '',
        'TIMBER_DB_DRIVER__' => TIMBER_DB_DRIVER,
        'TIMBER_DB_HOST__' => '',
        'TIMBER_DB_NAME__' => '',
        'TIMBER_DB_USER__' => '',
        'TIMBER_DB_PWD__' => '',
        'TIMBER_DB_CHARSET__' => TIMBER_DB_CHARSET,
        'TIMBER_DB_PREFIX__' => TIMBER_DB_PREFIX,
        'TIMBER_DB_PORT__' => TIMBER_DB_PORT,
        'ENCRYPT_LEVEL__' => '1',
        'STAFF_AUTH_SALT__' => '',
        'ADMINS_AUTH_SALT__' => '',
        'CLIENTS_AUTH_SALT__' => '',
        'APP_PUB_SALT__' => '',
        'RANDOM_HASH__' => '',
        'TIMBER_ADMIN_IDEN__' => '',
        'TIMBER_STAFF_IDEN__' => '',
        'TIMBER_CLIENT_IDEN__' => '',
    );

    /**
     * Default response
     *
     * @since 1.0
     * @access private
     * @var array $this->response
     */
    private $response = array(
        'status' => 'error',
        'data' => ''
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
        $this->timber->filter->setDepen($timber)->config('installer');
        return $this;
    }

    /**
     * Render install tpl according to install step
     *
     * @since 1.0
     * @access public
     */
    public function render()
    {
        if( !($this->clientExist()) || !($this->dbConnected()) ){
            return $this->timber->render( 'install', $this->getData(1, '25%') );
        }
        if( ($this->dbConnected()) && !($this->tablesExist()) ){
            # Create Most Tables to Reduce Load Time
            $this->initApp(array(), true);
            return $this->timber->render( 'install', $this->getData(2, '50%') );
        }
        if( !($this->appInstalled()) ){
            return $this->timber->render( 'install', $this->getData(3, '75%') );
        }
        # app installed
        $this->timber->redirect( $this->timber->config('request_url') . '/login' );
    }

    /**
     * Bind and get data
     *
     * @since 1.0
     * @access private
     * @return array
     */
    private function getData($step, $bar_step)
    {
        $this->data['server_info'] = $this->bindServerInfo();
        $this->data['site_title'] = $this->timber->translator->trans("Install | Timber");
        $this->data['step'] = $step;
        $this->data['bar_step'] = $bar_step;
        $this->data['bar_step_value'] = trim($bar_step, '%');

        $this->data['footer_scripts'] = "jQuery(document).ready(function($) { ";
        $this->data['footer_scripts'] .= "timber.utils.init();";
        $this->data['footer_scripts'] .=  "timber.install.init();";
        $this->data['footer_scripts'] .= " });";
        return $this->data;
    }

    /**
     * Perform all install requests according to step
     *
     * @since 1.0
     * @access public
     * @return string
     */
    public function requests()
    {
        if( !($this->clientExist()) || !($this->dbConnected()) ){
            $this->configAppClient();
            $this->getResponse();
        }

        if( ($this->dbConnected()) && !($this->tablesExist()) ){
            $this->configAppOptions();
            $this->getResponse();
        }

        if( !($this->appInstalled()) ){
            $this->configAppAdmin();
            $this->getResponse();
        }
        $this->timber->redirect( $this->timber->config('request_url') . '/404' );
    }

    /**
     * Render filters
     *
     * @since 1.0
     * @access public
     */
    public function renderFilters()
    {
        if( TIMBER_INSTALLED ){
            $this->timber->redirect( $this->timber->config('request_url') . '/login' );
        }
        $this->timber->filter->issueRun();
        $this->timber->filter->configLibs();
    }

    /**
     * Request filters
     *
     * @since 1.0
     * @access public
     */
    public function requestFilters()
    {
        if( TIMBER_INSTALLED || !($this->timber->request->isAjax()) ){
            $this->timber->redirect( $this->timber->config('request_url') . '/404' );
        }
        $this->timber->filter->issueRun();
        $this->timber->filter->configLibs();
    }

    /**
     * Init client file
     *
     * @since 1.0
     * @access private
     * @return boolean
     */
    private function configAppClient()
    {
        $install_data = $this->timber->validator->clear(array(
            'mysql_host_name' => array(
                'req'=> 'post',
                'sanit'=> 'sstring',
                'valid'=> 'vnotempty',
                'default'=>'',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('The MySQL host name is invalid.')
                )
            ),
            'mysql_db_name' => array(
                'req'=> 'post',
                'sanit'=> 'sstring',
                'valid'=> 'vnotempty',
                'default'=>'',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('The MySQL database name is invalid.')
                )
            ),
            'mysql_db_username' => array(
                'req'=> 'post',
                'sanit'=> 'sstring',
                'valid'=> 'vnotempty',
                'default'=>'',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('The MySQL username is invalid.')
                )
            ),
            'mysql_db_pwd' => array(
                'req'=> 'post',
                'sanit'=> 'sstring',
                'valid'=> 'vnotempty',
                'default'=>''
            ),
            'app_home_url' => array(
                'req'=> 'post',
                'sanit'=> 'surl',
                'valid'=> 'vnotempty&vurl',
                'default'=>'',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('The Home URL is invalid.'),
                    'vurl' => $this->timber->translator->trans('The Home URL is invalid.'),
                )
            ),
        ));
        if(true === $install_data['error_status']){
            $this->response['data'] = $install_data['error_text'];
            return false;
        }
        $this->configs['TIMBER_HOME_URL__'] = $install_data['app_home_url']['value'];
        $this->configs['TIMBER_DB_HOST__'] = $install_data['mysql_host_name']['value'];
        $this->configs['TIMBER_DB_NAME__'] = $install_data['mysql_db_name']['value'];
        $this->configs['TIMBER_DB_USER__'] = $install_data['mysql_db_username']['value'];
        $this->configs['TIMBER_DB_PWD__'] = $install_data['mysql_db_pwd']['value'];

        $this->configs['STAFF_AUTH_SALT__'] = $this->timber->faker->randSalt(18);
        $this->configs['ADMINS_AUTH_SALT__'] = $this->timber->faker->randSalt(18);
        $this->configs['CLIENTS_AUTH_SALT__'] = $this->timber->faker->randSalt(18);

        $this->configs['APP_PUB_SALT__'] = $this->timber->faker->randSalt(18);
        $this->configs['RANDOM_HASH__'] = $this->timber->faker->randSalt(18);

        $this->configs['TIMBER_ADMIN_IDEN__'] = $this->timber->faker->randHash(4);
        $this->configs['TIMBER_STAFF_IDEN__'] = $this->timber->faker->randHash(4);
        $this->configs['TIMBER_CLIENT_IDEN__'] = $this->timber->faker->randHash(4);

        /*$this->configs['ENCRYPT_LEVEL__'] = ( !extension_loaded('mcrypt') ) ? 1 : 2;*/
        $this->configs['ENCRYPT_LEVEL__'] = 1;

        if( $this->initClient() ){
            $this->response['status'] = 'success';
            $this->response['data'] = $this->timber->translator->trans('The Client File created successfully.');
            return true;
        }else{
            $this->response['data'] = $this->timber->translator->trans('Error during creating client file, Try to make it manually. Read Documentation for more info!');
            return false;
        }
    }

    /**
     * Init app options
     *
     * @since 1.0
     * @access private
     * @return boolean
     */
    private function configAppOptions()
    {
        $install_data = $this->timber->validator->clear(array(
            'site_name' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:2,50',
                'default' => '',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('The site name is invalid.'),
                    'vstrlenbetween' => $this->timber->translator->trans('The site name is too short.')
                )
            ),
            'site_email' => array(
                'req' => 'post',
                'sanit' => 'semail',
                'valid' => 'vnotempty&vemail',
                'default' => '',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('The site email is invalid.'),
                    'vemail' => $this->timber->translator->trans('The site email is invalid.')
                )
            ),
        ));

        if(true === $install_data['error_status']){
            $this->response['data'] = $install_data['error_text'];
            return false;
        }

        $app_data = array(
            'site_name' => $install_data['site_name']['value'],
            'site_email' => $install_data['site_email']['value'],
        );

        if($this->initApp($app_data)){
            $this->response['status'] = 'success';
            $this->response['data'] = $this->timber->translator->trans('The Site configured successfully.');
            return true;
        }else{
            $this->response['data'] = $this->timber->translator->trans('Error during configuring site.');
            return false;
        }
    }

    /**
     * Init admin account
     *
     * @since 1.0
     * @access private
     * @return boolean
     */
    private function configAppAdmin()
    {

        $install_data = $this->timber->validator->clear(array(
            'admin_username' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:5,20&vusername',
                'default' => '',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('The username must not be empty.'),
                    'vstrlenbetween' => $this->timber->translator->trans('The username must have a lenght of five or more.'),
                    'vusername' => $this->timber->translator->trans('The username must have only letters and numbers.')
                )
            ),
            'admin_email' => array(
                'req' => 'post',
                'sanit' => 'semail',
                'valid' => 'vnotempty&vemail',
                'default' => '',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('The email must not be empty.'),
                    'vemail' => $this->timber->translator->trans('The email is invalid.')
                )
            ),
            'admin_pwd' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:8,20&vpassword',
                'default' => '',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('The password must not be empty.'),
                    'vstrlenbetween' => $this->timber->translator->trans('The password must have a lenght of eight or more.'),
                    'vpassword' => $this->timber->translator->trans('The password must contain at least two numbers and only valid special chars.')
                )
            ),
            'admin_pwd_config' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:8,20&vpassword',
                'default' => '',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('The verify password must match password.'),
                    'vstrlenbetween' => $this->timber->translator->trans('The verify password must match password.'),
                    'vpassword' => $this->timber->translator->trans('The verify password must match password.')
                )
            )
        ));

        if(true === $install_data['error_status']){
            $this->response['data'] = $install_data['error_text'];
            return false;
        }

        if($install_data['admin_pwd_config']['value'] != $install_data['admin_pwd']['value']){
            $this->response['data'] = $this->timber->translator->trans('Passwords must match.');
            return false;
        }

        $admin_data = array(
            'admin_username' => $install_data['admin_username']['value'],
            'admin_email' => $install_data['admin_email']['value'],
            'admin_pwd' => $install_data['admin_pwd']['value'],
        );

        if( $this->initAdmin($admin_data) ){
            $this->response['status'] = 'success';
            $this->response['data'] = $this->timber->translator->trans('Awesome! You did it. Login now!');
            $this->blockInstall();
            return true;
        }else{
            $this->response['data'] = $this->timber->translator->trans('Error during configuring admin account.');
            return false;
        }
    }

    /**
     * Get response
     *
     * @since 1.0
     * @access private
     * @return string
     */
    private function getResponse()
    {
        header('Content: application/json');
        echo json_encode($this->response);
        die();
    }

    /**
     * Create client file with user defined settings
     *
     * @since 1.0
     * @access private
     * @return boolean
     */
    private function initClient()
    {
        $client_sample_file = TIMBER_ROOT . '/timber/client-sample.php';
        $client_file = TIMBER_ROOT . '/timber/client.php';

        if( !(is_file($client_sample_file)) || !(file_exists($client_sample_file)) ){
            return false;
        }

        if( !(is_readable($client_sample_file)) ){
            @chmod($client_sample_file, 0755);
        }

        $file_content = @file_get_contents($client_sample_file);
        if( ($file_content === false) || (empty($file_content)) ){
            return false;
        }

        $file_content = str_replace(array_keys($this->configs), array_values($this->configs), $file_content);

        if( (is_file($client_file)) && (file_exists($client_file)) ){
            if( !(is_readable($client_file)) || !(is_writable($client_file)) ){
                @chmod($client_file, 0755);
            }
        }

        $handle = @fopen($client_file, 'w');
        @fwrite($handle, $file_content);
        @fclose($handle);
        return (boolean) (file_exists($client_file));
    }

    /**
     * Run migrations and init app options
     *
     * @since 1.0
     * @access private
     * @param array $site_data
     * @return boolean
     */
    private function initApp($site_data, $tables_only = false)
    {
        if( $tables_only ){
            //run migrations
            \Timber\Database\Migrations\Files::instance()->dump()->run();
            \Timber\Database\Migrations\Invoices::instance()->dump()->run();
            \Timber\Database\Migrations\Items::instance()->dump()->run();
            \Timber\Database\Migrations\Messages::instance()->dump()->run();
            \Timber\Database\Migrations\Metas::instance()->dump()->run();
            \Timber\Database\Migrations\Milestones::instance()->dump()->run();
            \Timber\Database\Migrations\Projects::instance()->dump()->run();
            \Timber\Database\Migrations\ProjectsMeta::instance()->dump()->run();
            \Timber\Database\Migrations\Quotations::instance()->dump()->run();
            \Timber\Database\Migrations\Subscriptions::instance()->dump()->run();
            \Timber\Database\Migrations\Tasks::instance()->dump()->run();
            \Timber\Database\Migrations\Tickets::instance()->dump()->run();
            \Timber\Database\Migrations\Users::instance()->dump()->run();
            \Timber\Database\Migrations\UsersMeta::instance()->dump()->run();
            return true;
        }

        \Timber\Database\Migrations\Options::instance()->dump()->run();

        //add options
        $options = $this->timber->option_model->addOptions(array(
            array(
                'op_key' => '_site_title',
                'op_value' => $site_data['site_name'],
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_site_description',
                'op_value' => 'Timber is a Full-Featured Freelancer Platform. It comes with many features to extend both Appearance and Functionality.',
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_site_logo',
                'op_value' => '0',
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_site_country',
                'op_value' => '',
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_site_city',
                'op_value' => '',
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_site_address_line1',
                'op_value' => '',
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_site_address_line2',
                'op_value' => '',
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_site_vat_number',
                'op_value' => '',
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_site_phone',
                'op_value' => '',
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_site_currency',
                'op_value' => 'USD',
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_site_currency_symbol',
                'op_value' => '$',
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_site_tax_rates',
                'op_value' => serialize(array(
                    array('name' => 'VAT', 'value' => '10.00'),
                    array('name' => 'EXT', 'value' => '20.00'),
                )),
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_site_lang',
                'op_value' => 'en_US',
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_site_timezone',
                'op_value' => 'America/New_York',
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_site_email',
                'op_value' => $site_data['site_email'],
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_site_emails_sender',
                'op_value' => 'no_reply@timber.com',
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_site_maintainance_mode',
                'op_value' => 'off',
                'autoload' => 'on',
            ),

            # Appearance Settings
            array(
                'op_key' => '_default_gravatar',
                'op_value' => 'grav1',
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_gravatar_platform',
                'op_value' => 'gravatar',
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_site_custom_styles',
                'op_value' => '',
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_site_custom_scripts',
                'op_value' => '',
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_site_tracking_codes',
                'op_value' => '',
                'autoload' => 'on',
            ),


            # Rules Settings
            array(
                'op_key' => '_site_caching',
                'op_value' => serialize(array('status' => 'off', 'purge_each' => '7', 'last_run' => time() )),
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_cron_key',
                'op_value' => $this->timber->faker->randHash(20),
                'autoload' => 'off',
            ),
            array(
                'op_key' => '_google_login_status',
                'op_value' => 'off',
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_google_login_app_key',
                'op_value' => '',
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_google_login_app_secret',
                'op_value' => '',
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_twitter_login_status',
                'op_value' => 'off',
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_twitter_login_app_key',
                'op_value' => '',
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_twitter_login_app_secret',
                'op_value' => '',
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_facebook_login_status',
                'op_value' => 'off',
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_facebook_login_app_id',
                'op_value' => '',
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_facebook_login_app_secret',
                'op_value' => '',
                'autoload' => 'on',
            ),


            # Payments Settings
            array(
                'op_key' => '_bank_transfer_details',
                'op_value' => serialize(array(
                    'status' => 'on',
                    'account_number' =>  '',
                    'country' => '',
                    'swift_code' => '',
                    'additional_data' => '',
                )),
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_paypal_details',
                'op_value' => serialize(array(
                    'status' => 'on',
                    'username' => 'info_api1.clivern.com',
                    'password' => 'NWWGVKFEUY7CKYBM',
                    'signature' => 'AFcWxV21C7fd0v3bYYYRCpSSRl31AAIyxSGoFGfqwupzcxPwbqwytLuT',
                    'test_mode' => true,
                )),
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_stripe_details',
                'op_value' => serialize(array(
                    'status' => 'off',
                    'client_api' => '',
                )),
                'autoload' => 'on',
            ),

            # Template Settings
            array(
                'op_key' => '_verify_email_tpl',
                'op_value' => 'Dear {$full_name},<br><br>Please visit the following URL <{$verify_url}> to verify your email.<br><br>Thanks,<br>Timber Team.',
                'autoload' => 'off',
            ),
            array(
                'op_key' => '_fpwd_tpl',
                'op_value' => 'Dear {$full_name},<br><br>Please visit the following URL <{$fpwd_url}> to change your password.<br><br>Thanks,<br>Timber Team.',
                'autoload' => 'off',
            ),
            array(
                'op_key' => '_login_info_tpl',
                'op_value' => 'Dear {$full_name},<br><br>You are invited to join Timber platform so Please use the following data to access your account:<br>Login URL: <{$login_url}><br>Email: {$email}<br>Username: {$user_name}<br>Password: {$password}<br><br>Thanks,<brTimber Team.',
                'autoload' => 'off',
            ),
            array(
                'op_key' => '_register_invite_tpl',
                'op_value' => 'Dear {$full_name},<br><br>You are invited to join Timber platform so Please visit the following URL <{$register_url}> to create your account.<br><br>Thanks,<br>Timber Team.',
                'autoload' => 'off',
            ),
            array(
                'op_key' => '_new_project_tpl',
                'op_value' => 'Dear {$full_name},<br><br>Now you are a member in {$project_title} project. Here is some informations about this project:<br>Reference: {$project_ref_id}<br>Budget: {$project_budget} {$site_currency}<br>Start Date: {$project_start_at}<br>End Date: {$project_end_at}<br><br>You can login from this URL <{$login_url}><br><br>Thanks,<br>Timber Team.',
                'autoload' => 'off',
            ),
            array(
                'op_key' => '_new_project_task_tpl',
                'op_value' => 'Dear {$full_name},<br><br>There is some updates to {$project_title} project. These is a new task assigned to {$assign_full_name}. You can get some info about this task:<br>Project Reference ID: {$project_ref_id}<br>Task: {$task_title}<br>Start Date: {$task_start_at}<br>End Date: {$task_end_at}<br><br>You can login from this URL <{$login_url}><br><br>Thanks,<br>Timber Team.',
                'autoload' => 'off',
            ),
            array(
                'op_key' => '_new_project_milestone_tpl',
                'op_value' => 'Dear {$full_name},<br><br>There is some updates to {$project_title} project. These is a new milestone. You can get some info about this milestone:<br>Project Reference ID: {$project_ref_id}<br>Milestone: {$milestone_title}<br>You can login from this URL <{$login_url}><br><br>Thanks,<br>Timber Team.',
                'autoload' => 'off',
            ),
            array(
                'op_key' => '_new_project_ticket_tpl',
                'op_value' => 'Dear {$full_name},<br><br>There is some updates to {$project_title} project. These is a new ticket opened. You can get some info about this ticket:<br>Project Reference ID: {$project_ref_id}<br>Subject: {$ticket_subject}<br>Reference: {$ticket_reference}<br>Opened By: {$opened_by_full_name}<{$opened_by_email}><br><br>You can login from this URL <{$login_url}><br><br>Thanks,<br>Timber Team.',
                'autoload' => 'off',
            ),
            array(
                'op_key' => '_new_project_files_tpl',
                'op_value' => 'Dear {$full_name},<br><br>There is some updates to {$project_title} project. These is a new file shared. You can get some info about this file:<br>Project Reference ID: {$project_ref_id}<br>File Name: {$file_name}<br>Uploaded By: {$uploaded_by_full_name}<{$uploaded_by_email}><br><br>You can login from this URL <{$login_url}><br><br>Thanks,<br>Timber Team.',
                'autoload' => 'off',
            ),
            array(
                'op_key' => '_new_message_tpl',
                'op_value' => 'Dear {$to_full_name},<br><br>{$from_full_name} sent you a message. You can login from here <{$login_url}> to check this message.<br><br>Thanks,<br>Timber Team.',
                'autoload' => 'off',
            ),
            array(
                'op_key' => '_new_quotation_tpl',
                'op_value' => 'Dear {$full_name},<br><br>Timber team send you a quotation. Here is some info about this quotation:<br>Title: {$quotation_title}<br>Reference ID: {$quotation_ref_id}<br><br>You can login from here <{$login_url}> to submit this quotation.<br><br>Thanks,<br>Timber Team.',
                'autoload' => 'off',
            ),
            array(
                'op_key' => '_new_public_quotation_tpl',
                'op_value' => 'Dear,<br><br>Timber team send you a quotation. Here is some info about this quotation:<br>Title: {$quotation_title}<br>Reference ID: {$quotation_ref_id}<br><br>You can submit the quotation from here <{$quotation_url}><br><br>Thanks,<br>Timber Team.',
                'autoload' => 'off',
            ),
            array(
                'op_key' => '_new_subscription_tpl',
                'op_value' => 'Dear {$full_name},<br><br>A new subscription attached to your profile. You can get some info about this subscription here:<br>Reference ID: {$subscription_ref_id}<br>Total: {$subscription_total} {$site_currency}<br>Start Date: {$subscription_begin_at}<br>End Date: {$subscription_end_at}<br><br>You can login from this URL <{$login_url}><br><br>Thanks,<br>Timber Team.',
                'autoload' => 'off',
            ),
            array(
                'op_key' => '_new_invoice_tpl',
                'op_value' => 'Dear {$full_name},<br><br>A new invoice attached to your profile. You can get some info about this invoice here:<br>Reference ID: {$invoice_ref_id}<br>Total: {$invoice_total} {$site_currency}<br>Issue Date: {$invoice_issue_date}<br>Due Date: {$invoice_due_date}<br><br>You can login from this URL <{$login_url}><br><br>Thanks,<br>Timber Team.',
                'autoload' => 'off',
            ),
            array(
                'op_key' => '_new_estimate_tpl',
                'op_value' => 'Dear {$full_name},<br><br>A new estimate attached to your profile. You can get some info about this estimate here:<br>Reference ID: {$estimate_ref_id}<br>Total: {$estimate_total} {$site_currency}<br>Issue Date: {$estimate_issue_date}<br>Due Date: {$estimate_due_date}<br><br>You can login from this URL <{$login_url}><br><br>Thanks,<br>Timber Team.',
                'autoload' => 'off',
            ),

            # Backups Settings
            array(
                'op_key' => '_site_backup_settings',
                'op_value' => serialize(array('status' => 'off','run' => time(), 'interval' => '7' , 'compress' => 'off', 'store' => 10)),
                'autoload' => 'off',
            ),
            array(
                'op_key' => '_site_backup_performed',
                'op_value' => serialize(array()),
                'autoload' => 'off',
            ),

            # Theme Settings
            array(
                'op_key' => '_site_theme',
                'op_value' => 'default',
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_site_skin',
                'op_value' => 'default',
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_google_font',
                'op_value' => 'Montserrat',
                'autoload' => 'on',
            ),

            # Internal Settings
            array(
                'op_key' => '_site_updates_settings',
                'op_value' => serialize( array('version' => TIMBER_CURRENT_VERSION, 'time' => time()) ),
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_themes_data',
                'op_value' => serialize(array()),
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_plugins_data',
                'op_value' => serialize(array()),
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_active_plugins',
                'op_value' => serialize(array()),
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_db_reset_key',
                'op_value' => $this->timber->faker->randHash(20) . time(),
                'autoload' => 'off',
            ),



            array(
                'op_key' => '_site_date_format',
                'op_value' => 'Y-m-d',
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_site_time_format',
                'op_value' => 'H:i:s',
                'autoload' => 'on',
            ),

            array(
                'op_key' => '_max_upload_size',
                'op_value' => '2',
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_allowed_upload_extensions',
                'op_value' => serialize(array('PNG', 'JPG', 'PDF', 'DOC')),
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_client_permissions',
                'op_value' => serialize(array(
                    'project.stats',
                    'project.files',
                    'project.tickets',
                    'project.tasks',
                    'project.milestones',
                    'project.members',
                    'message.staff',
                    'message.clients'
                )),
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_staff_permissions',
                'op_value' => serialize(array(
                    'add.invoices',
                    'edit.invoices',
                    'view.invoices',
                    'list.invoices',
                    'add.clients',
                    'edit.clients',
                    'view.clients',
                    'add.expenses',
                    'edit.expenses',
                    'view.expenses',
                    'list.expenses',
                    'add.estimates',
                    'edit.estimates',
                    'view.estimates',
                    'list.estimates',
                    'add.quotations',
                    'view.quotations',
                    'submit.quotations',
                    'list.quotations',
                    'add.items',
                    'edit.items',
                    'list.items',
                    'add.subscriptions',
                    'edit.subscriptions',
                    'view.subscriptions',
                    'list.subscriptions'
                )),
                'autoload' => 'on',
            ),
            array(
                'op_key' => '_mailer_smtp_server',
                'op_value' => serialize(array(
                    'status' => 'off',
                    'auth' => 'on',
                    'secure' => 'ssl',
                    'host' => 'smtp.gmail.com',
                    'port' => 465,
                    'username' => 'email@gmail.com',
                    'password' => ''
                )),
                'autoload' => 'off',
            ),

        ));

        return (boolean) ($options);
    }

    /**
     * Init admin account
     *
     * @since 1.0
     * @access private
     * @param array $admin_data
     * @return boolean
     */
    private function initAdmin($admin_data)
    {
        $user_id = $this->timber->user_model->addUser(array(
            'user_name' => $admin_data['admin_username'],
            'first_name' => '',
            'last_name' => '',
            'email' => $admin_data['admin_email'],
            'password' => $this->timber->hasher->HashPassword($admin_data['admin_pwd']),
            'identifier' => '',
            'auth_by' => '1',
            'access_rule' => '1',
            'sec_hash' => $this->timber->faker->randHash(20),
            'status' => '1',
            'website' => '',
            'phone_num' => '',
            'zip_code' => '',
            'vat_nubmer' => '',
            'language' => 'en_US',
            'job' => '',
            'grav_id' => '0',
            'country' => '',
            'company' => '',
            'city' => '',
            'address1' => '',
            'address2' => '',
            'created_at' => $this->timber->time->getCurrentDate(true),
            'updated_at' => $this->timber->time->getCurrentDate(true),
        ));
        $nonce_meta = $this->timber->user_meta_model->addMeta(array(
            'us_id' => $user_id,
            'me_key' => '_user_access_nonce',
            'me_value' => serialize(array(
                'n' => $this->timber->faker->randHash(10),
                't' => $this->timber->time->getCurrentDate(true),
            )),
        ));

        $install_option = $this->timber->option_model->addOption(array(
            'op_key' => '_timber_install',
            'op_value' => 'over',
            'autoload' => 'on',
        ));
        return (boolean)($nonce_meta && $install_option);
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
     * Check if app tables exist
     *
     * @since 1.0
     * @access private
     * @return boolean
     */
    private function tablesExist()
    {
        $tables = \ORM::get_db()->query('SHOW TABLES');
        if( !is_object($tables) ){
            return false;
        }
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
     * Check if app installed
     *
     * @since 1.0
     * @access private
     * @return boolean
     */
    private function appInstalled()
    {
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
     * Bind Server info
     *
     * @since 1.0
     * @access private
     */
    private function bindServerInfo()
    {
        $this->data['debug_report'] = $this->timber->debug->getReport();
        $this->data['debug_required_extensions'] = $this->data['debug_report']['required_extensions'];
        $this->data['debug_php_version'] = $this->data['debug_report']['php_version'];
        $this->data['debug_client_file'] = ($this->data['debug_report']['client_file']) ? true : false;
        $this->data['debug_db_connection'] = ($this->data['debug_report']['db_connection']) ? true : false;
        $this->data['debug_db_tables'] = ($this->data['debug_report']['db_tables']) ? true : false;
        $this->data['debug_app_installed'] = ($this->data['debug_report']['app_installed']) ? true : false;
        $this->data['debug_options'] = ($this->data['debug_report']['options_count_status']) ? true : false;
        $this->data['debug_admin_account'] = ($this->data['debug_report']['users_count_status'] && $this->data['debug_report']['users_meta_count_status']) ? true : false;
    }

    /**
     * Block timber installation process
     *
     * @since 1.0
     * @access private
     * @return boolean
     */
    private function blockInstall()
    {
        $client_file = TIMBER_ROOT . '/timber/client.php';

        if( (is_file($client_file)) && (file_exists($client_file)) ){
            if( !(is_readable($client_file)) || !(is_writable($client_file)) ){
                @chmod($client_file, 0755);
            }
        }else{
            return false;
        }
        $file_content = "\n\n/**\n * Whether timber installed\n * \n * @since 1.0\n * @var string\n */\n define('TIMBER_INSTALLED', true);";
        $handle = @fopen($client_file, 'a');
        @fwrite($handle, $file_content);
        @fclose($handle);
        return (boolean) (file_exists($client_file));
    }
}