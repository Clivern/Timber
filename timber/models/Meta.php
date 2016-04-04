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
 * Meta Model
 *
 * Perform All CRUD Operations.
 *
 * `me_id` int(11) not null auto_increment,
 * `rec_id` int(11) not null,
 * `rec_type` varchar(20) not null,
 * `me_key` varchar(60) not null,
 * `me_value` text not null,
 *
 * rec_type => (1)-discussion (2)-file (3)-invoice (4)-item (5)-message (6)-milestone (7)-quotation (8)-subscription (9)-task (10)-ticket (11) project (12) none
 *
 * @since 1.0
 */
class Meta {

	/**
	 * Metas table name
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
		$this->table = TIMBER_DB_PREFIX . TIMBER_DB_METAS_TABLE;
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
	 * Get metas with record id
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $rec_id
	 * @return array
	 */
	public function getMetasByRecordId($rec_id)
	{
		return \ORM::for_table($this->table)->where('rec_id', $rec_id)->find_array();
	}

	/**
	 * Get metas with record type
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $rec_type
	 * @return array
	 */
	public function getMetasByRecordType($rec_type)
	{
		return \ORM::for_table($this->table)->where('rec_type', $rec_type)->find_array();
	}

	/**
	 * Get metas
	 *
	 * @since 1.0
	 * @access public
	 * @param array $where
	 * @return array
	 */
	public function getMetasBy($where)
	{
		return \ORM::for_table($this->table)->where($where)->find_array();
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
		if(isset($meta_data['rec_id'])){
			$where['rec_id'] = $meta_data['rec_id'];
		}
		if(isset($meta_data['rec_type'])){
			$where['rec_type'] = $meta_data['rec_type'];
		}
		if(isset($meta_data['me_key'])){
			$where['me_key'] = $meta_data['me_key'];
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
		$meta->rec_id = $meta_data['rec_id'];
		$meta->rec_type = $meta_data['rec_type'];
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
		if(isset($meta_data['rec_id'])){
			unset($meta_data['rec_id']);
		}
		if(isset($meta_data['rec_type'])){
			unset($meta_data['rec_type']);
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
   	 * Delete note metas by id or post id
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param integer $me_id
   	 * @param integer $rec_id
   	 * @param integer $rec_type
   	 * @return boolean
   	 */
	public function dumpMetas($me_id = false, $rec_id = false, $rec_type = false)
	{
		if($me_id !== false){
			return (boolean) \ORM::for_table($this->table)->where_equal('me_id', $me_id)->delete_many();
		}
		if( ($rec_id !== false) && ($rec_type !== false) ){
			return (boolean) \ORM::for_table($this->table)->where_equal(array('rec_id' => $rec_id, 'rec_type' => $rec_type))->delete_many();
		}
	}
}