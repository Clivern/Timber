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

namespace Timber\Libraries;

/**
 * Extend Twig with Custom Filters, Custom Functions and Global Variables
 *
 * @since 1.0
 * @link http://twig.sensiolabs.org/documentation
 */
class TwigExt extends \Twig_Extension {

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
	 * Configure twig to render templates
	 *
	 * @since 1.0
	 * @access public
	 * @link http://twig.sensiolabs.org/documentation
	 * @param boolean $error
	 */
	public function config()
	{
		//silence is golden
	}

	/**
	 * Get option value with key
	 *
	 * @since 1.0
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function option($key)
	{
		return $this->timber->config($key);
	}

	/**
	 * Define whether radio button is checked
	 *
	 * @since 1.0
	 * @access public
	 * @param string $option_value
	 * @param string $field_value
	 * @return string
	 */
	public function checked($option_value, $field_value)
	{
		if($option_value == $field_value){
			echo "checked";
		}
	}

	/**
	 * Define whether option is selected
	 *
	 * @since 1.0
	 * @access public
	 * @param string $option_value
	 * @param string $field_value
	 * @return string
	 */
	public function selected($option_value, $field_value)
	{
		if( (is_array($field_value)) && (in_array($option_value, $field_value)) ){
			echo "selected";
		}
		if( (is_array($option_value)) && (in_array($field_value, $option_value)) ){
			echo "selected";
		}
		if( !(is_array($option_value)) && !(is_array($field_value)) && ($option_value == $field_value) ){
			echo "selected";
		}
	}

	/**
	 * Change timestamp to date
	 *
	 * @since 1.0
	 * @access public
	 * @param integer  $timestamp
	 * @param  boolean $gmt
	 * @param  string  $format
	 * @return string
	 */
	public function stampToDate($timestamp, $gmt = true, $format = 'Y-m-d H:i:s')
	{
		return $this->timber->time->timestampToDate($timestamp, $gmt, $format);
	}

	/**
	 * Change date to timestamp
	 *
	 * @since 1.0
	 * @access public
	 * @param string $datetime
	 * @param boolean $gmt
	 * @return string
	 */
	public function dateToStamp($datetime, $gmt = true)
	{
		return $this->timber->time->dateToTimestamp($datetime, $gmt);
	}

	/**
	 * Change timestamp to diff
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $timestamp
	 * @param boolean $gmt
	 * @return string
	 */
	public function stampToDiff($timestamp, $gmt = true)
	{
		return $this->timber->time->humanDiff($this->timber->time->getTimestampDiff($timestamp, $gmt));
	}

	/**
	 * Change date to diff
	 *
	 * @since 1.0
	 * @access public
	 * @param string $datetime
	 * @param boolean $gmt
	 * @return string
	 */
	public function dateToDiff($datetime, $gmt = true)
	{
		return $this->timber->time->humanDiff($this->timber->time->getDatetimeDiff($datetime, $gmt));
	}

	/**
	 * Change SQL file name to diff
	 *
	 * @since 1.0
	 * @access public
	 * @param string $sql_file
	 * @param boolean $gmt
	 * @return string
	 */
	public function sqlToDiff($sql_file, $gmt = false)
	{
		$sql_file = str_replace('.sql.gz', '', $sql_file);
		$sql_file = str_replace('.sql', '', $sql_file);
		$sql_file = explode('__', $sql_file);
		$sql_file[0] = str_replace('_', '-', $sql_file[0]);
		$sql_file[1] = str_replace('_', ':', $sql_file[1]);
		$sql_file = implode(' ', $sql_file);
		return $this->timber->time->humanDiff($this->timber->time->getDatetimeDiff($sql_file, $gmt));
	}

	/**
	 * Get Gravatar Link
	 *
	 * @since 1.0
	 * @access public
	 * @param string $email
	 * @param integer $file_id
	 * @param integer $size
	 * @return string
	 */
	public function gravatar($email, $file_id, $size = 60)
	{
		return $this->timber->gravatar->email($email)->fileId($file_id)->gUrl($size);
	}

	/**
	 * Get file Link
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $file_id
	 * @param string $default
	 * @return string
	 */
	public function fileUrl($file_id, $default = '')
	{
		return $this->timber->storage->getFileUrl($file_id, $default);
	}

