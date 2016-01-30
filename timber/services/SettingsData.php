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

namespace Timber\Services;

/**
 * Settings Data Services
 *
 * @since 1.0
 */
class SettingsData extends \Timber\Services\Base {

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
	 * Get Company Data
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function companyData()
	{
		$data = array();

		$data['c_form_action'] = $this->timber->config('request_url') . '/request/backend/ajax/settings/company';

		# Company Profile
		$data['c_site_title'] = $this->timber->config('_site_title');
		$data['c_site_description'] = $this->timber->config('_site_description');
		$data['c_site_logo'] = $this->timber->config('_site_logo');
		$data['c_site_country'] = $this->timber->config('_site_country');
		$data['c_site_city'] = $this->timber->config('_site_city');
		$data['c_site_address_line1'] = $this->timber->config('_site_address_line1');
		$data['c_site_address_line2'] = $this->timber->config('_site_address_line2');
		$data['c_site_vat_number'] = $this->timber->config('_site_vat_number');
		$data['c_site_phone'] = $this->timber->config('_site_phone');
		$data['c_site_currency'] = $this->timber->config('_site_currency');
		$data['c_site_currency_symbol'] = $this->timber->config('_site_currency_symbol');

		# In form of
		# array(
		# 	array('name' => 'VAT', 'value' => '10.00'),
		#	array('name' => 'EXT', 'value' => '20.00'),
		# )
		$data['c_site_tax_rates'] = unserialize($this->timber->config('_site_tax_rates'));
		$data['c_site_languages'] = $this->timber->translator->getLocales();
		$data['c_site_language'] = $this->timber->config('_site_lang');
		$data['time_zones'] = $this->timber->time->listIdentifiers();
		$data['c_site_timezone'] = $this->timber->config('_site_timezone');
		$data['c_site_email'] = $this->timber->config('_site_email');
		$data['c_emails_sender'] = $this->timber->config('_site_emails_sender');
		$data['c_maintenance_mode'] = $this->timber->config('_site_maintainance_mode');

		return $data;
	}

