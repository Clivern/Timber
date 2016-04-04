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
 * Items Controller
 *
 * @since 1.0
 */
class Items {

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
		$this->services->Common->renderFilter(array('staff', 'admin'), '/admin/items');

		if( !($this->timber->access->checkPermission($page . '.items')) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

	}

	/**
	 * Render Items Page
	 *
	 * @since 1.0
	 * @access public
	 * @param string $page
	 * @param string $item_id
	 */
	public function render($page = 'list', $item_id = '')
	{
		if( !in_array($page, array('list', 'add', 'edit')) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		return $this->timber->render( 'items-' . $page, $this->getData($page, $item_id) );
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

		$this->services->ItemsRequests->setRequest($form);
		$this->services->ItemsRequests->processRequest(
			array(
				'add' => array('admin', 'staff'),
				'edit' => array('admin', 'staff'),
				'delete' => array('admin', 'staff'),
			),
			array(
				'add' => array('real_admin', 'real_staff'),
				'edit' => array('real_admin', 'real_staff'),
				'delete' => array('real_admin', 'real_staff'),
			),
			$form,
			$this->services->ItemsRequests,
			array(
				'add' => 'addItem',
				'edit' => 'editItem',
				'delete' => 'deleteItem',
			)
		);
		$this->services->ItemsRequests->getResponse();

		# $this->timber->bench->end();
		# $this->timber->bench->log();
	}

	/**
	 * Bind and get data
	 *
	 * @since 1.0
	 * @access private
	 * @param string $page
	 * @param string $item_id
	 * @return array
	 */
	private function getData($page = 'list', $item_id = '')
	{
		$data = array();

		if( 'list' == $page ){

			$data = array_merge($data,
				$this->services->Common->subPageName( $this->timber->translator->trans('Items') . " | " ),
                $this->services->ItemsData->currentUserData(),
                $this->services->ItemsData->listData(),
				$this->services->Common->runtimeScripts( 'itemsList' )
			);

		}elseif( 'add' == $page ){

			$data = array_merge($data,
				$this->services->Common->subPageName( $this->timber->translator->trans('Add New Item') . " | " ),
                $this->services->ItemsData->currentUserData(),
                $this->services->ItemsData->addData(),
				$this->services->Common->runtimeScripts( 'itemsAdd' )
			);

		}elseif( 'edit' == $page ){

			$data = array_merge($data,
				$this->services->Common->subPageName( $this->timber->translator->trans('Edit Item') . " | " ),
                $this->services->ItemsData->currentUserData(),
                $this->services->ItemsData->editData($item_id),
				$this->services->Common->runtimeScripts( 'itemsEdit' )
			);

		}

		return $data;
	}
}