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
 * Ticket Model
 *
 * Perform All CRUD Operations.
 *
 * `ti_id` int(11) not null auto_increment,
 * `pr_id` int(11) not null,
 * `parent_id` int(11) not null,
 * `reference` varchar(50) not null,
 * `owner_id` int(11) not null,
 * `status` enum('1','2','3','4','5','6','7','8','9') not null,
 * `type` enum('1','2','3','4','5','6','7','8','9') not null,
 * `depth` enum('1','2','3','4','5','6','7','8','9') not null,
 * `subject` varchar(150) not null,
 * `content` text not null,
 * `attach` enum('on','off') not null,
 * `created_at` datetime not null,
 * `updated_at` datetime not null,
 *
 * status => (1-Pending) (2-Opened) (3-Closed) (4-) (5-) (6-) (7-)
 * type => (1-Inquiry) (2-Suggestion) (3-Normal Bug) (4-Critical Bug) (5-Security Bug) (6-) (7-)
 *
 * @since 1.0
 */
class Ticket {

	/**
	 * Tickets table name
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->table
	 */
	private $table;

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
		//prefix table name
		$this->table = TIMBER_DB_PREFIX . TIMBER_DB_TICKETS_TABLE;
	}

	/**
	 * Get instance of orm object
	 *
   	 * @since 1.0
   	 * @access public
   	 * @return object
	 */
	public function queryBuilder()
	{
		return \ORM::for_table($this->table);
	}

	/**
	 * Count total tickets
	 *
   	 * @since 1.0
   	 * @access public
   	 * @return integer
	 */
	public function countTickets()
	{
		return \ORM::for_table($this->table)->count();
	}

	/**
	 * Count total tickets by custom filter
	 *
   	 * @since 1.0
   	 * @access public
   	 * @param array $where
   	 * @return integer
	 */
	public function countTicketsBy($where)
	{
		return \ORM::for_table($this->table)->where($where)->count();
	}

  	/**
   	 * Count tickets according to search term
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param string $column
   	 * @param string $column_value
   	 * @param array $where
   	 * @return integer
   	 */
	public function countSearchTickets($column, $column_value, $where)
	{
		if( (is_array($where)) && (count($where) > 0) ){
			return \ORM::for_table($this->table)->where($where)->where_like($column, $column_value)->count();
		}else{
			return \ORM::for_table($this->table)->where_like($column, $column_value)->count();
		}
	}

	/**
	 * Get tickets
	 *
	 * @since 1.0
	 * @access public
   	 * @param integer $offset
   	 * @param integer $limit
   	 * @param string $order_type
   	 * @param string $order_by
	 * @return array
	 */
	public function getTickets($offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
	{
		if(($offset === false) && ($limit === false)){
			if( 'desc' == $order_type ){
				return \ORM::for_table($this->table)->order_by_desc($order_by)->find_array();
			}else{
				return \ORM::for_table($this->table)->order_by_asc($order_by)->find_array();
			}
		}else{
			if( 'desc' == $order_type ){
				return \ORM::for_table($this->table)->order_by_desc($order_by)->limit($limit)->offset($offset)->find_array();
			}else{
				return \ORM::for_table($this->table)->order_by_asc($order_by)->limit($limit)->offset($offset)->find_array();
			}
		}
	}

	/**
	 * Get tickets by custom condition
	 *
	 * @since 1.0
	 * @access public
	 * @param array $where
   	 * @param integer $offset
   	 * @param integer $limit
   	 * @param string $order_type
   	 * @param string $order_by
	 * @return array
	 */
	public function getTicketsBy($where, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
	{
		if(($offset === false) && ($limit === false)){
			if( 'desc' == $order_type ){
				return \ORM::for_table($this->table)->where($where)->order_by_desc($order_by)->find_array();
			}else{
				return \ORM::for_table($this->table)->where($where)->order_by_asc($order_by)->find_array();
			}
		}else{
			if( 'desc' == $order_type ){
				return \ORM::for_table($this->table)->where($where)->order_by_desc($order_by)->limit($limit)->offset($offset)->find_array();
			}else{
				return \ORM::for_table($this->table)->where($where)->order_by_asc($order_by)->limit($limit)->offset($offset)->find_array();
			}
		}
	}

  	/**
   	 * Get tickets according to search term
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param string $column
   	 * @param string $column_value
   	 * @param integer $offset
   	 * @param integer $limit
   	 * @param string $order_type
   	 * @param string $order_by
   	 * @return array
   	 */
	public function getSearchTickets($column, $column_value, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
	{
		if(($offset === false) && ($limit === false)){
			if( 'desc' == $order_type ){
				return \ORM::for_table($this->table)->where_like($column, $column_value)->order_by_desc($order_by)->find_array();
			}else{
				return \ORM::for_table($this->table)->where_like($column, $column_value)->order_by_asc($order_by)->find_array();
			}
		}else{
			if( 'desc' == $order_type ){
				return \ORM::for_table($this->table)->where_like($column, $column_value)->order_by_desc($order_by)->limit($limit)->offset($offset)->find_array();
			}else{
				return \ORM::for_table($this->table)->where_like($column, $column_value)->order_by_asc($order_by)->limit($limit)->offset($offset)->find_array();
			}
		}
	}

  	/**
   	 * Get tickets according to search term and custom condition
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param array $where
   	 * @param string $column
   	 * @param string $column_value
   	 * @param integer $offset
   	 * @param integer $limit
   	 * @param string $order_type
   	 * @param string $order_by
   	 * @return array
   	 */
	public function getSearchTicketsBy($where, $column, $column_value, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
	{
		if(($offset === false) && ($limit === false)){
			if( 'desc' == $order_type ){
				return \ORM::for_table($this->table)->where($where)->where_like($column, $column_value)->order_by_desc($order_by)->find_array();
			}else{
				return \ORM::for_table($this->table)->where($where)->where_like($column, $column_value)->order_by_asc($order_by)->find_array();
			}
		}else{
			if( 'desc' == $order_type ){
				return \ORM::for_table($this->table)->where($where)->where_like($column, $column_value)->order_by_desc($order_by)->limit($limit)->offset($offset)->find_array();
			}else{
				return \ORM::for_table($this->table)->where($where)->where_like($column, $column_value)->order_by_asc($order_by)->limit($limit)->offset($offset)->find_array();
			}
		}
	}

	/**
	 * Get ticket by id
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $ti_id
	 * @return object
	 */
	public function getTicketById($ti_id)
	{
		return \ORM::for_table($this->table)->where('ti_id', $ti_id)->find_one();
	}

	/**
	 * Get ticket by multiple
	 *
	 * @since 1.0
	 * @access public
	 * @param array $ticket_data
	 * @return object
	 */
	public function getTicketByMultiple($ticket_data)
	{
		$where = array();
		if(isset($ticket_data['ti_id'])){
			$where['ti_id'] = $ticket_data['ti_id'];
		}
		if(isset($ticket_data['pr_id'])){
			$where['pr_id'] = $ticket_data['pr_id'];
		}
		if(isset($ticket_data['parent_id'])){
			$where['parent_id'] = $ticket_data['parent_id'];
		}
		if(isset($ticket_data['reference'])){
			$where['reference'] = $ticket_data['reference'];
		}
		if(isset($ticket_data['owner_id'])){
			$where['owner_id'] = $ticket_data['owner_id'];
		}
		/*
		if(isset($ticket_data['status'])){
			$where['status'] = $ticket_data['status'];
		}
		*/
		if(isset($ticket_data['type'])){
			$where['type'] = $ticket_data['type'];
		}
		if(isset($ticket_data['depth'])){
			$where['depth'] = $ticket_data['depth'];
		}
		return \ORM::for_table($this->table)->where($where)->find_one();
	}

	/**
	 * Add ticket
	 *
	 * @since 1.0
	 * @access public
	 * @param array $ticket_data
	 * @return boolean
	 */
	public function addTicket($ticket_data)
	{
		$ticket = \ORM::for_table($this->table)->create();
		$ticket->pr_id = $ticket_data['pr_id'];
		$ticket->parent_id = $ticket_data['parent_id'];
		$ticket->reference = $ticket_data['reference'];
		$ticket->owner_id = $ticket_data['owner_id'];
		$ticket->status = $ticket_data['status'];
		$ticket->type = $ticket_data['type'];
		$ticket->depth = $ticket_data['depth'];
		$ticket->subject = $ticket_data['subject'];
		$ticket->content = $ticket_data['content'];
		$ticket->attach = $ticket_data['attach'];
		$ticket->created_at = $ticket_data['created_at'];
		$ticket->updated_at = $ticket_data['updated_at'];
		$ticket->save();
		return $ticket->id();
	}

   	/**
   	 * Get unique fresh reference for ticket
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @return string
   	 */
	public function newReference($for)
	{
		$reference_found = false;
		while ( !$reference_found ) {
			$reference = "{$for}-" . substr(md5(uniqid(mt_rand(), true)), 0, 5);
			$ticket = $this->getTicketByMultiple(array('reference' => $reference));
			if( (false === $ticket) || !(is_object($ticket)) ){
				$reference_found = true;
			}
		}
		return $reference;
	}

	/**
	 * Update ticket by id
	 *
	 * @since 1.0
	 * @access public
	 * @param array $ticket_data
	 * @return boolean
	 */
	public function updateTicketById($ticket_data)
	{
		$ticket = $this->getTicketById($ticket_data['ti_id']);
   		if( (false === $ticket) || !(is_object($ticket)) ){
   			return false;
   		}
		unset($ticket_data['ti_id']);
		$ticket->set($ticket_data);
		return (boolean) $ticket->save();
	}

	/**
	 * Update ticket by multiple
	 *
	 * @since 1.0
	 * @access public
	 * @param array $ticket_data
	 * @return boolean
	 */
	public function updateTicketByMultiple($ticket_data)
	{
		$ticket = $this->getTicketByMultiple($ticket_data);
   		if( (false === $ticket) || !(is_object($ticket)) ){
   			return false;
   		}
   		if(isset($ticket_data['ti_id'])){
			unset($ticket_data['ti_id']);
		}
   		if(isset($ticket_data['pr_id'])){
			unset($ticket_data['pr_id']);
		}
   		if(isset($ticket_data['parent_id'])){
			unset($ticket_data['parent_id']);
		}
   		if(isset($ticket_data['reference'])){
			unset($ticket_data['reference']);
		}
   		if(isset($ticket_data['owner_id'])){
			unset($ticket_data['owner_id']);
		}
		$ticket->set($ticket_data);
		return (boolean) $ticket->save();
	}

	/**
	 * Delete ticket by id
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $ti_id
	 * @return boolean
	 */
	public function deleteTicketById($ti_id)
	{
		$ticket = $this->getTicketById($ti_id);
   		if( (false === $ticket) || !(is_object($ticket)) ){
   			return false;
   		}
		return (boolean) $ticket->delete();
	}

	/**
	 * Delete ticket by multiple
	 *
	 * @since 1.0
	 * @access public
	 * @param array $ticket_data
	 * @return boolean
	 */
	public function deleteTicketByMultiple($ticket_data)
	{
		$ticket = $this->getTicketByMultiple($ticket_data);
   		if( (false === $ticket) || !(is_object($ticket)) ){
   			return false;
   		}
		return (boolean) $ticket->delete();

	}

   	/**
   	 * Delete ticket by id or post id
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param integer $ti_id
   	 * @param integer $pr_id
   	 * @return boolean
   	 */
	public function dumpTicket($ti_id = false, $pr_id = false)
	{
		if($ti_id !== false){
			return (boolean) \ORM::for_table($this->table)->where_equal('ti_id', $ti_id)->delete_many();
		}
		if($pr_id !== false){
			return (boolean) \ORM::for_table($this->table)->where_equal('pr_id', $pr_id)->delete_many();
		}
	}
}