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
 * Common Services
 *
 * @since 1.0
 */
class Common extends \Timber\Services\Base {

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
	 * Render Filters
	 *
	 * @since 1.0
	 * @access public
	 * @param  array $rules
	 * @param  string $redirect_to
	 */
	public function renderFilter($rules, $redirect_to)
	{
		if( !($this->timber->security->isAuth()) ){
			# Send To Login
			$this->timber->security->endSession();
			$this->timber->security->sendToLoginWithRedirect($redirect_to);
		}

		# Check Access Permission
		if( !($this->timber->security->canAccess( $rules )) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}
	}

	/**
	 * Perform ajax check
	 *
	 * @since 1.0
	 * @access public
	 */
	public function ajaxCheck()
	{
		if( !($this->timber->request->isAjax()) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}
	}

	/**
	 * Set footer and header scripts
	 *
	 * @since 1.0
	 * @access public
	 * @param string $identifier
	 * @return array
	 */
	public function runtimeScripts($identifier)
	{
		$allowed_extensions = unserialize($this->timber->config('_allowed_upload_extensions'));
		$allowed_extensions = '.' . strtolower(implode(',.', $allowed_extensions));
		$avatar_extensions = ".png,.jpg";
		$max_file_size = $this->timber->config('_max_upload_size');
		$nonce = $this->timber->security->getNonce();

		$data = array();

		$data['footer_scripts']  = "jQuery(document).ready(function($){ ";
		$data['footer_scripts'] .= "timber.utils.init();";
		$data['footer_scripts'] .= "timber.{$identifier}.init();";
		$data['footer_scripts'] .= " });";

		$date = $this->timber->time->getCurrentDate(false, 'Y-m-d');
		$data['header_scripts']  = "var current_date = '{$date}';";
		$data['header_scripts'] .= "var current_user_nonce = '{$nonce}';";
		$data['header_scripts'] .= "var uploader_global_settings = {acceptedfiles:'{$allowed_extensions}', maxfilesize: {$max_file_size}, maxFiles: 12};";
		$data['header_scripts'] .= "var avatar_uploader_settings = {acceptedfiles:'{$avatar_extensions}', maxfilesize: {$max_file_size}, maxFiles: 1};";
		$data['header_scripts'] .= "var upload_file_socket = '{$this->timber->config('request_url')}/request/backend/ajax/upload/';";
		$data['header_scripts'] .= "var dump_file_socket = '{$this->timber->config('request_url')}/request/backend/ajax/upload/dump_file';";
		$data['header_scripts'] .= ($this->timber->config('_gravatar_platform') == 'native') ? "var gravatar_platform = 'native';" : "var gravatar_platform = 'gravatar';";
		$data['header_scripts'] .= "var i18nStrings = {alert: '{$this->timber->translator->trans('Are You Sure!')}'};";
		$data['header_scripts'] .= "var system_alerts_url = '{$this->timber->config('request_url')}/request/frontend/direct/helpers/alerts';";

		return $data;
	}

	/**
	 * Inject scripts in header
	 *
	 * @since 1.0
	 * @access public
	 * @param array $new_vars
	 * @return array
	 */
	public function injectScripts($new_vars = array())
	{
		$data = array();

		$data['injected_scripts'] = '';

		if( (is_array($new_vars)) && (count($new_vars) > 0) ){
			foreach ($new_vars as $key => $value) {
				$data['injected_scripts'] .= "var {$key} = '{$value}';";
			}
		}

		return $data;
	}

	/**
	 * Set sub-page name
	 *
	 * @since 1.0
	 * @access public
	 * @param string $title
	 * @return array
	 */
	public function subPageName($title)
	{
		return array( 'site_sub_page' => $title );
	}
}