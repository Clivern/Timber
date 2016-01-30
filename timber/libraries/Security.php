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
 * Perform All Security Related Tasks form Auth, Captcha, Nonces...etc
 *
 * @since 1.0
 */
class Security {

    /**
     * User ID
     *
     * @since 1.0
     * @access private
     * @var integer $this->user_id
     */
    private $user_id;

    /**
     * User nonce
     *
     * @since 1.0
     * @access private
     * @var string $this->user_nonce
     */
    private $user_nonce = '';

    /**
     * User hash
     *
     * @since 1.0
     * @access private
     * @var string $this->user_hash
     */
    private $user_hash = '';

    /**
     * User access iden
     *
     * @since 1.0
     * @access private
     * @var string $this->user_access
     */
    private $user_access;

    /**
     * Is auth method cached result
     *
     * @since 1.0
     * @access private
     * @var string $this->cached_is_auth
     */
    private $cached_is_auth = '';

    /**
     * Is user method cached result
     *
     * @since 1.0
     * @access private
     * @var string $this->cached_is_staff
     */
    private $cached_is_staff = '';

    /**
     * Is client method cached result
     *
     * @since 1.0
     * @access private
     * @var string $this->cached_is_client
     */
    private $cached_is_client = '';

    /**
     * Is admin method cached result
     *
     * @since 1.0
     * @access private
     * @var string $this->cached_is_admin
     */
    private $cached_is_admin = '';

    /**
     * Is real user method cached result
     *
     * @since 1.0
     * @access private
     * @var string $this->cached_is_real_staff
     */
    private $cached_is_real_staff = '';

    /**
     * Is real client method cached result
     *
     * @since 1.0
     * @access private
     * @var string $this->cached_is_real_client
     */
    private $cached_is_real_client = '';

