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
 * Message Model
 *
 * Perform All CRUD Operations.
 *
 * `ms_id` int(11) not null auto_increment,
 * `sender_id` int(11) not null,
 * `receiver_id` int(11) not null,
 * `parent_id` int(11) not null,
 * `subject` varchar(150) not null,
 * `rece_cat` varchar(15) not null,
 * `send_cat` varchar(15) not null,
 * `rece_hide` enum('on','off') not null,
 * `send_hide` enum('on','off') not null,
 * `content` text not null,
 * `attach` enum('on','off') not null,
 * `created_at` datetime not null,
 * `updated_at` datetime not null,
 * `sent_at` datetime not null,
 *
 * @since 1.0
 */
class Message {

	/**
	 * Messages table name
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
		$this->table = TIMBER_DB_PREFIX . TIMBER_DB_MESSAGES_TABLE;
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
	 * Count total messages
	 *
   	 * @since 1.0
   	 * @access public
   	 * @return integer
	 */
	public function countMessages()
	{
		return \ORM::for_table($this->table)->count();
	}

	/**
	 * Count total messages by custom filter
	 *
   	 * @since 1.0
   	 * @access public
   	 * @param array $where
   	 * @return integer
	 */
	public function countMessagesBy($where)
	{
		return \ORM::for_table($this->table)->where($where)->count();
	}

  	/**
   	 * Count messages according to search term
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param string $column
   	 * @param string $column_value
   	 * @param array $where
   	 * @return integer
   	 */
	public function countSearchMessages($column, $column_value, $where)
	{
		if( (is_array($where)) && (count($where) > 0) ){
			return \ORM::for_table($this->table)->where($where)->where_like($column, $column_value)->count();
		}else{
			return \ORM::for_table($this->table)->where_like($column, $column_value)->count();
		}
	}

