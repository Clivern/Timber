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
 * Users Model
 *
 * Perform All CRUD Operations.
 *
 * `us_id` int(11) not null auto_increment,
 * `user_name` varchar(60) not null,
 * `first_name` varchar(60) not null,
 * `last_name` varchar(60) not null,
 * `company` varchar(60) not null,
 * `email` varchar(100) not null,
 * `website` varchar(150) not null,
 * `phone_num` varchar(60) not null,
 * `zip_code` varchar(60) not null,
 * `vat_nubmer` varchar(60) not null,
 * `language` varchar(20) not null,
 * `job` varchar(60) not null,
 * `grav_id` int(11) not null,
 * `country` varchar(20) not null,
 * `city` varchar(60) not null,
 * `address1` varchar(60) not null,
 * `address2` varchar(60) not null,
 * `password` varchar(250) not null,
 * `sec_hash` varchar(100) not null,
 * `identifier` varchar(250) not null,
 * `auth_by` enum('1','2','3','4','5','6','7','8','9') not null,
 * `access_rule` enum('1','2','3','4','5','6','7','8','9') not null,
 * `status` enum('1','2','3','4','5','6','7','8','9') not null,
 * `created_at` datetime not null,
 * `updated_at` datetime not null,
 *
 * auth_by => (1) dan  (2) twitter  (3) facebook  (4) google
 * access_rule => (1) admin  (2) staff  (3) client
 * status => (1) Active (2) Pending (3) Disabled
 *
 * @since 1.0
 */
class User {

	/**
	 * Users table name
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
		$this->table = TIMBER_DB_PREFIX . TIMBER_DB_USERS_TABLE;
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
	 * Count total users
	 *
   	 * @since 1.0
   	 * @access public
   	 * @return integer
	 */
	public function countUsers()
	{
		return \ORM::for_table($this->table)->count();
	}

	/**
	 * Count users by custom condition
	 *
   	 * @since 1.0
   	 * @access public
	 * @param array $where
	 * @return integer
	 */
	public function countUsersBy($where)
	{
		return \ORM::for_table($this->table)->where($where)->count();
	}

   	/**
   	 * Count users according to search term
   	 *
   	 * @since 1.0
   	 * @access public
	 * @param string $column
	 * @param string $search_term
   	 * @return integer
   	 */
	public function countSearchUsers($column, $search_term, $where)
	{
		if( (is_array($where)) && (count($where) > 0) ){
			return \ORM::for_table($this->table)->where($where)->where_like($column, $search_term)->count();
		}else{
			return \ORM::for_table($this->table)->where_like($column, $search_term)->count();
		}
	}

