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
 * Subscriptions Controller
 *
 * @since 1.0
 */
class Subscriptions {

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
		$this->services->Common->renderFilter(array('client', 'staff', 'admin'), '/admin/subscriptions');

		if( !($this->timber->access->checkPermission($page . '.subscriptions')) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}
	}

	/**
	 * Render Subscriptions Page
	 *
	 * @since 1.0
	 * @access public
	 * @param string $page
	 * @param string $subscription_id
	 */
	public function render( $page = 'list', $subscription_id = '' )
	{
		if( !in_array($page, array('list', 'add', 'edit', 'view')) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		return $this->timber->render( 'subscriptions-' . $page, $this->getData($page, $subscription_id) );
	}

	/**
	 * Run common tasks before requests
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

		$this->services->SubscriptionsRequests->setRequest($form);
		$this->services->SubscriptionsRequests->processRequest(
			array(
				'add' => array('admin', 'staff'),
				'edit' => array('admin', 'staff'),
				'delete' => array('admin', 'staff'),
				'invoice' => array('admin', 'staff'),
				'mark' => array('admin', 'staff', 'client'),
			),
			array(
				'add' => array('real_admin', 'real_staff'),
				'edit' => array('real_admin', 'real_staff'),
				'delete' => array('real_admin', 'real_staff'),
				'invoice' => array('real_admin', 'real_staff'),
				'mark' => array('real_admin', 'real_staff', 'real_client'),
			),
			$form,
			$this->services->SubscriptionsRequests,
			array(
				'add' => 'addSubscription',
				'edit' => 'editSubscription',
				'delete' => 'deleteSubscription',
				'invoice' => 'invoiceSubscription',
				'mark' => 'markSubscription',
			)
		);
		$this->services->SubscriptionsRequests->getResponse();

		# $this->timber->bench->end();
		# $this->timber->bench->log();
	}

	/**
	 * Bind and get data
	 *
	 * @since 1.0
	 * @access private
	 * @param string $page
	 * @param string $subscription_id
	 * @return array
	 */
	private function getData($page = 'list', $subscription_id = '')
	{
		$data = array();

		if( 'list' == $page ){

			$data = array_merge($data,
				$this->services->Common->subPageName( $this->timber->translator->trans('Subscriptions') . " | " ),
                $this->services->SubscriptionsData->currentUserData(),
                $this->services->SubscriptionsData->listData(),
				$this->services->Common->runtimeScripts( 'subscriptionsList' )
			);

		}elseif( 'add' == $page ){

			$data = array_merge($data,
				$this->services->Common->subPageName( $this->timber->translator->trans('Add New Subscription') . " | " ),
                $this->services->SubscriptionsData->currentUserData(),
                $this->services->SubscriptionsData->addData(),
                $this->services->SubscriptionsData->uploaderInfo(),
				$this->services->Common->runtimeScripts( 'subscriptionsAdd' )
			);

		}elseif( 'edit' == $page ){

			$data = array_merge($data,
				$this->services->Common->subPageName( $this->timber->translator->trans('Edit Subscription') . " | " ),
                $this->services->SubscriptionsData->currentUserData(),
                $this->services->SubscriptionsData->editData($subscription_id),
                $this->services->SubscriptionsData->uploaderInfo(),
				$this->services->Common->runtimeScripts( 'subscriptionsEdit' )
			);

		}elseif( 'view' == $page ){

			$data = array_merge($data,
				$this->services->Common->subPageName( $this->timber->translator->trans('View Subscription') . " | " ),
                $this->services->SubscriptionsData->currentUserData(),
                $this->services->SubscriptionsData->viewData($subscription_id),
				$this->services->Common->runtimeScripts( 'subscriptionsView' )
			);

		}

		return $data;
	}
}