	/**
	 * Get url of a route
	 *
	 * @since 1.0
	 * @access public
	 * @param string $route
	 * @return string
	 */
	public function routeToUrl($route)
	{
		return $this->timber->config('request_url') . $route;
	}

	/**
	 * Get download file link
	 *
	 * @since 1.0
	 * @access public
	 * @param  string $file_id
	 * @param  string $hash
	 * @return string
	 */
	public function downloadLink($file_id = '', $hash = '')
	{
		return $this->timber->config('request_url') . "/request/backend/direct/download/sd27c7g8gb7hxsd/{$file_id}/{$hash}";
	}

	/**
	 * Get download bill link
	 *
	 * @since 1.0
	 * @access public
	 * @param  string $file_id
	 * @param  string $hash
	 * @return string
	 */
	public function downloadBill($file_id = '', $hash = '')
	{
		return $this->timber->config('request_url') . "/request/backend/direct/download/sdv289ry793r7ty/{$file_id}/{$hash}";
	}

	/**
	 * Themes dir url
	 *
	 * @since 1.0
	 * @access public
	 * @param string $rel_path
	 * @return string
	 */
	public function themesUrl($rel_path = '')
	{
		return rtrim( $this->timber->config('request_url'), '/index.php' ) . TIMBER_THEMES_DIR . $rel_path;
	}

	/**
	 * Default theme dir url
	 *
	 * @since 1.0
	 * @access public
	 * @param string $rel_path
	 * @return string
	 */
	public function defaultThemeUrl($rel_path = '')
	{
		return rtrim( $this->timber->config('request_url'), '/index.php' ) . TIMBER_THEMES_DIR . $this->timber->twig->getDefaultTheme() . $rel_path;
	}

	/**
	 * Default theme assets dir url
	 *
	 * @since 1.0
	 * @access public
	 * @param string $rel_path
	 * @return string
	 */
	public function defaultThemeAssetsUrl($rel_path = '')
	{
		return rtrim( $this->timber->config('request_url'), '/index.php' ) . TIMBER_THEMES_DIR . $this->timber->twig->getDefaultTheme() . '/assets' . $rel_path;
	}

	/**
	 * Enabled theme url
	 *
	 * @since 1.0
	 * @access public
	 * @param string $rel_path
	 * @return string
	 */
	public function themeUrl($rel_path = '')
	{
		return rtrim( $this->timber->config('request_url'), '/index.php' ) . TIMBER_THEMES_DIR . $this->timber->twig->getEnabledTheme() . $rel_path;
	}

	/**
	 * Enabled theme assets url
	 *
	 * @since 1.0
	 * @access public
	 * @param string $rel_path
	 * @return string
	 */
	public function themeAssetsUrl($rel_path = '')
	{
		return rtrim( $this->timber->config('request_url'), '/index.php' ) . TIMBER_THEMES_DIR . $this->timber->twig->getEnabledTheme() . '/assets' . $rel_path;
	}

	/**
	 * Plugin URL
	 *
	 * @since 1.0
	 * @access public
	 * @param string $rel_path
	 * @return string
	 */
	public function pluginUrl($rel_path = '')
	{
		return rtrim( $this->timber->config('request_url'), '/index.php' ) . TIMBER_PLUGINS_DIR . $rel_path;
	}

	/**
	 * Add nonce to url
	 *
	 * @since 1.0
	 * @access public
	 * @param string $url
	 * @return string
	 */
	public function nonceUrl($url)
	{
		return rtrim($url,'/') . '/' . $this->timber->security->getNonce();
	}

	/**
	 * Translate text
	 *
	 * @since 1.0
	 * @access public
	 * @param string $string
	 * @return string
	 */
	public function trans($string)
	{
		return $this->timber->translator->trans($string);
	}

	/**
	 * Translate text
	 *
	 * @since 1.0
	 * @access public
	 * @param string $string
	 * @return string
	 */
	public function __($string)
	{
		return $this->timber->translator->trans($string);
	}

	/**
	 * Add user nonce to form
	 *
	 * nonce value come from user cookie
	 *
	 * @since 1.0
	 * @access public
	 * @return string
	 */
	public function nonceForm()
	{
		echo "<input type='hidden' name='user_nonce' value='" . $this->timber->security->getNonce() ."'>";
	}

