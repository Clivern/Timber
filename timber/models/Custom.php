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

namespace Timber\Models;

/**
 * Custom Model
 *
 * Perform All CRUD Operations.
 *
 * @since 1.0
 */
class Custom {

	/**
	 * Files table name
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->files_table
	 */
	private $files_table;

	/**
	 * Invoices table name
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->invoices_table
	 */
	private $invoices_table;

	/**
	 * Items table name
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->items_table
	 */
	private $items_table;

	/**
	 * Messages table name
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->messages_table
	 */
	private $messages_table;

	/**
	 * Metas table name
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->metas_table
	 */
	private $metas_table;

	/**
	 * Milestones table name
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->milestones_table
	 */
	private $milestones_table;

	/**
	 * Options table name
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->options_table
	 */
	private $options_table;

	/**
	 * Projects table name
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->projects_table
	 */
	private $projects_table;

	/**
	 * Projects Meta table name
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->projectsmeta_table
	 */
	private $projectsmeta_table;

	/**
	 * Quotations table name
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->quotations_table
	 */
	private $quotations_table;

	/**
	 * Subscriptions table name
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->subscriptions_table
	 */
	private $subscriptions_table;

	/**
	 * Tasks table name
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->tasks_table
	 */
	private $tasks_table;

	/**
	 * Tickets table name
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->tickets_table
	 */
	private $tickets_table;

	/**
	 * Users table name
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->users_table
	 */
	private $users_table;

	/**
	 * Users Meta table name
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->usersmeta_table
	 */
	private $usersmeta_table;

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
	 * Class constructor
	 *
	 * @since 1.0
	 * @access public
	 */
	public function __construct()
	{
            $this->files_table = TIMBER_DB_PREFIX . TIMBER_DB_FILES_TABLE;
            $this->invoices_table = TIMBER_DB_PREFIX . TIMBER_DB_INVOICES_TABLE;
            $this->items_table = TIMBER_DB_PREFIX . TIMBER_DB_ITEMS_TABLE;
            $this->messages_table = TIMBER_DB_PREFIX . TIMBER_DB_MESSAGES_TABLE;
            $this->metas_table = TIMBER_DB_PREFIX . TIMBER_DB_METAS_TABLE;
            $this->milestones_table = TIMBER_DB_PREFIX . TIMBER_DB_MILESTONES_TABLE;
            $this->options_table = TIMBER_DB_PREFIX . TIMBER_DB_OPTIONS_TABLE;
            $this->projects_table = TIMBER_DB_PREFIX . TIMBER_DB_PROJECTS_TABLE;
            $this->projectsmeta_table = TIMBER_DB_PREFIX . TIMBER_DB_PROJECTS_META_TABLE;
            $this->quotations_table = TIMBER_DB_PREFIX . TIMBER_DB_QUOTATIONS_TABLE;
            $this->subscriptions_table = TIMBER_DB_PREFIX . TIMBER_DB_SUBSCRIPTIONS_TABLE;
            $this->tasks_table = TIMBER_DB_PREFIX . TIMBER_DB_TASKS_TABLE;
            $this->tickets_table = TIMBER_DB_PREFIX . TIMBER_DB_TICKETS_TABLE;
            $this->users_table = TIMBER_DB_PREFIX . TIMBER_DB_USERS_TABLE;
            $this->usersmeta_table = TIMBER_DB_PREFIX . TIMBER_DB_USERS_META_TABLE;
	}

	/**
	 * Check if user with that id, hash and nonce has admin access permission
	 *
	 * @since 1.0
	 * @access public
	 * @param array $user_data
	 * @return object
	 */
	public function hasAdminAccessRule($user_data)
	{
		return \ORM::for_table($this->users_table)
	      	->table_alias('user')
            	->select('user.us_id','user_id')
            	->select('meta.me_value', 'nonce')
            	->join($this->usersmeta_table, array('user.us_id', '=', 'meta.us_id'), 'meta')
            	->where('user.us_id', $user_data['u'])
            	->where('user.sec_hash', $user_data['h'])
            	->where('user.access_rule', '1')
            	->where('meta.me_key', '_user_access_nonce')
            	->find_one();
	}

