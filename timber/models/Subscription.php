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
 * Subscription Model
 *
 * Perform All CRUD Operations.
 *
 * `su_id` int(11) not null auto_increment,
 * `reference` varchar(50) not null,
 * `owner_id` int(11) not null,
 * `client_id` int(11) not null,
 * `status` enum('1','2','3','4','5','6','7','8','9') not null,
 * `frequency` varchar(20) not null,
 * `terms` text not null,
 * `tax` varchar(20) not null,
 * `discount` varchar(20) not null,
 * `total` varchar(20) not null,
 * `attach` enum('on','off') not null,
 * `begin_at` datetime not null,
 * `end_at` datetime not null,
 * `created_at` datetime not null,
 * `updated_at` datetime not null,
 *
 * @since 1.0
 */
class Subscription {

	/**
	 * Subscriptions table name
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
		$this->table = TIMBER_DB_PREFIX . TIMBER_DB_SUBSCRIPTIONS_TABLE;
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
	 * Count total subscriptions
	 *
   	 * @since 1.0
   	 * @access public
   	 * @return integer
	 */
	public function countSubscriptions()
	{
		return \ORM::for_table($this->table)->count();
	}

	/**
	 * Count total subscriptions by custom filter
	 *
   	 * @since 1.0
   	 * @access public
   	 * @param array $where
   	 * @return integer
	 */
	public function countSubscriptionsBy($where)
	{
		return \ORM::for_table($this->table)->where($where)->count();
	}

  	/**
   	 * Count subscriptions according to search term
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param string $column
   	 * @param string $column_value
   	 * @param array $where
   	 * @return integer
   	 */
	public function countSearchSubscriptions($column, $column_value, $where)
	{
		if( (is_array($where)) && (count($where) > 0) ){
			return \ORM::for_table($this->table)->where($where)->where_like($column, $column_value)->count();
		}else{
			return \ORM::for_table($this->table)->where_like($column, $column_value)->count();
		}
	}

	/**
	 * Get subscriptions
	 *
	 * @since 1.0
	 * @access public
   	 * @param integer $offset
   	 * @param integer $limit
   	 * @param string $order_type
   	 * @param string $order_by
	 * @return array
	 */
	public function getSubscriptions($offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
	 * Get subscriptions by custom condition
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
	public function getSubscriptionsBy($where, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
   	 * Get subscriptions according to search term
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
	public function getSearchSubscriptions($column, $column_value, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
   	 * Get subscriptions according to search term and custom condition
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
	public function getSearchSubscriptionsBy($where, $column, $column_value, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
	 * Get subscription by id
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $su_id
	 * @return object
	 */
	public function getSubscriptionById($su_id)
	{
		return \ORM::for_table($this->table)->where('su_id', $su_id)->find_one();
	}

	/**
	 * Get subscription by multiple
	 *
	 * @since 1.0
	 * @access public
	 * @param array $subscription_data
	 * @return object
	 */
	public function getSubscriptionByMultiple($subscription_data)
	{
		$where = array();
		if(isset($subscription_data['su_id'])){
			$where['su_id'] = $subscription_data['su_id'];
		}
		if(isset($subscription_data['reference'])){
			$where['reference'] = $subscription_data['reference'];
		}
		if(isset($subscription_data['owner_id'])){
			$where['owner_id'] = $subscription_data['owner_id'];
		}
		if(isset($subscription_data['client_id'])){
			$where['client_id'] = $subscription_data['client_id'];
		}
		if(isset($subscription_data['frequency'])){
			$where['frequency'] = $subscription_data['frequency'];
		}
		if(isset($subscription_data['status'])){
			$where['status'] = $subscription_data['status'];
		}
		return \ORM::for_table($this->table)->where($where)->find_one();
	}

	/**
	 * Add subscription
	 *
	 * @since 1.0
	 * @access public
	 * @param array $subscription_data
	 * @return boolean
	 */
	public function addSubscription($subscription_data)
	{
		$subscription = \ORM::for_table($this->table)->create();
		$subscription->reference = $subscription_data['reference'];
		$subscription->owner_id = $subscription_data['owner_id'];
		$subscription->client_id = $subscription_data['client_id'];
		$subscription->status = $subscription_data['status'];
		$subscription->frequency = $subscription_data['frequency'];
		$subscription->terms = $subscription_data['terms'];
		$subscription->tax = $subscription_data['tax'];
		$subscription->discount = $subscription_data['discount'];
		$subscription->total = $subscription_data['total'];
		$subscription->attach = $subscription_data['attach'];
		$subscription->begin_at = $subscription_data['begin_at'];
		$subscription->end_at = $subscription_data['end_at'];
		$subscription->created_at = $subscription_data['created_at'];
		$subscription->updated_at = $subscription_data['updated_at'];
		$subscription->save();
		return $subscription->id();
	}

   	/**
   	 * Get unique fresh reference for subscription
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
			$subscription = $this->getSubscriptionByMultiple(array('reference' => $reference));
			if( (false === $subscription) || !(is_object($subscription)) ){
				$reference_found = true;
			}
		}
		return $reference;
	}

	/**
	 * Update subscription by id
	 *
	 * @since 1.0
	 * @access public
	 * @param array $subscription_data
	 * @return boolean
	 */
	public function updateSubscriptionById($subscription_data)
	{
		$subscription = $this->getSubscriptionById($subscription_data['su_id']);
   		if( (false === $subscription) || !(is_object($subscription)) ){
   			return false;
   		}
		unset($subscription_data['su_id']);
		$subscription->set($subscription_data);
		return (boolean) $subscription->save();
	}

	/**
	 * Delete subscription by id
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $su_id
	 * @return boolean
	 */
	public function deleteSubscriptionById($su_id)
	{
		$subscription = $this->getSubscriptionById($su_id);
   		if( (false === $subscription) || !(is_object($subscription)) ){
   			return false;
   		}
		return (boolean) $subscription->delete();
	}

	/**
	 * Delete subscription by multiple
	 *
	 * @since 1.0
	 * @access public
	 * @param array $subscription_data
	 * @return boolean
	 */
	public function deleteSubscriptionByMultiple($subscription_data)
	{
		$subscription = $this->getSubscriptionByMultiple($subscription_data);
   		if( (false === $subscription) || !(is_object($subscription)) ){
   			return false;
   		}
		return (boolean) $subscription->delete();

	}

   	/**
   	 * Delete subscription by id or client id
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param integer $su_id
   	 * @param integer $client_id
   	 * @return boolean
   	 */
	public function dumpSubscription($su_id = false, $client_id = false)
	{
		if($su_id !== false){
			return (boolean) \ORM::for_table($this->table)->where_equal('su_id', $su_id)->delete_many();
		}
		if($client_id !== false){
			return (boolean) \ORM::for_table($this->table)->where_equal('client_id', $client_id)->delete_many();
		}
	}
}