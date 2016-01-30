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
 * Fpwd Controller
 *
 * @since 1.0
 */
class Fpwd {

	/**
     * Data to be used in template
     *
     * @since 1.0
     * @access private
     * @var array $this->data
     */
	private $data = array();

	/**
	 * Used to hold fpwd hash
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->hash
	 */
	private $hash = '';

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
		$this->timber->filter->setDepen($timber)->config();
		return $this;
	}

	/**
	 * Run common tasks before rendering
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
	 * Request Filters
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
	 * Render fpwd page
	 *
	 * @since 1.0
	 * @access public
	 * @param string $hash
	 */
	public function render($hash = '')
	{
		# render fpwd
		return $this->timber->render( 'fpwd' , $this->getData($hash) );
	}

	/**
	 * Bind and get data
	 *
	 * @since 1.0
	 * @access private
	 * @param string $hash
	 * @return array
	 */
	private function getData($hash = '')
	{
		$this->data['step'] = $this->detectStep($hash);
		$this->data['hash'] = $this->hash;

		$this->data['footer_scripts']  = "jQuery(document).ready(function($){";
		$this->data['footer_scripts'] .= "timber.utils.init();";
		$this->data['footer_scripts'] .= "timber.fpwd.init();";
		$this->data['footer_scripts'] .= "});";

		$this->bindSubPage( $this->timber->translator->trans('Forgot Password') . " | " );
		return $this->data;
	}

	/**
	 * Process fpwd requests and respond
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
	 * Process fpwd requests
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

		if( (isset($_POST['fpwd_hash'])) && ($_POST['fpwd_hash'] != '') ){
			return $this->secondStepRequests();
		}
		return $this->firstStepRequests();
	}

	/**
	 * Process fpwd first step
	 *
	 * Used only during requests
	 *
	 * @since 1.0
	 * @access private
	 * @return boolean
	 */
	private function firstStepRequests()
	{
		$fpwd_data = $this->timber->validator->clear(array(
			'fpwd_email' => array(
  				'req' => 'post',
            	'sanit' => 'semail',
               	'valid' => 'vnotempty&vemail',
               	'default' => '',
               	'errors' => array(
               		'vnotempty' => $this->timber->translator->trans('The email is invalid.'),
               		'vemail' => $this->timber->translator->trans('The email is invalid.'),
               	),
			),
		));

		if(true === $fpwd_data['error_status']){
			$this->response['data'] = $fpwd_data['error_text'];
			return false;
		}
		$email = ($fpwd_data['fpwd_email']['status']) ? $fpwd_data['fpwd_email']['value'] : '';

		$user_data = $this->timber->user_model->getUserByMultiple( array('email' => $email, 'auth_by' => '1') );

		if( (false === $user_data) || !(is_object($user_data)) ){
			$this->response['data'] = $this->timber->translator->trans('The email is invalid.');
			return false;
		}

		$user_data = $user_data->as_array();

		//delete old metas
		$this->timber->user_meta_model->dumpUserMeta( $user_data['us_id'], '_user_fpwd_hash' );

		//insert new one
		$hash = $this->timber->faker->randHash(20) . time();
		$meta_status = $this->timber->user_meta_model->addMeta(array(
			'us_id' => $user_data['us_id'],
			'me_key' => '_user_fpwd_hash',
			'me_value' => $hash,
		));

		# Run Now and don't run as a cron
		$message_status = $this->timber->notify->execMailerCron(array(
			'method_name' => 'fpwdEmailNotifier',
			'user_id' => $user_data['us_id'],
			'hash' => $hash,
		));

		if( $meta_status && $message_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Reset message sent successfully.');
			return true;
		}

		$this->response['data'] = $this->timber->translator->trans('Something goes wrong! We apologize. try again later.');
		return false;
	}

	/**
	 * Process fpwd second step
	 *
	 * Used only during requests
	 *
	 * @since 1.0
	 * @access private
	 * @return boolean
	 */
	private function secondStepRequests()
	{
		$fpwd_data = $this->timber->validator->clear(array(
			'fpwd_new_password' => array(
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
			'fpwd_hash' => array(
  				'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:8,150',
                'default' => '',
                'errors' => array(
                	'vnotempty' => $this->timber->translator->trans('Invalid request! try to reset password again.'),
                	'vstrlenbetween' => $this->timber->translator->trans('Invalid request! try to reset password again.')
                )
			),
		));

		if(true === $fpwd_data['error_status']){
			$this->response['data'] = $fpwd_data['error_text'];
			return false;
		}

		$new_password = ( $fpwd_data['fpwd_new_password']['status'] ) ? $fpwd_data['fpwd_new_password']['value'] : '';
		$hash = ( ($fpwd_data['fpwd_new_password']['status']) && ($fpwd_data['fpwd_hash']['status']) ) ? $fpwd_data['fpwd_hash']['value'] : '';

		$user_meta = $this->timber->user_meta_model->getMetaByMultiple(array(
			'me_key' => '_user_fpwd_hash',
			'me_value' => $hash
		));

		if( (false === $user_meta) || !(is_object($user_meta)) ){
			$this->response['data'] =  $this->timber->translator->trans('Invalid request! try to reset password again.');
			return false;
		}

		$user_meta = $user_meta->as_array();

		//update password
		$update_status = $this->timber->user_model->updateUserById(array(
			'us_id' => $user_meta['us_id'],
			'password' => $this->timber->hasher->HashPassword($new_password),
			'updated_at' => $this->timber->time->getCurrentDate(true)
		));
		$meta_status = $this->timber->user_meta_model->deleteMetaById($user_meta['me_id']);

		if( $update_status && $meta_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Password changed successfully.');
			return true;
		}
		$this->response['data'] = $this->timber->translator->trans('Something goes wrong! We apologize. try again later.');
		return false;
	}

	/**
	 * Detect fpwd step using hash
	 *
	 * Used during rendering only
	 *
	 * @since 1.0
	 * @access private
	 * @param string $hash
	 * @return 1|2
	 */
	private function detectStep($hash)
	{
		if( empty($hash) ){
			return '1';
		}

		$hash = filter_var(trim($hash), FILTER_SANITIZE_STRING);
		if( empty($hash) ){
			return '1';
		}

		$user_meta = $this->timber->user_meta_model->getMetaByMultiple(array(
			'me_key' => '_user_fpwd_hash',
			'me_value' => $hash
		));

		if( (false === $user_meta) || !(is_object($user_meta)) ){
			return '1';
		}

		$this->hash = $hash;
		return '2';
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