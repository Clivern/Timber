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
 * Invoice Model
 *
 * Perform All CRUD Operations.
 *
 * `in_id` int(11) not null auto_increment,
 * `reference` varchar(50) not null,
 * `owner_id` int(11) not null,
 * `client_id` int(11) not null,
 * `status` enum('1','2','3','4','5','6','7','8','9') not null,
 * `type` enum('1','2','3','4','5','6','7','8','9') not null,
 * `terms` text not null,
 * `tax` varchar(20) not null,
 * `discount` varchar(20) not null,
 * `total` varchar(20) not null,
 * `attach` enum('on','off') not null,
 * `rec_type` varchar(20) not null,
 * `rec_id` int(11) not null,
 * `due_date` datetime not null,
 * `issue_date` datetime not null,
 * `created_at` datetime not null,
 * `updated_at` datetime not null,
 *
 * type => (1) invoice  (2) estimate (3) expense
 * status => (1) invoice unpaid (2) invoice paid
 *  	  => (1) estimate opened  (2) estimate sent to client  (3) estimate accepted from client  (4) estimate rejected from client  (5) estimate invoiced  (6) estimate closed
 *     	  => (1) payment expense (2) refund expense
 *
 * @since 1.0
 */

class Invoice {

	/**
	 * Invoices table name
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
		$this->table = TIMBER_DB_PREFIX . TIMBER_DB_INVOICES_TABLE;
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
	 * Count total invoices
	 *
   	 * @since 1.0
   	 * @access public
   	 * @return integer
	 */
	public function countInvoices()
	{
		return \ORM::for_table($this->table)->count();
	}

	/**
	 * Count total invoices by custom filter
	 *
   	 * @since 1.0
   	 * @access public
   	 * @param array $where
   	 * @return integer
	 */
	public function countInvoicesBy($where)
	{
		return \ORM::for_table($this->table)->where($where)->count();
	}

  	/**
   	 * Count invoices according to search term
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param string $column
   	 * @param string $column_value
   	 * @param array $where
   	 * @return integer
   	 */
	public function countSearchInvoices($column, $column_value, $where)
	{
		if( (is_array($where)) && (count($where) > 0) ){
			return \ORM::for_table($this->table)->where($where)->where_like($column, $column_value)->count();
		}else{
			return \ORM::for_table($this->table)->where_like($column, $column_value)->count();
		}
	}

