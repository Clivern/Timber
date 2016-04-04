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
 * Expenses Controller
 *
 * @since 1.0
 */
class Expenses {

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
	 * @parm string $page
	 * @return boolean
	 */
	public function renderFilters($page = 'list')
	{
		$this->timber->filter->issueDetect();
		$this->timber->filter->configLibs();
		$this->timber->security->cookieCheck();
		$this->services->Common->renderFilter(array('staff', 'admin'), '/admin/expenses');

		if( !($this->timber->access->checkPermission($page . '.expenses')) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}
	}

	/**
	 * Render Expenses Page
	 *
	 * @since 1.0
	 * @access public
	 * @param string $page
	 * @param string $expense_id
	 */
	public function render( $page = 'list', $expense_id = '' )
	{
		if( !in_array($page, array('list', 'add', 'edit', 'view')) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		return $this->timber->render( 'expenses-' . $page, $this->getData($page, $expense_id) );
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

		$this->services->ExpensesRequests->setRequest($form);
		$this->services->ExpensesRequests->processRequest(
			array(
				'add' => array('admin', 'staff'),
				'edit' => array('admin', 'staff'),
				'delete' => array('admin', 'staff'),
				'mark' => array('admin', 'staff'),
			),
			array(
				'add' =>array('real_admin', 'real_staff'),
				'edit' => array('real_admin', 'real_staff'),
				'delete' => array('real_admin', 'real_staff'),
				'mark' => array('real_admin', 'real_staff'),
			),
			$form,
			$this->services->ExpensesRequests,
			array(
				'add' => 'addExpense',
				'edit' => 'editExpense',
				'delete' => 'deleteExpense',
				'mark' => 'markExpense',
			)
		);
		$this->services->ExpensesRequests->getResponse();

		# $this->timber->bench->end();
		# $this->timber->bench->log();
	}

	/**
	 * Bind and get data
	 *
	 * @since 1.0
	 * @access private
	 * @param string $page
	 * @param string $expense_id
	 * @return array
	 */
	private function getData($page = 'list', $expense_id = '')
	{
		$data = array();

		if( 'list' == $page ){

			$data = array_merge($data,
				$this->services->Common->subPageName( $this->timber->translator->trans('Expenses') . " | " ),
                $this->services->ExpensesData->currentUserData(),
                $this->services->ExpensesData->listData(),
				$this->services->Common->runtimeScripts( 'expensesList' )
			);

		}elseif( 'add' == $page ){

			$data = array_merge($data,
				$this->services->Common->subPageName( $this->timber->translator->trans('Add New Expense') . " | " ),
                $this->services->ExpensesData->currentUserData(),
                $this->services->ExpensesData->addData(),
                $this->services->ExpensesData->uploaderInfo(),
				$this->services->Common->runtimeScripts( 'expensesAdd' )
			);

		}elseif( 'edit' == $page ){

			$data = array_merge($data,
				$this->services->Common->subPageName( $this->timber->translator->trans('Edit Expense') . " | " ),
                $this->services->ExpensesData->currentUserData(),
                $this->services->ExpensesData->editData($expense_id),
                $this->services->ExpensesData->uploaderInfo(),
				$this->services->Common->runtimeScripts( 'expensesEdit' )
			);

		}elseif( 'view' == $page ){

			$data = array_merge($data,
				$this->services->Common->subPageName( $this->timber->translator->trans('View Expense') . " | " ),
                $this->services->ExpensesData->currentUserData(),
                $this->services->ExpensesData->viewData($expense_id),
				$this->services->Common->runtimeScripts( 'expensesView' )
			);

		}

		return $data;
	}
}