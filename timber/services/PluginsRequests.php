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

namespace Timber\Services;

/**
 * Plugins Requests Services
 *
 * @since 1.0
 */
class PluginsRequests extends \Timber\Services\Base {

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 * @access public
	 * @param object $timber
	 */
    public function __construct($timber)
    {
        parent::__construct($timber);
    }

	/**
	 * Activate Plugin Request
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function activatePlugin()
	{
		if( !(isset($_POST['plugin'])) || ($_POST['plugin'] == '') ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		$plugin = filter_var(strtolower($_POST['plugin']), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

		if( $this->timber->plugins->activatePlugin($plugin) ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Plugin activated successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}

	/**
	 * Deactivate Plugin Request
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function deactivatePlugin()
	{
		if( !(isset($_POST['plugin'])) || ($_POST['plugin'] == '') ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		$plugin = filter_var(strtolower($_POST['plugin']), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		$plugins_list = $this->timber->plugins->getPluginsList();

		if( (in_array($plugin, $plugins_list)) && ($this->timber->plugins->deactivatePlugin($plugin)) ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Plugin deactivated successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}

	/**
	 * Delete Plugin Request
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function deletePlugin()
	{
		if( !(isset($_POST['plugin'])) || ($_POST['plugin'] == '')  ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		$plugin = filter_var(strtolower($_POST['plugin']), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		$plugins_list = $this->timber->plugins->getPluginsList();

		if( (in_array($plugin, $plugins_list)) && ($this->timber->plugins->deletePlugin($plugin)) ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Plugin deleted successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}
}