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
 * Base Services
 *
 * @since 1.0
 */
class Base {

	/**
	 * Request name
	 *
	 * @since 1.0
	 * @access protected
	 * @var string $this->request
	 */
	protected $request;

	/**
	 * Default response
	 *
	 * @since 1.0
	 * @access protected
	 * @var array $this->response
	 */
	protected $response = array(
		'status' => 'error',
		'data' => '',
		'info' => array(),
	);

	/**
	 * A list of registered services
	 *
	 * @since 1.0
	 * @access protected
	 * @var array $this->services
	 */
	protected $services = array();

	/**
	 * Instance of timber app
	 *
	 * @since 1.0
	 * @access protected
	 * @var object $this->timber
	 */
	protected $timber;

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 * @access public
	 * @param object $timber
	 */
	public function __construct($timber)
	{
		$this->timber = $timber;
	}

	/**
	 * Get Service
	 *
	 * @since 1.0
	 * @access public
	 * @param string $service
	 * @return object
	 */
	public function __get($service)
    {
        if ( array_key_exists($service, $this->services) ) {
            return $this->services[$service];
        }

        $this->$service = null;
        return $this->services[$service];
    }

	/**
	 * Set Service
	 *
	 * @since 1.0
	 * @access public
	 * @param string $service
	 * @return object
	 */
    public function __set($service, $service_obj = null)
    {
		$native_service = "\Timber\Services\\" . $service;
		$this->services[$service] = ($service_obj == null) ? new $native_service($this->timber) : $service_obj;
    }

	/**
	 * Register Service
	 *
	 * @since 1.0
	 * @access public
	 * @param string $service
	 */
    public function register($service, $service_obj)
    {
		$this->services[$service] =  $service_obj;
    }

	/**
	 * Set Request
	 *
	 * @since 1.0
	 * @access public
	 */
    public function setRequest($request)
    {
    	$this->request = $request;
    }

	/**
	 * Check Access Permissions
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
    public function processRequest($first_access, $second_access, $current_request, $object, $requests)
    {
    	if( !(isset($requests[$current_request])) || !(method_exists($object, $requests[$current_request])) ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
    	}

		if( !($this->timber->security->cookieCheck()) ){
			$this->response['data'] = $this->timber->translator->trans('App requires cookies to be enabled in your browser.');
			return false;
		}

		if( (is_array($first_access)) && !($this->timber->security->canAccess( $first_access[$current_request] )) ){
			$this->response['data'] = $this->timber->translator->trans('Unauthorized access. Please login again.');
			$this->timber->security->endSession();
			return false;
		}

		if( (is_array($second_access)) && !($this->timber->security->canAccess( $second_access[$current_request] )) ){
			$this->response['data'] = $this->timber->translator->trans('Unauthorized access. Please login again.');
			$this->timber->security->endSession();
			return false;
		}

		if( !($this->timber->security->checkVerifyNonce()) ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}


		$method = $requests[$current_request];
		$this->timber->demo->demoJsonDie($method, $current_request);
		return $object->$method();
    }

	/**
	 * Get response
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function getPlainResponse()
	{
		return $this->response;
	}

	/**
	 * Get response
	 *
	 * @since 1.0
	 * @access public
	 * @return string
	 */
	public function getResponse()
	{
		header('Content: application/json');
		echo json_encode($this->response);
		die();
	}
}