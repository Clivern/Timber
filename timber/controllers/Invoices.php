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
 * Invoices Controller
 *
 * @since 1.0
 */
class Invoices {

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
	public function renderFilters($page = 'list')
	{
		$this->timber->filter->issueDetect();
		$this->timber->filter->configLibs();
		$this->timber->security->cookieCheck();
		$this->services->Common->renderFilter(array('client', 'staff', 'admin'), '/admin/invoices');

		if( !($this->timber->access->checkPermission($page . '.invoices')) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}
	}

	/**
	 * Render Invoices Page
	 *
	 * @since 1.0
	 * @access public
	 * @param string $page
	 * @param string $invoice_id
	 */
	public function render( $page = 'list', $invoice_id = '' )
	{
		if( !in_array($page, array('list', 'add', 'edit', 'view', 'checkout')) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		return $this->timber->render( 'invoices-' . $page, $this->getData($page, $invoice_id) );
	}

	/**
	 *  Run common tasks before requests
	 *
	 * @since 1.0
	 * @access public
	 */
	public function requestFilters()
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
		$this->services->InvoicesRequests->setRequest($form);
		$this->services->InvoicesRequests->processRequest(
			array(
				'add' => array('admin', 'staff'),
				'edit' => array('admin', 'staff'),
				'delete' => array('admin', 'staff'),
				'mark' => array('admin', 'staff','client'),
			),
			array(
				'add' => array('real_admin', 'real_staff'),
				'edit' => array('real_admin', 'real_staff'),
				'delete' => array('real_admin', 'real_staff'),
				'mark' => array('real_admin', 'real_staff','real_client'),
			),
			$form,
			$this->services->InvoicesRequests,
			array(
				'add' => 'addInvoice',
				'edit' => 'editInvoice',
				'delete' => 'deleteInvoice',
				'mark' => 'markInvoice',
			)
		);
		$this->services->InvoicesRequests->getResponse();

		# $this->timber->bench->end();
		# $this->timber->bench->log();
	}

	/**
	 * Bind and get data
	 *
	 * @since 1.0
	 * @access private
	 * @param string $page
	 * @param string $invoice_id
	 * @return array
	 */
	private function getData($page = 'list', $invoice_id = '')
	{
		$data = array();

		if( 'list' == $page ){

			$data = array_merge($data,
				$this->services->Common->subPageName( $this->timber->translator->trans('Invoices') . " | " ),
                $this->services->InvoicesData->currentUserData(),
                $this->services->InvoicesData->listData(),
				$this->services->Common->runtimeScripts( 'invoicesList' ),
				$this->services->Common->injectScripts(array(
					'checkout_return_message' => $this->timber->cachier->errorPhrase(),
				))
			);

		}elseif( 'add' == $page ){

			$data = array_merge($data,
				$this->services->Common->subPageName( $this->timber->translator->trans('Add New Invoice') . " | " ),
                $this->services->InvoicesData->currentUserData(),
                $this->services->InvoicesData->addData(),
                $this->services->InvoicesData->uploaderInfo(),
				$this->services->Common->runtimeScripts( 'invoicesAdd' )
			);

		}elseif( 'edit' == $page ){

			$data = array_merge($data,
				$this->services->Common->subPageName( $this->timber->translator->trans('Edit Invoice') . " | " ),
                $this->services->InvoicesData->currentUserData(),
                $this->services->InvoicesData->editData($invoice_id),
                $this->services->InvoicesData->uploaderInfo(),
				$this->services->Common->runtimeScripts( 'invoicesEdit' )
			);

		}elseif( 'view' == $page ){

			$data = array_merge($data,
				$this->services->Common->subPageName( $this->timber->translator->trans('View Invoice') . " | " ),
                $this->services->InvoicesData->currentUserData(),
                $this->services->InvoicesData->viewData($invoice_id),
				$this->services->Common->runtimeScripts( 'invoicesView' )
			);

		}elseif( 'checkout' == $page ){
            $data = array_merge($data,
				$this->services->Common->subPageName( $this->timber->translator->trans('Checkout') . " | " ),
                $this->services->InvoicesData->currentUserData(),
                $this->services->InvoicesData->checkoutData(),
				$this->services->Common->runtimeScripts( 'invoicesCheckout' ),
				$this->services->Common->injectScripts(array(
					'checkout_return_message' => $this->timber->cachier->errorPhrase(),
				))
			);
		}

		return $data;
	}
}