	/**
	 * Check if user with that id, hash and nonce has staff access permission
	 *
	 * @since 1.0
	 * @access public
	 * @param array $user_data
	 * @return object
	 */
	public function hasStaffAccessRule($user_data)
	{
		return \ORM::for_table($this->users_table)
	      	->table_alias('user')
            	->select('user.us_id','user_id')
            	->select('meta.me_value', 'nonce')
            	->join($this->usersmeta_table, array('user.us_id', '=', 'meta.us_id'), 'meta')
            	->where('user.us_id', $user_data['u'])
            	->where('user.sec_hash', $user_data['h'])
            	->where('user.access_rule', '2')
            	->where('meta.me_key', '_user_access_nonce')
            	->find_one();
	}

	/**
	 * Check if user with that id, hash and nonce has client access permission
	 *
	 * @since 1.0
	 * @access public
	 * @param array $user_data
	 * @return object
	 */
	public function hasClientAccessRule($user_data)
	{
		return \ORM::for_table($this->users_table)
	      	->table_alias('user')
            	->select('user.us_id','user_id')
            	->select('meta.me_value', 'nonce')
            	->join($this->usersmeta_table, array('user.us_id', '=', 'meta.us_id'), 'meta')
            	->where('user.us_id', $user_data['u'])
            	->where('user.sec_hash', $user_data['h'])
            	->where('user.access_rule', '3')
            	->where('meta.me_key', '_user_access_nonce')
            	->find_one();
	}

	/**
	 * Get user data for login with social iden, social provider
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $identifier
	 * @param integer $provider
	 * @return object
	 */
	public function getUserWithIden($identifier, $provider)
	{
		return \ORM::for_table($this->users_table)
	      	->table_alias('user')
            	->select('user.us_id','user_id')
            	->select('user.access_rule','user_access_rule')
            	->select('user.sec_hash','user_hash')
            	->select('user.status','user_status')
            	->select('meta.me_value','user_nonce')
            	->select('meta.me_id','meta_id')
            	->join($this->usersmeta_table, array('user.us_id', '=', 'meta.us_id'), 'meta')
            	->where('user.identifier', $identifier)
            	->where('user.auth_by', $provider)
            	->where('meta.me_key', '_user_access_nonce')
            	->find_one();
	}

	/**
	 * Get user data for login with username
	 *
	 * @since 1.0
	 * @access public
	 * @param string $username
	 * @return object
	 */
	public function getUserWithUsername($username)
	{
		return \ORM::for_table($this->users_table)
	      	->table_alias('user')
            	->select('user.us_id','user_id')
            	->select('user.access_rule','user_access_rule')
            	->select('user.sec_hash','user_hash')
            	->select('user.status','user_status')
            	->select('user.password','user_password')
            	->select('meta.me_value','user_nonce')
            	->select('meta.me_id','meta_id')
            	->join($this->usersmeta_table, array('user.us_id', '=', 'meta.us_id'), 'meta')
            	->where('user.user_name', $username)
            	->where('meta.me_key', '_user_access_nonce')
            	->find_one();
	}

	/**
	 * Get user data for login with email
	 *
	 * @since 1.0
	 * @access public
	 * @param string $email
	 * @return object
	 */
	public function getUserWithEmail($email)
	{
		return \ORM::for_table($this->users_table)
	      	->table_alias('user')
            	->select('user.us_id','user_id')
            	->select('user.access_rule','user_access_rule')
            	->select('user.sec_hash','user_hash')
            	->select('user.status','user_status')
            	->select('user.password','user_password')
            	->select('meta.me_value','user_nonce')
            	->select('meta.me_id','meta_id')
            	->join($this->usersmeta_table, array('user.us_id', '=', 'meta.us_id'), 'meta')
            	->where('user.email', $email)
            	->where('meta.me_key', '_user_access_nonce')
            	->find_one();
	}

	/**
	 * Count projects by status and user id
	 *
	 * @since 1.0
	 * @since public
	 * @param integer $status
	 * @param integer $user_id
	 * @return integer
	 */
	public function countProjectsByStatusAndUser($status, $user_id)
	{
		return \ORM::for_table($this->projects_table)
	      	->table_alias('p')
            	->select('p.*')
            	->join($this->metas_table, array('p.pr_id', '=', 'm.rec_id'), 'm')
				->where('p.status', $status)
            	->where('m.rec_type', 11)
            	->where('m.me_key', 'project_members_list')
            	->where_like('m.me_value', '%|' . $user_id . '|%')
            	->count();
	}

