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

namespace Timber\Controllers;

/**
 * Verify Controller
 *
 * @since 1.0
 */
class Verify {

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
		return true;
	}

	/**
	 * Render
	 *
	 * @since 1.0
	 * @access public
	 * @param string $email
	 * @param string $hash
	 */
	public function render($email = '', $hash = '')
	{

		if( ($email === '') || ($hash === '') ){
			$this->timber->redirect( $this->timber->config('request_url') . '/login' );
		}

		$email = ( (boolean) filter_var($email, FILTER_VALIDATE_EMAIL) ) ? filter_var($email, FILTER_SANITIZE_EMAIL) : false;
		$hash = filter_var($hash, FILTER_SANITIZE_STRING);

		if( ($email === false) || ($hash === false) || (empty($hash)) || (empty($email)) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/login' );
		}

		$update_status = (boolean) $this->timber->user_model->updateUserByMultiple( array('email' => $email, 'sec_hash' => $hash, 'status' => 2), array('status' => 1) );
		//invalid link
		if( $update_status ){
			$this->timber->redirect( $this->timber->config('request_url') . '/login/6' );
		}else{
			$this->timber->redirect( $this->timber->config('request_url') . '/login/7' );
		}
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
	 * Process requests and respond
	 *
	 * @since 1.0
	 * @access public
	 * @param string $form
	 * @return string
	 */
	public function requests($form = '')
	{
		$this->checkRequest($form);
		$this->getResponse();
	}


	/**
	 * Process profile update requests
	 *
	 * @since 1.0
	 * @access public
	 * @param string $form
	 * @return boolean
	 */
	private function checkRequest($form)
	{
		if( !($this->timber->security->cookieCheck()) ){
			$this->response['data'] = $this->timber->translator->trans('App requires cookies to be enabled in your browser.');
			return false;
		}

		if( !($this->timber->security->isAuth()) || ( !($this->timber->security->isClient()) && !($this->timber->security->isStaff()) && !($this->timber->security->isAdmin()) ) ){
			$this->response['data'] = $this->timber->translator->trans('Unauthorized access. Please login again.');
			$this->timber->security->endSession();
			return false;
		}

		if( !($this->timber->security->isAuth()) || ( !($this->timber->security->isRealClient()) && !($this->timber->security->isRealStaff()) && !($this->timber->security->isRealAdmin()) ) ){
			$this->response['data'] = $this->timber->translator->trans('Unauthorized access. Please login again.');
			$this->timber->security->endSession();
			return false;
		}

		if( !($this->timber->security->checkVerifyNonce()) ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		switch ($form) {
			case 'verifyemail':
				$this->checkverifyEmail();
				break;
			default:
				$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
				break;
		}
	}

	/**
	 * Verify Email request
	 *
	 * @since 1.0
	 * @access private
	 * @return boolean
	 */
	private function checkverifyEmail()
	{
		$user_id = $this->timber->security->getId();
		$user_data = $this->timber->user_model->getUserById($user_id);

		if( (false === $user_data) || !(is_object($user_data)) ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		$user_data = $user_data->as_array();
		if( $user_data['status'] != 2 ){
			$this->response['data'] = $this->timber->translator->trans('Invalid request.');
			return false;
		}

		$message_status = $this->timber->notify->execMailerCron(array(
			'method_name' => 'verifyEmailNotifier',
			'user_id' => $user_id,
			'hash' => $user_data['sec_hash'],
		));


		if( $message_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Verification message sent to your email.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something wrong! we apologize, try again later.');
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
}