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
 * Quotation Model
 *
 * Perform All CRUD Operations.
 *
 * `qu_id` int(11) not null auto_increment,
 * `title` varchar(50) not null,
 * `reference` varchar(50) not null,
 * `owner_id` int(11) not null,
 * `terms` text not null,
 * `created_at` datetime not null,
 * `updated_at` datetime not null,
 *
 * @since 1.0
 */
class Quotation {

	/**
	 * Quotations table name
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
		$this->table = TIMBER_DB_PREFIX . TIMBER_DB_QUOTATIONS_TABLE;
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
	 * Count total quotations
	 *
   	 * @since 1.0
   	 * @access public
   	 * @return integer
	 */
	public function countQuotations()
	{
		return \ORM::for_table($this->table)->count();
	}

	/**
	 * Count total quotations by custom filter
	 *
   	 * @since 1.0
   	 * @access public
   	 * @param array $where
   	 * @return integer
	 */
	public function countQuotationsBy($where)
	{
		return \ORM::for_table($this->table)->where($where)->count();
	}

  	/**
   	 * Count quotations according to search term
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param string $column
   	 * @param string $column_value
   	 * @param array $where
   	 * @return integer
   	 */
	public function countSearchQuotations($column, $column_value, $where)
	{
		if( (is_array($where)) && (count($where) > 0) ){
			return \ORM::for_table($this->table)->where($where)->where_like($column, $column_value)->count();
		}else{
			return \ORM::for_table($this->table)->where_like($column, $column_value)->count();
		}
	}

	/**
	 * Get quotations
	 *
	 * @since 1.0
	 * @access public
   	 * @param integer $offset
   	 * @param integer $limit
   	 * @param string $order_type
   	 * @param string $order_by
	 * @return array
	 */
	public function getQuotations($offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
	 * Get quotations by custom condition
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
	public function getQuotationsBy($where, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
   	 * Get quotations according to search term
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
	public function getSearchQuotations($column, $column_value, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
   	 * Get quotations according to search term and custom condition
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
	public function getSearchQuotationsBy($where, $column, $column_value, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
	 * Get quotation by id
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $qu_id
	 * @return object
	 */
	public function getQuotationById($qu_id)
	{
		return \ORM::for_table($this->table)->where('qu_id', $qu_id)->find_one();
	}

	/**
	 * Get quotation by multiple
	 *
	 * @since 1.0
	 * @access public
	 * @param array $quotation_data
	 * @return object
	 */
	public function getQuotationByMultiple($quotation_data)
	{
		$where = array();
		if(isset($quotation_data['qu_id'])){
			$where['qu_id'] = $quotation_data['qu_id'];
		}
		if(isset($quotation_data['reference'])){
			$where['reference'] = $quotation_data['reference'];
		}
		if(isset($quotation_data['owner_id'])){
			$where['owner_id'] = $quotation_data['owner_id'];
		}
		return \ORM::for_table($this->table)->where($where)->find_one();
	}

	/**
	 * Add quotation
	 *
	 * @since 1.0
	 * @access public
	 * @param array $quotation_data
	 * @return boolean
	 */
	public function addQuotation($quotation_data)
	{
		$quotation = \ORM::for_table($this->table)->create();
		$quotation->title = $quotation_data['title'];
		$quotation->reference = $quotation_data['reference'];
		$quotation->owner_id = $quotation_data['owner_id'];
		$quotation->terms = $quotation_data['terms'];
		$quotation->created_at = $quotation_data['created_at'];
		$quotation->updated_at = $quotation_data['updated_at'];
		$quotation->save();
		return $quotation->id();
	}

   	/**
   	 * Get unique fresh reference for quotation
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
			$quotation = $this->getQuotationByMultiple(array('reference' => $reference));
			if( (false === $quotation) || !(is_object($quotation)) ){
				$reference_found = true;
			}
		}
		return $reference;
	}

	/**
	 * Update quotation by id
	 *
	 * @since 1.0
	 * @access public
	 * @param array $quotation_data
	 * @return boolean
	 */
	public function updateQuotationById($quotation_data)
	{
		$quotation = $this->getQuotationById($quotation_data['qu_id']);
   		if( (false === $quotation) || !(is_object($quotation)) ){
   			return false;
   		}
		unset($quotation_data['qu_id']);
		$quotation->set($quotation_data);
		return (boolean) $quotation->save();
	}

	/**
	 * Delete quotation by id
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $qu_id
	 * @return boolean
	 */
	public function deleteQuotationById($qu_id)
	{
		$quotation = $this->getQuotationById($qu_id);
   		if( (false === $quotation) || !(is_object($quotation)) ){
   			return false;
   		}
		return (boolean) $quotation->delete();
	}

	/**
	 * Delete quotation by multiple
	 *
	 * @since 1.0
	 * @access public
	 * @param array $quotation_data
	 * @return boolean
	 */
	public function deleteQuotationByMultiple($quotation_data)
	{
		$quotation = $this->getQuotationByMultiple($quotation_data);
   		if( (false === $quotation) || !(is_object($quotation)) ){
   			return false;
   		}
		return (boolean) $quotation->delete();

	}

   	/**
   	 * Delete quotation by id or post id
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param integer $qu_id
   	 * @return boolean
   	 */
	public function dumpQuotation($qu_id = false)
	{
		if($qu_id !== false){
			return (boolean) \ORM::for_table($this->table)->where_equal('qu_id', $qu_id)->delete_many();
		}
	}
}