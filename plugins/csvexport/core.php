<?php
/**
 * CSV Export - Export Records to CSV
 *
 * @author      Clivern <support@clivern.com>
 * @copyright   2016 Clivern
 * @link        http://clivern.com
 * @license     http://codecanyon.com/license
 * @version     1.0
 * @package     CSV Export
 */

/**
 * CSVExport Plugin Core
 *
 * @since 1.0
 */
class CSVExport {

	/**
	 * Current used services
	 *
	 * @since 1.0
	 * @access private
	 * @var object
	 */
	private $services;

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
			if($plugin !== 'csvexport'){
				return;
			}
			$this->activate();
		});

		$this->timber->hook('timber.deactivate_plugin', function( $timber, $plugin ){
			if($plugin !== 'csvexport'){
				return;
			}

			$this->deactivate();
		});

		$this->timber->hook('timber.delete_plugin', function( $timber, $plugin ){
			if($plugin !== 'csvexport'){
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
			if($plugin !== 'csvexport'){
				return;
			}

			$this->renderFilters($services);
		});

		$this->timber->hook('timber.csvexport.sandbox_page', function(){
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
		# Silence is golden
	}

	/**
	 * Plugin Deactivation
	 *
	 * @since 1.0
	 * @access public
	 */
	public function deactivate()
	{
		# Silence is golden
	}

	/**
	 * Pluign Deletion
	 *
	 * @since 1.0
	 * @access public
	 */
	public function delete()
	{
		# Silence is golden
	}

	/**
	 * Load Plugin Settings
	 *
	 * @since 1.0
	 * @access public
	 */
	public function load()
	{
		# Silence is golden
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
           <li<?php if( $this->timber->twigext->activeRoute('/csvexport') ){ ?> class="active"<?php } ?>>
               <a href="<?php echo $this->timber->config('request_url') ?>/sandbox/csvexport">
                   <i class="fa fa-fw fa-question"></i> CSV Export
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
		$this->services->Common->renderFilter(array('admin'), '/sandbox/csvexport');

		if( (isset($_GET['action'])) && ($_GET['action'] == 'export_projects') ){
			Writer::instance()->config($this->exportProjects())->buildCSV()->downloadCSV();
		}

		if( (isset($_GET['action'])) && ($_GET['action'] == 'export_members') ){
			Writer::instance()->config($this->exportMembers())->buildCSV()->downloadCSV();
		}

		if( (isset($_GET['action'])) && ($_GET['action'] == 'export_invoices') ){
			Writer::instance()->config($this->exportInvoices())->buildCSV()->downloadCSV();
		}

		if( (isset($_GET['action'])) && ($_GET['action'] == 'export_expenses') ){
			Writer::instance()->config($this->exportExpenses())->buildCSV()->downloadCSV();
		}

		if( (isset($_GET['action'])) && ($_GET['action'] == 'export_estimates') ){
			Writer::instance()->config($this->exportEstimates())->buildCSV()->downloadCSV();
		}

		if( (isset($_GET['action'])) && ($_GET['action'] == 'export_subscriptions') ){
			Writer::instance()->config($this->exportSubscriptions())->buildCSV()->downloadCSV();
		}

		if( (isset($_GET['action'])) && ($_GET['action'] == 'export_items') ){
			Writer::instance()->config($this->exportItems())->buildCSV()->downloadCSV();
		}
	}

	/**
	 * Export Projects
	 *
	 * @since 1.0
	 * @access private
	 * @return array
	 */
	private function exportProjects()
	{
		$this->timber->project_model->queryBuilder();
	}

	/**
	 * Export Members
	 *
	 * @since 1.0
	 * @access private
	 * @return array
	 */
	private function exportMembers()
	{

	}

	/**
	 * Export Invoices
	 *
	 * @since 1.0
	 * @access private
	 * @return array
	 */
	private function exportInvoices()
	{

	}

	/**
	 * Export Expenses
	 *
	 * @since 1.0
	 * @access private
	 * @return array
	 */
	private function exportExpenses()
	{

	}

	/**
	 * Export Estimates
	 *
	 * @since 1.0
	 * @access private
	 * @return array
	 */
	private function exportEstimates()
	{

	}

	/**
	 * Export Subscriptions
	 *
	 * @since 1.0
	 * @access private
	 * @return array
	 */
	private function exportSubscriptions()
	{

	}

	/**
	 * Export Items
	 *
	 * @since 1.0
	 * @access private
	 * @return array
	 */
	private function exportItems()
	{

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
                        <h4 class="font-light m-b-xs"><i class="fa fa-fw fa-question"></i> CSV Export</h4>
                    </div>
                </div>
            </div>

            <div class="sixteen wide column">
                <div class="metabox">
                    <div class="metabox-body">
                    	<p>
							<a href="<?php echo $this->timber->config('request_url') ?>/sandbox/csvexport?action=export_projects">Export Projects</a>
						</p>
						<p>
							<a href="<?php echo $this->timber->config('request_url') ?>/sandbox/csvexport?action=export_members">Export Members</a>
						</p>
						<p>
							<a href="<?php echo $this->timber->config('request_url') ?>/sandbox/csvexport?action=export_invoices">Export Invoices</a>
						</p>
						<p>
							<a href="<?php echo $this->timber->config('request_url') ?>/sandbox/csvexport?action=export_expenses">Export Expenses</a>
						</p>
						<p>
							<a href="<?php echo $this->timber->config('request_url') ?>/sandbox/csvexport?action=export_estimates">Export Estimates</a>
						</p>
						<p>
							<a href="<?php echo $this->timber->config('request_url') ?>/sandbox/csvexport?action=export_subscriptions">Export Subscriptions</a>
						</p>
						<p>
							<a href="<?php echo $this->timber->config('request_url') ?>/sandbox/csvexport?action=export_items">Export Items</a>
						</p>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}