	/**
	 * Get projects by status and user id
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $status
	 * @param integer $user_id
   	 * @param integer $offset
   	 * @param integer $limit
   	 * @param string $order_type
   	 * @param string $order_by
	 * @return array
	 */
	public function getProjectsByStatusAndUser($status, $user_id, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
	{
		if(($offset === false) && ($limit === false)){
			if( 'desc' == $order_type ){
				return \ORM::for_table($this->projects_table)
			      	->table_alias('p')
		            	->select('p.*')
		            	->join($this->metas_table, array('p.pr_id', '=', 'm.rec_id'), 'm')
						->where('p.status', $status)
		            	->where('m.rec_type', 11)
		            	->where('m.me_key', 'project_members_list')
		            	->where_like('m.me_value', '%|' . $user_id . '|%')
		            	->order_by_desc('p.' . $order_by)
		            	->find_array();
			}else{
				return \ORM::for_table($this->projects_table)
			      	->table_alias('p')
		            	->select('p.*')
		            	->join($this->metas_table, array('p.pr_id', '=', 'm.rec_id'), 'm')
						->where('p.status', $status)
		            	->where('m.rec_type', 11)
		            	->where('m.me_key', 'project_members_list')
		            	->where_like('m.me_value', '%|' . $user_id . '|%')
		            	->order_by_asc('p.' . $order_by)
		            	->find_array();
			}
		}else{
			if( 'desc' == $order_type ){
				return \ORM::for_table($this->projects_table)
			      	->table_alias('p')
		            	->select('p.*')
		            	->join($this->metas_table, array('p.pr_id', '=', 'm.rec_id'), 'm')
						->where('p.status', $status)
		            	->where('m.rec_type', 11)
		            	->where('m.me_key', 'project_members_list')
		            	->where_like('m.me_value', '%|' . $user_id . '|%')
		            	->order_by_desc('p.' . $order_by)
		            	->limit($limit)
		            	->offset($offset)
		            	->find_array();
			}else{
				return \ORM::for_table($this->projects_table)
			      	->table_alias('p')
		            	->select('p.*')
		            	->join($this->metas_table, array('p.pr_id', '=', 'm.rec_id'), 'm')
						->where('p.status', $status)
		            	->where('m.rec_type', 11)
		            	->where('m.me_key', 'project_members_list')
		            	->where_like('m.me_value', '%|' . $user_id . '|%')
		            	->order_by_asc('p.' . $order_by)
		            	->limit($limit)
		            	->offset($offset)
		            	->find_array();
			}
		}
	}

	/**
	 * Get projects by user id
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $user_id
   	 * @param integer $offset
   	 * @param integer $limit
   	 * @param string $order_type
   	 * @param string $order_by
	 * @return array
	 */
	public function getProjectsByUser($user_id, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
	{
		if(($offset === false) && ($limit === false)){
			if( 'desc' == $order_type ){
				return \ORM::for_table($this->projects_table)
			      	->table_alias('p')
		            	->select('p.*')
		            	->join($this->metas_table, array('p.pr_id', '=', 'm.rec_id'), 'm')
		            	->where('m.rec_type', 11)
		            	->where('m.me_key', 'project_members_list')
		            	->where_like('m.me_value', '%|' . $user_id . '|%')
		            	->order_by_desc('p.' . $order_by)
		            	->find_array();
			}else{
				return \ORM::for_table($this->projects_table)
			      	->table_alias('p')
		            	->select('p.*')
		            	->join($this->metas_table, array('p.pr_id', '=', 'm.rec_id'), 'm')
		            	->where('m.rec_type', 11)
		            	->where('m.me_key', 'project_members_list')
		            	->where_like('m.me_value', '%|' . $user_id . '|%')
		            	->order_by_asc('p.' . $order_by)
		            	->find_array();
			}
		}else{
			if( 'desc' == $order_type ){
				return \ORM::for_table($this->projects_table)
			      	->table_alias('p')
		            	->select('p.*')
		            	->join($this->metas_table, array('p.pr_id', '=', 'm.rec_id'), 'm')
		            	->where('m.rec_type', 11)
		            	->where('m.me_key', 'project_members_list')
		            	->where_like('m.me_value', '%|' . $user_id . '|%')
		            	->order_by_desc('p.' . $order_by)
		            	->limit($limit)
		            	->offset($offset)
		            	->find_array();
			}else{
				return \ORM::for_table($this->projects_table)
			      	->table_alias('p')
		            	->select('p.*')
		            	->join($this->metas_table, array('p.pr_id', '=', 'm.rec_id'), 'm')
		            	->where('m.rec_type', 11)
		            	->where('m.me_key', 'project_members_list')
		            	->where_like('m.me_value', '%|' . $user_id . '|%')
		            	->order_by_asc('p.' . $order_by)
		            	->limit($limit)
		            	->offset($offset)
		            	->find_array();
			}
		}
	}

	/**
	 * Count total tickets by status and user id
	 *
   	 * @since 1.0
   	 * @access public
	 * @param integer $status
	 * @param integer $user_id
   	 * @return integer
	 */
	public function countTicketsByStatusAndUser($status, $user_id)
	{
		return \ORM::for_table($this->tickets_table)
	      	->table_alias('t')
            	->select('t.*')
            	->join($this->metas_table, array('t.pr_id', '=', 'm.rec_id'), 'm')
				->where('t.status', $status)
				->where('t.depth', 1)
            	->where('m.rec_type', 11)
            	->where('m.me_key', 'project_members_list')
            	->where_like('m.me_value', '%|' . $user_id . '|%')
            	->count();
	}

	/**
	 * Get tickets by status and user id
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $status
	 * @param integer $user_id
   	 * @param integer $offset
   	 * @param integer $limit
   	 * @param string $order_type
   	 * @param string $order_by
	 * @return array
	 */
	public function getTicketsByStatusAndUser($status, $user_id, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
	{
		if(($offset === false) && ($limit === false)){
			if( 'desc' == $order_type ){
				return \ORM::for_table($this->tickets_table)
			      	->table_alias('t')
		            	->select('t.*')
		            	->join($this->metas_table, array('t.pr_id', '=', 'm.rec_id'), 'm')
						->where('t.status', $status)
						->where('t.depth', 1)
		            	->where('m.rec_type', 11)
		            	->where('m.me_key', 'project_members_list')
		            	->where_like('m.me_value', '%|' . $user_id . '|%')
		            	->order_by_desc('t.' . $order_by)
		            	->find_array();
			}else{
				return \ORM::for_table($this->tickets_table)
			      	->table_alias('t')
		            	->select('t.*')
		            	->join($this->metas_table, array('t.pr_id', '=', 'm.rec_id'), 'm')
						->where('t.status', $status)
						->where('t.depth', 1)
		            	->where('m.rec_type', 11)
		            	->where('m.me_key', 'project_members_list')
		            	->where_like('m.me_value', '%|' . $user_id . '|%')
		            	->order_by_asc('t.' . $order_by)
		            	->find_array();
			}
		}else{
			if( 'desc' == $order_type ){
				return \ORM::for_table($this->tickets_table)
			      	->table_alias('t')
		            	->select('t.*')
		            	->join($this->metas_table, array('t.pr_id', '=', 'm.rec_id'), 'm')
						->where('t.status', $status)
						->where('t.depth', 1)
		            	->where('m.rec_type', 11)
		            	->where('m.me_key', 'project_members_list')
		            	->where_like('m.me_value', '%|' . $user_id . '|%')
		            	->order_by_desc('t.' . $order_by)
		            	->limit($limit)
		            	->offset($offset)
		            	->find_array();
			}else{
				return \ORM::for_table($this->tickets_table)
			      	->table_alias('t')
		            	->select('t.*')
		            	->join($this->metas_table, array('t.pr_id', '=', 'm.rec_id'), 'm')
						->where('t.status', $status)
						->where('t.depth', 1)
		            	->where('m.rec_type', 11)
		            	->where('m.me_key', 'project_members_list')
		            	->where_like('m.me_value', '%|' . $user_id . '|%')
		            	->order_by_asc('t.' . $order_by)
		            	->limit($limit)
		            	->offset($offset)
		            	->find_array();
			}
		}
	}
}