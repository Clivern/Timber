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
 * Users Meta Model
 *
 * Perform All CRUD Operations.
 *
 * `me_id` int(11) not null auto_increment,
 * `us_id` int(11) not null,
 * `me_key` varchar(60) not null,
 * `me_value` text not null,
 *
 * @since 1.0
 */
class UserMeta {

	/**
	 * Users meta table name
	 *
	 * @since 1.0
	 * @var string
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
		$this->table = TIMBER_DB_PREFIX . TIMBER_DB_USERS_META_TABLE;
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
	 * Count all metas
	 *
	 * This method used for debugging purposes
	 *
   	 * @since 1.0
   	 * @access public
   	 * @return integer
	 */
	public function countMetas()
	{
		return \ORM::for_table($this->table)->count();
	}

	/**
	 * Get metas
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function getMetas()
	{
		return \ORM::for_table($this->table)->find_array();
	}

	/**
	 * Get metas with key
	 *
	 * @since 1.0
	 * @access public
	 * @param string $me_key
	 * @return array
	 */
	public function getMetasByKey($me_key)
	{
		return \ORM::for_table($this->table)->where('me_key', $me_key)->find_array();
	}

	/**
	 * Get metas with user id
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $us_id
	 * @return array
	 */
	public function getMetasByUserId($us_id)
	{
		return \ORM::for_table($this->table)->where('us_id', $us_id)->find_array();
	}

	/**
	 * Get meta by id
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $me_id
	 * @return object
	 */
	public function getMetaById($me_id)
	{
		return \ORM::for_table($this->table)->where('me_id', $me_id)->find_one();
	}

	/**
	 * Get meta by multiple
	 *
	 * @since 1.0
	 * @access public
	 * @param array $meta_data
	 * @return object
	 */
	public function getMetaByMultiple($meta_data)
	{
		$where = array();
		if(isset($meta_data['me_id'])){
			$where['me_id'] = $meta_data['me_id'];
		}
		if(isset($meta_data['us_id'])){
			$where['us_id'] = $meta_data['us_id'];
		}
		if(isset($meta_data['me_key'])){
			$where['me_key'] = $meta_data['me_key'];
		}
        if(isset($meta_data['me_value'])){
            $where['me_value'] = $meta_data['me_value'];
        }
		return \ORM::for_table($this->table)->where($where)->find_one();
	}

	/**
	 * Add meta
	 *
	 * @since 1.0
	 * @access public
	 * @param array $meta_data
	 * @return boolean
	 */
	public function addMeta($meta_data)
	{
		$meta = \ORM::for_table($this->table)->create();
		$meta->us_id = $meta_data['us_id'];
		$meta->me_key = $meta_data['me_key'];
		$meta->me_value = $meta_data['me_value'];
		$meta->save();
		return $meta->id();
	}

	/**
	 * Update meta by id
	 *
	 * @since 1.0
	 * @access public
	 * @param array $meta_data
	 * @return boolean
	 */
	public function updateMetaById($meta_data)
	{
		$meta = $this->getMetaById($meta_data['me_id']);
   		if( (false === $meta) || !(is_object($meta)) ){
   			return false;
   		}
		unset($meta_data['me_id']);
		$meta->set($meta_data);
		return (boolean) $meta->save();
	}

	/**
	 * Update meta by multiple
	 *
	 * @since 1.0
	 * @access public
	 * @param array $meta_data
	 * @return boolean
	 */
	public function updateMetaByMultiple($meta_data)
	{
		$meta = $this->getMetaByMultiple($meta_data);
   		if( (false === $meta) || !(is_object($meta)) ){
   			return false;
   		}
		if(isset($meta_data['me_id'])){
			unset($meta_data['me_id']);
		}
		if(isset($meta_data['us_id'])){
			unset($meta_data['us_id']);
		}
		if(isset($meta_data['me_key'])){
			unset($meta_data['me_key']);
		}
		$meta->set($meta_data);
		return (boolean) $meta->save();
	}

	/**
	 * Delete meta by id
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $me_id
	 * @return boolean
	 */
	public function deleteMetaById($me_id)
	{
		$meta = $this->getMetaById($me_id);
   		if( (false === $meta) || !(is_object($meta)) ){
   			return false;
   		}
		return (boolean) $meta->delete();
	}

	/**
	 * Delete meta by multiple
	 *
	 * @since 1.0
	 * @access public
	 * @param array $meta_data
	 * @return boolean
	 */
	public function deleteMetaByMultiple($meta_data)
	{
		$meta = $this->getMetaByMultiple($meta_data);
   		if( (false === $meta) || !(is_object($meta)) ){
   			return false;
   		}
		return (boolean) $meta->delete();
	}

   	/**
   	 * Delete User metas by id
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param integer $us_id
   	 * @return boolean
   	 */
	public function dumpUserMetas($us_id)
	{
		return (boolean) \ORM::for_table($this->table)->where_equal('us_id', $us_id)->delete_many();
	}

   	/**
   	 * Delete User meta by id and key
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param integer $us_id
   	 * @param string $meta_key
   	 * @return boolean
   	 */
	public function dumpUserMeta($us_id, $meta_key)
	{
		return (boolean) \ORM::for_table($this->table)->where_equal('us_id', $us_id)->where_equal('me_key', $meta_key)->delete_many();
	}
}