	/**
	 * Get users
	 *
	 * @since 1.0
	 * @access public
   	 * @param integer $offset
   	 * @param integer $limit
   	 * @param string $order_type
   	 * @param string $order_by
	 * @return array
	 */
	public function getUsers($offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
	 * Get users by custom condition
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
	public function getUsersBy($where, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
   	 * Get users according to search term
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
	public function getSearchUsers($column, $column_value, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
   	 * Get users according to search term and custom condition
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
	public function getSearchUsersBy($where, $column, $column_value, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
   	 * Get user by id
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param integer $us_id
   	 * @return object
   	 */
   	public function getUserById($us_id)
   	{
   		return \ORM::for_table($this->table)->where('us_id', $us_id)->find_one();
   	}

   	/**
   	 * Get user by nicename
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param string $user_name
   	 * @return object
   	 */
   	public function getUserByUsername($user_name)
   	{
   		return \ORM::for_table($this->table)->where('user_name', $user_name)->find_one();
   	}

   	/**
   	 * Get user by email
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param string $email
   	 * @return object
   	 */
   	public function getUserByEmail($email)
   	{
   		return \ORM::for_table($this->table)->where('email', $email)->find_one();
   	}

   	/**
   	 * Get user by hash
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param string $sec_hash
   	 * @return object
   	 */
	public function getUserByHash($sec_hash)
	{
		return \ORM::for_table($this->table)->where('sec_hash', $sec_hash)->find_one();
	}

   	/**
   	 * Get user by multiple conditions
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param array $user_data
   	 * @return object
   	 */
   	public function getUserByMultiple($user_data)
   	{
		$where = array();
		if(isset($user_data['us_id'])){
			$where['us_id'] = $user_data['us_id'];
		}
		if(isset($user_data['user_name'])){
			$where['user_name'] = $user_data['user_name'];
		}
		if(isset($user_data['email'])){
			$where['email'] = $user_data['email'];
		}
		if(isset($user_data['password'])){
			$where['password'] = $user_data['password'];
		}
		if(isset($user_data['auth_by'])){
			$where['auth_by'] = $user_data['auth_by'];
		}
		if(isset($user_data['access_rule'])){
			$where['access_rule'] = $user_data['access_rule'];
		}
		if(isset($user_data['status'])){
			$where['status'] = $user_data['status'];
		}
		if(isset($user_data['sec_hash'])){
			$where['sec_hash'] = $user_data['sec_hash'];
		}
		return \ORM::for_table($this->table)->where($where)->find_one();
   	}

   	/**
   	 * Add new user
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param array $user_data
   	 * @return boolean
   	 */
	public function addUser($user_data)
	{
		$user = \ORM::for_table($this->table)->create();
		$user->user_name = $user_data['user_name'];
		$user->first_name = $user_data['first_name'];
		$user->last_name = $user_data['last_name'];
		$user->company = $user_data['company'];
		$user->email = $user_data['email'];
		$user->website = $user_data['website'];
		$user->phone_num = $user_data['phone_num'];
		$user->zip_code = $user_data['zip_code'];
		$user->vat_nubmer = $user_data['vat_nubmer'];
		$user->language = $user_data['language'];
		$user->job = $user_data['job'];
		$user->grav_id = $user_data['grav_id'];
		$user->country = $user_data['country'];
		$user->city = $user_data['city'];
		$user->address1 = $user_data['address1'];
		$user->address2 = $user_data['address2'];
		$user->password = $user_data['password'];
		$user->sec_hash = $user_data['sec_hash'];
		$user->identifier = $user_data['identifier'];
		$user->auth_by = $user_data['auth_by'];
		$user->access_rule = $user_data['access_rule'];
		$user->status = $user_data['status'];
		$user->created_at = $user_data['created_at'];
		$user->updated_at = $user_data['updated_at'];
		$user->save();
		return $user->id();
	}

   	/**
   	 * Update user by id
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param array $user_data
   	 * @return boolean
   	 */
	public function updateUserById($user_data)
	{
		$user = $this->getUserById($user_data['us_id']);
   		if( (false === $user) || !(is_object($user)) ){
   			return false;
   		}
		unset($user_data['us_id']);
		$user->set($user_data);
		return (boolean) $user->save();
	}

   	/**
   	 * Update user by nicename
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param array $user_data
   	 * @return boolean
   	 */
	public function updateUserByUsername($user_data)
	{
		$user = $this->getUserByUsername($user_data['user_name']);
   		if( (false === $user) || !(is_object($user)) ){
   			return false;
   		}
		unset($user_data['user_name']);
		$user->set($user_data);
		return (boolean) $user->save();
	}

   	/**
   	 * Update user by email
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param array $user_data
   	 * @return boolean
   	 */
	public function updateUserByEmail($user_data)
	{
		$user = $this->getUserByEmail($user_data['email']);
   		if( (false === $user) || !(is_object($user)) ){
   			return false;
   		}
		unset($user_data['email']);
		$user->set($user_data);
		return (boolean) $user->save();
	}

   	/**
   	 * Update user by hash
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param array $user_data
   	 * @return boolean
   	 */
	public function updateUserByHash($user_data)
	{
		$user = $this->getUserByHash($user_data['sec_hash']);
   		if( (false === $user) || !(is_object($user)) ){
   			return false;
   		}
		unset($user_data['sec_hash']);
		$user->set($user_data);
		return (boolean) $user->save();
	}

   	/**
   	 * Update user by multiple
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param array $where
   	 * @param array $user_data
   	 * @return boolean
   	 */
	public function updateUserByMultiple($where, $user_data)
	{
		$user = $this->getUserByMultiple($where);
   		if( (false === $user) || !(is_object($user)) ){
   			return false;
   		}
		$user->set($user_data);
		return (boolean) $user->save();
	}

	/**
	 * Delete user by id
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $us_id
	 * @return boolean
	 */
	public function deleteUserById($us_id)
	{
		$user = $this->getUserById($us_id);
   		if( (false === $user) || !(is_object($user)) ){
   			return false;
   		}
		return (boolean) $user->delete();
	}

	/**
	 * Delete user by username
	 *
	 * @since 1.0
	 * @access public
	 * @param string $user_name
	 * @return boolean
	 */
	public function deleteUserByUsername($user_name)
	{
		$user = $this->getUserByUsername($user_name);
   		if( (false === $user) || !(is_object($user)) ){
   			return false;
   		}
		return (boolean) $user->delete();
	}

	/**
	 * Delete user by email
	 *
	 * @since 1.0
	 * @access public
	 * @param string $email
	 * @return boolean
	 */
	public function deleteUserByEmail($email)
	{
		$user = $this->getUserByEmail($email);
   		if( (false === $user) || !(is_object($user)) ){
   			return false;
   		}
		return (boolean) $user->delete();
	}

	/**
	 * Delete user by hash
	 *
	 * @since 1.0
	 * @access public
	 * @param string $sec_hash
	 * @return boolean
	 */
	public function deleteUserByHash($sec_hash)
	{
		$user = $this->getUserByHash($sec_hash);
   		if( (false === $user) || !(is_object($user)) ){
   			return false;
   		}
		return (boolean) $user->delete();
	}

	/**
	 * Delete user by multiple
	 *
	 * @since 1.0
	 * @access public
	 * @param array $user_data
	 * @return boolean
	 */
	public function deleteUserByMultiple($user_data)
	{
		$user = $this->getUserByMultiple($user_data);
   		if( (false === $user) || !(is_object($user)) ){
   			return false;
   		}
		return (boolean) $user->delete();
	}

   	/**
   	 * Delete User by id
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param integer $us_id
   	 * @return boolean
   	 */
	public function dumpUser($us_id)
	{
		return (boolean) \ORM::for_table($this->table)->where_equal('us_id', $us_id)->delete_many();
	}
}