	/**
	 * Get Appearance Data
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function appearanceData()
	{
		$data = array();

		$data['a_form_action'] = $this->timber->config('request_url') . '/request/backend/ajax/settings/appearance';

		$data['a_site_gravatar'] = $this->timber->config('_default_gravatar');
		$data['a_gravatar_platform'] = $this->timber->config('_gravatar_platform');
		$data['a_custom_styles'] = $this->timber->config('_site_custom_styles');
		$data['a_custom_scripts'] = $this->timber->config('_site_custom_scripts');
		$data['a_google_analytics'] = $this->timber->config('_site_tracking_codes');

		$data['gravatars'] = array(
			array('id' => 'garvRadios1', 'src' => '/img/grav1.png', 'name' => 'a_garvRadios', 'value' => 'grav1'),
			array('id' => 'garvRadios2', 'src' => '/img/grav2.png', 'name' => 'a_garvRadios', 'value' => 'grav2'),
			array('id' => 'garvRadios3', 'src' => '/img/grav3.png', 'name' => 'a_garvRadios', 'value' => 'grav3'),
			array('id' => 'garvRadios4', 'src' => '/img/grav4.png', 'name' => 'a_garvRadios', 'value' => 'grav4'),
			array('id' => 'garvRadios5', 'src' => '/img/grav5.png', 'name' => 'a_garvRadios', 'value' => 'grav5'),
			array('id' => 'garvRadios6', 'src' => '/img/grav6.png', 'name' => 'a_garvRadios', 'value' => 'grav6'),
			array('id' => 'garvRadios7', 'src' => '/img/grav7.png', 'name' => 'a_garvRadios', 'value' => 'grav7'),
		);

		return $data;
	}

	/**
	 * Get Rules Data
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function rulesData()
	{
		$data = array();

		$data['r_form_action'] = $this->timber->config('request_url') . '/request/backend/ajax/settings/rules';

		# Caching Settings
		$data['r_site_caching'] = $this->timber->config('_site_caching');
		$data['r_site_caching'] = unserialize($data['r_site_caching']);
		$data['r_site_caching_status'] = $data['r_site_caching']['status'];
		$data['r_site_caching_purge_each'] = $data['r_site_caching']['purge_each'];
		$data['r_site_caching_last_run'] = $data['r_site_caching']['last_run'];

		# Crons Settings
		$data['r_crons_key'] = $this->timber->option_model->getOptionByKey('_cron_key');
		if( (false !== $data['r_crons_key']) && (is_object($data['r_crons_key'])) ){
			$data['r_crons_key'] = $data['r_crons_key']->as_array();
			$data['r_crons_key'] = $data['r_crons_key']['op_value'];
		}
		$data['r_crons_link'] = $this->timber->config('request_url') . '/crons/' . $data['r_crons_key'];

		# Upload Settings
		$allowed_extensions = unserialize($this->timber->config('_allowed_upload_extensions'));
		$allowed_extensions = implode('|', $allowed_extensions);
		$max_file_size = $this->timber->config('_max_upload_size');
		$data['r_allowed_upload_extensions'] = $allowed_extensions;
		$data['r_max_upload_size'] = $max_file_size;

		# Social Login
		$data['r_google_login_status'] = $this->timber->config('_google_login_status');
		$data['r_google_login_key'] = $this->timber->config('_google_login_app_key');
		$data['r_google_login_secret'] = $this->timber->config('_google_login_app_secret');
		$data['r_twitter_login_status'] = $this->timber->config('_twitter_login_status');
		$data['r_twitter_login_key'] = $this->timber->config('_twitter_login_app_key');
		$data['r_twitter_login_secret'] = $this->timber->config('_twitter_login_app_secret');
		$data['r_facebook_login_status'] = $this->timber->config('_facebook_login_status');
		$data['r_facebook_login_key'] = $this->timber->config('_facebook_login_app_id');
		$data['r_facebook_login_secret'] = $this->timber->config('_facebook_login_app_secret');

		# Access Permissions
        $data['r_client_permissions'] = unserialize($this->timber->config('_client_permissions'));
        $data['r_staff_permissions'] = unserialize($this->timber->config('_staff_permissions'));
        $data['r_all_client_permissions'] = $this->timber->access->getClientPermissions();
        $data['r_all_staff_permissions'] = $this->timber->access->getStaffPermissions();

        # SMTP Server Settings
        $smtp_server = $this->timber->option_model->getOptionByKey('_mailer_smtp_server');
        if( (false !== $smtp_server) && (is_object($smtp_server)) ){
            $smtp_server = $smtp_server->as_array();
            $smtp_server['op_value'] = unserialize($smtp_server['op_value']);

            $data['r_smtp_server_status'] = $smtp_server['op_value']['status'];
            $data['r_smtp_server_auth'] = $smtp_server['op_value']['auth'];
            $data['r_smtp_server_secure'] = $smtp_server['op_value']['secure'];
            $data['r_smtp_server_host'] = $smtp_server['op_value']['host'];
            $data['r_smtp_server_port'] = $smtp_server['op_value']['port'];
            $data['r_smtp_server_username'] = $smtp_server['op_value']['username'];
            $data['r_smtp_server_password'] = $smtp_server['op_value']['password'];
        }

		return $data;
	}

	/**
	 * Get Payments Data
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function paymentsData()
	{
		$data = array();

		$data['p_form_action'] = $this->timber->config('request_url') . '/request/backend/ajax/settings/payments';

		# Payments
		# array(status, account_number, country, swift_code, additional_data)
		$data['p_bank_transfer_details'] = unserialize($this->timber->config('_bank_transfer_details'));
		# array(status, username, password, signature, test_mode)
		$data['p_paypal_transfer_details'] = unserialize($this->timber->config('_paypal_details'));
		# array(status, client_api)
		$data['p_stripe_transfer_details'] = unserialize($this->timber->config('_stripe_details'));

		return $data;
	}

	/**
	 * Get Templates Data
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function templatesData()
	{
		$data = array();

		$data['t_form_action'] = $this->timber->config('request_url') . '/request/backend/ajax/settings/templates';

        # Email Templates
        $data['t_verify_email_tpl'] = $this->timber->option_model->getOptionValueByKey('_verify_email_tpl');
        $data['t_fpwd_tpl'] = $this->timber->option_model->getOptionValueByKey('_fpwd_tpl');
        $data['t_login_info_tpl'] = $this->timber->option_model->getOptionValueByKey('_login_info_tpl');
        $data['t_register_invite_tpl'] = $this->timber->option_model->getOptionValueByKey('_register_invite_tpl');
        $data['t_new_project_tpl'] = $this->timber->option_model->getOptionValueByKey('_new_project_tpl');
        $data['t_new_project_task_tpl'] = $this->timber->option_model->getOptionValueByKey('_new_project_task_tpl');
        $data['t_new_project_milestone_tpl'] = $this->timber->option_model->getOptionValueByKey('_new_project_milestone_tpl');
        $data['t_new_project_ticket_tpl'] = $this->timber->option_model->getOptionValueByKey('_new_project_ticket_tpl');
        $data['t_new_project_files_tpl'] = $this->timber->option_model->getOptionValueByKey('_new_project_files_tpl');
        $data['t_new_message_tpl'] = $this->timber->option_model->getOptionValueByKey('_new_message_tpl');
        $data['t_new_quotation_tpl'] = $this->timber->option_model->getOptionValueByKey('_new_quotation_tpl');
        $data['t_new_public_quotation_tpl'] = $this->timber->option_model->getOptionValueByKey('_new_public_quotation_tpl');
        $data['t_new_subscription_tpl'] = $this->timber->option_model->getOptionValueByKey('_new_subscription_tpl');
        $data['t_new_invoice_tpl'] = $this->timber->option_model->getOptionValueByKey('_new_invoice_tpl');
        $data['t_new_estimate_tpl'] = $this->timber->option_model->getOptionValueByKey('_new_estimate_tpl');

		return $data;
	}

	/**
	 * Get Backups Data
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function backupsData()
	{
		$data = array();

		$data['b_form_action'] = $this->timber->config('request_url') . '/request/backend/ajax/settings/backups';
		$data['b_backups_settings'] = $this->timber->option_model->getOptionByKey('_site_backup_settings');

		if( (false !== $data['b_backups_settings']) && (is_object($data['b_backups_settings'])) ){
			$data['b_backups_settings'] = $data['b_backups_settings']->as_array();
			$data['b_backups_settings'] = unserialize($data['b_backups_settings']['op_value']);
		}

		$data['b_backups_status'] = $data['b_backups_settings']['status'];
		$data['b_backups_interval'] = $data['b_backups_settings']['interval'];
		$data['b_backups_run'] = $data['b_backups_settings']['run'];
		$data['b_backups_compress'] = $data['b_backups_settings']['compress'];
		$data['b_backups_store'] = $data['b_backups_settings']['store'];

		$data['b_backups_performed'] = $this->timber->option_model->getOptionByKey('_site_backup_performed');
		$data['b_backups_performed_status'] = false;

		if( (false !== $data['b_backups_performed']) && (is_object($data['b_backups_performed'])) ){
			$data['b_backups_performed'] = $data['b_backups_performed']->as_array();
			$data['b_backups_performed'] = unserialize($data['b_backups_performed']['op_value']);
			$data['b_backups_performed_status'] = (count($data['b_backups_performed']) > 0) ? true : false;
		}

		return $data;
	}

	/**
	 * Get About Data
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function aboutData()
	{
		$data = array();

		$data['h_updates_data'] = $this->timber->config('_site_updates_settings');
		$data['h_updates_data'] = unserialize($data['h_updates_data']);
		$data['h_updates_current_version'] = TIMBER_CURRENT_VERSION;
		$data['h_updates_latest_version'] = $data['h_updates_data']['version'];
		$data['h_updates_need_updates'] = $this->timber->remote->needUpdate();
		$data['h_updates_last_check'] = $data['h_updates_data']['time'];

		$data['h_debug_report'] = $this->timber->debug->getReport();
		$data['h_debug_required_extensions'] = $data['h_debug_report']['required_extensions'];

		$data['h_debug_php_version'] = $data['h_debug_report']['php_version'];
		$data['h_debug_client_file'] = ($data['h_debug_report']['client_file']) ? true : false;
		$data['h_debug_db_connection'] = ($data['h_debug_report']['db_connection']) ? true : false;
		$data['h_debug_db_tables'] = ($data['h_debug_report']['db_tables']) ? true : false;
		$data['h_debug_app_installed'] = ($data['h_debug_report']['app_installed']) ? true : false;
		$data['h_debug_options'] = ($data['h_debug_report']['options_count_status']) ? true : false;
		$data['h_debug_admin_account'] = ($data['h_debug_report']['users_count_status'] && $data['h_debug_report']['users_meta_count_status']) ? true : false;

		$data['h_debug_app_health']  = $data['h_debug_client_file'];
		$data['h_debug_app_health'] &= $data['h_debug_app_installed'];
		$data['h_debug_app_health'] &= $data['h_debug_db_connection'];
		$data['h_debug_app_health'] &= $data['h_debug_db_tables'];
		$data['h_debug_app_health'] &= $data['h_debug_options'];
		$data['h_debug_app_health'] &= $data['h_debug_admin_account'];

		$data['settings_different_action'] = $this->timber->config('request_url') . '/request/backend/ajax/settings/actions';

		return $data;
	}

	/**
	 * Get Uploader Info
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function uploaderInfo()
	{
		$data = array();

		# $allowed_extensions = unserialize($this->timber->config('_allowed_upload_extensions'));
		# $allowed_extensions = implode(', ', $allowed_extensions);
		$allowed_extensions = "png, jpg, gif";
		$max_file_size = $this->timber->config('_max_upload_size');

		$data['page_uploaders'][] = array(
			'id' => 'uploadModal',
			'title' => $this->timber->translator->trans('Media Uploader'),
			'description' => sprintf( $this->timber->translator->trans('Allowed extensions (%1$s) and Max file size (%2$s MB).'), $allowed_extensions , $max_file_size ),
			'uploader_id' => 'dropzone_uploader',
			'uploader_class' => 'dropzone',
			'field_id' => 'uploader_files',
			'field_name' => 'uploaded_files',
		);

		return $data;
	}

	/**
	 * Get Current User Data
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function currentUserData()
	{
		$data = array();
		$user_id = $this->timber->security->getId();
		$user = $this->timber->user_model->getUserById($user_id);

		if( (false === $user) || !(is_object($user)) ){
			$this->timber->security->endSession();
			$this->timber->redirect( $this->timber->config('request_url') . '/500' );
		}

		$user = $user->as_array();
		foreach ($user as $key => $value) {
			$data['current_user_' . $key] = $value;
		}

		return $data;
	}
}