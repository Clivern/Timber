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
 * Item Model
 *
 * Perform All CRUD Operations.
 *
 * `it_id` int(11) not null auto_increment,
 * `title` varchar(100) not null,
 * `owner_id` int(11) not null,
 * `description` varchar(250) not null,
 * `cost` varchar(20) not null,
 * `created_at` datetime not null,
 * `updated_at` datetime not null,
 *
 * @since 1.0
 */
class Item {

	/**
	 * Items table name
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
		$this->table = TIMBER_DB_PREFIX . TIMBER_DB_ITEMS_TABLE;
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
	 * Count total items
	 *
   	 * @since 1.0
   	 * @access public
   	 * @return integer
	 */
	public function countItems()
	{
		return \ORM::for_table($this->table)->count();
	}

	/**
	 * Count total item by custom filter
	 *
   	 * @since 1.0
   	 * @access public
   	 * @param array $where
   	 * @return integer
	 */
	public function countItemsBy($where)
	{
		return \ORM::for_table($this->table)->where($where)->count();
	}

  	/**
   	 * Count items according to search term
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param string $column
   	 * @param string $column_value
   	 * @param array $where
   	 * @return integer
   	 */
	public function countSearchItems($column, $column_value, $where)
	{
		if( (is_array($where)) && (count($where) > 0) ){
			return \ORM::for_table($this->table)->where($where)->where_like($column, $column_value)->count();
		}else{
			return \ORM::for_table($this->table)->where_like($column, $column_value)->count();
		}
	}

	/**
	 * Get items
	 *
	 * @since 1.0
	 * @access public
   	 * @param integer $offset
   	 * @param integer $limit
   	 * @param string $order_type
   	 * @param string $order_by
	 * @return array
	 */
	public function getItems($offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
	 * Get items by custom condition
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
	public function getItemsBy($where, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
   	 * Get items according to search term
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
	public function getSearchItems($column, $column_value, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
   	 * Get items according to search term and custom condition
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
	public function getSearchItemsBy($where, $column, $column_value, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
	 * Get item by id
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $it_id
	 * @return object
	 */
	public function getItemById($it_id)
	{
		return \ORM::for_table($this->table)->where('it_id', $it_id)->find_one();
	}

	/**
	 * Get item by multiple
	 *
	 * @since 1.0
	 * @access public
	 * @param array $item_data
	 * @return object
	 */
	public function getItemByMultiple($item_data)
	{
		$where = array();
		if(isset($item_data['it_id'])){
			$where['it_id'] = $item_data['it_id'];
		}
		if(isset($item_data['owner_id'])){
			$where['owner_id'] = $item_data['owner_id'];
		}
		return \ORM::for_table($this->table)->where($where)->find_one();
	}

	/**
	 * Add item
	 *
	 * @since 1.0
	 * @access public
	 * @param array $item_data
	 * @return boolean
	 */
	public function addItem($item_data)
	{
		$item = \ORM::for_table($this->table)->create();
		$item->title = $item_data['title'];
		$item->owner_id = $item_data['owner_id'];
		$item->description = $item_data['description'];
		$item->cost = $item_data['cost'];
		$item->created_at = $item_data['created_at'];
		$item->updated_at = $item_data['updated_at'];
		$item->save();
		return $item->id();
	}

	/**
	 * Update item by id
	 *
	 * @since 1.0
	 * @access public
	 * @param array $item_data
	 * @return boolean
	 */
	public function updateItemById($item_data)
	{
		$item = $this->getItemById($item_data['it_id']);
   		if( (false === $item) || !(is_object($item)) ){
   			return false;
   		}
		unset($item_data['it_id']);
		$item->set($item_data);
		return (boolean) $item->save();
	}

	/**
	 * Delete item by id
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $it_id
	 * @return boolean
	 */
	public function deleteItemById($it_id)
	{
		$item = $this->getItemById($it_id);
   		if( (false === $item) || !(is_object($item)) ){
   			return false;
   		}
		return (boolean) $item->delete();
	}

	/**
	 * Delete item by multiple
	 *
	 * @since 1.0
	 * @access public
	 * @param array $item_data
	 * @return boolean
	 */
	public function deleteItemByMultiple($item_data)
	{
		$item = $this->getItemByMultiple($item_data);
   		if( (false === $item) || !(is_object($item)) ){
   			return false;
   		}
		return (boolean) $item->delete();

	}

   	/**
   	 * Delete item by id or project id
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param integer $it_id
   	 * @return boolean
   	 */
	public function dumpItem($it_id = false)
	{
		if($it_id !== false){
			return (boolean) \ORM::for_table($this->table)->where_equal('it_id', $it_id)->delete_many();
		}
	}
}