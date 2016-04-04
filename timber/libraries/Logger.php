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

namespace Timber\Libraries;

/**
 * Third party logger writer for slim
 *
 * <code>
 * $app = new \Slim\Slim(array(
 *     'log.writer' => \Timber\Configs\Logger::instance()
 * ));
 * </code>
 *
 * @since 1.0
 */
class Logger {

   	/**
   	 * Log file content
   	 *
	 * @since 1.0
	 * @access private
     	 * @var string $this->resource
     	 */
    	private $resource;

    	/**
    	 * Log file settings
    	 *
	 * @since 1.0
	 * @access private
     	 * @var array $this->settings
     	 */
    	private $settings = array(
		'rel_path' => TIMBER_LOGS_DIR,
		'name_format' => 'Y-m-d',
		'extension' => 'log',
		'message_format' => '%label% - %date% - %message%'
    	);

	/**
	 * Instance of timber app
	 *
	 * @since 1.0
	 * @access private
	 * @var object $this->timber
	 */
	private $timber;

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
	 * Set class dependencies
	 *
	 * @since 1.0
	 * @access public
	 * @param object $timber
	 * @return object
	 */
	public function setDepen($timber)
	{
		$this->timber = $timber;
		return $this;
	}

	/**
	 * Configure class
	 *
	 * @since 1.0
	 * @access public
	 */
	public function config()
	{
		//silence is golden
	}

    	/**
     	 * Write to log
     	 *
	 * @since 1.0
	 * @access public
     	 * @param   mixed $object
     	 * @param   int   $level
     	 * @return  void
     	 */
    	public function write($object, $level)
    	{
	  	//Determine label
	  	$label = 'DEBUG';
	  	switch ($level) {
			case \Slim\Log::FATAL:
		    		$label = 'FATAL';
		    		break;
			case \Slim\Log::ERROR:
		    		$label = 'ERROR';
		    		break;
			case \Slim\Log::WARN:
		    		$label = 'WARN';
		    		break;
			case \Slim\Log::INFO:
		    		$label = 'INFO';
		    		break;
	  	}
	  	//Get formatted log message
	  	$message = str_replace(array(
			'%label%',
			'%date%',
			'%message%'
	  	), array(
			$label,
			date('c'),
			(string)$object
	  	), $this->settings['message_format']);
	  	//Open resource handle to log file
	  	if (!$this->resource) {
			$filename = date($this->settings['name_format']);
			if (! empty($this->settings['extension'])) {
		    		$filename .= '.' . $this->settings['extension'];
			}
			$this->resource = @fopen( TIMBER_ROOT . $this->settings['rel_path'] . '/' . $filename, 'a');
	  	}
	  	//Output to resource
	  	@fwrite($this->resource, $message . PHP_EOL);
    	}
}