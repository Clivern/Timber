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

namespace Timber\Services;

/**
 * Plugins Data Services
 *
 * @since 1.0
 */
class PluginsData extends \Timber\Services\Base {

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 * @access public
	 * @param object $timber
	 */
    public function __construct($timber) {
        parent::__construct($timber);
    }

	/**
	 * Get Plugin Data
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function pluginsData()
	{
		$data = array();

		$data['activate_form_action'] = $this->timber->config('request_url') . '/request/backend/ajax/plugins/activate';
		$data['deactivate_form_action'] = $this->timber->config('request_url') . '/request/backend/ajax/plugins/deactivate';
		$data['delete_form_action'] = $this->timber->config('request_url') . '/request/backend/ajax/plugins/delete';
		$this->timber->plugins->syncPlugins();
		$data['plugins_data'] = $this->timber->plugins->getPluginsData();
		$active_plugins = $this->timber->plugins->getActivePlugins();

		foreach ($data['plugins_data'] as $plugin => $plugin_data) {
			$data['plugins_data'][$plugin]['enabled'] = (in_array($plugin, $active_plugins)) ? true : false;
		}

		return $data;
	}

	/**
	 * Get Current User Data
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function currentUserData()
	{
		$data = array();
		$user_id = $this->timber->security->getId();
		$user = $this->timber->user_model->getUserById($user_id);

		if( (false === $user) || !(is_object($user)) ){
			$this->timber->security->endSession();
			$this->timber->redirect( $this->timber->config('request_url') . '/500' );
		}

		$user = $user->as_array();
		foreach ($user as $key => $value) {
			$data['current_user_' . $key] = $value;
		}

		return $data;
	}
}