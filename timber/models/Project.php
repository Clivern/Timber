<?php
/**
 * Timber - Ultimate Freelancer Platform
 *
 * @author      Clivern <support@clivern.com>
 * @copyright   2015 Clivern
 * @link        http://clivern.com
 * @license     http://codecanyon.com/license
 * @version     1.0
 * @package     Timber
 */

namespace Timber\Models;

/**
 * Project Model
 *
 * Perform All CRUD Operations.
 *
 * `pr_id` int(11) not null auto_increment,
 * `title` varchar(60) not null,
 * `reference` varchar(60) not null,
 * `description` text not null,
 * `version` varchar(20) not null,
 * `progress` varchar(20) not null,
 * `budget` varchar(20) not null,
 * `status` enum('1','2','3','4','5','6','7','8','9') not null,
 * `owner_id` int(11) not null,
 * `tax` varchar(20) not null,
 * `discount` varchar(20) not null,
 * `attach` enum('on','off') not null,
 * `created_at` datetime not null,
 * `updated_at` datetime not null,
 * `start_at` datetime not null,
 * `end_at` datetime not null,
 *
 * status => (1-Pending) (2-In Progress) (3-Overdue) (4-Done) (5-Archived) (6-) (7-)
 *
 * @since 1.0
 */
class Project {

	/**
	 * Projects table name
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
		$this->table = TIMBER_DB_PREFIX . TIMBER_DB_PROJECTS_TABLE;
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
	 * Count total projects
	 *
   	 * @since 1.0
   	 * @access public
   	 * @return integer
	 */
	public function countProjects()
	{
		return \ORM::for_table($this->table)->count();
	}

	/**
	 * Count projects by custom condition
	 *
	 * @since 1.0
	 * @since public
	 * @param array $where
	 * @return integer
	 */
	public function countProjectsBy($where)
	{
		return \ORM::for_table($this->table)->where($where)->count();
	}

  	/**
   	 * Count projects according to search term and custom condition
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param string $column
   	 * @param string $column_value
   	 * @param array $where
   	 * @return integer
   	 */
	public function countSearchProjects($column, $column_value, $where)
	{
		if( (is_array($where)) && (count($where) > 0) ){
			return \ORM::for_table($this->table)->where($where)->where_like($column, $column_value)->count();
		}else{
			return \ORM::for_table($this->table)->where_like($column, $column_value)->count();
		}
	}

	/**
	 * Get projects
	 *
	 * @since 1.0
	 * @access public
   	 * @param integer $offset
   	 * @param integer $limit
   	 * @param string $order_type
   	 * @param string $order_by
	 * @return array
	 */
	public function getProjects($offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
	 * Get projects by custom condition
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
	public function getProjectsBy($where, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
   	 * Get projects according to search term
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
	public function getSearchProjects($column, $column_value, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
   	 * Get projects according to search term and custom condition
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
	public function getSearchProjectsBy($where, $column, $column_value, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
   	 * Get project by id
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param integer $pr_id
   	 * @return object
   	 */
	public function getProjectById($pr_id)
	{
		return \ORM::for_table($this->table)->where('pr_id', $pr_id)->find_one();
	}

   	/**
   	 * Get project by reference
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param string $reference
   	 * @return object
   	 */
	public function getProjectByReference($reference)
	{
		return \ORM::for_table($this->table)->where('reference', $reference)->find_one();
	}

   	/**
   	 * Get project by multiple
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param array $project_data
   	 * @return object
   	 */
	public function getProjectByMultiple($project_data)
	{
		$where = array();
		if(isset($project_data['pr_id'])){
			$where['pr_id'] = $project_data['pr_id'];
		}
		if(isset($project_data['reference'])){
			$where['reference'] = $project_data['reference'];
		}
		if(isset($project_data['status'])){
			$where['status'] = $project_data['status'];
		}
		if(isset($project_data['owner_id'])){
			$where['owner_id'] = $project_data['owner_id'];
		}
		if(isset($project_data['attach'])){
			$where['attach'] = $project_data['attach'];
		}
		return \ORM::for_table($this->table)->where($where)->find_one();
	}

   	/**
   	 * Add project
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param array $project_data
   	 * @return boolean
   	 */
	public function addProject($project_data)
	{
		$project = \ORM::for_table($this->table)->create();
		$project->title = $project_data['title'];
		$project->reference = $project_data['reference'];
		$project->description = $project_data['description'];
		$project->version = $project_data['version'];
		$project->progress = $project_data['progress'];
		$project->budget = $project_data['budget'];
		$project->status = $project_data['status'];
		$project->owner_id = $project_data['owner_id'];
		$project->tax = $project_data['tax'];
		$project->discount = $project_data['discount'];
		$project->attach = $project_data['attach'];
		$project->created_at = $project_data['created_at'];
		$project->updated_at = $project_data['updated_at'];
		$project->start_at = $project_data['start_at'];
		$project->end_at = $project_data['end_at'];
		$project->save();
		return $project->id();
	}

   	/**
   	 * Get unique fresh reference for project
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
			$project = $this->getProjectByMultiple(array('reference' => $reference));
			if( (false === $project) || !(is_object($project)) ){
				$reference_found = true;
			}
		}
		return $reference;
	}

   	/**
   	 * Update project by id
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param array $project_data
   	 * @return boolean
   	 */
	public function updateProjectById($project_data)
	{
		$project = $this->getProjectById($project_data['pr_id']);
   		if( (false === $project) || !(is_object($project)) ){
   			return false;
   		}
		unset($project_data['pr_id']);
		$project->set($project_data);
		return (boolean) $project->save();
	}

   	/**
   	 * Update project by reference
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param array $project_data
   	 * @return boolean
   	 */
	public function updateProjectByReference($project_data)
	{
		$project = $this->getProjectByReference($project_data['reference']);
   		if( (false === $project) || !(is_object($project)) ){
   			return false;
   		}
		unset($project_data['reference']);
		$project->set($project_data);
		return (boolean) $project->save();
	}

   	/**
   	 * Update project by multiple
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param array $where
   	 * @param array $project_data
   	 * @return boolean
   	 */
	public function updateProjectByMultiple($where, $project_data)
	{
		$project = $this->getProjectByMultiple($where);
   		if( (false === $project) || !(is_object($project)) ){
   			return false;
   		}
		$project->set($project_data);
		return (boolean) $project->save();
	}

   	/**
   	 * Delete project by id
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param integer $pr_id
   	 * @return boolean
   	 */
	public function deleteProjectById($pr_id)
	{
		$project = $this->getProjectById($pr_id);
   		if( (false === $project) || !(is_object($project)) ){
   			return false;
   		}
		return (boolean) $project->delete();
	}

   	/**
   	 * Delete project by reference
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param string $reference
   	 * @return boolean
   	 */
	public function deleteProjectByReference($reference)
	{
		$project = $this->getProjectByReference($reference);
   		if( (false === $project) || !(is_object($project)) ){
   			return false;
   		}
		return (boolean) $project->delete();
	}

   	/**
   	 * Delete project by multiple
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param array $project_data
   	 * @return boolean
   	 */
	public function deleteProjectByMultiple($project_data)
	{
		$project = $this->getProjectByMultiple($project_data);
   		if( (false === $project) || !(is_object($project)) ){
   			return false;
   		}
		return (boolean) $project->delete();
	}

   	/**
   	 * Delete project by id
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param integer $pr_id
   	 * @return boolean
   	 */
	public function dumpProject($pr_id)
	{
		return (boolean) \ORM::for_table($this->table)->where_equal('pr_id', $pr_id)->delete_many();
	}
}