    /**
     * Is real admin method cached result
     *
     * @since 1.0
     * @access private
     * @var string $this->cached_is_real_admin
     */
    private $cached_is_real_admin = '';

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
        # Silence is Golden
    }

    /**
     * Check if client in frontend
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function isFrontend()
    {
        $full_url = rtrim($this->timber->request->getScheme() . '://' . $this->timber->request->getHost() . $this->timber->request->getRootUri(), '/') . '/' . $this->timber->request->getPath();
        return !((boolean) strpos($full_url, 'admin') > 0);
    }

    /**
     * Check if client in backend
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function isBackend()
    {
        $full_url = rtrim($this->timber->request->getScheme() . '://' . $this->timber->request->getHost() . $this->timber->request->getRootUri(), '/') . '/' . $this->timber->request->getPath();
        return ((boolean) strpos($full_url, 'admin') > 0);
    }

    /**
     * Auth admins
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function authAdmin($user_data, $remember_me = null)
    {
        $auth_data = array(
            'n' => $user_data['user_nonce'],
            'h' => $user_data['user_hash'],
            'u' => $user_data['user_id'],
            'a' => TIMBER_ADMIN_IDEN,
        );
        $encrypted_auth_data = $this->timber->encrypter->encrypt(
            $auth_data,
            ADMINS_AUTH_SALT
        );

        return $this->timber->cookie->set(
            $this->timber->config('auth_admin'),
            $encrypted_auth_data,
            $remember_me
        );
    }

    /**
     * Auth stuff
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function authStaff($user_data, $remember_me = null)
    {
        $auth_data = array(
            'n' => $user_data['user_nonce'],
            'h' => $user_data['user_hash'],
            'u' => $user_data['user_id'],
            'a' => TIMBER_STAFF_IDEN,
        );
        $encrypted_auth_data = $this->timber->encrypter->encrypt(
            $auth_data,
            STAFF_AUTH_SALT
        );

        return $this->timber->cookie->set(
            $this->timber->config('auth_staff'),
            $encrypted_auth_data,
            $remember_me
        );
    }

    /**
     * Auth clients
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function authClient($user_data, $remember_me = null)
    {
        $auth_data = array(
            'n' => $user_data['user_nonce'],
            'h' => $user_data['user_hash'],
            'u' => $user_data['user_id'],
            'a' => TIMBER_CLIENT_IDEN,
        );
        $encrypted_auth_data = $this->timber->encrypter->encrypt(
            $auth_data,
            CLIENTS_AUTH_SALT
        );

        return $this->timber->cookie->set(
            $this->timber->config('auth_client'),
            $encrypted_auth_data,
            $remember_me
        );
    }

    /**
     * Check if visitor seems to be authorized
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function isAuth()
    {
        if($this->cached_is_auth !== ''){
            return $this->cached_is_auth;
        }
        $this->cached_is_auth =  (boolean) ( $this->timber->cookie->exist($this->timber->config('auth_staff'))
             || $this->timber->cookie->exist($this->timber->config('auth_admin'))
             || $this->timber->cookie->exist($this->timber->config('auth_client')) );
        return $this->cached_is_auth;
    }

    /**
     * Check if current logged user is admin
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function isAdmin()
    {
        if($this->cached_is_admin !== ''){
            return $this->cached_is_admin;
        }
        if( !($this->timber->cookie->exist($this->timber->config('auth_admin'))) ){
            $this->cached_is_admin = false;
            return $this->cached_is_admin;
        }
        $encrypted_cookie_value = $this->timber->cookie->get(
            $this->timber->config('auth_admin'),
            false
        );

        if($encrypted_cookie_value == false){
            $this->cached_is_admin = false;
            return $this->cached_is_admin;
        }
        $decrypted_cookie_value = $this->timber->encrypter->decrypt(
            $encrypted_cookie_value,
            ADMINS_AUTH_SALT
        );

        if( !(is_array($decrypted_cookie_value)) || !(count($decrypted_cookie_value) == 4) ){
            $this->cached_is_admin = false;
            return $this->cached_is_admin;
        }
        $nonce = (isset($decrypted_cookie_value['n'])) ? $decrypted_cookie_value['n'] : false;
        $hash = (isset($decrypted_cookie_value['h'])) ? $decrypted_cookie_value['h'] : false;
        $id = (isset($decrypted_cookie_value['u'])) ? $decrypted_cookie_value['u'] : false;
        $access = (isset($decrypted_cookie_value['a'])) ? $decrypted_cookie_value['a'] : false;

        if( ($nonce === false) || ($hash === false) || ($id === false) || ($access === false) ){
            $this->cached_is_admin = false;
            return $this->cached_is_admin;
        }

        $cleared_auth_data = $this->timber->validator->clear_values(array(
            'n' => array(
                'value' => $nonce,
                'sanit' => 'sstring',
                'valid' => 'vnotempty',
                'default' => false
                ),
            'h' => array(
                'value' => $hash,
                'sanit' => 'sstring',
                'valid' => 'vnotempty',
                'default' => false
                ),
            'u' => array(
                'value' => $id,
                'sanit' => 'snumberint',
                'valid' => 'vnotempty&vint',
                'default' => false
                ),
            'a' => array(
                'value' => $access,
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vequals:' . TIMBER_ADMIN_IDEN,
                'default' => false
                )
        ));

        $this->cached_is_admin = (boolean) ( $cleared_auth_data['n']['status']
            && $cleared_auth_data['h']['status']
            && $cleared_auth_data['u']['status']
            && $cleared_auth_data['a']['status']
        );

        $this->user_nonce = ((boolean) $cleared_auth_data['n']['status']) ? $cleared_auth_data['n']['value'] : false;
        $this->user_hash = ((boolean) $cleared_auth_data['h']['status']) ? $cleared_auth_data['h']['value'] : false;
        $this->user_id = ((boolean) $cleared_auth_data['u']['status']) ? $cleared_auth_data['u']['value'] : false;
        $this->user_access = ((boolean) $cleared_auth_data['a']['status']) ? $cleared_auth_data['a']['value'] : false;

        return $this->cached_is_admin;
    }

    /**
     * Check if user is admin by comparing cookie data with db
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function isRealAdmin()
    {
        if($this->cached_is_real_admin !== ''){
            return $this->cached_is_real_admin;
        }
        $user = $this->timber->custom_model->hasAdminAccessRule(array(
            'u' => $this->user_id,
            'h' => $this->user_hash,
            'n' => $this->user_nonce,
        ));

        if( (false === $user) || !(is_object($user)) ){
            return false;
        }
        $user = $user->as_array();
        $user['nonce'] = unserialize($user['nonce']);

        if( $user['nonce']['n'] == $this->user_nonce ){
            $this->cached_is_real_admin = true;
        }else{
            $this->cached_is_real_admin = false;
        }
        return $this->cached_is_real_admin;
    }

    /**
     * Check if current logged user is just a staff
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function isStaff()
    {
        if($this->timber->config('_site_maintainance_mode') == 'on'){
            return false;
        }
        if($this->cached_is_staff !== ''){
            return $this->cached_is_staff;
        }
        if( !($this->timber->cookie->exist($this->timber->config('auth_staff'))) ){
            $this->cached_is_staff = false;
            return $this->cached_is_staff;
        }
        $encrypted_cookie_value = $this->timber->cookie->get(
            $this->timber->config('auth_staff'),
            false
        );

        if($encrypted_cookie_value == false){
            $this->cached_is_staff = false;
            return $this->cached_is_staff;
        }
        $decrypted_cookie_value = $this->timber->encrypter->decrypt(
            $encrypted_cookie_value,
            STAFF_AUTH_SALT
        );

        if( !(is_array($decrypted_cookie_value)) || !(count($decrypted_cookie_value) == 4) ){
            $this->cached_is_staff = false;
            return $this->cached_is_staff;
        }
        $nonce = (isset($decrypted_cookie_value['n'])) ? $decrypted_cookie_value['n'] : false;
        $hash = (isset($decrypted_cookie_value['h'])) ? $decrypted_cookie_value['h'] : false;
        $id = (isset($decrypted_cookie_value['u'])) ? $decrypted_cookie_value['u'] : false;
        $access = (isset($decrypted_cookie_value['a'])) ? $decrypted_cookie_value['a'] : false;

        if( ($nonce === false) || ($hash === false) || ($id === false) || ($access === false) ){
            $this->cached_is_staff = false;
            return $this->cached_is_staff;
        }

        $cleared_auth_data = $this->timber->validator->clear_values(array(
            'n' => array(
                'value' => $nonce,
                'sanit' => 'sstring',
                'valid' => 'vnotempty',
                'default' => false
                ),
            'h' => array(
                'value' => $hash,
                'sanit' => 'sstring',
                'valid' => 'vnotempty',
                'default' => false
                ),
            'u' => array(
                'value' => $id,
                'sanit' => 'snumberint',
                'valid' => 'vnotempty&vint',
                'default' => false
                ),
            'a' => array(
                'value' => $access,
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vequals:' . TIMBER_STAFF_IDEN,
                'default' => false
                )
        ));

        $this->cached_is_staff = (boolean) ( $cleared_auth_data['n']['status']
            && $cleared_auth_data['h']['status']
            && $cleared_auth_data['u']['status']
            && $cleared_auth_data['a']['status']
        );

        $this->user_nonce = ((boolean) $cleared_auth_data['n']['status']) ? $cleared_auth_data['n']['value'] : false;
        $this->user_hash = ((boolean) $cleared_auth_data['h']['status']) ? $cleared_auth_data['h']['value'] : false;
        $this->user_id = ((boolean) $cleared_auth_data['u']['status']) ? $cleared_auth_data['u']['value'] : false;
        $this->user_access = ((boolean) $cleared_auth_data['a']['status']) ? $cleared_auth_data['a']['value'] : false;

        return $this->cached_is_staff;
    }

    /**
     * Check if user is staff by comparing cookie data with db
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function isRealStaff()
    {
        if($this->timber->config('_site_maintainance_mode') == 'on'){
            return false;
        }
        if($this->cached_is_real_staff !== ''){
            return $this->cached_is_real_staff;
        }
        $user = $this->timber->custom_model->hasStaffAccessRule(array(
            'u' => $this->user_id,
            'h' => $this->user_hash,
            'n' => $this->user_nonce,
        ));
        if( (false === $user) || !(is_object($user)) ){
            return false;
        }
        $user = $user->as_array();
        $user['nonce'] = unserialize($user['nonce']);

        if( $user['nonce']['n'] == $this->user_nonce ){
            $this->cached_is_real_staff = true;
        }else{
            $this->cached_is_real_staff = false;
        }
        return $this->cached_is_real_staff;
    }

    /**
     * Check if current logged user is just a client
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function isClient()
    {
        if($this->timber->config('_site_maintainance_mode') == 'on'){
            return false;
        }
        if($this->cached_is_client !== ''){
            return $this->cached_is_client;
        }
        if( !($this->timber->cookie->exist($this->timber->config('auth_client'))) ){
            $this->cached_is_client = false;
            return $this->cached_is_client;
        }
        $encrypted_cookie_value = $this->timber->cookie->get(
            $this->timber->config('auth_client'),
            false
        );

        if($encrypted_cookie_value == false){
            $this->cached_is_client = false;
            return $this->cached_is_client;
        }
        $decrypted_cookie_value = $this->timber->encrypter->decrypt(
            $encrypted_cookie_value,
            CLIENTS_AUTH_SALT
        );

        if( !(is_array($decrypted_cookie_value)) || !(count($decrypted_cookie_value) == 4) ){
            $this->cached_is_client = false;
            return $this->cached_is_client;
        }
        $nonce = (isset($decrypted_cookie_value['n'])) ? $decrypted_cookie_value['n'] : false;
        $hash = (isset($decrypted_cookie_value['h'])) ? $decrypted_cookie_value['h'] : false;
        $id = (isset($decrypted_cookie_value['u'])) ? $decrypted_cookie_value['u'] : false;
        $access = (isset($decrypted_cookie_value['a'])) ? $decrypted_cookie_value['a'] : false;

        if( ($nonce === false) || ($hash === false) || ($id === false) || ($access === false) ){
            $this->cached_is_client = false;
            return $this->cached_is_client;
        }

        $cleared_auth_data = $this->timber->validator->clear_values(array(
            'n' => array(
                'value' => $nonce,
                'sanit' => 'sstring',
                'valid' => 'vnotempty',
                'default' => false
                ),
            'h' => array(
                'value' => $hash,
                'sanit' => 'sstring',
                'valid' => 'vnotempty',
                'default' => false
                ),
            'u' => array(
                'value' => $id,
                'sanit' => 'snumberint',
                'valid' => 'vnotempty&vint',
                'default' => false
                ),
            'a' => array(
                'value' => $access,
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vequals:' . TIMBER_CLIENT_IDEN,
                'default' => false
                )
        ));

        $this->cached_is_client = (boolean) ( $cleared_auth_data['n']['status']
            && $cleared_auth_data['h']['status']
            && $cleared_auth_data['u']['status']
            && $cleared_auth_data['a']['status']
        );

        $this->user_nonce = ((boolean) $cleared_auth_data['n']['status']) ? $cleared_auth_data['n']['value'] : false;
        $this->user_hash = ((boolean) $cleared_auth_data['h']['status']) ? $cleared_auth_data['h']['value'] : false;
        $this->user_id = ((boolean) $cleared_auth_data['u']['status']) ? $cleared_auth_data['u']['value'] : false;
        $this->user_access = ((boolean) $cleared_auth_data['a']['status']) ? $cleared_auth_data['a']['value'] : false;

        return $this->cached_is_client;
    }

    /**
     * Check if user is client by comparing cookie data with db
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function isRealClient()
    {
        if($this->timber->config('_site_maintainance_mode') == 'on'){
            return false;
        }
        if($this->cached_is_real_client !== ''){
            return $this->cached_is_real_client;
        }
        $user = $this->timber->custom_model->hasClientAccessRule(array(
            'u' => $this->user_id,
            'h' => $this->user_hash,
            'n' => $this->user_nonce,
        ));
        if( (false === $user) || !(is_object($user)) ){
            return false;
        }
        $user = $user->as_array();
        $user['nonce'] = unserialize($user['nonce']);

        if( $user['nonce']['n'] == $this->user_nonce ){
            $this->cached_is_real_client = true;
        }else{
            $this->cached_is_real_client = false;
        }
        return $this->cached_is_real_client;
    }

    /**
     * Check Access Rule
     *
     * <code>
     *  # Check Access Permission
     *  if( !($this->timber->security->canAccess( array('client', 'staff', 'admin', 'real_client', 'real_staff', 'real_admin') )) ){
     *      $this->timber->redirect( $this->timber->config('request_url') . '/404' );
     *  }
     * </code>
     *
     * @since 1.0
     * @access public
     * @param array $rules
     * @return boolean
     */
    public function canAccess($rules)
    {
        $can_access = false;
        foreach ($rules as $rule ) {
            switch ($rule) {
                case 'client':
                    $can_access |= (boolean) ( $this->timber->security->isAuth() && $this->timber->security->isClient() );
                    break;
                case 'staff':
                    $can_access |= (boolean) ( $this->timber->security->isAuth() && $this->timber->security->isStaff() );
                    break;
                case 'admin':
                    $can_access |= (boolean) ( $this->timber->security->isAuth() && $this->timber->security->isAdmin() );
                    break;
                case 'real_client':
                    $can_access |= (boolean) ( $this->timber->security->isAuth() && $this->timber->security->isRealClient() );
                    break;
                case 'real_staff':
                    $can_access |= (boolean) ( $this->timber->security->isAuth() && $this->timber->security->isRealStaff() );
                    break;
                case 'real_admin':
                    $can_access |= (boolean) ( $this->timber->security->isAuth() && $this->timber->security->isRealAdmin() );
                    break;
            }
        }
        return (boolean) $can_access;
    }

    /**
     * Get user nonce from user session
     *
     * Appended in URLs and Forms
     *
     * @since 1.0
     * @access public
     * @return string
     */
    public function getNonce()
    {
        return $this->user_nonce;
    }

    /**
     * Get user hash from user session
     *
     * @since 1.0
     * @access public
     * @return string
     */
    public function getHash()
    {
        return $this->user_hash;
    }

    /**
     * Get user id from user session
     *
     * @since 1.0
     * @access public
     * @return integer
     */
    public function getId()
    {
        return (integer) ($this->user_id);
    }

    /**
     * Check if current visitor has cookies enabled
     *
     * @since 1.0
     * @access public
     */
    public function cookieCheck()
    {
        if( !($this->timber->cookie->exist($this->timber->config('cookie_check'))) ){
            $this->timber->cookie->set($this->timber->config('cookie_check'), 'timber', 360);
            return false;
        }
        return true;
    }

    /**
     * Check for hash in $_POST then $_GET and validate
     *
     * This should run on forms submit and url that perform sensitive action
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function checkVerifyHash()
    {
        $hash = $this->timber->request->params('user_hash', false);
        return (boolean)( ($hash !== false) && ($hash == $this->user_hash) );
    }

    /**
     * Check for nonce in $_POST then $_GET and validate
     *
     * This should run on forms submit and url that perform sensitive action
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function checkVerifyNonce()
    {
        $nonce = $this->timber->request->params('user_nonce', false);
        return (boolean)( ($nonce !== false) && ($nonce == $this->user_nonce) );
    }

    /**
     * Append return route to login url
     *
     * This method used when non auth user try to access restricted area
     * so app will redirect them to login page with get var to return them back to restricted area
     *
     * @since 1.0
     * @access public
     * @param string $route
     * @return boolean
     */
    public function sendToLoginWithRedirect($route)
    {
        $this->timber->redirect( $this->timber->config('request_url') . '/login?redirect=' . urldecode($route) );
    }

    /**
     * Decode redirect route in url if exist and send to
     * or send to default route (eg./admin)
     *
     * @since 1.0
     * @access public
     * @param string $default
     */
    public function goToReturnURLOrDefault($default)
    {
        $redirect = (isset($_GET['redirect'])) ? urldecode($_GET['redirect']) : $default;
        $this->timber->redirect( $this->timber->config('request_url') . $redirect );
    }

      /**
       * Get OS, Browser of client and IP
       *
       * it is a simple detection and not very accurate but still it can achieve our purpose.
       *
       * @since 1.0
       * @access public
       * @return array
       */
      public function clientMachine()
      {
            $user_agent = $this->timber->request->getUserAgent();

            if ( (stripos($user_agent, 'opera mini') !== false) || (stripos($user_agent, 'opera') !== false) || (stripos($user_agent, 'OPR') !== false) ) {
                  $client_machine[ 'br' ] = 'o';
            } elseif ( (stripos($user_agent, 'Chrome') === false) && (stripos($user_agent, 'Safari') !== false) && (stripos($user_agent, 'iPhone') === false) && (stripos($user_agent, 'iPod') === false) ) {
                  $client_machine[ 'br' ] = 's';
            } elseif ( stripos($user_agent, 'Chrome') !== false ) {
                  $client_machine[ 'br' ] = 'c';
            } elseif ( (stripos($user_agent, 'safari') === false) && (stripos($user_agent, 'Firefox') !== false) ) {
                  $client_machine[ 'br' ] = 'f';
            } elseif ( (stripos($user_agent,'Trident/7.0; rv:11.0') !== false) || (stripos($user_agent, 'microsoft internet explorer') !== false) || ( (stripos($user_agent, 'msie') !== false) && (stripos($user_agent, 'opera') === false) ) || ( (stripos($user_agent, 'mspie') !== false) || (stripos($user_agent, 'pocket') !== false) ) ) {
                  $client_machine[ 'br' ] = 'i';
            } else {
                  $client_machine[ 'br' ] = 'ot';
            }

            if ( (strpos($user_agent, 'Mac') !== false) && (strpos($user_agent, 'iPhone') === false) && (strpos($user_agent, 'iPod') === false) && (strpos($user_agent, 'iPad') === false)) {
                  $client_machine[ 'os' ] = 'm';
            } elseif ( strpos($user_agent, 'Win') !== false ) {
                  $client_machine[ 'os' ] = 'w';
            } elseif ( (strpos($user_agent, 'linux') !== false) || (strpos($user_agent, 'buntu') !== false) ) {
                  $client_machine[ 'os' ] = 'l';
            } else {
                  $client_machine[ 'os' ] = 'ot';
            }

            $client_machine[ 'ip' ] = (filter_var(trim($this->timber->request->getIp()), FILTER_VALIDATE_IP)) ? trim($this->timber->request->getIp()) : '';

            return $client_machine;
      }

      /**
       * Delete user or admin session if app suspect
       *
       * @since 1.0
       * @access public
       */
      public function endSession()
      {
        if( $this->timber->cookie->exist( $this->timber->config('auth_staff')) ){
            $this->timber->cookie->delete( $this->timber->config('auth_staff'), true);
        }
        if( $this->timber->cookie->exist($this->timber->config('auth_admin')) ){
            $this->timber->cookie->delete( $this->timber->config('auth_admin'), true);
        }
        if( $this->timber->cookie->exist($this->timber->config('auth_client')) ){
            $this->timber->cookie->delete( $this->timber->config('auth_client'), true);
        }
      }
}