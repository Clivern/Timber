<?php
/**
 * Help - Timber Help Plugin
 *
 * @author      Clivern <support@clivern.com>
 * @copyright   2015 Clivern
 * @link        http://clivern.com
 * @license     http://codecanyon.com/license
 * @version     1.0
 * @package     Help
 */

/**
 * Help Plugin Core
 *
 * @since 1.0
 */
class Help {

	/**
	 * Current used services
	 *
	 * @since 1.0
	 * @access private
	 * @var object
	 */
	private $services;

	/**
     * Plugin Settings
     *
     * @since 1.0
     * @access private
     * @var array $this->settings
     */
	private $settings = array(
		'dummy' => 'data',
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
		// timber.top_menu
		// timber.bottom_menu
		$this->timber->hook('timber.activate_plugin', function( $timber, $plugin ){
			if($plugin !== 'help'){
				return;
			}
			$this->activate();
		});

		$this->timber->hook('timber.deactivate_plugin', function( $timber, $plugin ){
			if($plugin !== 'help'){
				return;
			}

			$this->deactivate();
		});

		$this->timber->hook('timber.delete_plugin', function( $timber, $plugin ){
			if($plugin !== 'help'){
				return;
			}

			$this->delete();
		});

		$this->timber->hook('timber.init', function( $timber ){
			$this->load();
		});

		$this->timber->hook('timber.bottom_menu', function( $timber ){
			$this->menu();
		});

		$this->timber->hook('timber.render_filters', function( $plugin, $timber, $services ){
			if($plugin !== 'help'){
				return;
			}

			$this->renderFilters($services);
		});

		$this->timber->hook('timber.help.sandbox_page', function(){
			$this->renderContent();
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
		$settings = $this->timber->option_model->getOptionByKey('_help_plugin_settings');
		if( (false === $settings) || !(is_object($settings)) ){
			$this->timber->option_model->addOption(array(
				'op_key' => '_help_plugin_settings',
				'op_value' => serialize($this->settings),
				'autoload' => 'off',
			));
		}
	}

	/**
	 * Plugin Deactivation
	 *
	 * @since 1.0
	 * @access public
	 */
	public function deactivate()
	{
		$this->timber->option_model->deleteOptionByKey('_help_plugin_settings');
	}

	/**
	 * Pluign Deletion
	 *
	 * @since 1.0
	 * @access public
	 */
	public function delete()
	{
		$this->timber->option_model->deleteOptionByKey('_help_plugin_settings');
	}

	/**
	 * Load Plugin Settings
	 *
	 * @since 1.0
	 * @access public
	 */
	public function load()
	{
		$settings = $this->timber->option_model->getOptionByKey('_help_plugin_settings');
		if( (false === $settings) || !(is_object($settings)) ){
			$this->timber->option_model->addOption(array(
				'op_key' => '_help_plugin_settings',
				'op_value' => serialize($this->settings),
				'autoload' => 'off',
			));
		}else{
			$settings = $settings->as_array();
			$this->settings = unserialize($settings['op_value']);
		}
	}

	/**
	 * Add Menu Item
	 *
	 * @since 1.0
	 * @access public
	 */
	public function menu()
	{
		if( !($this->timber->security->isAdmin()) ){
			return "";
		}
		?>
           <li<?php if( $this->timber->twigext->activeRoute('/help') ){ ?> class="active"<?php } ?>>
               <a href="<?php echo $this->timber->config('request_url') ?>/sandbox/help">
                   <i class="fa fa-fw fa-question"></i> Help
               </a>
           </li>
		<?php
	}

	/**
	 * Add Plugin Page Filters (Auth...etc)
	 *
	 * @since 1.0
	 * @access public
	 * @param object $services
	 */
	public function renderFilters($services)
	{
		$this->services = $services;
		$this->services->Common->renderFilter(array('admin'), '/sandbox/help');
	}

	/**
	 * Render Page Content
	 *
	 * @since1.0
	 * @access public
	 */
	public function renderContent()
	{
		?>
        <div class="ui stackable grid">
            <div class="sixteen wide column">
                <div class="metabox blue">
                    <div class="metabox-body">
                        <h4 class="font-light m-b-xs"><i class="fa fa-fw fa-question"></i> Help</h4>
                    </div>
                </div>
            </div>

            <div class="sixteen wide column">
                <div class="metabox">
                    <div class="metabox-body">
						<p>Hello World!</p>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}

# Run Plugin
Help::instance()->setDepen()->run();