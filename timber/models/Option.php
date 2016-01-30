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
 * Options Model
 *
 * Perform All CRUD Operations.
 *
 * `op_id` int(11) not null auto_increment,
 * `op_key` varchar(60) not null,
 * `op_value` text not null,
 * `autoload` enum('on','off') not null,
 *
 * @since 1.0
 */
class Option {

	/**
	 * Options table name
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
		$this->table = TIMBER_DB_PREFIX . TIMBER_DB_OPTIONS_TABLE;
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
	 * Count all options
	 *
	 * This method used for debugging purposes
	 *
   	 * @since 1.0
   	 * @access public
   	 * @return integer
	 */
	public function countOptions()
	{
		return \ORM::for_table($this->table)->count();
	}

	/**
	 * Get option by id
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $op_id
	 * @return object
	 */
	public function getOptionById($op_id)
	{
		return \ORM::for_table($this->table)->where('op_id', $op_id)->find_one();
	}

	/**
	 * Get option by key
	 *
	 * @since 1.0
	 * @access public
	 * @param string $op_key
	 * @return object
	 */
	public function getOptionByKey($op_key)
	{
		return \ORM::for_table($this->table)->where('op_key', $op_key)->find_one();
	}

    /**
     * Get option value by key
     *
     * @since 1.0
     * @access public
     * @param string $op_key
     * @return object
     */
    public function getOptionValueByKey($op_key)
    {
        $option = \ORM::for_table($this->table)->where('op_key', $op_key)->find_one();

        if( (false !== $option) && (is_object($option)) ){
            $option = $option->as_array();
            return $option['op_value'];
        }else{
            return '';
        }
    }

	/**
	 * Get option by key
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $op_id
	 * @param string $op_key
	 * @return object
	 */
	public function getOptionByIdAndKey($op_id, $op_key)
	{
		return \ORM::for_table($this->table)->where(array('op_id' => $op_id, 'op_key' => $op_key))->find_one();
	}

	/**
	 * Get options that needed at each request (autoload is on)
	 *
	 * @since 1.0
	 * @access public
	 * @param on|off $autoload
	 * @return array
	 */
	public function getOptions($autoload)
	{
		return \ORM::for_table($this->table)->where('autoload', $autoload)->find_array();
	}

	/**
	 * Add option
	 *
	 * @since 1.0
	 * @access public
	 * @param array $option_data
	 * @return boolean
	 */
	public function addOption($option_data)
	{
		$option = \ORM::for_table($this->table)->create();
		$option->op_key = $option_data['op_key'];
		$option->op_value = $option_data['op_value'];
		$option->autoload = $option_data['autoload'];
		$option->save();
		return $option->id();
	}

	/**
	 * Add options
	 *
	 * @since 1.0
	 * @access public
	 * @param array $options_data
	 * @return boolean
	 */
	public function addOptions($options_data)
	{
		$status = true;
		foreach($options_data as $option_data){
			$option = \ORM::for_table($this->table)->create();
			$option->op_key = $option_data['op_key'];
			$option->op_value = $option_data['op_value'];
			$option->autoload = $option_data['autoload'];
			$status &= $option->save();
		}
		return (boolean) $status;
	}

	/**
	 * Update option with id
	 *
	 * @since 1.0
	 * @access public
	 * @param array $option_data
	 * @return boolean
	 */
	public function updateOptionById($option_data)
	{
		$option = $this->getOptionById($option_data['op_id']);
   		if( (false === $option) || !(is_object($option)) ){
   			return false;
   		}
		unset($option_data['op_id']);
		$option->set($option_data);
		return (boolean) $option->save();
	}

	/**
	 * Update option with key
	 *
	 * @since 1.0
	 * @access public
	 * @param array $option_data
	 * @return boolean
	 */
	public function updateOptionByKey($option_data)
	{
		$option = $this->getOptionByKey($option_data['op_key']);
   		if( (false === $option) || !(is_object($option)) ){
   			return false;
   		}
		unset($option_data['op_key']);
		$option->set($option_data);
		return (boolean) $option->save();
	}

	/**
	 * Update option with id and key
	 *
	 * @since 1.0
	 * @access public
	 * @param array $option_data
	 * @return boolean
	 */
	public function updateOptionByIdAndKey($option_data)
	{
		$option = $this->getOptionByIdAndKey($option_data['op_id'], $option_data['op_key']);
   		if( (false === $option) || !(is_object($option)) ){
   			return false;
   		}
		unset($option_data['op_id']);
		unset($option_data['op_key']);
		$option->set($option_data);
		return (boolean) $option->save();
	}

	/**
	 * Delete option with id
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $op_id
	 * @return boolean
	 */
	public function deleteOptionById($op_id)
	{
		$option = $this->getOptionById($op_id);
   		if( (false === $option) || !(is_object($option)) ){
   			return false;
   		}
		return (boolean) $option->delete();
	}

	/**
	 * Delete option with key
	 *
	 * @since 1.0
	 * @access public
	 * @param string $op_key
	 * @return boolean
	 */
	public function deleteOptionByKey($op_key)
	{
		$option = $this->getOptionByKey($op_key);
   		if( (false === $option) || !(is_object($option)) ){
   			return false;
   		}
		return (boolean) $option->delete();
	}

	/**
	 * Delete option with id and key
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $op_id
	 * @param string $op_key
	 * @return boolean
	 */
	public function deleteOptionByIdAndKey($op_id, $op_key)
	{
		$option = $this->getOptionByIdAndKey($op_id, $op_key);
   		if( (false === $option) || !(is_object($option)) ){
   			return false;
   		}
		return (boolean) $option->delete();
	}
}