	/**
	 * Add user hash to form
	 *
	 * hash value come from user cookie
	 *
	 * @since 1.0
	 * @access public
	 * @return string
	 */
	public function hashForm()
	{
		return "<input type='hidden' name='user_hash' value='" . $this->timber->security->getHash() ."'>";
	}

	/**
	 * Check if text need to be broken for attacks
	 *
	 * @since 1.0
	 * @access public
	 * @param string $text
	 * @param integer $max_chars
	 * @return string
	 */
	public function breakText($text, $max_chars)
	{
		$text = explode(' ', $text);
		$fix = false;
		foreach ($text as $sub_text) {
			if( strlen($sub_text) >= $max_chars ){
				$fix = true;
			}
		}
		if( $fix === true ){
			echo " npt_fix";
		}
	}

	/**
	 * Check if provider route is active
	 *
	 * @since 1.0
	 * @access public
	 * @param string $unique_text
	 * @return string
	 */
	public function activeRoute($unique_text)
	{
		$full_url = rtrim($this->timber->request->getScheme() . '://' . $this->timber->request->getHost() . $this->timber->request->getRootUri(), '/') . '/' . $this->timber->request->getPath();
		return ((boolean) strpos($full_url, $unique_text) > 0);
	}

	/**
	 * Print Notification as Integer
	 *
	 * @since 1.0
	 * @access public
	 * @param string $item
	 * @param string $expire_route
	 * @param string $format
	 * @param mixed $user_id
	 */
	public function notifyPrint($item, $expire_route = '/', $format = '{$VALUE}', $user_id = false)
	{
		return $this->timber->notify->display($item, $expire_route, $format, $user_id);
	}

	/**
	 * Apply Hook
	 *
	 * @since 1.0
	 * @access public
	 * @param string $hook_name
	 * @param array $args
	 */
	public function applyHook($hook_name, $args = array())
	{
		return $this->timber->applyHook($hook_name, $this->timber, $args);
	}

	/**
	 * Check Permission
	 *
	 * @since 1.0
	 * @access public
	 * @param string $perm
	 * @return boolean
	 */
	public function checkPerm($perm)
	{
		return $this->timber->access->checkPermission($perm);
	}

	/**
	 * Get Demo Data
	 *
	 * @since 1.0
	 * @access public
	 * @return string
	 */
	public function demoData()
	{
		if( $this->timber->demo->demoActive() ){
			echo "<br/><p><strong>Admin Login:</strong> admin/12345678</p><p><strong>Staff Login:</strong> staff/12345678</p><p><strong>Client Login:</strong> client/12345678</p>";
		}
	}

