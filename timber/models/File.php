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
 * File Model
 *
 * Perform All CRUD Operations.
 *
 * `fi_id` int(11) not null auto_increment,
 * `title` varchar(100) not null,
 * `hash` varchar(150) not null,
 * `owner_id` int(11) not null,
 * `description` varchar(150) not null,
 * `storage` enum('1','2','3','4','5','6','7','8','9') not null,
 * `type` varchar(50) not null,
 * `uploaded_at` datetime not null,
 *
 * storage (1) public (2) private
 *
 * @since 1.0
 */
class File {

	/**
	 * Files table name
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
		$this->table = TIMBER_DB_PREFIX . TIMBER_DB_FILES_TABLE;
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
	 * Count total files
	 *
   	 * @since 1.0
   	 * @access public
   	 * @return integer
	 */
	public function countFiles()
	{
		return \ORM::for_table($this->table)->count();
	}

	/**
	 * Count total files by custom filter
	 *
   	 * @since 1.0
   	 * @access public
   	 * @param array $where
   	 * @return integer
	 */
	public function countFilesBy($where)
	{
		return \ORM::for_table($this->table)->where($where)->count();
	}

  	/**
   	 * Count files according to search term
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param string $column
   	 * @param string $column_value
   	 * @param array $where
   	 * @return integer
   	 */
	public function countSearchFiles($column, $column_value, $where)
	{
		if( (is_array($where)) && (count($where) > 0) ){
			return \ORM::for_table($this->table)->where($where)->where_like($column, $column_value)->count();
		}else{
			return \ORM::for_table($this->table)->where_like($column, $column_value)->count();
		}
	}

	/**
	 * Get files
	 *
	 * @since 1.0
	 * @access public
   	 * @param integer $offset
   	 * @param integer $limit
   	 * @param string $order_type
   	 * @param string $order_by
	 * @return array
	 */
	public function getFiles($offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
	 * Get files by custom condition
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
	public function getFilesBy($where, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
   	 * Get files according to search term
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
	public function getSearchFiles($column, $column_value, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
   	 * Get files according to search term and custom condition
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
	public function getSearchFilesBy($where, $column, $column_value, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
	 * Get file by id
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $fi_id
	 * @return object
	 */
	public function getFileById($fi_id)
	{
		return \ORM::for_table($this->table)->where('fi_id', $fi_id)->find_one();
	}

	/**
	 * Get file by multiple
	 *
	 * @since 1.0
	 * @access public
	 * @param array $file_data
	 * @return object
	 */
	public function getFileByMultiple($file_data)
	{
		$where = array();
		if(isset($file_data['fi_id'])){
			$where['fi_id'] = $file_data['fi_id'];
		}
		if(isset($file_data['title'])){
			$where['title'] = $file_data['title'];
		}
		if(isset($file_data['hash'])){
			$where['hash'] = $file_data['hash'];
		}
		if(isset($file_data['owner_id'])){
			$where['owner_id'] = $file_data['owner_id'];
		}
		if(isset($file_data['storage'])){
			$where['storage'] = $file_data['storage'];
		}
		if(isset($file_data['type'])){
			$where['type'] = $file_data['type'];
		}
		return \ORM::for_table($this->table)->where($where)->find_one();
	}

	/**
	 * Add file
	 *
	 * @since 1.0
	 * @access public
	 * @param array $file_data
	 * @return boolean
	 */
	public function addFile($file_data)
	{
		$file = \ORM::for_table($this->table)->create();
		$file->title = $file_data['title'];
		$file->hash = $file_data['hash'];
		$file->owner_id = $file_data['owner_id'];
		$file->description = $file_data['description'];
		$file->storage = $file_data['storage'];
		$file->type = $file_data['type'];
		$file->uploaded_at = $file_data['uploaded_at'];
		$file->save();
		return $file->id();
	}

	/**
	 * Update file by id
	 *
	 * @since 1.0
	 * @access public
	 * @param array $file_data
	 * @return boolean
	 */
	public function updateFileById($file_data)
	{
		$file = $this->getFileById($file_data['fi_id']);
   		if( (false === $file) || !(is_object($file)) ){
   			return false;
   		}
		unset($file_data['fi_id']);
		$file->set($file_data);
		return (boolean) $file->save();
	}

	/**
	 * Delete file by id
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $fi_id
	 * @return boolean
	 */
	public function deleteFileById($fi_id)
	{
		$file = $this->getFileById($fi_id);
   		if( (false === $file) || !(is_object($file)) ){
   			return false;
   		}
		return (boolean) $file->delete();
	}

	/**
	 * Delete file by multiple
	 *
	 * @since 1.0
	 * @access public
	 * @param array $file_data
	 * @return boolean
	 */
	public function deleteFileByMultiple($file_data)
	{
		$file = $this->getFileByMultiple($file_data);
   		if( (false === $file) || !(is_object($file)) ){
   			return false;
   		}
		return (boolean) $file->delete();

	}

   	/**
   	 * Delete file by id or post id
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param integer $fi_id
   	 * @param string $hash
   	 * @return boolean
   	 */
	public function dumpFile($fi_id = false, $hash = false)
	{
		if($fi_id !== false){
			return (boolean) \ORM::for_table($this->table)->where_equal('fi_id', $fi_id)->delete_many();
		}
		if($hash !== false){
			return (boolean) \ORM::for_table($this->table)->where_equal('hash', $hash)->delete_many();
		}
	}
}