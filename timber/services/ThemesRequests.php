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
 * Themes Requests Services
 *
 * @since 1.0
 */
class ThemesRequests extends \Timber\Services\Base {

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
	 * Save Theme Request
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function saveTheme()
	{
		if( !(isset($_POST['theme'])) || ($_POST['theme'] == '') || ($_POST['skin'] == '') || ($_POST['font'] == '') ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		$theme = filter_var(strtolower($_POST['theme']), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		$skin = filter_var($_POST['skin'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		$font = filter_var($_POST['font'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

		if( ($this->timber->twig->validateTheme($theme)) && ($this->timber->twig->validateSkin($skin)) && ($this->timber->twig->validateFont($font)) && ($this->timber->twig->updateTheme($skin, $font)) ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Theme updated successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}

	/**
	 * Activate Theme Request
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function activateTheme()
	{
		if( !(isset($_POST['theme'])) || ($_POST['theme'] == '') || ($_POST['skin'] == '') || ($_POST['font'] == '') ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}
		$theme = filter_var(strtolower($_POST['theme']), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		$skin = filter_var($_POST['skin'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		$font = filter_var($_POST['font'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

		if( ($this->timber->twig->validateTheme($theme)) && ($this->timber->twig->validateSkin($skin)) && ($this->timber->twig->validateFont($font)) && ($this->timber->twig->activateTheme($theme, $skin, $font)) ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Theme activated successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}

	/**
	 * Delete Theme Request
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function deleteTheme()
	{
		if( !(isset($_POST['theme'])) || ($_POST['theme'] == '') ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}
		$theme = filter_var(strtolower($_POST['theme']), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		$enabled_theme = $this->timber->twig->getEnabledTheme();

		if( ($this->timber->twig->validateTheme($theme)) && ($enabled_theme != $theme) && ($this->timber->twig->deleteTheme($theme)) ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Theme deleted successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}
}