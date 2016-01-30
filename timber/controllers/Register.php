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
 * Register Controller
 *
 * @since 1.0
 */
class Register {

	/**
	 * Data to be used in template
	 *
	 * @since 1.0
	 * @access private
	 * @var array $this->data
	 */
	private $data = array();

    /**
     * Ajax Request Responses
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
		$this->timber->filter->setDepen($timber)->config();
		return $this;
	}

	/**
	 * Render Filters
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function renderFilters()
	{
		$this->timber->filter->issueDetect();
		$this->timber->filter->configLibs();
		$this->timber->security->cookieCheck();

		if( ($this->timber->security->canAccess( array('client', 'staff', 'admin') )) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/admin/dashboard');
		}

		return true;
	}

	/**
	 *  Run common tasks before requests
	 *
	 * @since 1.0
	 * @access public
	 */
	public function requestFilters()
	{
		if( !($this->timber->request->isAjax()) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}
		$this->timber->filter->issueDetect();
		$this->timber->filter->configLibs();
	}

	/**
	 * Render login page
	 *
	 * @since 1.0
	 * @access public
	 */
	public function render($hash = '')
	{
		# Check Hash
		if( !($this->checkHash($hash)) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

        $this->timber->cookie->set('_user_register_hash', $hash, 2);

		# Render
		return $this->timber->render( 'register', $this->getData($hash));
	}

	/**
	 * Bind and get data
	 *
	 * @since 1.0
	 * @access private
	 * @return array
	 */
	private function getData($hash)
	{
		$this->data['member_hash'] = filter_var(trim($hash), FILTER_SANITIZE_STRING);
		$this->data['footer_scripts']  = "jQuery(document).ready(function($){";
		$this->data['footer_scripts'] .= "timber.utils.init();";
		$this->data['footer_scripts'] .= "timber.register.init();";
		$this->data['footer_scripts'] .= "});";

		$this->bindSubPage( $this->timber->translator->trans('Register') . " | " );
		return $this->data;
	}

	/**
	 * Process login requests and respond
	 *
	 * @since 1.0
	 * @access public
	 * @return string
	 */
	public function requests()
	{
		$this->checkRequest();
		$this->getResponse();
	}

	/**
	 * Process login requests
	 *
	 * @since 1.0
	 * @access private
	 * @return boolean
	 */
	private function checkRequest()
	{
		if( !($this->timber->security->cookieCheck()) ){
			$this->response['data'] = $this->timber->translator->trans('App requires cookies to be enabled in your browser.');
			return false;
		}

		if( ($this->timber->config('_site_maintainance_mode') == 'on') ){
			$this->response['data'] = $this->timber->translator->trans('We apologize, site is under maintainance. Try again later.');
			return false;
		}

		$cleared_data = $this->timber->validator->clear(array(
			'member_hash' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vstrlenbetween:5,300',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Invalid Request.'),
					'vstrlenbetween' => $this->timber->translator->trans('Invalid Request.'),
				),
			),
			'user_username' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vstrlenbetween:5,20&vusername',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Username must not be empty.'),
					'vstrlenbetween' => $this->timber->translator->trans('Username must have a lenght of five or more.'),
					'vusername' => $this->timber->translator->trans('Username must contain only letters and numbers.'),
				),
			),
			'user_email' => array(
				'req' => 'post',
				'sanit' => 'semail',
				'valid' => 'vnotempty&vemail',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('The email must not be empty.'),
					'vemail' => $this->timber->translator->trans('The email is invalid.')
				)
			),
			'user_password' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vstrlenbetween:8,20&vpassword',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('The password must not be empty.'),
					'vstrlenbetween' => $this->timber->translator->trans('The password must have a lenght of eight or more.'),
					'vpassword' => $this->timber->translator->trans('The password must contain at least two numbers and not contain any invalid chars.')
				)
			),
			'terms_agreement' => array(
				'req'=> 'post',
				'valid'=> 'vcheckbox',
				'default'=> ''
			),
		));

		$hash_data = $this->getHashData($cleared_data['member_hash']['value']);

		if(false === $hash_data){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		$user_data = $this->timber->user_model->getUserById($hash_data['us_id']);
		if( (false === $user_data) || !(is_object($user_data)) ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}
		$user_data = $user_data->as_array();

		if( true === $cleared_data['error_status'] ){
			$this->response['data'] = $cleared_data['error_text'];
			return false;
		}

		if( false === $cleared_data['terms_agreement']['status'] ){
			$this->response['data'] = $this->timber->translator->trans('You must accept terms of use.');
			return false;
		}

		if( ($cleared_data['user_username']['value'] != $user_data['user_name']) && (true === $this->usernameExists($cleared_data['user_username']['value'])) ){
			$this->response['data'] = $this->timber->translator->trans('The username already exists.');
			return false;
		}

		if( ($cleared_data['user_email']['value'] != $user_data['email']) && (true === $this->emailExists($cleared_data['user_email']['value'])) ){
			$this->response['data'] = $this->timber->translator->trans('The email already exists.');
			return false;
		}

		$user_data = array(
			'user_id' => $hash_data['us_id'],
			'meta_id' => $hash_data['me_id'],
			'user_username' => $cleared_data['user_username']['value'],
			'user_email' => $cleared_data['user_email']['value'],
			'user_password' => $cleared_data['user_password']['value'],
		);

		if( $this->updateUser($user_data) ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Account created successfully. You can log in.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something wrong! we apologize, try again later.');
			return false;
		}
		return false;
	}

	/**
	 * Update user
	 *
	 * @since 1.0
	 * @access private
	 * @param array $user_data
	 * @return boolean
	 */
	private function updateUser($user_data)
	{
		$status = (boolean) $this->timber->user_model->updateUserById(array(
			'us_id' => $user_data['user_id'],
			'user_name' => $user_data['user_username'],
			'email' => $user_data['user_email'],
			'password' => $this->timber->hasher->HashPassword($user_data['user_password']),
			'updated_at' => $this->timber->time->getCurrentDate(true),
			'status' => '1',
			'auth_by' => '1',
		));
		# Dump Meta
		$status &= (boolean) $this->timber->user_meta_model->deleteMetaById($user_data['meta_id']);

		return $status;
	}

	/**
	 * Check if username exists in db
	 *
	 * @since 1.0
	 * @access private
	 * @param string $username
	 * @return boolean
	 */
	private function usernameExists($username)
	{
		$username_exists = $this->timber->user_model->getUserByUsername($username);
		if( (false === $username_exists) || !(is_object($username_exists)) ){
			return false;
		}
		return true;
	}

	/**
	 * Check if email exists in db
	 *
	 * @since 1.0
	 * @access private
	 * @param string $email
	 * @return boolean
	 */
	private function emailExists($email)
	{
		$email_exists = $this->timber->user_model->getUserByEmail($email);
		if( (false === $email_exists) || !(is_object($email_exists)) ){
			return false;
		}
		return true;
	}

	/**
	 * Check Register Hash
	 *
	 * @since 1.0
	 * @access private
	 * @param string $hash
	 * @return boolean
	 */
	private function checkHash($hash)
	{
		if( empty($hash) ){
			return false;
		}

		$hash = filter_var(trim($hash), FILTER_SANITIZE_STRING);
		if( empty($hash) ){
			return false;
		}

		$user_meta = $this->timber->user_meta_model->getMetaByMultiple(array(
			'me_key' => '_user_register_hash',
			'me_value' => $hash
		));

		if( (false === $user_meta) || !(is_object($user_meta)) ){
			return false;
		}

		return true;
	}

	/**
	 * Get user data from hash
	 *
	 * @since 1.0
	 * @access private
	 * @param string $hash
	 * @return boolean|array
	 */
	private function getHashData($hash)
	{
		if( empty($hash) ){
			return false;
		}

		$hash = filter_var(trim($hash), FILTER_SANITIZE_STRING);
		if( empty($hash) ){
			return false;
		}

		$user_meta = $this->timber->user_meta_model->getMetaByMultiple(array(
			'me_key' => '_user_register_hash',
			'me_value' => $hash
		));

		if( (false === $user_meta) || !(is_object($user_meta)) ){
			return false;
		}

		return $user_meta->as_array();
	}

	/**
	 * Bind sub page
	 *
	 * @since 1.0
	 * @access private
	 * @param string $sub_page
	 */
	private function bindSubPage($sub_page)
	{
		$this->data['site_sub_page'] = $sub_page;
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
}