	/**
	 * Get invoices
	 *
	 * @since 1.0
	 * @access public
   	 * @param integer $offset
   	 * @param integer $limit
   	 * @param string $order_type
   	 * @param string $order_by
	 * @return array
	 */
	public function getInvoices($offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
	 * Get invoices by custom condition
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
	public function getInvoicesBy($where, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
   	 * Get invoices according to search term
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
	public function getSearchInvoices($column, $column_value, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
   	 * Get invoices according to search term and custom condition
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
	public function getSearchInvoicesBy($where, $column, $column_value, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
	 * Get invoice by id
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $in_id
	 * @return object
	 */
	public function getInvoiceById($in_id)
	{
		return \ORM::for_table($this->table)->where('in_id', $in_id)->find_one();
	}

	/**
	 * Get invoice by multiple
	 *
	 * @since 1.0
	 * @access public
	 * @param array $invoice_data
	 * @return object
	 */
	public function getInvoiceByMultiple($invoice_data)
	{
		$where = array();
		if(isset($invoice_data['in_id'])){
			$where['in_id'] = $invoice_data['in_id'];
		}
		if(isset($invoice_data['reference'])){
			$where['reference'] = $invoice_data['reference'];
		}
		if(isset($invoice_data['owner_id'])){
			$where['owner_id'] = $invoice_data['owner_id'];
		}
		if(isset($invoice_data['client_id'])){
			$where['client_id'] = $invoice_data['client_id'];
		}
		if(isset($invoice_data['status'])){
			$where['status'] = $invoice_data['status'];
		}
		if(isset($invoice_data['type'])){
			$where['type'] = $invoice_data['type'];
		}
		if(isset($invoice_data['attach'])){
			$where['attach'] = $invoice_data['attach'];
		}
		if(isset($invoice_data['rec_type'])){
			$where['rec_type'] = $invoice_data['rec_type'];
		}
		if(isset($invoice_data['rec_id'])){
			$where['rec_id'] = $invoice_data['rec_id'];
		}
		return \ORM::for_table($this->table)->where($where)->find_one();
	}

	/**
	 * Add invoice
	 *
	 * @since 1.0
	 * @access public
	 * @param array $invoice_data
	 * @return boolean
	 */
	public function addInvoice($invoice_data)
	{
		$invoice = \ORM::for_table($this->table)->create();
		$invoice->reference = $invoice_data['reference'];
		$invoice->owner_id = $invoice_data['owner_id'];
		$invoice->client_id = $invoice_data['client_id'];
		$invoice->status = $invoice_data['status'];
		$invoice->type = $invoice_data['type'];
		$invoice->terms = $invoice_data['terms'];
		$invoice->tax = $invoice_data['tax'];
		$invoice->discount = $invoice_data['discount'];
		$invoice->total = $invoice_data['total'];
		$invoice->attach = $invoice_data['attach'];
		$invoice->rec_type = $invoice_data['rec_type'];
		$invoice->rec_id = $invoice_data['rec_id'];
		$invoice->due_date = $invoice_data['due_date'];
		$invoice->issue_date = $invoice_data['issue_date'];
		$invoice->created_at = $invoice_data['created_at'];
		$invoice->updated_at = $invoice_data['updated_at'];
		$invoice->save();
		return $invoice->id();
	}

   	/**
   	 * Get unique fresh reference for invoice
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param string $for
   	 * @return string
   	 */
	public function newReference($for)
	{
		$reference_found = false;
		while ( !$reference_found ) {
			$reference = "{$for}-" . substr(md5(uniqid(mt_rand(), true)), 0, 5);
			$invoice = $this->getInvoiceByMultiple(array('reference' => $reference));
			if( (false === $invoice) || !(is_object($invoice)) ){
				$reference_found = true;
			}
		}
		return $reference;
	}

	/**
	 * Update invoice by id
	 *
	 * @since 1.0
	 * @access public
	 * @param array $invoice_data
	 * @return boolean
	 */
	public function updateInvoiceById($invoice_data)
	{
		$invoice = $this->getInvoiceById($invoice_data['in_id']);
   		if( (false === $invoice) || !(is_object($invoice)) ){
   			return false;
   		}
		unset($invoice_data['in_id']);
		$invoice->set($invoice_data);
		return (boolean) $invoice->save();
	}

	/**
	 * Delete invoice by id
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $in_id
	 * @return boolean
	 */
	public function deleteInvoiceById($in_id)
	{
		$invoice = $this->getInvoiceById($in_id);
   		if( (false === $invoice) || !(is_object($invoice)) ){
   			return false;
   		}
		return (boolean) $invoice->delete();
	}

	/**
	 * Delete invoice by multiple
	 *
	 * @since 1.0
	 * @access public
	 * @param array $invoice_data
	 * @return boolean
	 */
	public function deleteInvoiceByMultiple($invoice_data)
	{
		$invoice = $this->getInvoiceByMultiple($invoice_data);
   		if( (false === $invoice) || !(is_object($invoice)) ){
   			return false;
   		}
		return (boolean) $invoice->delete();

	}

   	/**
   	 * Delete invoice by id or owner id or client id
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param integer $in_id
   	 * @param integer $owner_id
   	 * @param integer $client_id
   	 * @return boolean
   	 */
	public function dumpInvoice($in_id = false, $owner_id = false, $client_id = false)
	{
		if($in_id !== false){
			return (boolean) \ORM::for_table($this->table)->where_equal('in_id', $in_id)->delete_many();
		}
		if($owner_id !== false){
			return (boolean) \ORM::for_table($this->table)->where_equal('owner_id', $owner_id)->delete_many();
		}
		if($client_id !== false){
			return (boolean) \ORM::for_table($this->table)->where_equal('client_id', $client_id)->delete_many();
		}
	}
}