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
 * Helpers Controller
 *
 * @since 1.0
 */
class Helpers {

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
     * @param object $timber
     * @return object
     */
    public function setDepen($timber)
    {
        $this->timber = $timber;
        $this->timber->filter->setDepen($timber)->config();
        $this->services = new \Timber\Services\Base($this->timber);
        return $this;
    }

    /**
     * Helpers filter
     *
     * @since 1.0
     * @access public
     */
    public function filters()
    {
        $this->services->Common->ajaxCheck();
        $this->timber->filter->issueDetect();
        $this->timber->filter->configLibs();
    }

    /**
     * Render helper
     *
     * @since 1.0
     * @access public
     * @param string $helper
     * @return string
     */
    public function render($helper = '')
    {
        if( in_array($helper, array('alerts')) ){
            return $this->alerts();
        }

        //no helper found end
        $this->timber->redirect( $this->timber->config('request_url') . '/404' );
    }

    /**
     * Render alerts
     *
     * @since 1.0
     * @access public
     * @return string
     */
    private function alerts()
    {
        $response = array(
            'status' => 'error',
            'data' => '',
            'type' => 'error'
        );

        if( ($this->timber->security->isAuth()) && ( ($this->timber->security->isStaff()) || ($this->timber->security->isAdmin()) || ($this->timber->security->isClient()) ) ){
            $user_id = $this->timber->security->getId();
            $response = ( $this->timber->security->isAdmin() ) ? $this->adminAlerts($user_id) : $this->userAlerts($user_id);
        }

        header('Content: application/json');
        echo json_encode($response);
        die();
    }

    /**
     * Get users alerts
     *
     * # NEED IMPROVEMENTS
     *
     * @since 1.0
     * @return string
     */
    private function userAlerts($user_id)
    {
        $response = array(
            'status' => 'error',
            'data' => '',
            'type' => 'error'
        );

        $user_data = $this->timber->user_model->getUserById($user_id);
        if( (false === $user_data) || !(is_object($user_data)) ){
            return $response;
        }
        $user_data = $user_data->as_array();
        if( $user_data['status'] == 2 ){
            $response['type'] = "warning";
            $response['status'] = "success";
            $response['data'] = $this->timber->translator->trans("Your Email is Pending Approval. Please Visit Your Profile to Send Approval Message.");
            return $response;
        }
        return $response;
    }

    /**
     * Get admins alerts
     *
     * # NEED IMPROVEMENTS
     *
     * @since 1.0
     * @return string
     */
    private function adminAlerts($user_id)
    {
        $response = array(
            'status' => 'error',
            'data' => '',
            'type' => 'error'
        );

        if( TIMBER_INSTALLED === false ){
            $response['type'] = "error";
            $response['status'] = "success";
            $response['data'] = $this->timber->translator->trans("Timber Installation Page should be Blocked for Further Security. Read System Alerts section in Documentation.");
            return $response;
        }

        //check if app need to be updated
        if( $this->timber->remote->needUpdate() ){
            $response['type'] = "error";
            $response['status'] = "success";
            $response['data'] = $this->timber->translator->trans("Timber Need to be Updated. Read System Alerts section in Documentation.");
            return $response;
        }

        return $response;
    }
}