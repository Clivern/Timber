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
 * Milestone Model
 *
 * Perform All CRUD Operations.
 *
 * `mi_id` int(11) not null auto_increment,
 * `pr_id` int(11) not null,
 * `owner_id` int(11) not null,
 * `title` varchar(150) not null,
 * `description` varchar(250) not null,
 * `status` enum('1','2','3','4','5','6','7','8','9') not null,
 * `priority` enum('1','2','3','4','5','6','7','8','9') not null,
 * `created_at` datetime not null,
 * `updated_at` datetime not null,
 *
 * status => (1-Pending) (2-In Progress) (3-Overdue) (4-Done) (5-) (6-) (7-)
 * priority => (1-Low) (2-Middle) (3-High) (4-Critical) (5-) (6-) (7-)
 *
 * @since 1.0
 */
class Milestone {

	/**
	 * Milestones table name
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
		$this->table = TIMBER_DB_PREFIX . TIMBER_DB_MILESTONES_TABLE;
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
	 * Count total milestones
	 *
   	 * @since 1.0
   	 * @access public
   	 * @return integer
	 */
	public function countMilestones()
	{
		return \ORM::for_table($this->table)->count();
	}

	/**
	 * Count total milestones by custom filter
	 *
   	 * @since 1.0
   	 * @access public
   	 * @param array $where
   	 * @return integer
	 */
	public function countMilestonesBy($where)
	{
		return \ORM::for_table($this->table)->where($where)->count();
	}

  	/**
   	 * Count milestones according to search term
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param string $column
   	 * @param string $column_value
   	 * @param array $where
   	 * @return integer
   	 */
	public function countSearchMilestones($column, $column_value, $where)
	{
		if( (is_array($where)) && (count($where) > 0) ){
			return \ORM::for_table($this->table)->where($where)->where_like($column, $column_value)->count();
		}else{
			return \ORM::for_table($this->table)->where_like($column, $column_value)->count();
		}
	}

	/**
	 * Get milestones
	 *
	 * @since 1.0
	 * @access public
   	 * @param integer $offset
   	 * @param integer $limit
   	 * @param string $order_type
   	 * @param string $order_by
	 * @return array
	 */
	public function getMilestones($offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
	 * Get milestones by custom condition
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
	public function getMilestonesBy($where, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
   	 * Get milestones according to search term
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
	public function getSearchMilestones($column, $column_value, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
   	 * Get milestones according to search term and custom condition
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
	public function getSearchMilestonesBy($where, $column, $column_value, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
	 * Get milestone by id
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $mi_id
	 * @return object
	 */
	public function getMilestoneById($mi_id)
	{
		return \ORM::for_table($this->table)->where('mi_id', $mi_id)->find_one();
	}

	/**
	 * Get milestone by multiple
	 *
	 * @since 1.0
	 * @access public
	 * @param array $milestone_data
	 * @return object
	 */
	public function getMilestoneByMultiple($milestone_data)
	{
		$where = array();
		if(isset($milestone_data['mi_id'])){
			$where['mi_id'] = $milestone_data['mi_id'];
		}
		if(isset($milestone_data['pr_id'])){
			$where['pr_id'] = $milestone_data['pr_id'];
		}
		if(isset($milestone_data['owner_id'])){
			$where['owner_id'] = $milestone_data['owner_id'];
		}
		if(isset($milestone_data['status'])){
			$where['status'] = $milestone_data['status'];
		}
		if(isset($milestone_data['priority'])){
			$where['priority'] = $milestone_data['priority'];
		}
		return \ORM::for_table($this->table)->where($where)->find_one();
	}

	/**
	 * Add milestone
	 *
	 * @since 1.0
	 * @access public
	 * @param array $milestone_data
	 * @return boolean
	 */
	public function addMilestone($milestone_data)
	{
		$milestone = \ORM::for_table($this->table)->create();
		$milestone->pr_id = $milestone_data['pr_id'];
		$milestone->owner_id = $milestone_data['owner_id'];
		$milestone->title = $milestone_data['title'];
		$milestone->description = $milestone_data['description'];
		$milestone->status = $milestone_data['status'];
		$milestone->priority = $milestone_data['priority'];
		$milestone->created_at = $milestone_data['created_at'];
		$milestone->updated_at = $milestone_data['updated_at'];
		$milestone->save();
		return $milestone->id();
	}

	/**
	 * Update milestone by id
	 *
	 * @since 1.0
	 * @access public
	 * @param array $milestone_data
	 * @return boolean
	 */
	public function updateMilestoneById($milestone_data)
	{
		$milestone = $this->getMilestoneById($milestone_data['mi_id']);
   		if( (false === $milestone) || !(is_object($milestone)) ){
   			return false;
   		}
		unset($milestone_data['mi_id']);
		$milestone->set($milestone_data);
		return (boolean) $milestone->save();
	}

	/**
	 * Delete milestone by id
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $mi_id
	 * @return boolean
	 */
	public function deleteMilestoneById($mi_id)
	{
		$milestone = $this->getMilestoneById($mi_id);
   		if( (false === $milestone) || !(is_object($milestone)) ){
   			return false;
   		}
		return (boolean) $milestone->delete();
	}

	/**
	 * Delete milestone by multiple
	 *
	 * @since 1.0
	 * @access public
	 * @param array $milestone_data
	 * @return boolean
	 */
	public function deleteMilestoneByMultiple($milestone_data)
	{
		$milestone = $this->getMilestoneByMultiple($milestone_data);
   		if( (false === $milestone) || !(is_object($milestone)) ){
   			return false;
   		}
		return (boolean) $milestone->delete();

	}

   	/**
   	 * Delete milestone by id or project id
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param integer $mi_id
   	 * @param integer $pr_id
   	 * @return boolean
   	 */
	public function dumpMilestone($mi_id = false, $pr_id = false)
	{
		if($mi_id !== false){
			return (boolean) \ORM::for_table($this->table)->where_equal('mi_id', $mi_id)->delete_many();
		}
		if($pr_id !== false){
			return (boolean) \ORM::for_table($this->table)->where_equal('pr_id', $pr_id)->delete_many();
		}
	}
}