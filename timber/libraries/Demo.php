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

namespace Timber\Libraries;

/**
 * Demo Class
 *
 * @since 1.0
 */
class Demo {

	/**
	 * Whether demo is active
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->active
	 */
	private $active = TIMBER_DEMO_MODE;

	/**
	 * Default response
	 *
	 * @since 1.0
	 * @access protected
	 * @var array $this->response
	 */
	private $response = array(
		'status' => 'error',
		'data' => 'Sorry! this action is disabled in demo.',
		'info' => array(),
	);

	/**
	 * Alist of prevented requests
	 *
	 * @since 1.0
	 * @access private
	 * @var array $this->prevented_requests
	 */
	private $prevented_requests = array(
		/*'add => addEstimate',*/
		/*'edit => editEstimate',*/
		'delete => deleteEstimate',
		'mark => markEstimate',

		/*'add => addExpense',*/
		/*'edit => editExpense',*/
		'delete => deleteExpense',
		'mark => markExpense',

		/*'add => addInvoice',*/
		/*'edit => editInvoice',*/
		'delete => deleteInvoice',
		'mark => markInvoice',

		/*'add => addItem',*/
		/*'edit => editItem',*/
		'delete => deleteItem',

		'add_member => addMember',
		'update_member_profile => updateMemberProfile',
		'delete_member => deleteMember',

		/*'add_message => addMessage',*/
		/*'reply_to_message => addReply',*/
		'mark_message => markMessage',

		'activate => activatePlugin',
		'deactivate => deactivatePlugin',
		'delete => deletePlugin',

		'update_user_profile => updateUserProfile',

		'add => addProject',
		'edit => editProject',
		'delete => deleteProject',
		'mark => markProject',

		'sync => syncFiles',

		/*'edit_milestone => editMilestone',*/
		/*'add_milestone => addMilestone',*/
		'delete_milestone => deleteMilestone',

		/*'edit_task => editTask',*/
		/*'add_task => addTask',*/
		'delete_task => deleteTask',
		'mark_task => markTask',

		/*'add_ticket => addTicket',*/
		/*'edit_ticket => editTicket',*/
		'delete_ticket => deleteTicket',
		'mark_ticket => markTicket',

		/*'add => addQuotation',*/
		'delete => deleteQuotation',
		'mark => markQuotation',
		/*'submit => submitQuotation',*/

		'company => updateCompanyData',
		'appearance => updateAppearanceData',
		'rules => updateRulesData',
		'backups => updateBackupsData',
		'templates => updateTemplatesData',
		'payments => updatePaymentsData',
		'actions => performActions',

		/*'add => addSubscription',*/
		/*'edit => editSubscription',*/
		'delete => deleteSubscription',
		'invoice => invoiceSubscription',
		'mark => markSubscription',

		'save => saveTheme',
		'activate => activateTheme',
		'delete => deleteTheme',
	);

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
	 * Check if demo is active
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function demoActive()
	{
		return $this->active;
	}

	/**
	 * End Json Request
	 *
	 * @since 1.0
	 * @access public
	 * @param  string $method
	 * @param  string $current_request
	 * @return string
	 */
	public function demoJsonDie($method, $current_request)
	{
		if( !$this->active ){
			return;
		}

		if( $this->parseRequest($method, $current_request) ){
			return;
		}

		header('Content: application/json');
		echo json_encode($this->response);
		die();
	}

	/**
	 * Parse Request
	 *
	 * @since 1.0
	 * @access private
	 * @param  string $method
	 * @param  string $current_request
	 * @return boolean
	 */
	private function parseRequest($method, $current_request)
	{
		if( in_array($current_request . ' => ' . $method, $this->prevented_requests) ){
			return false;
		}

		return true;
	}
}