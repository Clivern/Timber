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

namespace Timber\Libraries;

/**
 * Send Custom Emails to App Users
 *
 * @since 1.0
 */
class Mailer {

	/**
	 * App name added to file header
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->app_name
	 */
	private $app_name = 'Timber';

	/**
	 * App email added to file header
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->app_email
	 */
	private $app_email = 'no_reply@timber.com';

	/**
	 * App version added to file header
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->app_version
	 */
	private $app_version = TIMBER_CURRENT_VERSION;

	/**
	 * Email client object
	 *
	 * @since 1.0
	 * @access private
	 * @var object $this->email_client
	 */
	private $email_client;

	/**
	 * SMTP server info
	 *
	 * @since 1.0
	 * @access private
	 * @var array $this->smtp_server
	 */
	private $smtp_server = array(
		'debug' => 3,
		'status' => 'off',
		'auth' => 'on',
		'secure' => 'ssl',
		'host' => 'smtp.gmail.com',
		'port' => 465,
		'username' => 'email@gmail.com',
		'password' => ''
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
		return $this;
	}

	/**
	 * Set app name to site name
	 *
	 * @since 1.0
	 * @access public
	 */
	public function config()
	{
		$this->app_name = $this->timber->config('_site_title');
		$this->app_email = $this->timber->config('_site_emails_sender');
	}

	/**
	 * Everything done here, email settings set, check if values valid and send email
	 *
	 * @since 1.0
	 * @access public
	 * @param string $subject
	 * @param mixed $to_name
	 * @param mixed $to_email
	 * @param mixed $user_id
	 * @param mixed $tpl_key
	 * @param mixed $filters
	 * @param mixed $message
	 * @return boolean
	 */
	public function mail($subject, $to_name = false, $to_email = false, $user_id = false, $tpl_key = false, $filters = false, $message = false)
	{
		if( !(function_exists('mail')) ){
			return false;
		}

		$smtp_server = $this->timber->option_model->getOptionByKey('_mailer_smtp_server');

		// Get message or template and apply filters
		if( ($message === false) && ($tpl_key !== false) && ($filters !== false) ){
			$tpl = $this->timber->option_model->getOptionByKey($tpl_key);

			if( (false !== $smtp_server) && (is_object($smtp_server)) ){
				$smtp_server = $smtp_server->as_array();
				$this->smtp_server =  unserialize($smtp_server['op_value']);
			}else{
				return false;
			}

			if( (false !== $tpl) && (is_object($tpl)) ){
				$tpl = $tpl->as_array();
				$tpl = $tpl['op_value'];
			}else{
				return false;
			}

			$message = str_replace(array_keys($filters), array_values($filters), $tpl);
		}elseif( ($message !== false) && ($tpl_key === false) && ($filters === false) ){
			$message = $message;
		}else{
			return false;
		}

		// Get username and email
		if( ($user_id !== false) && ($to_email === false) && ($to_name === false) ){

			$user_data = $this->timber->user_model->getUserById($user_id);

			if( (false !== $user_data) && (is_object($user_data)) ){
				$user_data = $user_data->as_array();
			}else{
				return false;
			}
			$to_name = (!empty($user_data['first_name']) || !empty($user_data['last_name'])) ? $user_data['first_name'] . " " . $user_data['last_name'] : $user_data['user_name'];
			$to_email = $user_data['email'];
		}elseif( ($user_id === false) && ($to_email !== false) && ($to_name !== false) ){
			$to_name = $to_name;
			$to_email = $to_email;
		}else{
			return false;
		}

		// Init mailer class and configure the instance
		$this->email_client = new \PHPMailer();

		if( 'on' ==  $this->smtp_server['status'] ){

			$this->email_client->isSMTP();
			//$this->email_client->SMTPDebug = $this->smtp_server['debug'];
			$this->email_client->Host = $this->smtp_server['host'];
			$this->email_client->SMTPAuth = (boolean) $this->smtp_server['auth'] == 'on';
			$this->email_client->Username = $this->smtp_server['username'];
			$this->email_client->Password = $this->smtp_server['password'];
			$this->email_client->SMTPSecure = $this->smtp_server['secure'];
			$this->email_client->Port = $this->smtp_server['port'];

		}

		$this->email_client->isHTML(true);
		$this->email_client->From = $this->app_email;
		$this->email_client->FromName = $this->app_name;
		$this->email_client->addAddress( $to_email, $to_name );
		$this->email_client->Subject = $subject;
		$this->email_client->Body = $message;

		// Send email and debug error incase of failure
		if( !$this->email_client->send() ) {
		  	//$this->timber->log->info('Mailer Error: ' . $this->email_client->ErrorInfo);
		  	return false;
		} else {
		    return true;
		}
	}
}