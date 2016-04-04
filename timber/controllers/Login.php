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

namespace Timber\Controllers;

/**
 * Login Controller
 *
 * Identifier may cause issue as google return extremely long integers
 *
 * @since 1.0
 */
class Login {

    /**
     * Data to be used in template
     *
     * @since 1.0
     * @access private
     * @var array $this->data
     */
    private $data = array();

    /**
     * Nonce update interval in seconds
     *
     * Updated Each Week
     *
     * @since 1.0
     * @access private
     * @var integer $this->nonce_interval
     */
    private $nonce_interval = 302400;

    /**
     * Default response
     *
     * @since 1.0
     * @access private
     * @var array $this->response
     */
    private $response = array(
        'status' => 'error',
        'data' => ''
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
        $this->timber->filter->setDepen($timber)->config();
        return $this;
    }

    /**
     * Run common tasks before rendering
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function renderFilters()
    {
        $this->timber->filter->issueDetect();
        $this->timber->filter->configLibs();
        $this->timber->security->cookieCheck();

        if( ($this->timber->security->canAccess( array('client', 'staff', 'admin') )) ){
            $this->timber->security->goToReturnURLOrDefault('/admin/dashboard');
        }

        return true;
    }

    /**
     *  Requests Filters
     *
     * @since 1.0
     * @access public
     */
    public function requestFilters()
    {
        if( !($this->timber->request->isAjax()) ){
            $this->timber->redirect( $this->timber->config('request_url') . '/404' );
        }
        $this->timber->filter->issueDetect();
        $this->timber->filter->configLibs();
    }

    /**
     * Render login page
     *
     * @since 1.0
     * @access public
     * @param integer $error
     */
    public function render($error = '')
    {
        return $this->timber->render( 'login', $this->getData($error) );
    }

    /**
     * Bind and get data
     *
     * @since 1.0
     * @access private
     * @param integer $error
     * @return array
     */
    private function getData($error = '')
    {
        $this->data['error'] = $this->requestErrors($error);
        $this->data['alerts'] = $this->requestAlerts($error);

        $this->data['footer_scripts']  = "jQuery(document).ready(function($){";
        $this->data['footer_scripts'] .= "timber.utils.init();";
        $this->data['footer_scripts'] .= "timber.login.init();";
        $this->data['footer_scripts'] .= "});";

        $this->bindSubPage( $this->timber->translator->trans('Login') . " | " );
        return $this->data;
    }

    /**
     * Process Requests
     *
     * @since 1.0
     * @access public
     * @return string
     */
    public function requests()
    {
        $this->checkRequest();
        $this->getResponse();
    }

