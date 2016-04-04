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
 * Task Model
 *
 * Perform All CRUD Operations.
 *
 * `ta_id` int(11) not null auto_increment,
 * `mi_id` int(11) not null,
 * `pr_id` int(11) not null,
 * `owner_id` int(11) not null,
 * `assign_to` int(11) not null,
 * `title` varchar(150) not null,
 * `description` varchar(250) not null,
 * `status` enum('1','2','3','4','5','6','7','8','9') not null,
 * `priority` enum('1','2','3','4','5','6','7','8','9') not null,
 * `start_at` date not null,
 * `end_at` date not null,
 * `created_at` datetime not null,
 * `updated_at` datetime not null,
 *
 * status => (1-Pending) (2-In Progress) (3-Overdue) (4-Done) (5-) (6-) (7-)
 * priority => (1-Low) (2-Middle) (3-High) (4-Critical) (5-) (6-) (7-)
 *
 * @since 1.0
 */
class Task {

	/**
	 * Tasks table name
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
		$this->table = TIMBER_DB_PREFIX . TIMBER_DB_TASKS_TABLE;
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
	 * Count total tasks
	 *
   	 * @since 1.0
   	 * @access public
   	 * @return integer
	 */
	public function countTasks()
	{
		return \ORM::for_table($this->table)->count();
	}

	/**
	 * Count total tasks by custom filter
	 *
   	 * @since 1.0
   	 * @access public
   	 * @param array $where
   	 * @return integer
	 */
	public function countTasksBy($where)
	{
		return \ORM::for_table($this->table)->where($where)->count();
	}

  	/**
   	 * Count tasks according to search term
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param string $column
   	 * @param string $column_value
   	 * @param array $where
   	 * @return integer
   	 */
	public function countSearchTasks($column, $column_value, $where)
	{
		if( (is_array($where)) && (count($where) > 0) ){
			return \ORM::for_table($this->table)->where($where)->where_like($column, $column_value)->count();
		}else{
			return \ORM::for_table($this->table)->where_like($column, $column_value)->count();
		}
	}

	/**
	 * Get tasks
	 *
	 * @since 1.0
	 * @access public
   	 * @param integer $offset
   	 * @param integer $limit
   	 * @param string $order_type
   	 * @param string $order_by
	 * @return array
	 */
	public function getTasks($offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
	 * Get tasks by custom condition
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
	public function getTasksBy($where, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
   	 * Get tasks according to search term
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
	public function getSearchTasks($column, $column_value, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
   	 * Get tasks according to search term and custom condition
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
	public function getSearchTasksBy($where, $column, $column_value, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
	 * Get task by id
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $ta_id
	 * @return object
	 */
	public function getTaskById($ta_id)
	{
		return \ORM::for_table($this->table)->where('ta_id', $ta_id)->find_one();
	}

	/**
	 * Get task by multiple
	 *
	 * @since 1.0
	 * @access public
	 * @param array $task_data
	 * @return object
	 */
	public function getTaskByMultiple($task_data)
	{
		$where = array();
		if(isset($task_data['ta_id'])){
			$where['ta_id'] = $task_data['ta_id'];
		}
		if(isset($task_data['mi_id'])){
			$where['mi_id'] = $task_data['mi_id'];
		}
		if(isset($task_data['pr_id'])){
			$where['pr_id'] = $task_data['pr_id'];
		}
		if(isset($task_data['owner_id'])){
			$where['owner_id'] = $task_data['owner_id'];
		}
		if(isset($task_data['assign_to'])){
			$where['assign_to'] = $task_data['assign_to'];
		}
		/*
		if(isset($task_data['status'])){
			$where['status'] = $task_data['status'];
		}
		if(isset($task_data['priority'])){
			$where['priority'] = $task_data['priority'];
		}
		*/
		return \ORM::for_table($this->table)->where($where)->find_one();
	}

	/**
	 * Add task
	 *
	 * @since 1.0
	 * @access public
	 * @param array $task_data
	 * @return boolean
	 */
	public function addTask($task_data)
	{
		$task = \ORM::for_table($this->table)->create();
		$task->mi_id = $task_data['mi_id'];
		$task->pr_id = $task_data['pr_id'];
		$task->owner_id = $task_data['owner_id'];
		$task->assign_to = $task_data['assign_to'];
		$task->title = $task_data['title'];
		$task->description = $task_data['description'];
		$task->status = $task_data['status'];
		$task->priority = $task_data['priority'];
		$task->start_at = $task_data['start_at'];
		$task->end_at = $task_data['end_at'];
		$task->created_at = $task_data['created_at'];
		$task->updated_at = $task_data['updated_at'];
		$task->save();
		return $task->id();
	}

	/**
	 * Update task by id
	 *
	 * @since 1.0
	 * @access public
	 * @param array $task_data
	 * @return boolean
	 */
	public function updateTaskById($task_data)
	{
		$task = $this->getTaskById($task_data['ta_id']);
   		if( (false === $task) || !(is_object($task)) ){
   			return false;
   		}
		unset($task_data['ta_id']);
		$task->set($task_data);
		return (boolean) $task->save();
	}

	/**
	 * Update task by multiple
	 *
	 * @since 1.0
	 * @access public
	 * @param array $task_data
	 * @return boolean
	 */
	public function updateTaskByMultiple($task_data)
	{
		$task = $this->getTaskByMultiple($task_data);
   		if( (false === $task) || !(is_object($task)) ){
   			return false;
   		}
   		if(isset($task_data['ta_id'])){
			unset($task_data['ta_id']);
		}
   		if(isset($task_data['mi_id'])){
			unset($task_data['mi_id']);
		}
   		if(isset($task_data['pr_id'])){
			unset($task_data['pr_id']);
		}
   		if(isset($task_data['owner_id'])){
			unset($task_data['owner_id']);
		}
   		if(isset($task_data['assign_to'])){
			unset($task_data['assign_to']);
		}
		$task->set($task_data);
		return (boolean) $task->save();
	}

	/**
	 * Delete task by id
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $ta_id
	 * @return boolean
	 */
	public function deleteTaskById($ta_id)
	{
		$task = $this->getTaskById($ta_id);
   		if( (false === $task) || !(is_object($task)) ){
   			return false;
   		}
		return (boolean) $task->delete();
	}

	/**
	 * Delete task by multiple
	 *
	 * @since 1.0
	 * @access public
	 * @param array $task_data
	 * @return boolean
	 */
	public function deleteTaskByMultiple($task_data)
	{
		$task = $this->getTaskByMultiple($task_data);
   		if( (false === $task) || !(is_object($task)) ){
   			return false;
   		}
		return (boolean) $task->delete();

	}

   	/**
   	 * Delete task by id or post id
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param integer $ta_id
   	 * @param integer $mi_id
   	 * @param integer $pr_id
   	 * @return boolean
   	 */
	public function dumpTask($ta_id = false, $mi_id = false, $pr_id = false)
	{
		if($ta_id !== false){
			return (boolean) \ORM::for_table($this->table)->where_equal('ta_id', $ta_id)->delete_many();
		}
		if($mi_id !== false){
			return (boolean) \ORM::for_table($this->table)->where_equal('mi_id', $mi_id)->delete_many();
		}
		if($pr_id !== false){
			return (boolean) \ORM::for_table($this->table)->where_equal('pr_id', $pr_id)->delete_many();
		}
	}
}