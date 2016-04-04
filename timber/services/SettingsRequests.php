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

namespace Timber\Services;

/**
 * Settings Requests Services
 *
 * @since 1.0
 */
class SettingsRequests extends \Timber\Services\Base {

    /**
     * Class Constructor
     *
     * @since 1.0
     * @access public
     * @param object $timber
     */
    public function __construct($timber)
    {
        parent::__construct($timber);
    }

    /**
     * Update Company Data
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function updateCompanyData()
    {
        $company_settings = $this->timber->validator->clear(array(
            'c_site_title' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:2,50',
                'default' => '',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('The site name is invalid.'),
                    'vstrlenbetween' => $this->timber->translator->trans('The site name is too short.')
                )
            ),
            'c_site_description'=> array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vstrlenbetween:0,500',
                'default' => '',
                'errors' => array(
                    'vstrlenbetween' => $this->timber->translator->trans('Site description must be short and descriptive.'),
                ),
            ),
            'c_site_logo' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vfile:png,jpg,gif',
                'default' => '',
                'errors' => array()
            ),
            'c_site_country' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vcountry',
                'default' => 'US',
                'errors' => array()
            ),
            'c_site_city' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:0,150',
                'default' => '',
                'errors' => array()
            ),
            'c_site_address_line1' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:0,250',
                'default' => '',
                'errors' => array()
            ),
            'c_site_address_line2' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:0,250',
                'default' => '',
                'errors' => array()
            ),
            'c_site_vat_number' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:0,150',
                'default' => '',
                'errors' => array()
            ),
            'c_site_phone' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:0,150',
                'default' => '',
                'errors' => array()
            ),
            'c_site_currency' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vcurrency',
                'default' => 'USD',
                'errors' => array(
                    'vcurrency' => $this->timber->translator->trans('Provided currency is invalid.'),
                )
            ),
            'c_site_currency_symbol' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:1,6',
                'default' => '$',
                'errors' => array(
                    'vstrlenbetween' => $this->timber->translator->trans('Provided currency symbol is invalid.'),
                )
            ),
            'c_site_tax_rates' => array(
                'req' => 'post',
                'sanit' => 'stax',
                'valid' => 'vtax',
                'default' => serialize(array()),
                'errors' => array(
                    'vtax' => $this->timber->translator->trans('Provided tax rates are invalid.'),
                )
            ),
            'c_site_language'=> array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vlocale',
                'default' => 'en_US',
                'errors' => array(),
            ),
            'c_site_timezone'=> array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vtimezone',
                'default' => 'America/New_York',
                'errors' => array(),
            ),
            'c_maintenance_mode'=> array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vinarray:on,off',
                'default' => 'off',
                'errors' => array(),
            ),
            'c_site_email' => array(
                'req' => 'post',
                'sanit' => 'semail',
                'valid' => 'vnotempty&vemail',
                'default' => '',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('The site email must not be empty.'),
                    'vemail' => $this->timber->translator->trans('The site email is invalid.')
                )
            ),
            'c_emails_sender' => array(
                'req' => 'post',
                'sanit' => 'semail',
                'valid' => 'vnotempty&vemail',
                'default' => '',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('The email sender must not be empty.'),
                    'vemail' => $this->timber->translator->trans('The email sender is invalid.')
                )
            ),
        ));

        if( true === $company_settings['error_status'] ){
            $this->response['data'] = $company_settings['error_text'];
            return false;
        }

        # Store site logo
        if( $company_settings['c_site_logo']['value'] != '' ){
            $file_data = explode('--||--', $company_settings['c_site_logo']['value']);

            $file_id = $this->timber->file_model->addFile(array(
                'title' => $file_data[1],
                'hash' => $file_data[0],
                'owner_id' => $this->timber->security->getId(),
                'description' => "Site Logo",
                'storage' => 1,
                'type' => pathinfo($file_data[1], PATHINFO_EXTENSION),
                'uploaded_at' => $this->timber->time->getCurrentDate(true),
            ));

            $company_settings['c_site_logo']['value'] = $file_id;

            # Delete Old One
            if( $this->timber->config('_site_logo') > 0 ){
                $this->timber->file_model->deleteFileById($this->timber->config('_site_logo'));
            }
        }

        //update
        $options_updated_values = array(
            array('op_key' => '_site_title', 'op_value' => $company_settings['c_site_title']['value']),
            array('op_key' => '_site_description', 'op_value' => $company_settings['c_site_description']['value']),
            array('op_key' => '_site_logo', 'op_value' => $company_settings['c_site_logo']['value']),
            array('op_key' => '_site_lang', 'op_value' => $company_settings['c_site_language']['value']),
            array('op_key' => '_site_timezone', 'op_value' => $company_settings['c_site_timezone']['value']),
            array('op_key' => '_site_maintainance_mode', 'op_value' => $company_settings['c_maintenance_mode']['value']),
            array('op_key' => '_site_email', 'op_value' => $company_settings['c_site_email']['value']),
            array('op_key' => '_site_emails_sender', 'op_value' => $company_settings['c_emails_sender']['value']),
            array('op_key' => '_site_country', 'op_value' => $company_settings['c_site_country']['value']),
            array('op_key' => '_site_city', 'op_value' => $company_settings['c_site_city']['value']),
            array('op_key' => '_site_address_line1', 'op_value' => $company_settings['c_site_address_line1']['value']),
            array('op_key' => '_site_address_line2', 'op_value' => $company_settings['c_site_address_line2']['value']),
            array('op_key' => '_site_vat_number', 'op_value' => $company_settings['c_site_vat_number']['value']),
            array('op_key' => '_site_phone', 'op_value' => $company_settings['c_site_phone']['value']),
            array('op_key' => '_site_currency', 'op_value' => $company_settings['c_site_currency']['value']),
            array('op_key' => '_site_currency_symbol', 'op_value' => $company_settings['c_site_currency_symbol']['value']),
            array('op_key' => '_site_tax_rates', 'op_value' => serialize($company_settings['c_site_tax_rates']['value'])),
        );

        $update_status = true;

        foreach ($options_updated_values as $option_updated_value) {
            if( ( $option_updated_value['op_key'] == '_site_logo' ) && ( $option_updated_value['op_value'] == '' ) ){ continue; }
            $update_status &= (boolean) $this->timber->option_model->updateOptionByKey($option_updated_value);
        }

        if( $update_status ){
            $this->response['status'] = 'success';
            $this->response['data'] = $this->timber->translator->trans('Settings updated successfully.');
            return true;
        }else{
            $this->response['data'] = $this->timber->translator->trans('Internal Server Error! Please try again later.');
            return false;
        }
    }

    /**
     * Update Appearance Data
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function updateAppearanceData()
    {
        $appearance_settings = $this->timber->validator->clear(array(
            'a_site_gravatar' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vgrav',
                'default' => 'grav1',
                'errors' => array(),
            ),
            'a_gravatar_platform' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vinarray:gravatar,native',
                'default' => 'gravatar',
                'errors' => array(),
            ),
            'a_custom_styles' => array(
                'req' => 'post',
                'sanit' => '',
                'valid' => 'vstrlenbetween:0,6000',
                'default' => '',
                'errors' => array(
                    'vstrlenbetween' => $this->timber->translator->trans('Custom styles are too long. Add to stylesheet file instead.')
                ),
            ),
            'a_custom_scripts' => array(
                'req' => 'post',
                'sanit' => '',
                'valid' => 'vstrlenbetween:0,6000',
                'default' => '',
                'errors' => array(
                    'vstrlenbetween' => $this->timber->translator->trans('Custom scripts are too long. Add to scripts file instead.')
                ),
            ),
            'a_google_analytics' => array(
                'req' => 'post',
                'sanit' => '',
                'valid' => 'vstrlenbetween:0,6000',
                'default' => '',
                'errors' => array(
                    'vstrlenbetween' => $this->timber->translator->trans('Google analytics are too long. Add to scripts file instead.')
                ),
            ),
        ));

        if( true === $appearance_settings['error_status'] ){
            $this->response['data'] = $appearance_settings['error_text'];
            return false;
        }

        //update
        $options_updated_values = array(
            array('op_key' => '_default_gravatar', 'op_value' => $appearance_settings['a_site_gravatar']['value']),
            array('op_key' => '_gravatar_platform', 'op_value' => $appearance_settings['a_gravatar_platform']['value']),
            array('op_key' => '_site_custom_styles', 'op_value' => $appearance_settings['a_custom_styles']['value']),
            array('op_key' => '_site_custom_scripts', 'op_value' => $appearance_settings['a_custom_scripts']['value']),
            array('op_key' => '_site_tracking_codes', 'op_value' => $appearance_settings['a_google_analytics']['value']),
        );

        $update_status = true;
        foreach ($options_updated_values as $option_updated_value) {
            $update_status &= (boolean) $this->timber->option_model->updateOptionByKey($option_updated_value);
        }

        if( $update_status ){
            $this->response['status'] = 'success';
            $this->response['data'] = $this->timber->translator->trans('Settings updated successfully.');
            return true;
        }else{
            $this->response['data'] = $this->timber->translator->trans('Internal Server Error! Please try again later.');
            return false;
        }
    }

    /**
     * Update Rules Data
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function updateRulesData()
    {
        $rules_settings = $this->timber->validator->clear(array(
            'r_google_login_status'=> array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vinarray:on,off',
                'default' => 'off',
                'errors' => array(),
            ),
            'r_google_login_key' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:5,150',
                'default' => '',
                'errors' => array()
            ),
            'r_google_login_secret' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:5,150',
                'default' => '',
                'errors' => array()
            ),
            'r_twitter_login_status'=> array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vinarray:on,off',
                'default' => 'off',
                'errors' => array(),
            ),
            'r_twitter_login_key' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:5,150',
                'default' => '',
                'errors' => array()
            ),
            'r_twitter_login_secret' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:5,150',
                'default' => '',
                'errors' => array()
            ),
            'r_facebook_login_status'=> array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vinarray:on,off',
                'default' => 'off',
                'errors' => array(),
            ),
            'r_facebook_login_key' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:5,150',
                'default' => '',
                'errors' => array()
            ),
            'r_facebook_login_secret' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:5,150',
                'default' => '',
                'errors' => array()
            ),
            'r_allowed_upload_extensions' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:2,50',
                'default' => 'PNG|JPG|PDF|DOC',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('Allowed Extensions has invalid value.'),
                    'vstrlenbetween' => $this->timber->translator->trans('Allowed Extensions has invalid value.'),
                )
            ),
            'r_max_upload_size' => array(
                'req' => 'post',
                'sanit' => 'snumberint',
                'valid' => 'vnotempty&vint',
                'default' => '2',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('Max. File Size has invalid value.'),
                    'vint' => $this->timber->translator->trans('Max. File Size has invalid value.'),
                ),
            ),
            'r_site_caching_status' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vinarray:on,off',
                'default' => 'off',
                'errors' => array(),
            ),
            'r_site_caching_purge_each' => array(
                'req' => 'post',
                'sanit' => 'snumberint',
                'valid' => 'vnotempty&vint',
                'default' => '7',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('Purge cache field has invalid value.'),
                    'vint' => $this->timber->translator->trans('Purge cache field has invalid value.'),
                ),
            ),
            'r_staff_permissions' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty',
                'default' => array(),
                'errors' => array(),
            ),
            'r_client_permissions' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty',
                'default' => array(),
                'errors' => array(),
            ),

            'r_smtp_server_status' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vinarray:on,off',
                'default' => 'off',
                'errors' => array(),
            ),
            'r_smtp_server_auth' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vinarray:on,off',
                'default' => 'off',
                'errors' => array(),
            ),
            'r_smtp_server_secure'=> array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty',
                'default' => 'ssl',
                'errors' => array(),
            ),
            'r_smtp_server_host'=> array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty',
                'default' => 'smtp.gmail.com',
                'errors' => array(),
            ),
            'r_smtp_server_port'=> array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty',
                'default' => '465',
                'errors' => array(),
            ),
            'r_smtp_server_username'=> array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty',
                'default' => 'email@gmail.com',
                'errors' => array(),
            ),
            'r_smtp_server_password'=> array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty',
                'default' => '',
                'errors' => array(),
            ),
        ));

        if( true === $rules_settings['error_status'] ){
            $this->response['data'] = $rules_settings['error_text'];
            return false;
        }

        if( $rules_settings['r_google_login_status']['value'] == 'on' ){
            if( !($rules_settings['r_google_login_key']['status']) || !($rules_settings['r_google_login_secret']['status']) ){
                $this->response['data'] = $this->timber->translator->trans('You must provide google application data.');
                return false;
            }
        }

        if( $rules_settings['r_twitter_login_status']['value'] == 'on' ){
            if( !($rules_settings['r_twitter_login_key']['status']) || !($rules_settings['r_twitter_login_secret']['status']) ){
                $this->response['data'] = $this->timber->translator->trans('You must provide twitter application data.');
                return false;
            }
        }

        if( $rules_settings['r_facebook_login_status']['value'] == 'on' ){
            if( !($rules_settings['r_facebook_login_key']['status']) || !($rules_settings['r_facebook_login_secret']['status']) ){
                $this->response['data'] = $this->timber->translator->trans('You must provide facebook application data.');
                return false;
            }
        }

        //get site caching data to store the saved value of purge caching last run
        $site_caching_data = $this->timber->config('_site_caching');
        $site_caching_data = unserialize($site_caching_data);

        if( !is_array($rules_settings['r_client_permissions']['value']) ){
            $rules_settings['r_client_permissions']['value'] = empty($rules_settings['r_client_permissions']['value']) ? array() : explode(',', $rules_settings['r_client_permissions']['value']);
        }

        if( !is_array($rules_settings['r_staff_permissions']['value']) ){
            $rules_settings['r_staff_permissions']['value'] = empty($rules_settings['r_staff_permissions']['value']) ? array() : explode(',', $rules_settings['r_staff_permissions']['value']);
        }

        $rules_settings['r_allowed_upload_extensions']['value'] = empty($rules_settings['r_allowed_upload_extensions']['value']) ? array('PNG', 'JPG', 'PDF', 'DOC') : explode('|', $rules_settings['r_allowed_upload_extensions']['value']);

        //update
        $options_updated_values = array(
            array('op_key' => '_google_login_status', 'op_value' => $rules_settings['r_google_login_status']['value']),
            array('op_key' => '_google_login_app_key', 'op_value' => $rules_settings['r_google_login_key']['value']),
            array('op_key' => '_google_login_app_secret', 'op_value' => $rules_settings['r_google_login_secret']['value']),
            array('op_key' => '_twitter_login_status', 'op_value' => $rules_settings['r_twitter_login_status']['value']),
            array('op_key' => '_twitter_login_app_key', 'op_value' => $rules_settings['r_twitter_login_key']['value']),
            array('op_key' => '_twitter_login_app_secret', 'op_value' => $rules_settings['r_twitter_login_secret']['value']),
            array('op_key' => '_facebook_login_status', 'op_value' => $rules_settings['r_facebook_login_status']['value']),
            array('op_key' => '_facebook_login_app_id', 'op_value' => $rules_settings['r_facebook_login_key']['value']),
            array('op_key' => '_facebook_login_app_secret', 'op_value' => $rules_settings['r_facebook_login_secret']['value']),
            array('op_key' => '_allowed_upload_extensions', 'op_value' => serialize($rules_settings['r_allowed_upload_extensions']['value'])),
            array('op_key' => '_max_upload_size', 'op_value' => $rules_settings['r_max_upload_size']['value']),
            array('op_key' => '_site_caching', 'op_value' => serialize(array('status' => $rules_settings['r_site_caching_status']['value'], 'purge_each' => $rules_settings['r_site_caching_purge_each']['value'], 'last_run' => $site_caching_data['last_run'] )) ),
            array('op_key' => '_client_permissions', 'op_value' => serialize($rules_settings['r_client_permissions']['value']) ),
            array('op_key' => '_staff_permissions', 'op_value' => serialize($rules_settings['r_staff_permissions']['value']) ),
            array('op_key' => '_mailer_smtp_server', 'op_value' => serialize(array( 'status' => $rules_settings['r_smtp_server_status']['value'], 'auth' => $rules_settings['r_smtp_server_auth']['value'], 'secure' => $rules_settings['r_smtp_server_secure']['value'], 'host' => $rules_settings['r_smtp_server_host']['value'], 'port' => $rules_settings['r_smtp_server_port']['value'], 'username' => $rules_settings['r_smtp_server_username']['value'], 'password' => $rules_settings['r_smtp_server_password']['value']))),
        );

        $update_status = true;
        foreach ($options_updated_values as $option_updated_value) {
            $update_status &= (boolean) $this->timber->option_model->updateOptionByKey($option_updated_value);
        }

        if( $update_status ){
            $this->response['status'] = 'success';
            $this->response['data'] = $this->timber->translator->trans('Settings updated successfully.');
            return true;
        }else{
            $this->response['data'] = $this->timber->translator->trans('Internal Server Error! Please try again later.');
            return false;
        }
    }

    /**
     * Update Backups Data
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function updateBackupsData()
    {
        $backups_settings = $this->timber->validator->clear(array(
            'b_backups_status' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vinarray:on,off',
                'default' => 'off',
                'errors' => array(),
            ),
            'b_backups_compress' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vinarray:on,off',
                'default' => 'off',
                'errors' => array(),
            ),
            'b_backups_interval' => array(
                'req' => 'post',
                'sanit' => 'snumberint',
                'valid' => 'vnotempty&vint',
                'default' => '7',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('Backups interval field has invalid value.'),
                    'vint' => $this->timber->translator->trans('Backups interval field has invalid value.'),
                ),
            ),
            'b_backups_store' => array(
                'req' => 'post',
                'sanit' => 'snumberint',
                'valid' => 'vnotempty&vint',
                'default' => '10',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('Backups store field has invalid value.'),
                    'vint' => $this->timber->translator->trans('Backups store field has invalid value.'),
                ),
            ),
        ));

        if( true === $backups_settings['error_status'] ){
            $this->response['data'] = $backups_settings['error_text'];
            return false;
        }

        //get site backups settings to store last run
        $site_backups_data = $this->timber->option_model->getOptionByKey('_site_backup_settings');
        $site_backups_data = $site_backups_data->as_array();
        $site_backups_data = unserialize($site_backups_data['op_value']);

        //update
        $options_updated_values = array(
            array('op_key' => '_site_backup_settings', 'op_value' => serialize(array('status' => $backups_settings['b_backups_status']['value'], 'run' => $site_backups_data['run'], 'interval' => $backups_settings['b_backups_interval']['value'] , 'compress' => $backups_settings['b_backups_compress']['value'], 'store' => $backups_settings['b_backups_store']['value'])))
        );

        $update_status = true;
        foreach ($options_updated_values as $option_updated_value) {
            $update_status &= (boolean) $this->timber->option_model->updateOptionByKey($option_updated_value);
        }

        if( $update_status ){
            $this->response['status'] = 'success';
            $this->response['data'] = $this->timber->translator->trans('Settings updated successfully.');
            return true;
        }else{
            $this->response['data'] = $this->timber->translator->trans('Internal Server Error! Please try again later.');
            return false;
        }
    }

    /**
     * Update templates data
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function updateTemplatesData()
    {
        $template_settings = $this->timber->validator->clear(array(
            't_verify_email_tpl' => array(
                'req' => 'post',
                'sanit' => '',
                'valid' => 'vstrlenbetween:0,6000',
                'default' => '',
                'errors' => array(
                    'vstrlenbetween' => $this->timber->translator->trans('Verify Email template is too long.')
                ),
            ),
            't_fpwd_tpl' => array(
                'req' => 'post',
                'sanit' => '',
                'valid' => 'vstrlenbetween:0,6000',
                'default' => '',
                'errors' => array(
                    'vstrlenbetween' => $this->timber->translator->trans('Forgot password template is too long.')
                ),
            ),
            't_login_info_tpl' => array(
                'req' => 'post',
                'sanit' => '',
                'valid' => 'vstrlenbetween:0,6000',
                'default' => '',
                'errors' => array(
                    'vstrlenbetween' => $this->timber->translator->trans('Login info template is too long.')
                ),
            ),
            't_register_invite_tpl' => array(
                'req' => 'post',
                'sanit' => '',
                'valid' => 'vstrlenbetween:0,6000',
                'default' => '',
                'errors' => array(
                    'vstrlenbetween' => $this->timber->translator->trans('Register invite template is too long.')
                ),
            ),
            't_new_project_tpl' => array(
                'req' => 'post',
                'sanit' => '',
                'valid' => 'vstrlenbetween:0,6000',
                'default' => '',
                'errors' => array(
                    'vstrlenbetween' => $this->timber->translator->trans('New project template is too long.')
                ),
            ),
            't_new_project_task_tpl' => array(
                'req' => 'post',
                'sanit' => '',
                'valid' => 'vstrlenbetween:0,6000',
                'default' => '',
                'errors' => array(
                    'vstrlenbetween' => $this->timber->translator->trans('New project task template is too long.')
                ),
            ),
            't_new_project_milestone_tpl' => array(
                'req' => 'post',
                'sanit' => '',
                'valid' => 'vstrlenbetween:0,6000',
                'default' => '',
                'errors' => array(
                    'vstrlenbetween' => $this->timber->translator->trans('New project milestone template is too long.')
                ),
            ),
            't_new_project_ticket_tpl' => array(
                'req' => 'post',
                'sanit' => '',
                'valid' => 'vstrlenbetween:0,6000',
                'default' => '',
                'errors' => array(
                    'vstrlenbetween' => $this->timber->translator->trans('New project ticket template is too long.')
                ),
            ),
            't_new_project_files_tpl' => array(
                'req' => 'post',
                'sanit' => '',
                'valid' => 'vstrlenbetween:0,6000',
                'default' => '',
                'errors' => array(
                    'vstrlenbetween' => $this->timber->translator->trans('New project file template is too long.')
                ),
            ),
            't_new_message_tpl' => array(
                'req' => 'post',
                'sanit' => '',
                'valid' => 'vstrlenbetween:0,6000',
                'default' => '',
                'errors' => array(
                    'vstrlenbetween' => $this->timber->translator->trans('New message template is too long.')
                ),
            ),
            't_new_quotation_tpl' => array(
                'req' => 'post',
                'sanit' => '',
                'valid' => 'vstrlenbetween:0,6000',
                'default' => '',
                'errors' => array(
                    'vstrlenbetween' => $this->timber->translator->trans('New quotation template is too long.')
                ),
            ),
            't_new_public_quotation_tpl' => array(
                'req' => 'post',
                'sanit' => '',
                'valid' => 'vstrlenbetween:0,6000',
                'default' => '',
                'errors' => array(
                    'vstrlenbetween' => $this->timber->translator->trans('New public quotation template is too long.')
                ),
            ),
            't_new_subscription_tpl' => array(
                'req' => 'post',
                'sanit' => '',
                'valid' => 'vstrlenbetween:0,6000',
                'default' => '',
                'errors' => array(
                    'vstrlenbetween' => $this->timber->translator->trans('New subscription template is too long.')
                ),
            ),
            't_new_invoice_tpl' => array(
                'req' => 'post',
                'sanit' => '',
                'valid' => 'vstrlenbetween:0,6000',
                'default' => '',
                'errors' => array(
                    'vstrlenbetween' => $this->timber->translator->trans('New invoice template is too long.')
                ),
            ),
            't_new_estimate_tpl' => array(
                'req' => 'post',
                'sanit' => '',
                'valid' => 'vstrlenbetween:0,6000',
                'default' => '',
                'errors' => array(
                    'vstrlenbetween' => $this->timber->translator->trans('New Estimate template is too long.')
                ),
            ),
        ));

        if( true === $template_settings['error_status'] ){
            $this->response['data'] = $template_settings['error_text'];
            return false;
        }


        //update
        $options_updated_values = array(
            array('op_key' => '_fpwd_tpl', 'op_value' => $template_settings['t_fpwd_tpl']['value']),
            array('op_key' => '_verify_email_tpl', 'op_value' => $template_settings['t_verify_email_tpl']['value']),
            array('op_key' => '_login_info_tpl', 'op_value' => $template_settings['t_login_info_tpl']['value']),
            array('op_key' => '_register_invite_tpl', 'op_value' => $template_settings['t_register_invite_tpl']['value']),
            array('op_key' => '_new_project_tpl', 'op_value' => $template_settings['t_new_project_tpl']['value']),
            array('op_key' => '_new_project_task_tpl', 'op_value' => $template_settings['t_new_project_task_tpl']['value']),
            array('op_key' => '_new_project_milestone_tpl', 'op_value' => $template_settings['t_new_project_milestone_tpl']['value']),
            array('op_key' => '_new_project_ticket_tpl', 'op_value' => $template_settings['t_new_project_ticket_tpl']['value']),
            array('op_key' => '_new_project_files_tpl', 'op_value' => $template_settings['t_new_project_files_tpl']['value']),
            array('op_key' => '_new_message_tpl', 'op_value' => $template_settings['t_new_message_tpl']['value']),
            array('op_key' => '_new_quotation_tpl', 'op_value' => $template_settings['t_new_quotation_tpl']['value']),
            array('op_key' => '_new_public_quotation_tpl', 'op_value' => $template_settings['t_new_public_quotation_tpl']['value']),
            array('op_key' => '_new_subscription_tpl', 'op_value' => $template_settings['t_new_subscription_tpl']['value']),
            array('op_key' => '_new_invoice_tpl', 'op_value' => $template_settings['t_new_invoice_tpl']['value']),
            array('op_key' => '_new_estimate_tpl', 'op_value' => $template_settings['t_new_estimate_tpl']['value']),
        );

        $update_status = true;
        foreach ($options_updated_values as $option_updated_value) {
            $update_status &= (boolean) $this->timber->option_model->updateOptionByKey($option_updated_value);
        }

        if( $update_status ){
            $this->response['status'] = 'success';
            $this->response['data'] = $this->timber->translator->trans('Settings updated successfully.');
            return true;
        }else{
            $this->response['data'] = $this->timber->translator->trans('Internal Server Error! Please try again later.');
            return false;
        }
    }

    /**
     * Update payments data
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function updatePaymentsData()
    {
        $payment_settings = $this->timber->validator->clear(array(
            'p_bank_details_status' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vinarray:on,off',
                'default' => 'off',
                'errors' => array(),
            ),
            'p_bank_details_account_number' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:1,200',
                'default' => '',
                'errors' => array()
            ),
            'p_bank_details_country' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:1,60',
                'default' => '',
                'errors' => array()
            ),
            'p_bank_details_swift_code' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:1,60',
                'default' => '',
                'errors' => array()
            ),
            'p_bank_details_additional_data' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:1,6000',
                'default' => '',
                'errors' => array()
            ),
            'p_paypal_details_status' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vinarray:on,off',
                'default' => 'off',
                'errors' => array(),
            ),
            'p_paypal_details_username' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:1,200',
                'default' => '',
                'errors' => array()
            ),
            'p_paypal_details_password' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:1,200',
                'default' => '',
                'errors' => array()
            ),
            'p_paypal_details_signature' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:1,200',
                'default' => '',
                'errors' => array()
            ),
            'p_paypal_details_test_mode' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vinarray:on,off',
                'default' => 'off',
                'errors' => array(),
            ),
            'p_stripe_details_status' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vinarray:on,off',
                'default' => 'off',
                'errors' => array(),
            ),
            'p_stripe_details_client_api' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:1,200',
                'default' => '',
                'errors' => array()
            ),
        ));

        if( true === $payment_settings['error_status'] ){
            $this->response['data'] = $payment_settings['error_text'];
            return false;
        }

        $bank_details = array(
            'status' => $payment_settings['p_bank_details_status']['value'],
            'account_number' => $payment_settings['p_bank_details_account_number']['value'],
            'country' => $payment_settings['p_bank_details_country']['value'],
            'swift_code' => $payment_settings['p_bank_details_swift_code']['value'],
            'additional_data' => $payment_settings['p_bank_details_additional_data']['value'],
        );
        $paypal_details = array(
            'status' => $payment_settings['p_paypal_details_status']['value'],
            'username' => $payment_settings['p_paypal_details_username']['value'],
            'password' => $payment_settings['p_paypal_details_password']['value'],
            'signature' => $payment_settings['p_paypal_details_signature']['value'],
            'test_mode' => ($payment_settings['p_paypal_details_test_mode']['value'] == 'on') ? true : false,
        );
        $stripe_details = array(
            'status' => $payment_settings['p_stripe_details_status']['value'],
            'client_api' => $payment_settings['p_stripe_details_client_api']['value'],
        );

        //update
        $options_updated_values = array(
            array('op_key' => '_bank_transfer_details', 'op_value' => serialize($bank_details)),
            array('op_key' => '_paypal_details', 'op_value' => serialize($paypal_details)),
            array('op_key' => '_stripe_details', 'op_value' => serialize($stripe_details)),
        );

        $update_status = true;
        foreach ($options_updated_values as $option_updated_value) {
            $update_status &= (boolean) $this->timber->option_model->updateOptionByKey($option_updated_value);
        }

        if( $update_status ){
            $this->response['status'] = 'success';
            $this->response['data'] = $this->timber->translator->trans('Settings updated successfully.');
            return true;
        }else{
            $this->response['data'] = $this->timber->translator->trans('Internal Server Error! Please try again later.');
            return false;
        }
    }

    /**
     * Perform different actions
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function performActions()
    {
        $this->performCronRefresh();
        $this->performBackup();
    }

    /**
     * Perform Cron Refresh
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function performCronRefresh()
    {
        if( !(isset($_POST['action'])) || !($_POST['action'] == 'cron_refresh')  ){
            return false;
        }

        $hash = $this->timber->faker->randHash(20);
        $update_status = (boolean) $this->timber->option_model->updateOptionByKey(array(
            'op_key' => '_cron_key',
            'op_value' => $hash
        ));
        if( $update_status ){
            $this->response['status'] = 'success';
            $this->response['data'] = $this->timber->config('request_url') . '/crons/' . $hash;
            return true;
        }else{
            $this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
            return false;
        }
    }

    /**
     * Perform Backups
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function performBackup()
    {
        if( !(isset($_POST['action'])) || !($_POST['action'] == 'force_backup')  ){
            return false;
        }

        $backup_status = (boolean) $this->timber->backup->executeSchedule(true);

        if( $backup_status ){
            $this->response['status'] = 'success';
            $this->response['data'] = $this->timber->translator->trans('Backup created successfully.');
            return true;
        }else{
            $this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
            return false;
        }
    }
}