	/**
	 * Bind Global Variables to Twig
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function getGlobals()
	{
		return array(
			# App Info
			'app_name' => $this->timber->config('app_name'),
			'app_author' => $this->timber->config('app_author'),
			'app_author_url' => $this->timber->config('app_author_url'),
			'copyright_date' => date('Y'),

			# URL and Schema
			'home_url' => $this->timber->config('request_url'),
			'url_schema' => $this->timber->config('url_schema'),

			# Site Options
			'site_title' => $this->timber->config('_site_title'),
			'site_sub_page' => '',
			'site_lang' => $this->timber->config('_site_lang'),
			'site_timezone' => $this->timber->config('_site_timezone'),
			'site_email' => $this->timber->config('_site_email'),
			'site_description' => $this->timber->config('_site_description'),
			'site_keywords' => '',
			'site_maintainance_mode' => $this->timber->config('_site_maintainance_mode'),
			'site_custom_styles' => $this->timber->config('_site_custom_styles'),
			'site_custom_scripts' => $this->timber->config('_site_custom_scripts'),
			'site_tracking_codes' => $this->timber->config('_site_tracking_codes'),
			'site_theme' => $this->timber->config('_site_theme'),
			'site_skin' => $this->timber->config('_site_skin'),
			'site_logo' => $this->timber->config('_site_logo'),
			'site_logo_path' => rtrim( $this->timber->config('request_url'), '/index.php' ) . '/' . TIMBER_STORAGE_DIR . '/public/' . $this->timber->config('_site_logo'),
			'google_font' => $this->timber->config('_google_font'),
			'gravatar_platform' => $this->timber->config('_gravatar_platform'),

			'themes_data' => unserialize($this->timber->config('_themes_data')),
			'plugins_data' => unserialize($this->timber->config('_plugins_data')),

			# Social Login Status
			'social_login' => (( ($this->timber->config('_facebook_login_status') == 'on') || ($this->timber->config('_twitter_login_status') == 'on') || ($this->timber->config('_google_login_status') == 'on') ) ? 'on' : 'off'),
			'facebook_login' => $this->timber->config('_facebook_login_status'),
			'twitter_login' => $this->timber->config('_twitter_login_status'),
			'google_login' => $this->timber->config('_google_login_status'),

			# Auth Data
			'is_auth' => ( $this->timber->security->isAuth() ),
			'is_admin' => ( $this->timber->security->isAdmin() ),
			'is_staff' => ( $this->timber->security->isStaff() ),
			'is_client' => ( $this->timber->security->isClient() ),

			# Frontend and Backend Check
			'is_frontend' => ( $this->timber->security->isFrontend() ),
			'is_backend' => ( $this->timber->security->isBackend() ),

			'invoices_checkout_menu' => $this->timber->cookie->exist('_checkout_invoices'),
		);
	}

	/**
	 * Bind Functions to Twig
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('route_to_url', array($this, 'routeToUrl')),
			new \Twig_SimpleFunction('themes_url', array($this, 'themesUrl')),
			new \Twig_SimpleFunction('default_theme_url', array($this, 'defaultThemeUrl')),
			new \Twig_SimpleFunction('default_theme_assets_url', array($this, 'defaultThemeAssetsUrl')),
			new \Twig_SimpleFunction('theme_url', array($this, 'themeUrl')),
			new \Twig_SimpleFunction('theme_assets_url', array($this, 'themeAssetsUrl')),
			new \Twig_SimpleFunction('nonce_url', array($this, 'nonceUrl')),
			new \Twig_SimpleFunction('selected', array($this, 'selected')),
			new \Twig_SimpleFunction('checked', array($this, 'checked')),
			new \Twig_SimpleFunction('lang_checked', array($this, 'langChecked')),
			new \Twig_SimpleFunction('option', array($this, 'option')),
			new \Twig_SimpleFunction('stamp_to_date', array($this, 'stampToDate')),
			new \Twig_SimpleFunction('date_to_stamp', array($this, 'dateToStamp')),
			new \Twig_SimpleFunction('stamp_to_diff', array($this, 'stampToDiff')),
			new \Twig_SimpleFunction('date_to_diff', array($this, 'dateToDiff')),
			new \Twig_SimpleFunction('sql_to_diff', array($this, 'sqlToDiff')),
			new \Twig_SimpleFunction('gravatar', array($this, 'gravatar')),
			new \Twig_SimpleFunction('file_url', array($this, 'fileUrl')),
			new \Twig_SimpleFunction('trans', array($this, 'trans')),
			new \Twig_SimpleFunction('__', array($this, '__')),
			new \Twig_SimpleFunction('nonce_form', array($this, 'nonceForm')),
			new \Twig_SimpleFunction('hash_form', array($this, 'hashForm')),
			new \Twig_SimpleFunction('break_text', array($this, 'breakText')),
			new \Twig_SimpleFunction('active_route', array($this, 'activeRoute')),
			new \Twig_SimpleFunction('apply_hook', array($this, 'applyHook')),
			new \Twig_SimpleFunction('check_perm', array($this, 'checkPerm')),
			new \Twig_SimpleFunction('plugin_url', array($this, 'pluginUrl')),
			new \Twig_SimpleFunction('download_link', array($this, 'downloadLink')),
			new \Twig_SimpleFunction('download_bill', array($this, 'downloadBill')),
			new \Twig_SimpleFunction('notify_print', array($this, 'notifyPrint')),
			new \Twig_SimpleFunction('demo_data', array($this, 'demoData')),
		);
	}

	/**
	 * Bind Filters To Twig
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function getFilters()
	{
		return array();
	}

	/**
	 * Bind Extension Name
	 *
	 * @since 1.0
	 * @access public
	 * @return string
	 */
	public function getName()
	{
		return 'Timber';
	}
}