    /**
     * Check Requests
     *
     * @since 1.0
     * @access private
     * @return boolean
     */
    private function checkRequest()
    {
        if( !($this->timber->security->cookieCheck()) ){
            $this->response['data'] = $this->timber->translator->trans('App requires cookies to be enabled in your browser.');
            return false;
        }

        $login_data = $this->timber->validator->clear(array(
            'login_username' => array(
                'req' => 'post',
                'sanit' => ( ( (isset($_POST['login_username'])) && (strrpos($_POST['login_username'], '@')) ) ? 'semail': 'sstring'),
                'valid' => ( ( (isset($_POST['login_username'])) && (strrpos($_POST['login_username'], '@')) ) ? 'vnotempty&vemail': 'vnotempty&vstrlenbetween:5,20&vusername'),
                'default' => '',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('Username or password is invalid.'),
                    'vemail' => $this->timber->translator->trans('Username or password is invalid.'),
                    'vusername' => $this->timber->translator->trans('Username or password is invalid.'),
                )
            ),
            'login_password' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:8,20&vpassword',
                'default' => '',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('Username or password is invalid.'),
                    'vstrlenbetween' => $this->timber->translator->trans('Username or password is invalid.'),
                    'vpassword' => $this->timber->translator->trans('Username or password is invalid.'),
                )
            ),
            'login_remember_me' => array(
                'req'=> 'post',
                'valid'=> 'vcheckbox',
                'default'=> ''
            ),
        ));

        if(true === $login_data['error_status']){
            $this->response['data'] = $login_data['error_text'];
            return false;
        }

        $login_data = array(
            'withemail' => ((boolean) strrpos($login_data['login_username']['value'], '@')),
            'login_username' => $login_data['login_username']['value'],
            'login_password' => $login_data['login_password']['value'],
            'login_remember_me' => ((true === $login_data['login_remember_me']['status']) ? 360 : null),
        );

        if( $this->nativeAuth($login_data) ){
            $this->response['status'] = 'success';
            $this->response['data'] = $this->timber->translator->trans('You logged in successfully.');
            return true;
        }else{
            return false;
        }
        return false;
    }

    /**
     * Native check and auth
     *
     * @since 1.0
     * @access private
     * @param array $login_data
     * @return boolean
     */
    private function nativeAuth($login_data)
    {
        //well get user with this iden
        $user_data = ($login_data['withemail']) ? $this->timber->custom_model->getUserWithEmail($login_data['login_username']) : $this->timber->custom_model->getUserWithUsername($login_data['login_username']);
        if( (false === $user_data) || !(is_object($user_data)) ){
            $this->response['data'] = $this->timber->translator->trans('Username or password is invalid.');
            return false;
        }

        $user_data = $user_data->as_array();
        $user_data['user_nonce'] = unserialize($user_data['user_nonce']);

        if( !($this->timber->hasher->CheckPassword($login_data['login_password'], $user_data['user_password'])) ){
            $this->response['data'] = $this->timber->translator->trans('Username or password is invalid.');
            return false;
        }

        if( $user_data['user_status'] == '3' ){
            $this->response['data'] = $this->timber->translator->trans('We apologize, your account is disabled.');
            return false;
        }

        if( ($this->timber->config('_site_maintainance_mode') == 'on') && (($user_data['user_access_rule'] == '2') || ($user_data['user_access_rule'] == '3')) ){
            $this->response['data'] = $this->timber->translator->trans('We apologize, site is under maintainance. Try again later.');
            return false;
        }

        if( (time() - $this->timber->time->dateToTimestamp($user_data['user_nonce']['t'], true)) >= ($this->nonce_interval) ){
            $user_data['user_nonce']['n'] =  $this->timber->faker->randHash(10);
            $this->timber->user_meta_model->updateMetaById(array(
                'me_id' => $user_data['meta_id'],
                'me_value' => serialize(array(
                    'n' => $user_data['user_nonce']['n'],
                    't' => $this->timber->time->getCurrentDate(true),
                ))
            ));

        }

        if($user_data['user_access_rule'] == '1'){

            $this->timber->security->authAdmin(array(
                'user_nonce' => $user_data['user_nonce']['n'],
                'user_hash' => $user_data['user_hash'],
                'user_id' => $user_data['user_id'],
            ), $login_data['login_remember_me']);

        }elseif($user_data['user_access_rule'] == '2'){

            $this->timber->security->authStaff(array(
                'user_nonce' => $user_data['user_nonce']['n'],
                'user_hash' => $user_data['user_hash'],
                'user_id' => $user_data['user_id'],
            ), $login_data['login_remember_me']);

        }elseif($user_data['user_access_rule'] == '3'){

            $this->timber->security->authClient(array(
                'user_nonce' => $user_data['user_nonce']['n'],
                'user_hash' => $user_data['user_hash'],
                'user_id' => $user_data['user_id'],
            ), $login_data['login_remember_me']);
        }
        return true;
    }

    /**
     * Auth user with provider (google or twitter or facebook)
     *
     * @since 1.0
     * @access public
     * @param string $provider
     */
    public function socialAuth($provider)
    {
        if( ('facebook' == $provider) && ($this->timber->config('_facebook_login_status') == 'on') ){
            $this->authWithFacebook();
        }

        if( ('twitter' == $provider) && ($this->timber->config('_twitter_login_status') == 'on') ){
            $this->authWithTwitter();
        }

        if( ('google' == $provider) && ($this->timber->config('_google_login_status') == 'on') ){
            $this->authWithGoogle();
        }

        if( ('endpoint' == $provider) ){
            $this->endPoint();
        }
        $this->timber->redirect( $this->timber->config('request_url') . '/404' );
    }

    /**
     * Social auth filter
     *
     * @since 1.0
     * @access public
     */
    public function socialAuthFilter()
    {
        $this->timber->filter->issueDetect();
        $this->timber->filter->configLibs();

        if( !($this->timber->security->cookieCheck()) ){
            $this->timber->redirect( $this->timber->config('request_url') . '/login/1' );
        }

        if( ($this->timber->security->canAccess( array('client', 'staff', 'admin') )) ){
            //send to dashboard
            $this->timber->security->goToReturnURLOrDefault('/admin/dashboard');
        }
        return true;
    }

    /**
     * Execute hybridauth endpoint
     *
     * This method will have its own route to execute
     *
     * @since 1.0
     * @access public
     */
    public function endPoint()
    {
        session_start();
        \Hybrid_Endpoint::process();
    }

    /**
     * Auth with facebook
     *
     * @since 1.0
     * @access public
     */
    private function authWithFacebook()
    {
        session_start();
        try{
            $hybridAuth = @new \Hybrid_Auth(array(
                "base_url" => $this->timber->config('request_url') . '/request/frontend/direct/auth/endpoint',
                "debug_mode" => TIMBER_DEBUG_MODE,
                "debug_file" => TIMBER_ROOT . TIMBER_LOGS_DIR . "/social_auth.txt",
                "providers" => array(
                    "Facebook" => array(
                        "enabled" => true,
                        "keys" => array("id" => $this->timber->config('_facebook_login_app_id'), "secret" => $this->timber->config('_facebook_login_app_secret')),
                        "trustForwarded" => false
                    )
                )
            ));

            $facebook = $hybridAuth->authenticate("Facebook");
            $is_user_logged_in = $facebook->isUserConnected();
            if( $is_user_logged_in ){
                $user_profile = $facebook->getUserProfile();
                $cleared_data = $this->clearProviderData($user_profile);
                $this->authWithProviderData($cleared_data, '3');
            }else{
                $this->timber->redirect( $this->timber->config('request_url') . '/login/2' );
            }
            $facebook->logout();
        }catch( Exception $e ){
            $this->timber->redirect( $this->timber->config('request_url') . '/login/2' );
        }
    }

    /**
     * Auth with twitter
     *
     * @since 1.0
     * @access public
     */
    private function authWithTwitter()
    {
        session_start();
        try{
            $hybridAuth = @new \Hybrid_Auth(array(
                "base_url" => $this->timber->config('request_url') . '/request/frontend/direct/auth/endpoint',
                "debug_mode" => TIMBER_DEBUG_MODE,
                "debug_file" => TIMBER_ROOT . TIMBER_LOGS_DIR . "/social_auth.txt",
                "providers" => array(
                    "Twitter" => array(
                    "enabled" => true,
                        "keys" => array("key" => $this->timber->config('_twitter_login_app_key'), "secret" => $this->timber->config('_twitter_login_app_secret'))
                    )
                )
            ));

            $twitter = $hybridAuth->authenticate("Twitter");
            $is_user_logged_in = $twitter->isUserConnected();
            if( $is_user_logged_in ){
                $user_profile = $twitter->getUserProfile();
                $cleared_data = $this->clearProviderData($user_profile);
                $this->authWithProviderData($cleared_data, '2');
            }else{
                $this->timber->redirect( $this->timber->config('request_url') . '/login/2' );
            }
            $twitter->logout();
        }catch( Exception $e ){
            $this->timber->redirect( $this->timber->config('request_url') . '/login/2' );
        }
    }

    /**
     * Auth with google
     *
     * @since 1.0
     * @access public
     */
    private function authWithGoogle()
    {
        session_start();
        try{
            $hybridAuth = @new \Hybrid_Auth(array(
                "base_url" => $this->timber->config('request_url') . '/request/frontend/direct/auth/endpoint',
                "debug_mode" => TIMBER_DEBUG_MODE,
                "debug_file" => TIMBER_ROOT . TIMBER_LOGS_DIR . "/social_auth.txt",
                "providers" => array(
                    "Google" => array(
                        "enabled" => true,
                        "keys" => array("id" => $this->timber->config('_google_login_app_key'), "secret" => $this->timber->config('_google_login_app_secret'))
                    )
                )
            ));

            $google = $hybridAuth->authenticate("Google");
            $is_user_logged_in = $google->isUserConnected();
            if( $is_user_logged_in ){
                $user_profile = $google->getUserProfile();
                $cleared_data = $this->clearProviderData($user_profile);
                $this->authWithProviderData($cleared_data, '4');
            }else{
                $this->timber->redirect( $this->timber->config('request_url') . '/login/2' );
            }
            $google->logout();
        }catch( Exception $e ){
            $this->timber->redirect( $this->timber->config('request_url') . '/login/2' );
        }
    }

    /**
     * Get response
     *
     * @since 1.0
     * @access private
     * @return string
     */
    private function getResponse()
    {
        header('Content: application/json');
        echo json_encode($this->response);
        die();
    }

    /**
     * Clear user profile data that come from provider
     * and return the needed data only
     *
     * identifier
     * photoURL
     * displayName
     * firstName
     * lastName
     * email
     * emailVerified
     *
     * @since 1.0
     * @access private
     * @param array $user_profile
     * @return array
     */
    private function clearProviderData($user_profile)
    {
        $identifier = ( isset($user_profile->identifier) ) ? $user_profile->identifier : '';
        $photoURL = ( isset($user_profile->photoURL) ) ? $user_profile->photoURL : '';
        $displayName = ( isset($user_profile->displayName) ) ? $user_profile->displayName : '';
        $firstName = ( isset($user_profile->firstName) ) ? $user_profile->firstName : '';
        $lastName = ( isset($user_profile->lastName) ) ? $user_profile->lastName : '';
        $email = ( isset($user_profile->email) ) ? $user_profile->email : '';
        $emailVerified = ( isset($user_profile->emailVerified) ) ? $user_profile->emailVerified : '';

        $cleared_auth_data = $this->timber->validator->clear_values(array(
            'identifier' => array(
                'value' => $identifier,
                'sanit' => 'sstring',
                'valid' => 'vnotempty',
                'default' => ''
            ),
            'photoURL' => array(
                'value' => $photoURL,
                'sanit' => 'sstring',
                'valid' => 'vnotempty',
                'default' => ''
            ),
            'displayName' => array(
                'value' => $displayName,
                'sanit' => 'sstring',
                'valid' => 'vnotempty',
                'default' => ''
            ),
            'firstName' => array(
                'value' => $firstName,
                'sanit' => 'sstring',
                'valid' => 'vnotempty',
                'default' => ''
            ),
            'lastName' => array(
                'value' => $lastName,
                'sanit' => 'sstring',
                'valid' => 'vnotempty',
                'default' => ''
            ),
            'email' => array(
                'value' => $email,
                'sanit' => 'semail',
                'valid' => 'vnotempty&vemail',
                'default' => ''
            ),
            'emailVerified' => array(
                'value' => $emailVerified,
                'sanit' => 'semail',
                'valid' => 'vnotempty&vemail',
                'default' => ''
            )
        ));

        $identifier = ((boolean) $cleared_auth_data['identifier']['status']) ? $cleared_auth_data['identifier']['value'] : '';
        $photoURL = ((boolean) $cleared_auth_data['photoURL']['status']) ? $cleared_auth_data['photoURL']['value'] : '';
        $displayName = ((boolean) $cleared_auth_data['displayName']['status']) ? $cleared_auth_data['displayName']['value'] : '';
        $firstName = ((boolean) $cleared_auth_data['firstName']['status']) ? $cleared_auth_data['firstName']['value'] : '';
        $lastName = ((boolean) $cleared_auth_data['lastName']['status']) ? $cleared_auth_data['lastName']['value'] : '';
        $email = ((boolean) $cleared_auth_data['email']['status']) ? $cleared_auth_data['email']['value'] : '';
        $emailVerified = ((boolean) $cleared_auth_data['emailVerified']['status']) ? $cleared_auth_data['emailVerified']['value'] : '';

        //if identifier is empty this will be a crap
        if( empty($identifier) ){
            $this->timber->redirect( $this->timber->config('request_url') . '/login/2' );
        }

        $verified_email = ( !(empty($emailVerified)) ) ? $emailVerified : $email;

        $user_auth_data = array(
            'identifier' => $identifier,
            'email' => $verified_email,
            'first_name' => trim($firstName),
            'last_name' => trim($lastName),
            'user_name' => trim($displayName),
        );
        return $user_auth_data;
    }

    /**
     * Check user if exist or not with cleared provider data
     *
     * @since 1.0
     * @access private
     * @param array $user_auth_data
     * @param 1|2|3 $provider
     */
    private function authWithProviderData($user_auth_data, $provider)
    {
        //well get user with this iden
        $user_data = $this->timber->custom_model->getUserWithIden($user_auth_data['identifier'], $provider);

        if( (false === $user_data) || !(is_object($user_data)) ){
            $this->updateUserAndAuth($user_auth_data, $provider);
        }else{
            $user_data = $user_data->as_array();
            $this->authUser($user_data);
        }
    }

    /**
     * Insert new user and auth
     *
     * redirect to login if site under maintainance or site don't allow new
     * members
     *
     * @since 1.0
     * @access private
     * @param array $user_data
     * @param 1|2|3 $provider
     */
    private function updateUserAndAuth($user_data, $provider)
    {
        # Check hash and get user data
        $hash = $this->timber->cookie->get('_user_register_hash', '');

        if( empty($hash) ){
            $this->timber->redirect( $this->timber->config('request_url') . '/login/3' );
        }

        $user_meta = $this->timber->user_meta_model->getMetaByMultiple(array(
            'me_key' => '_user_register_hash',
            'me_value' => $hash
        ));

        if( (false === $user_meta) || !(is_object($user_meta)) ){
            $this->timber->redirect( $this->timber->config('request_url') . '/login/3' );
        }

        $user_meta = $user_meta->as_array();
        $user_profile_data = $this->timber->user_model->getUserById($user_meta['us_id']);
        if( (false === $user_profile_data) || !(is_object($user_profile_data)) ){
            $this->timber->redirect( $this->timber->config('request_url') . '/login/3' );
        }
        $user_profile_data = $user_profile_data->as_array();

        if( ($this->timber->config('_site_maintainance_mode') == 'on') && (($user_profile_data['access_rule'] == '2') || ($user_profile_data['access_rule'] == '3')) ){
            //send to login page
            $this->timber->redirect( $this->timber->config('request_url') . '/login/4' );
        }

        $nonce_data = $this->timber->user_meta_model->getMetaByMultiple(array(
            'me_key' => '_user_access_nonce',
            'us_id' => $user_profile_data['us_id']
        ));

        $nonce_data = $nonce_data->as_array();
        $nonce_data['me_value'] = unserialize($nonce_data['me_value']);

        $status = (boolean) $this->timber->user_model->updateUserById(array(
            'us_id' => $user_profile_data['us_id'],
            'password' => '',
            'updated_at' => $this->timber->time->getCurrentDate(true),
            'status' => '1',
            'auth_by' => $provider,
            'identifier' => $user_data['identifier'],
        ));

        # Dump Meta
        $status &= (boolean) $this->timber->user_meta_model->deleteMetaById($user_meta['me_id']);

        if($user_profile_data['access_rule'] == '1'){

            $this->timber->security->authAdmin(array(
                'user_nonce' => $nonce_data['me_value']['n'],
                'user_hash' => $user_profile_data['sec_hash'],
                'user_id' => $user_profile_data['us_id'],
            ));

        }elseif($user_profile_data['access_rule'] == '2'){

            $this->timber->security->authStaff(array(
                'user_nonce' => $nonce_data['me_value']['n'],
                'user_hash' => $user_profile_data['sec_hash'],
                'user_id' => $user_profile_data['us_id'],
            ));

        }elseif($user_profile_data['access_rule'] == '3'){

            $this->timber->security->authClient(array(
                'user_nonce' => $nonce_data['me_value']['n'],
                'user_hash' => $user_profile_data['sec_hash'],
                'user_id' => $user_profile_data['us_id'],
            ));
        }

        $this->timber->security->goToReturnURLOrDefault('/admin/dashboard');
    }

    /**
     * Auth user (already registered user)
     *
     * Redirect to login if logged in account is user and inactive
     * or user and site under maintainance
     *
     * @since 1.0
     * @access private
     * @param array $user_data
     */
    private function authUser($user_data)
    {
        if( ($this->timber->config('_site_maintainance_mode') == 'on') && (($user_data['user_access_rule'] == '2') || ($user_data['user_access_rule'] == '3')) ){
            //send to login page
            $this->timber->redirect( $this->timber->config('request_url') . '/login/4' );
        }
        $user_data['user_nonce'] = unserialize($user_data['user_nonce']);

        //check if account inactive
        if( $user_data['user_status'] == '3' ){
            //redirect to login with error saying account inactive
            $this->timber->redirect( $this->timber->config('request_url') . '/login/5' );
        }

        if( (time() - $this->timber->time->dateToTimestamp($user_data['user_nonce']['t'], true)) > ($this->nonce_interval) ){
            $user_data['user_nonce']['n'] =  $this->timber->faker->randHash(10);
            $this->timber->user_meta_model->updateMetaById(array(
                'me_id' =>  $user_data['meta_id'],
                'me_value' => serialize(array(
                    'n' => $user_data['user_nonce']['n'],
                    't' => $this->timber->time->getCurrentDate(true),
                ))
            ));

        }

        if($user_data['user_access_rule'] == '1'){

            $this->timber->security->authAdmin(array(
                'user_nonce' => $user_data['user_nonce']['n'],
                'user_hash' => $user_data['user_hash'],
                'user_id' => $user_data['user_id'],
            ));

        }elseif($user_data['user_access_rule'] == '2'){

            $this->timber->security->authStaff(array(
                'user_nonce' => $user_data['user_nonce']['n'],
                'user_hash' => $user_data['user_hash'],
                'user_id' => $user_data['user_id'],
            ));

        }elseif($user_data['user_access_rule'] == '3'){

            $this->timber->security->authClient(array(
                'user_nonce' => $user_data['user_nonce']['n'],
                'user_hash' => $user_data['user_hash'],
                'user_id' => $user_data['user_id'],
            ));
        }

        $this->timber->security->goToReturnURLOrDefault('/admin/dashboard');
    }

    /**
     * Social Auth Detected Errors
     *
     * Social auth is evil if you care
     *
     * @since 1.0
     * @access private
     * @param string $error
     * @return string
     */
    private function requestErrors($error)
    {
        switch ($error) {
            case 1:
                $message = $this->timber->translator->trans('App requires cookies to be enabled in your browser.');
                break;
            case 2:
                $message = $this->timber->translator->trans('Invalid response, please try again later.');
                break;
            case 3:
                $message = $this->timber->translator->trans('You need to be invited to log in.');
                break;
            case 4:
                $message = $this->timber->translator->trans('We apologize, site is under maintainance. Try again later.');
                break;
            case 5:
                $message = $this->timber->translator->trans('We apologize, your account is disabled.');
                break;
            case 7:
                $message = $this->timber->translator->trans('It seems you visit old URL that become invalid.');
                break;
            default:
                $message = '';
                break;
        }
        return $message;
    }

    /**
     * Social Auth Detected Alerts
     *
     * Social auth is evil if you care
     *
     * @since 1.0
     * @access private
     * @param string $error
     * @return string
     */
    private function requestAlerts($error)
    {
        switch ($error) {
            case 6:
                $message = $this->timber->translator->trans('Email verified successfully.');
                break;
            default:
                $message = '';
                break;
        }
        return $message;
    }

    /**
     * Bind sub page
     *
     * @since 1.0
     * @access private
     * @param string $sub_page
     */
    private function bindSubPage($sub_page)
    {
        $this->data['site_sub_page'] = $sub_page;
    }
}