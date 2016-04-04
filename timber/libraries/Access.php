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

namespace Timber\Libraries;

/**
 * Perform Access Permissions
 *
 * @since 1.0
 */
class Access {

	/**
	 * Staff Permissions
	 *
	 * @since 1.0
	 * @access private
	 * @var array $this->staff_permissions
	 */
	private $staff_permissions = array(
		'view.dashboard',
		'list.members',

		'list.projects',
		'view.projects',
	);

	/**
	 * Client Permissions
	 *
	 * @since 1.0
	 * @access private
	 * @var array $this->client_permissions
	 */
	private $client_permissions = array(
		'view.dashboard',
		'list.invoices',
		'view.invoices',
		'list.estimates',
		'list.subscriptions',
		'view.subscriptions',

		'list.projects',
		'view.projects',
		'list.quotations',
		'submit.quotations',

		'checkout.invoices',
	);

	/**
	 * User rule
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->rule
	 */
	private $rule = false;

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
		return $this;
	}

	/**
	 * Configure class
	 *
	 * @since 1.0
	 * @access public
	 */
	public function config()
	{
		# Clients and Staff Permissions
		$staff_permissions = unserialize($this->timber->config('_staff_permissions'));
		$client_permissions = unserialize($this->timber->config('_client_permissions'));

		$this->staff_permissions = array_merge($this->staff_permissions, $staff_permissions);
		$this->client_permissions = array_merge($this->client_permissions, $client_permissions);

		$this->setRule();
	}

	/**
	 * Check Permission
	 *
	 * @since 1.0
	 * @access public
	 * @param string $perm
	 * @return boolean
	 */
	public function checkPermission($perm)
	{
		$rule = $this->rule;

		return (boolean) ( ($rule == 'admin') || ( ($rule == 'client') && (in_array($perm, $this->client_permissions)) ) || ( ($rule == 'staff') && (in_array($perm, $this->staff_permissions)) ) );
	}

	/**
	 * Get current logged user rule
	 *
	 * @since 1.0
	 * @access private
	 * @return string
	 */
	public function getRule()
	{
		return $this->rule;
	}

	/**
	 * Get A list of clients variable permissions
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function getClientPermissions()
	{
		$permissions = array(
			'message.admins' => $this->timber->translator->trans('Message Admins'),
			'message.staff' => $this->timber->translator->trans('Message Staff'),
			'message.clients' => $this->timber->translator->trans('Message Clients'),
		);

		return $permissions;
	}

	/**
	 * Get A list of staff variable permissions
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function getStaffPermissions()
	{
		$permissions = array(
			'add.invoices' => $this->timber->translator->trans('Add Invoices'),
			'edit.invoices' => $this->timber->translator->trans('Edit Invoices'),
			'view.invoices' => $this->timber->translator->trans('View Invoices'),
			'list.invoices' => $this->timber->translator->trans('List Invoices'),
			'delete.invoices' => $this->timber->translator->trans('Delete Invoices'),

			'add.clients' => $this->timber->translator->trans('Add Clients'),
			'edit.clients' => $this->timber->translator->trans('Edit Clients'),
			'view.clients' => $this->timber->translator->trans('View Clients'),
			'delete.clients' => $this->timber->translator->trans('Delete Clients'),

			'add.expenses' => $this->timber->translator->trans('Add Expenses'),
			'edit.expenses' => $this->timber->translator->trans('Edit Expenses'),
			'view.expenses' => $this->timber->translator->trans('View Expenses'),
			'list.expenses' => $this->timber->translator->trans('List Expenses'),
			'delete.expenses' => $this->timber->translator->trans('Delete Expenses'),

			'add.estimates' => $this->timber->translator->trans('Add Estimates'),
			'edit.estimates' => $this->timber->translator->trans('Edit Estimates'),
			'view.estimates' => $this->timber->translator->trans('View Estimates'),
			'list.estimates' => $this->timber->translator->trans('List Estimates'),
			'delete.estimates' => $this->timber->translator->trans('Delete Estimates'),

			'add.items' => $this->timber->translator->trans('Add Items'),
			'edit.items' => $this->timber->translator->trans('Edit Items'),
			'list.items' => $this->timber->translator->trans('List Items'),
			'delete.items' => $this->timber->translator->trans('Delete Items'),

			'view.calendar' => $this->timber->translator->trans('View Calendar'),

			'add.subscriptions' => $this->timber->translator->trans('Add Subscriptions'),
			'edit.subscriptions' => $this->timber->translator->trans('Edit Subscriptions'),
			'view.subscriptions' => $this->timber->translator->trans('View Subscriptions'),
			'list.subscriptions' => $this->timber->translator->trans('List Subscriptions'),
			'delete.subscriptions' => $this->timber->translator->trans('Delete Subscriptions'),

			'add.quotations' => $this->timber->translator->trans('Add Quotations'),
			'view.quotations' => $this->timber->translator->trans('View Quotations'),
			'submit.quotations' => $this->timber->translator->trans('Submit Quotations'),
			'list.quotations' => $this->timber->translator->trans('List Quotations'),
			'delete.quotations' => $this->timber->translator->trans('Delete Quotations'),
		);

		return $permissions;
	}

	/**
	 * Set current logged user rule
	 *
	 * @since 1.0
	 * @access private
	 */
	private function setRule()
	{
		$this->rule = ( $this->timber->security->isAuth() && $this->timber->security->isClient() ) ? 'client' : $this->rule;
		$this->rule = ( $this->timber->security->isAuth() && $this->timber->security->isStaff() ) ? 'staff' : $this->rule;
		$this->rule = ( $this->timber->security->isAuth() && $this->timber->security->isAdmin() ) ? 'admin' : $this->rule;
	}
}