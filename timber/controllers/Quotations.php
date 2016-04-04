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
 * Quotations Controller
 *
 * @since 1.0
 */
class Quotations {

	/**
	 * Current used services
	 *
	 * @since 1.0
	 * @access private
	 * @var object
	 */
	private $services;

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
		$this->services = new \Timber\Services\Base($this->timber);
		return $this;
	}

	/**
	 * Run common tasks before rendering
	 *
	 * @since 1.0
	 * @access public
	 * @param string $page
	 * @return boolean
	 */
	public function renderFilters($page = 'list', $public = false)
	{
		$this->timber->filter->issueDetect();
		$this->timber->filter->configLibs();
		$this->timber->security->cookieCheck();

		if( !$public ){
			$this->services->Common->renderFilter(array('client', 'staff', 'admin'), '/admin/quotations');

			if( !($this->timber->access->checkPermission($page . '.quotations')) ){
				$this->timber->redirect( $this->timber->config('request_url') . '/404' );
			}
		}
	}

	/**
	 * Render Quotations Page
	 *
	 * @since 1.0
	 * @access public
	 * @param string $page
	 * @param string $quotation_id
	 */
	public function render( $page = 'list', $quotation_id = '', $email = '', $public = false )
	{
		if( !in_array($page, array('list', 'add', 'view', 'submit')) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		return $this->timber->render( 'quotations-' . $page, $this->getData($page, $quotation_id, $email, $public) );
	}

	/**
	 * Run common tasks before requests
	 *
	 * @since 1.0
	 * @access public
	 */
	public function requestFilters($public = false)
	{
		$this->services->Common->ajaxCheck();
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
		# $this->timber->bench->start();

		if( $form != 'pubsubmit' ){
			$this->services->QuotationsRequests->setRequest($form);
			$this->services->QuotationsRequests->processRequest(
				array(
					'add' => array('admin', 'staff'),
					'delete' => array('admin', 'staff'),
					'mark' => array('admin', 'staff', 'client'),
					'submit' => array('admin', 'staff', 'client'),
				),
				array(
					'add' => array('real_admin', 'real_staff'),
					'delete' => array('real_admin', 'real_staff'),
					'mark' => array('real_admin', 'real_staff', 'real_client'),
					'submit' => array('real_admin', 'real_staff', 'real_client'),
				),
				$form,
				$this->services->QuotationsRequests,
				array(
					'add' => 'addQuotation',
					'delete' => 'deleteQuotation',
					'mark' => 'markQuotation',
					'submit' => 'submitQuotation',
				)
			);
		}else{
			$this->services->QuotationsRequests->publicSubmitQuotation();
		}

		$this->services->QuotationsRequests->getResponse();

		# $this->timber->bench->end();
		# $this->timber->bench->log();
	}

	/**
	 * Bind and get data
	 *
	 * @since 1.0
	 * @access private
	 * @param string $page
	 * @param string $quotation_id
	 * @return array
	 */
	private function getData($page = 'list', $quotation_id = '', $email = '', $public = false)
	{
		$data = array();

		if( 'list' == $page ){

			$data = array_merge($data,
				$this->services->Common->subPageName( $this->timber->translator->trans('Quotations') . " | " ),
                $this->services->QuotationsData->currentUserData(),
                $this->services->QuotationsData->listData(),
				$this->services->Common->runtimeScripts( 'quotationsList' )
			);

		}elseif( 'add' == $page ){

			$data = array_merge($data,
				$this->services->Common->subPageName( $this->timber->translator->trans('Add New Quotation') . " | " ),
                $this->services->QuotationsData->currentUserData(),
                $this->services->QuotationsData->addData(),
                $this->services->QuotationsData->uploaderInfo(),
				$this->services->Common->runtimeScripts( 'quotationsAdd' )
			);

		}elseif( 'view' == $page ){

			$data = array_merge($data,
				$this->services->Common->subPageName( $this->timber->translator->trans('View Quotation') . " | " ),
                $this->services->QuotationsData->currentUserData(),
                $this->services->QuotationsData->viewData($quotation_id),
				$this->services->Common->runtimeScripts( 'quotationsView' )
			);

		}elseif( 'submit' == $page ){

			if( !$public ){
				$data = array_merge($data,
					$this->services->Common->subPageName( $this->timber->translator->trans('Submit Quotation') . " | " ),
                    $this->services->QuotationsData->submitData($quotation_id),
					$this->services->Common->runtimeScripts( 'quotationsSubmit' )
				);
			}else{
				$data = array_merge($data,
					$this->services->Common->subPageName( $this->timber->translator->trans('Submit Quotation') . " | " ),
                    $this->services->QuotationsData->publicSubmitData($quotation_id, $email),
					$this->services->Common->runtimeScripts( 'quotationsPubSubmit' )
				);
			}
		}

		return $data;
	}
}