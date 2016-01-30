<?php
/**
 * Home - Timber Landing Page
 *
 * @author      Clivern <support@clivern.com>
 * @copyright   2015 Clivern
 * @link        http://clivern.com
 * @license     http://codecanyon.com/license
 * @version     1.0
 * @package     Home
 */

/**
 * Home Plugin Core
 *
 * @since 1.0
 */
class Home {

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
	 * @return object
	 */
	public function setDepen()
	{
		global $timber;

		$this->timber = $timber;
		return $this;
	}

	/**
	 * Run plugin
	 *
	 * @since 1.0
	 * @access public
	 */
	public function run()
	{
		$this->timber->hook('timber.activate_plugin', function( $timber, $plugin ){
			if($plugin !== 'home'){
				return;
			}

			$this->activate();
		});

		$this->timber->hook('timber.deactivate_plugin', function( $timber, $plugin ){
			if($plugin !== 'home'){
				return;
			}

			$this->deactivate();
		});

		$this->timber->hook('timber.delete_plugin', function( $timber, $plugin ){
			if($plugin !== 'home'){
				return;
			}

			$this->delete();
		});

		$this->timber->hook('timber.home_override', function( $timber ){
			$this->landingPage($timber);
			die();
		});
	}

	/**
	 * Plugin Activation
	 *
	 * @since 1.0
	 * @access public
	 */
	public function activate()
	{
		# silence is golden
	}

	/**
	 * Plugin Deactivation
	 *
	 * @since 1.0
	 * @access public
	 */
	public function deactivate()
	{
		# silence is golden
	}


	/**
	 * Pluign Deletion
	 *
	 * @since 1.0
	 * @access public
	 */
	public function delete()
	{
		# silence is golden
	}

	/**
	 * Render Landing Page
	 *
	 * @since 1.0
	 * @access public
	 * @param object $timber
	 */
	public function landingPage($timber)
	{
		include_once CLIVERN_HOME_ROOT_DIR . '/template.php';
	}
}

# Run Plugin
Home::instance()->setDepen()->run();