	/**
	 * Get messages
	 *
	 * @since 1.0
	 * @access public
   	 * @param integer $offset
   	 * @param integer $limit
   	 * @param string $order_type
   	 * @param string $order_by
	 * @return array
	 */
	public function getMessages($offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
	 * Get messages by custom condition
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
	public function getMessagesBy($where, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
	 * Get any messages by custom condition
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
	public function getAnyMessagesBy($where, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
	{
		if(($offset === false) && ($limit === false)){
			if( 'desc' == $order_type ){
				return \ORM::for_table($this->table)->where_any_is($where)->order_by_desc($order_by)->find_array();
			}else{
				return \ORM::for_table($this->table)->where_any_is($where)->order_by_asc($order_by)->find_array();
			}
		}else{
			if( 'desc' == $order_type ){
				return \ORM::for_table($this->table)->where_any_is($where)->order_by_desc($order_by)->limit($limit)->offset($offset)->find_array();
			}else{
				return \ORM::for_table($this->table)->where_any_is($where)->order_by_asc($order_by)->limit($limit)->offset($offset)->find_array();
			}
		}
	}

  	/**
   	 * Get messages according to search term
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
	public function getSearchMessages($column, $column_value, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
   	 * Get messages according to search term and custom condition
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
	public function getSearchMessagesBy($where, $column, $column_value, $offset = false, $limit = false, $order_type = 'desc', $order_by = 'created_at')
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
	 * Get message by id
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $ms_id
	 * @return object
	 */
	public function getMessageById($ms_id)
	{
		return \ORM::for_table($this->table)->where('ms_id', $ms_id)->find_one();
	}

	/**
	 * Get message by multiple
	 *
	 * @since 1.0
	 * @access public
	 * @param array $message_data
	 * @return object
	 */
	public function getMessageByMultiple($message_data)
	{
		$where = array();
		if(isset($message_data['ms_id'])){
			$where['ms_id'] = $message_data['ms_id'];
		}
		if(isset($message_data['sender_id'])){
			$where['sender_id'] = $message_data['sender_id'];
		}
		if(isset($message_data['receiver_id'])){
			$where['receiver_id'] = $message_data['receiver_id'];
		}
		if(isset($message_data['parent_id'])){
			$where['parent_id'] = $message_data['parent_id'];
		}
		if(isset($message_data['attach'])){
			$where['attach'] = $message_data['attach'];
		}
		if(isset($message_data['rece_cat'])){
			$where['rece_cat'] = $message_data['rece_cat'];
		}
		if(isset($message_data['send_cat'])){
			$where['send_cat'] = $message_data['send_cat'];
		}
		if(isset($message_data['rece_hide'])){
			$where['rece_hide'] = $message_data['rece_hide'];
		}
		if(isset($message_data['send_hide'])){
			$where['send_hide'] = $message_data['send_hide'];
		}

		return \ORM::for_table($this->table)->where($where)->find_one();
	}

	/**
	 * Add message
	 *
	 * @since 1.0
	 * @access public
	 * @param array $message_data
	 * @return boolean
	 */
	public function addMessage($message_data)
	{
		$message = \ORM::for_table($this->table)->create();
		$message->sender_id = $message_data['sender_id'];
		$message->receiver_id = $message_data['receiver_id'];
		$message->parent_id = $message_data['parent_id'];
   		$message->rece_cat = $message_data['rece_cat'];
   		$message->send_cat = $message_data['send_cat'];
   		$message->rece_hide = $message_data['rece_hide'];
   		$message->send_hide = $message_data['send_hide'];
		$message->subject = $message_data['subject'];
		$message->content = $message_data['content'];
		$message->attach = $message_data['attach'];
		$message->created_at = $message_data['created_at'];
		$message->updated_at = $message_data['updated_at'];
		$message->sent_at = $message_data['sent_at'];
		$message->save();
		return $message->id();
	}

	/**
	 * Update message by id
	 *
	 * @since 1.0
	 * @access public
	 * @param array $message_data
	 * @return boolean
	 */
	public function updateMessageById($message_data)
	{
		$message = $this->getMessageById($message_data['ms_id']);
   		if( (false === $message) || !(is_object($message)) ){
   			return false;
   		}
		unset($message_data['ms_id']);
		$message->set($message_data);
		return (boolean) $message->save();
	}

	/**
	 * Delete message by id
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $ms_id
	 * @return boolean
	 */
	public function deleteMessageById($ms_id)
	{
		$message = $this->getMessageById($ms_id);
   		if( (false === $message) || !(is_object($message)) ){
   			return false;
   		}
		return (boolean) $message->delete();
	}

	/**
	 * Delete message by multiple
	 *
	 * @since 1.0
	 * @access public
	 * @param array $message_data
	 * @return boolean
	 */
	public function deleteMessageByMultiple($message_data)
	{
		$message = $this->getMessageByMultiple($message_data);
   		if( (false === $message) || !(is_object($message)) ){
   			return false;
   		}
		return (boolean) $message->delete();

	}

   	/**
   	 * Delete message by id or post id
   	 *
   	 * @since 1.0
   	 * @access public
   	 * @param integer $ms_id
   	 * @param integer $parent_id
   	 * @param integer $sender_id
   	 * @param integer $receiver_id
   	 * @return boolean
   	 */
	public function dumpMessage($ms_id = false, $parent_id = false,  $sender_id = false, $receiver_id = false)
	{
		if($ms_id !== false){
			return (boolean) \ORM::for_table($this->table)->where_equal('ms_id', $ms_id)->delete_many();
		}
		if($parent_id !== false){
			return (boolean) \ORM::for_table($this->table)->where_equal('parent_id', $ms_id)->delete_many();
		}
		if($sender_id !== false){
			return (boolean) \ORM::for_table($this->table)->where_equal('sender_id', $sender_id)->delete_many();
		}
		if($receiver_id !== false){
			return (boolean) \ORM::for_table($this->table)->where_equal('receiver_id', $receiver_id)->delete_many();
		}
	}
}