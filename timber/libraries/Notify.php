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
 * Process Realtime Notifications
 *
 * <code>
 *  $this->timber->notify->push($item, $value, $user_id = false);
 *  $this->timber->notify->fetch($item, $user_id = false);
 *  $this->timber->notify->expire($item, $user_id = false);
 *
 *  # Items List
 *  $this->timber->notify->increment('messages_notif', $user_id);
 *  $this->timber->notify->increment('messages_index_notif', $user_id);
 *  $this->timber->notify->increment('messages_favorite_notif', $user_id);
 *  $this->timber->notify->increment('messages_sent_notif', $user_id);
 *
 *  $this->timber->notify->increment('dashboard_notif', $user_id);
 *  $this->timber->notify->increment('projects_notif', $user_id);
 *  $this->timber->notify->increment('members_notif', $user_id);
 *  $this->timber->notify->increment('invoices_notif', $user_id);
 *  $this->timber->notify->increment('estimates_notif', $user_id);
 *  $this->timber->notify->increment('expenses_notif', $user_id);
 *  $this->timber->notify->increment('items_notif', $user_id);
 *  $this->timber->notify->increment('quotations_notif', $user_id);
 *  $this->timber->notify->increment('subscriptions_notif', $user_id);
 *  $this->timber->notify->increment('calendar_notif', $user_id);
 *
 *  $this->timber->notify->setMailerCron(array(
 *      'method_name' => 'newProjectEmailNotifier',
 *      'project_id' => Int,
 *  ));
 *
 *  $this->timber->notify->setMailerCron(array(
 *      'method_name' => 'projectTaskEmailNotifier',
 *      'project_id' => Int,
 *      'task_id' => Int,
 *  ));
 *
 *  $this->timber->notify->setMailerCron(array(
 *      'method_name' => 'projectMilestoneEmailNotifier',
 *      'project_id' => Int,
 *      'miles_id' => Int,
 *  ));
 *
 *  $this->timber->notify->setMailerCron(array(
 *      'method_name' => 'projectTicketEmailNotifier',
 *      'project_id' => Int,
 *      'ticket_id' => Int,
 *  ));
 *
 *  $this->timber->notify->setMailerCron(array(
 *      'method_name' => 'projectFileEmailNotifier',
 *      'project_id' => Int,
 *      'file_id' => Int,
 *  ));
 *
 *  $this->timber->notify->setMailerCron(array(
 *      'method_name' => 'messageEmailNotifier',
 *      'to_user_id' => Int,
 *      'from_user_id' => Int,
 *  ));
 *
 *  $this->timber->notify->setMailerCron(array(
 *      'method_name' => 'newQuotationEmailNotifier',
 *      'qu_id' => Int,
 *      'user_id' => Int,
 *  ));
 *
 *  $this->timber->notify->setMailerCron(array(
 *      'method_name' => 'newPublicQuotationEmailNotifier',
 *      'qu_id' => Int,
 *      'email' => String,
 *  ));
 *
 *  $this->timber->notify->setMailerCron(array(
 *      'method_name' => 'newSubscriptionEmailNotifier',
 *      'sub_id' => Int,
 *  ));
 *
 *  $this->timber->notify->setMailerCron(array(
 *      'method_name' => 'newInvoiceEmailNotifier',
 *      'inv_id' => Int,
 *  ));
 *
 *  $this->timber->notify->setMailerCron(array(
 *      'method_name' => 'newEstimateEmailNotifier',
 *      'est_id' => Int,
 *  ));
 *
 *  # Mailer Crons List
 *  $this->timber->notify->fireMailerCrons();
 *  $this->timber->notify->setMailerCron($data);
 * </code>
 *
 * @since 1.0
 */
class Notify {

    /**
     * Current User ID
     *
     * @since 1.0
     * @access private
     * @var integer $this->user_id
     */
    private $user_id;

    /**
     * Current user notifications
     *
     * @since 1.0
     * @access private
     * @var array $this->user_notifications
     */
    private $user_notifications;

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
        # silence is golden
    }

    /**
     * Load Current or Custom user
     *
     * @since 1.0
     * @access public
     * @param mixed $user_id
     */
    public function loadUser($user_id = false)
    {

        // Fire security to identify current user id
        if( ($user_id === false) && !($this->timber->security->getId()) ){
            $this->timber->security->isAuth();
            $this->timber->security->isAdmin();
            $this->timber->security->isStaff();
            $this->timber->security->isClient();
        }

        $this->user_id = ($user_id === false) ? $this->timber->security->getId() : $user_id;

        if( isset($this->user_notifications[$this->user_id]) ){
            return true;
        }

        $this->user_notifications[$this->user_id] = $this->timber->user_meta_model->getMetaByMultiple( array('me_key' => '_user_system_notifications', 'us_id' => $this->user_id) );
        if( (false === $this->user_notifications[$this->user_id]) || !(is_object($this->user_notifications[$this->user_id])) ){
            $this->user_notifications[$this->user_id] = array(
                'us_id' => $this->user_id,
                'me_id' => '',
                'me_key' => '_user_system_notifications',
                'me_value' => array(),
            );
            $this->user_notifications[$this->user_id]['me_id'] = $this->timber->user_meta_model->addMeta(array(
                'us_id' => $this->user_id,
                'me_key' => '_user_system_notifications',
                'me_value' => serialize(array())
            ));
            return true;
        }

        $this->user_notifications[$this->user_id] = $this->user_notifications[$this->user_id]->as_array();
        $this->user_notifications[$this->user_id]['me_value'] = unserialize($this->user_notifications[$this->user_id]['me_value']);
        return true;
    }

    /**
     * Push notification to user
     *
     * @since 1.0
     * @access public
     * @param string  $item
     * @param mixed  $value
     * @param mixed $user_id
     * @return boolean
     */
    public function push($item, $value, $user_id = false)
    {
        $this->loadUser($user_id);
        $this->user_notifications[$this->user_id]['me_value'][$item] = $value;
        $this->store();
        return true;
    }

    /**
     * Fetch notification of user
     *
     * @since 1.0
     * @access public
     * @param string  $item
     * @param mixed $user_id
     * @return boolean
     */
    public function fetch($item, $user_id = false)
    {
        $this->loadUser($user_id);
        return ((isset($this->user_notifications[$this->user_id]['me_value'][$item])) ? $this->user_notifications[$this->user_id]['me_value'][$item] : false);
    }

    /**
     * Expire notification of user
     *
     * @since 1.0
     * @access public
     * @param string  $item
     * @param mixed $user_id
     * @return boolean
     */
    public function expire($item, $user_id = false)
    {
        $this->loadUser($user_id);
        if( isset($this->user_notifications[$this->user_id]['me_value'][$item]) ){
            unset($this->user_notifications[$this->user_id]['me_value'][$item]);
            $this->store();
        }
        return $this->timber->notify;
    }

    /**
     * Store User Notifications
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function store()
    {
        $this->timber->user_meta_model->updateMetaById( array(
            'me_id' => $this->user_notifications[$this->user_id]['me_id'],
            'me_value' => serialize($this->user_notifications[$this->user_id]['me_value']),
        ));
    }

    /**
     * Increment Notification as Integer
     *
     * @since 1.0
     * @access public
     * @param string $item
     * @param mixed $user_id
     */
    public function increment($item, $user_id = false)
    {
        $old_item = $this->fetch($item, $user_id);
        if( ($old_item !== false) && ( (is_integer($old_item)) || ($old_item === 0) ) ){
            $old_item += 1;
            $this->push($item, $old_item, $user_id);
        }else{
            $this->push($item, 1, $user_id);
        }
        return true;
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
    public function display($item, $expire_route = '/', $format = '{$VALUE}', $user_id = false)
    {
        $current_link = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        $actual_link = filter_var($current_link, FILTER_VALIDATE_URL) ? filter_var($current_link, FILTER_SANITIZE_URL) : '';
        if( strpos($actual_link, $expire_route) > 0 ){
            $this->expire($item, $user_id);
            return "";
        }
        $current_value = $this->fetch($item, $user_id);
        return ($current_value !== false) ? str_replace('{$VALUE}', $current_value, $format) : "";
    }

    /**
     * Set Mailer Cron
     *
     * @since 1.0
     * @access public
     * @param array $data
     * @return boolean
     */
    public function setMailerCron($data)
    {
        $notify_crons = $this->timber->option_model->getOptionByKey('_notify_crons');

        if( (false !== $notify_crons) && (is_object($notify_crons)) ){
            $notify_crons = $notify_crons->as_array();

            $notify_crons['op_value'] = unserialize($notify_crons['op_value']);
            $notify_crons['op_value'][] = $data;

            return (boolean) $this->timber->option_model->updateOptionById(array(
                'op_id' => $notify_crons['op_id'],
                'op_value' => serialize($notify_crons['op_value'])
            ));
        }

        return (boolean) $this->timber->option_model->addOption(array(
            'op_key' => '_notify_crons',
            'op_value' => serialize(array($data)),
            'autoload' => 'off'
        ));
    }

    /**
     * Fire All Mailer Crons
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function fireMailerCrons()
    {
        $notify_crons = $this->timber->option_model->getOptionByKey('_notify_crons');

        $status = true;

        if( (false !== $notify_crons) && (is_object($notify_crons)) ){
            $notify_crons = $notify_crons->as_array();

            $notify_crons['op_value'] = unserialize($notify_crons['op_value']);

            foreach ( $notify_crons['op_value'] as $key => $data ) {
                unset($notify_crons['op_value'][$key]);
                $status &= (boolean) $this->execMailerCron($data);
            }

            $status &= $this->timber->option_model->updateOptionById(array(
                'op_id' => $notify_crons['op_id'],
                'op_value' => serialize($notify_crons['op_value'])
            ));
        }

        return $status;
    }

    /**
     * Execute Mailer Cron with its data
     *
     * @since 1.0
     * @access public
     * @param array $data
     * @return boolean
     */
    public function execMailerCron($data)
    {

        if( method_exists($this, $data['method_name']) ){
            return (boolean) $this->$data['method_name']($data);
        }

        return false;
    }

    /**
     * Verify email notifier
     *
     * <code>
     * # Meta key
     * _verify_email_tpl
     *
     * # Available filters
     * {$verify_url}
     * {$login_url}
     * {$home_url}
     * {$full_name}
     * {$first_name}
     * {$last_name}
     * {$user_name}
     * {$email}
     *
     * $data
     *  - 'user_id' => Int,
     *  - 'hash' => String,
     * </code>
     *
     * @since 1.0
     * @access private
     * @param array $data
     * @return boolean
     */
    private function verifyEmailNotifier($data)
    {

        $user_data = $this->timber->user_model->getUserById($data['user_id']);

        if( (false !== $user_data) && (is_object($user_data)) ){
            $user_data = $user_data->as_array();
        }else{
            return false;
        }

        $to_name = (!empty($user_data['first_name']) || !empty($user_data['last_name'])) ? $user_data['first_name'] . " " . $user_data['last_name'] : $user_data['user_name'];
        $to_email = $user_data['email'];

        return (boolean) $this->timber->mailer->mail($this->timber->config('_site_title'),
            $to_name,
            $to_email,
            false,
            '_verify_email_tpl',
            array(
                '{$verify_url}' => $this->timber->config('request_url') . '/verify/' . $user_data['email'] . '/' . $data['hash'],
                '{$login_url}' => $this->timber->config('request_url') . '/login',
                '{$home_url}' => $this->timber->config('request_url'),
                '{$full_name}' => $to_name,
                '{$first_name}' => trim($user_data['first_name']),
                '{$last_name}' => trim($user_data['last_name']),
                '{$user_name}' => $user_data['user_name'],
                '{$email}' => $to_email
            ),
            false
        );
    }




    /**
     * FPWD email notifier
     *
     * <code>
     * # Meta key
     * _fpwd_tpl
     *
     * # Available filters
     * {$fpwd_url}
     * {$login_url}
     * {$home_url}
     * {$full_name}
     * {$first_name}
     * {$last_name}
     * {$user_name}
     * {$email}
     *
     * $data
     *  - 'user_id' => Int,
     *  - 'hash' => String,
     * </code>
     *
     * @since 1.0
     * @access private
     * @param array $data
     * @return boolean
     */
    private function fpwdEmailNotifier($data)
    {

        $user_data = $this->timber->user_model->getUserById($data['user_id']);

        if( (false !== $user_data) && (is_object($user_data)) ){
            $user_data = $user_data->as_array();
        }else{
            return false;
        }

        $to_name = (!empty($user_data['first_name']) || !empty($user_data['last_name'])) ? $user_data['first_name'] . " " . $user_data['last_name'] : $user_data['user_name'];
        $to_email = $user_data['email'];

        return (boolean) $this->timber->mailer->mail($this->timber->config('_site_title'),
            $to_name,
            $to_email,
            false,
            '_fpwd_tpl',
            array(
                '{$fpwd_url}' => $this->timber->config('request_url') . '/fpwd/' . $data['hash'],
                '{$login_url}' => $this->timber->config('request_url') . '/login',
                '{$home_url}' => $this->timber->config('request_url'),
                '{$full_name}' => $to_name,
                '{$first_name}' => trim($user_data['first_name']),
                '{$last_name}' => trim($user_data['last_name']),
                '{$user_name}' => $user_data['user_name'],
                '{$email}' => $to_email
            ),
            false
        );
    }

    /**
     * Login info email notifier
     *
     * <code>
     * # Meta key
     * _login_info_tpl
     *
     * # Available filters
     * {$login_url}
     * {$home_url}
     * {$full_name}
     * {$first_name}
     * {$last_name}
     * {$user_name}
     * {$email}
     * {$password}
     *
     * $data
     *  - 'first_name' => string,
     *  - 'last_name' => string,
     *  - 'user_name' => string,
     *  - 'email' => string,
     *  - 'password' => string,
     * </code>
     *
     * @since 1.0
     * @access private
     * @param array $data
     * @return boolean
     */
    private function loginInfoEmailNotifier($data)
    {

        $to_name = (!empty($data['first_name']) || !empty($data['last_name'])) ? $data['first_name'] . " " . $data['last_name'] : $data['user_name'];
        $to_email = $data['email'];

        return (boolean) $this->timber->mailer->mail($this->timber->config('_site_title'),
            $to_name,
            $to_email,
            false,
            '_login_info_tpl',
            array(
                '{$login_url}' => $this->timber->config('request_url') . '/login',
                '{$home_url}' => $this->timber->config('request_url'),
                '{$full_name}' => trim($to_name),
                '{$first_name}' => trim($data['first_name']),
                '{$last_name}' => trim($data['last_name']),
                '{$user_name}' => trim($data['user_name']),
                '{$email}' => $to_email,
                '{$password}' => $data['password'],
            ),
            false
        );
    }

    /**
     * Register info email notifier
     *
     * <code>
     * # Meta key
     * _register_invite_tpl
     *
     * # Available filters
     * {$register_url}
     * {$login_url}
     * {$home_url}
     * {$full_name}
     * {$first_name}
     * {$last_name}
     * {$user_name}
     * {$email}
     *
     * $data
     *  - 'first_name' => string,
     *  - 'last_name' => string,
     *  - 'user_name' => string,
     *  - 'email' => string,
     *  - 'hash' => string,
     * </code>
     *
     * @since 1.0
     * @access private
     * @param array $data
     * @return boolean
     */
    private function registerInviteEmailNotifier($data)
    {

        $to_name = (!empty($data['first_name']) || !empty($data['last_name'])) ? $data['first_name'] . " " . $data['last_name'] : $data['user_name'];
        $to_email = $data['email'];

        return (boolean) $this->timber->mailer->mail($this->timber->config('_site_title'),
            trim($to_name),
            $to_email,
            false,
            '_register_invite_tpl',
            array(
                '{$register_url}' => $this->timber->config('request_url') . '/register/' . $data['hash'],
                '{$login_url}' => $this->timber->config('request_url') . '/login',
                '{$home_url}' => $this->timber->config('request_url'),
                '{$full_name}' => trim($to_name),
                '{$first_name}' => trim($data['first_name']),
                '{$last_name}' => trim($data['last_name']),
                '{$user_name}' => trim($data['user_name']),
                '{$email}' => $to_email
            ),
            false
        );
    }

    /**
     * New Project Email Notification
     *
     * <code>
     * # Meta key
     * _new_project_tpl
     *
     * # Available filters
     * {$login_url}
     * {$home_url}
     * {$site_currency}
     * {$project_title}
     * {$project_reference}
     * {$project_ref_id}
     * {$project_created_at}
     * {$project_updated_at}
     * {$project_start_at}
     * {$project_end_at}
     * {$project_budget}
     * {$full_name}
     * {$email}
     * {$first_name}
     * {$last_name}
     * {$user_name}
     *
     * # Method args
     * $data
     *  - 'project_id' => Int,
     * </code>
     *
     * <code>
     *      $this->timber->notify->setMailerCron(array(
     *          'method_name' => 'newProjectEmailNotifier',
     *          'project_id' => 2,
     *      ));
     * </code>
     *
     * @since 1.0
     * @access private
     * @param array $data
     * @return boolean
     */
    private function newProjectEmailNotifier($data)
    {

        $site_currency = $this->timber->config('_site_currency_symbol');

        # Begin project data
        $project = $this->timber->project_model->getProjectById( $data['project_id'] );

        if( (false === $project) || !(is_object($project)) ){
            return false;
        }

        $project = $project->as_array();
        # End project data

        $data['users_ids'] = array();
        $data['users_ids'][] = $project['owner_id'];
        $staff = $this->timber->project_meta_model->getMetaByMultiple(array(
            'pr_id' => $data['project_id'],
            'me_key' => 'project_staff_members'
        ));

        $clients = $this->timber->project_meta_model->getMetaByMultiple(array(
            'pr_id' => $data['project_id'],
            'me_key' => 'project_clients_members'
        ));

        if( (false !== $clients) && (is_object($clients)) ){
            $clients = $clients->as_array();
            $clients_ids = unserialize($clients['me_value']);
            $data['users_ids'] = array_merge($data['users_ids'], $clients_ids);
        }

        if( (false !== $staff) && (is_object($staff)) ){
            $staff = $staff->as_array();
            $staff_ids = unserialize($staff['me_value']);
            $data['users_ids'] = array_merge($data['users_ids'], $staff_ids);
        }

        $status = true;

        foreach ($data['users_ids'] as $user_id) {

            if( empty($user_id) ){ continue; }

            # Begin user to data
            $user_data = $this->timber->user_model->getUserById($user_id);

            if( (false !== $user_data) && (is_object($user_data)) ){
                $user_data = $user_data->as_array();
            }else{
                continue;
            }

            $to_name = (!empty($user_data['first_name']) || !empty($user_data['last_name'])) ? $user_data['first_name'] . " " . $user_data['last_name'] : $user_data['user_name'];
            $to_email = $user_data['email'];
            # End user to data

            $status &= (boolean) $this->timber->mailer->mail($this->timber->config('_site_title'),
                trim($to_name),
                $to_email,
                false,
                '_new_project_tpl',
                array(
                    '{$login_url}' => $this->timber->config('request_url') . '/login',
                    '{$home_url}' => $this->timber->config('request_url'),

                    '{$site_currency}' => $site_currency,

                    '{$project_title}' => $project['title'],
                    '{$project_reference}' => $project['reference'],
                    '{$project_ref_id}' => "PRO-" . str_pad($project['pr_id'], 8, '0', STR_PAD_LEFT),
                    '{$project_created_at}' => $project['created_at'],
                    '{$project_updated_at}' => $project['updated_at'],
                    '{$project_start_at}' => $project['start_at'],
                    '{$project_end_at}' => $project['end_at'],
                    '{$project_budget}' => $project['budget'],

                    '{$full_name}' => trim($to_name),
                    '{$email}' => $to_email,
                    '{$first_name}' => trim($user_data['first_name']),
                    '{$last_name}' => trim($user_data['last_name']),
                    '{$user_name}' => trim($user_data['user_name']),
                ),
                false
            );
        }

        return (boolean) $status;
    }

    /**
     * New Task Email Notification
     *
     * <code>
     * # Meta key
     * _new_project_task_tpl
     *
     * # Available filters
     * {$login_url}
     * {$home_url}
     * {$site_currency}
     * {$project_title}
     * {$project_reference}
     * {$project_ref_id}
     * {$project_created_at}
     * {$project_updated_at}
     * {$project_start_at}
     * {$project_end_at}
     * {$task_title}
     * {$task_created_at}
     * {$task_start_at}
     * {$task_end_at}
     * {$assign_full_name}
     * {$assign_email}
     * {$assign_first_name}
     * {$assign_last_name}
     * {$assign_user_name}
     * {$full_name}
     * {$email}
     * {$first_name}
     * {$last_name}
     * {$user_name}
     *
     * # Method args
     * $data
     *  - 'project_id' => Int,
     *  - 'task_id' => Int,
     * </code>
     *
     * <code>
     *      $this->timber->notify->setMailerCron(array(
     *          'method_name' => 'projectTaskEmailNotifier',
     *          'project_id' => 2,
     *          'task_id' => 2,
     *      ));
     * </code>
     *
     * @since 1.0
     * @access private
     * @param array $data
     * @return boolean
     */
    public function projectTaskEmailNotifier($data)
    {

        $site_currency = $this->timber->config('_site_currency_symbol');

        # Begin project data
        $project = $this->timber->project_model->getProjectById( $data['project_id'] );
        if( (false === $project) || !(is_object($project)) ){
            return false;
        }
        $project = $project->as_array();
        # End project data

        # Begin task data
        $task = $this->timber->task_model->getTaskById( $data['task_id'] );
        if( (false === $task) || !(is_object($task)) ){
            return false;
        }
        $task = $task->as_array();
        # End task data

        # Begin task owner data
        $task_owner = $this->timber->user_model->getUserById($task['owner_id']);
        if( (false === $task_owner) || !(is_object($task_owner)) ){
            return false;
        }

        $task_owner = $task_owner->as_array();

        $task_owner_name = (!empty($task_owner['first_name']) || !empty($task_owner['last_name'])) ? $task_owner['first_name'] . " " . $task_owner['last_name'] : $task_owner['user_name'];
        $task_owner_email = $task_owner['email'];
        # End task owner data

        $data['users_ids'] = array();
        $data['users_ids'][] = $project['owner_id'];
        $staff = $this->timber->project_meta_model->getMetaByMultiple(array(
            'pr_id' => $data['project_id'],
            'me_key' => 'project_staff_members'
        ));

        $clients = $this->timber->project_meta_model->getMetaByMultiple(array(
            'pr_id' => $data['project_id'],
            'me_key' => 'project_clients_members'
        ));

        if( (false !== $clients) && (is_object($clients)) ){
            $clients = $clients->as_array();
            $clients_ids = unserialize($clients['me_value']);
            $data['users_ids'] = array_merge($data['users_ids'], $clients_ids);
        }

        if( (false !== $staff) && (is_object($staff)) ){
            $staff = $staff->as_array();
            $staff_ids = unserialize($staff['me_value']);
            $data['users_ids'] = array_merge($data['users_ids'], $staff_ids);
        }

        $status = true;

        foreach ($data['users_ids'] as $user_id) {

            if( empty($user_id) ){ continue; }

            # Begin user data
            $user_data = $this->timber->user_model->getUserById($user_id);

            if( (false !== $user_data) && (is_object($user_data)) ){
                $user_data = $user_data->as_array();
            }else{
                continue;
            }

            $to_name = (!empty($user_data['first_name']) || !empty($user_data['last_name'])) ? $user_data['first_name'] . " " . $user_data['last_name'] : $user_data['user_name'];
            $to_email = $user_data['email'];
            # End user data

            $status &= (boolean) $this->timber->mailer->mail($this->timber->config('_site_title'),
                trim($to_name),
                $to_email,
                false,
                '_new_project_task_tpl',
                array(
                    '{$login_url}' => $this->timber->config('request_url') . '/login',
                    '{$home_url}' => $this->timber->config('request_url'),

                    //'{$site_currency}' => $site_currency,

                    '{$project_title}' => $project['title'],
                    '{$project_reference}' => $project['reference'],
                    '{$project_ref_id}' => "PRO-" . str_pad($project['pr_id'], 8, '0', STR_PAD_LEFT),
                    '{$project_created_at}' => $project['created_at'],
                    '{$project_updated_at}' => $project['updated_at'],
                    '{$project_start_at}' => $project['start_at'],
                    '{$project_end_at}' => $project['end_at'],

                    '{$task_title}' => $task['title'],
                    '{$task_created_at}' => $task['created_at'],
                    '{$task_start_at}' => $task['start_at'],
                    '{$task_end_at}' => $task['end_at'],

                    '{$assign_full_name}' => $task_owner_name,
                    '{$assign_email}' => $task_owner_email,
                    '{$assign_first_name}' => $task_owner['first_name'],
                    '{$assign_last_name}' => $task_owner['last_name'],
                    '{$assign_user_name}' => $task_owner['user_name'],

                    '{$full_name}' => trim($to_name),
                    '{$email}' => $to_email,
                    '{$first_name}' => trim($user_data['first_name']),
                    '{$last_name}' => trim($user_data['last_name']),
                    '{$user_name}' => trim($user_data['user_name']),
                ),
                false
            );
        }

        return (boolean) $status;
    }

    /**
     * New Milestone Email Notification
     *
     * <code>
     * # Meta key
     * _new_project_milestone_tpl
     *
     * # Available filters
     * {$login_url}
     * {$home_url}
     * {$project_title}
     * {$project_reference}
     * {$project_ref_id}
     * {$project_created_at}
     * {$project_updated_at}
     * {$project_start_at}
     * {$project_end_at}
     * {$milestone_title}
     * {$milestone_created_at}
     * {$full_name}
     * {$email}
     * {$first_name}
     * {$last_name}
     * {$user_name}
     *
     * # Method args
     * $data
     *  - 'project_id' => Int,
     *  - 'miles_id' => Int,
     * </code>
     *
     * <code>
     *      $this->timber->notify->setMailerCron(array(
     *          'method_name' => 'projectMilestoneEmailNotifier',
     *          'project_id' => 2,
     *          'miles_id' => 2,
     *      ));
     * </code>
     *
     * @since 1.0
     * @access private
     * @param array $data
     * @return boolean
     */
    private function projectMilestoneEmailNotifier($data)
    {

        # Begin project data
        $project = $this->timber->project_model->getProjectById( $data['project_id'] );

        if( (false === $project) || !(is_object($project)) ){
            return false;
        }

        $project = $project->as_array();
        # End project data


        # Begin milestone data
        $milestone = $this->timber->milestone_model->getMilestoneById( $data['miles_id'] );

        if( (false === $milestone) || !(is_object($milestone)) ){
            return false;
        }

        $milestone = $milestone->as_array();
        # End milestone data

        $data['users_ids'] = array();
        $data['users_ids'][] = $project['owner_id'];
        $staff = $this->timber->project_meta_model->getMetaByMultiple(array(
            'pr_id' => $data['project_id'],
            'me_key' => 'project_staff_members'
        ));

        $clients = $this->timber->project_meta_model->getMetaByMultiple(array(
            'pr_id' => $data['project_id'],
            'me_key' => 'project_clients_members'
        ));

        if( (false !== $clients) && (is_object($clients)) ){
            $clients = $clients->as_array();
            $clients_ids = unserialize($clients['me_value']);
            $data['users_ids'] = array_merge($data['users_ids'], $clients_ids);
        }

        if( (false !== $staff) && (is_object($staff)) ){
            $staff = $staff->as_array();
            $staff_ids = unserialize($staff['me_value']);
            $data['users_ids'] = array_merge($data['users_ids'], $staff_ids);
        }

        $status = true;

        foreach ($data['users_ids'] as $user_id) {

            if( empty($user_id) ){ continue; }

            # Begin user to data
            $user_data = $this->timber->user_model->getUserById($user_id);

            if( (false !== $user_data) && (is_object($user_data)) ){
                $user_data = $user_data->as_array();
            }else{
                continue;
            }

            $to_name = (!empty($user_data['first_name']) || !empty($user_data['last_name'])) ? $user_data['first_name'] . " " . $user_data['last_name'] : $user_data['user_name'];
            $to_email = $user_data['email'];
            # End user to data

            $status &= (boolean) $this->timber->mailer->mail($this->timber->config('_site_title'),
                trim($to_name),
                $to_email,
                false,
                '_new_project_milestone_tpl',
                array(
                    '{$login_url}' => $this->timber->config('request_url') . '/login',
                    '{$home_url}' => $this->timber->config('request_url'),

                    '{$project_title}' => $project['title'],
                    '{$project_reference}' => $project['reference'],
                    '{$project_ref_id}' => "PRO-" . str_pad($project['pr_id'], 8, '0', STR_PAD_LEFT),
                    '{$project_created_at}' => $project['created_at'],
                    '{$project_updated_at}' => $project['updated_at'],
                    '{$project_start_at}' => $project['start_at'],
                    '{$project_end_at}' => $project['end_at'],

                    '{$milestone_title}' => $milestone['title'],
                    '{$milestone_created_at}' => $milestone['created_at'],

                    '{$full_name}' => trim($to_name),
                    '{$email}' => $to_email,
                    '{$first_name}' => trim($user_data['first_name']),
                    '{$last_name}' => trim($user_data['last_name']),
                    '{$user_name}' => trim($user_data['user_name']),
                ),
                false
            );
        }

        return (boolean) $status;
    }

    /**
     * New Ticket Email Notification
     *
     * <code>
     * # Meta key
     * _new_project_ticket_tpl
     *
     * # Available filters
     * {$login_url}
     * {$home_url}
     * {$project_title}
     * {$project_reference}
     * {$project_ref_id}
     * {$project_created_at}
     * {$project_updated_at}
     * {$project_start_at}
     * {$project_end_at}
     * {$ticket_subject}
     * {$ticket_date}
     * {$ticket_reference}
     * {$opened_by_full_name}
     * {$opened_by_email}
     * {$opened_by_first_name}
     * {$opened_by_last_name}
     * {$opened_by_user_name}
     * {$full_name}
     * {$email}
     * {$first_name}
     * {$last_name}
     * {$user_name}
     *
     * # Method args
     * $data
     *  - 'project_id' => Int,
     *  - 'ticket_id' => Int,
     * </code>
     *
     * <code>
     *      $this->timber->notify->setMailerCron(array(
     *          'method_name' => 'projectTicketEmailNotifier',
     *          'project_id' => 2,
     *          'ticket_id' => 2,
     *      ));
     * </code>
     *
     * @since 1.0
     * @access private
     * @param array $data
     * @return boolean
     */
    private function projectTicketEmailNotifier($data)
    {

        # Begin project data
        $project = $this->timber->project_model->getProjectById( $data['project_id'] );

        if( (false === $project) || !(is_object($project)) ){
            return false;
        }

        $project = $project->as_array();
        # End project data


        # Begin ticket data
        $ticket = $this->timber->ticket_model->getTicketById( $data['ticket_id'] );

        if( (false === $ticket) || !(is_object($ticket)) ){
            return false;
        }

        $ticket = $ticket->as_array();
        # End ticket data


        # Begin ticket owner data
        $ticket_owner = $this->timber->user_model->getUserById($ticket['owner_id']);

        if( (false !== $ticket_owner) && (is_object($ticket_owner)) ){
            $ticket_owner = $ticket_owner->as_array();
        }else{
            return false;
        }

        $ticket_owner_name = (!empty($ticket_owner['first_name']) || !empty($ticket_owner['last_name'])) ? $ticket_owner['first_name'] . " " . $ticket_owner['last_name'] : $ticket_owner['user_name'];
        $ticket_owner_email = $ticket_owner['email'];
        # End ticket owner data

        $data['users_ids'] = array();
        $data['users_ids'][] = $project['owner_id'];
        $staff = $this->timber->project_meta_model->getMetaByMultiple(array(
            'pr_id' => $data['project_id'],
            'me_key' => 'project_staff_members'
        ));

        $clients = $this->timber->project_meta_model->getMetaByMultiple(array(
            'pr_id' => $data['project_id'],
            'me_key' => 'project_clients_members'
        ));

        if( (false !== $clients) && (is_object($clients)) ){
            $clients = $clients->as_array();
            $clients_ids = unserialize($clients['me_value']);
            $data['users_ids'] = array_merge($data['users_ids'], $clients_ids);
        }

        if( (false !== $staff) && (is_object($staff)) ){
            $staff = $staff->as_array();
            $staff_ids = unserialize($staff['me_value']);
            $data['users_ids'] = array_merge($data['users_ids'], $staff_ids);
        }

        $status = true;

        foreach ($data['users_ids'] as $user_id) {

            if( empty($user_id) ){ continue; }

            # Begin user to data
            $user_data = $this->timber->user_model->getUserById($user_id);

            if( (false !== $user_data) && (is_object($user_data)) ){
                $user_data = $user_data->as_array();
            }else{
                continue;
            }

            $to_name = (!empty($user_data['first_name']) || !empty($user_data['last_name'])) ? $user_data['first_name'] . " " . $user_data['last_name'] : $user_data['user_name'];
            $to_email = $user_data['email'];
            # End user to data

            $status &= (boolean) $this->timber->mailer->mail($this->timber->config('_site_title'),
                trim($to_name),
                $to_email,
                false,
                '_new_project_ticket_tpl',
                array(
                    '{$login_url}' => $this->timber->config('request_url') . '/login',
                    '{$home_url}' => $this->timber->config('request_url'),

                    '{$project_title}' => $project['title'],
                    '{$project_reference}' => $project['reference'],
                    '{$project_ref_id}' => "PRO-" . str_pad($project['pr_id'], 8, '0', STR_PAD_LEFT),
                    '{$project_created_at}' => $project['created_at'],
                    '{$project_updated_at}' => $project['updated_at'],
                    '{$project_start_at}' => $project['start_at'],
                    '{$project_end_at}' => $project['end_at'],

                    '{$ticket_subject}' => $ticket['subject'],
                    '{$ticket_date}' => $ticket['created_at'],
                    '{$ticket_reference}' => $ticket['reference'],

                    '{$opened_by_full_name}' => $ticket_owner_name,
                    '{$opened_by_email}' => $ticket_owner_email,
                    '{$opened_by_first_name}' => $ticket_owner['first_name'],
                    '{$opened_by_last_name}' => $ticket_owner['last_name'],
                    '{$opened_by_user_name}' => $ticket_owner['user_name'],

                    '{$full_name}' => trim($to_name),
                    '{$email}' => $to_email,
                    '{$first_name}' => trim($user_data['first_name']),
                    '{$last_name}' => trim($user_data['last_name']),
                    '{$user_name}' => trim($user_data['user_name']),
                ),
                false
            );
        }

        return (boolean) $status;
    }

    /**
     * New File Email Notification
     *
     * <code>
     * // Meta key
     * _new_project_files_tpl
     *
     * // Available filters
     * {$login_url}
     * {$home_url}
     * {$project_title}
     * {$project_reference}
     * {$project_ref_id}
     * {$project_created_at}
     * {$project_updated_at}
     * {$project_start_at}
     * {$project_end_at}
     * {$file_name}
     * {$file_date}
     * {$uploaded_by_full_name}
     * {$uploaded_by_email}
     * {$uploaded_by_first_name}
     * {$uploaded_by_last_name}
     * {$uploaded_by_user_name}
     * {$full_name}
     * {$email}
     * {$first_name}
     * {$last_name}
     * {$user_name}
     *
     * # Method args
     * $data
     *  - 'project_id' => Int,
     *  - 'file_id' => Int,
     * </code>
     *
     * <code>
     *      $this->timber->notify->setMailerCron(array(
     *          'method_name' => 'projectFileEmailNotifier',
     *          'project_id' => 2,
     *          'file_id' => 2,
     *      ));
     * </code>
     *
     * @since 1.0
     * @access private
     * @param array $data
     * @return boolean
     */
    private function projectFileEmailNotifier($data)
    {

        # Begin Project Data
        $project = $this->timber->project_model->getProjectById( $data['project_id'] );

        if( (false === $project) || !(is_object($project)) ){
            return false;
        }

        $project = $project->as_array();
        # End Project Data

        # Begin File Data
        $file = $this->timber->file_model->getFileById( $data['file_id'] );

        if( (false === $file) || !(is_object($file)) ){
            return false;
        }

        $file = $file->as_array();
        # End File Data

        # Begin File Owner
        $file_owner = $this->timber->user_model->getUserById($file['owner_id']);

        if( (false !== $file_owner) && (is_object($file_owner)) ){
            $file_owner = $file_owner->as_array();
        }else{
            return false;
        }

        $file_owner_name = (!empty($file_owner['first_name']) || !empty($file_owner['last_name'])) ? $file_owner['first_name'] . " " . $file_owner['last_name'] : $file_owner['user_name'];
        $file_owner_email = $file_owner['email'];
        # End File Owner

        $data['users_ids'] = array();
        $data['users_ids'][] = $project['owner_id'];
        $staff = $this->timber->project_meta_model->getMetaByMultiple(array(
            'pr_id' => $data['project_id'],
            'me_key' => 'project_staff_members'
        ));

        $clients = $this->timber->project_meta_model->getMetaByMultiple(array(
            'pr_id' => $data['project_id'],
            'me_key' => 'project_clients_members'
        ));

        if( (false !== $clients) && (is_object($clients)) ){
            $clients = $clients->as_array();
            $clients_ids = unserialize($clients['me_value']);
            $data['users_ids'] = array_merge($data['users_ids'], $clients_ids);
        }

        if( (false !== $staff) && (is_object($staff)) ){
            $staff = $staff->as_array();
            $staff_ids = unserialize($staff['me_value']);
            $data['users_ids'] = array_merge($data['users_ids'], $staff_ids);
        }

        $status = true;

        foreach ($data['users_ids'] as $user_id) {

            if( empty($user_id) ){ continue; }

            # Begin user to data
            $user_data = $this->timber->user_model->getUserById($user_id);
            if( (false !== $user_data) && (is_object($user_data)) ){
                $user_data = $user_data->as_array();
            }else{
                continue;
            }
            $to_name = (!empty($user_data['first_name']) || !empty($user_data['last_name'])) ? $user_data['first_name'] . " " . $user_data['last_name'] : $user_data['user_name'];
            $to_email = $user_data['email'];
            # End user to data

            $status &= (boolean) $this->timber->mailer->mail($this->timber->config('_site_title'),
                trim($to_name),
                $to_email,
                false,
                '_new_project_files_tpl',
                array(
                    '{$login_url}' => $this->timber->config('request_url') . '/login',
                    '{$home_url}' => $this->timber->config('request_url'),

                    '{$project_title}' => $project['title'],
                    '{$project_reference}' => $project['reference'],
                    '{$project_ref_id}' => "PRO-" . str_pad($project['pr_id'], 8, '0', STR_PAD_LEFT),
                    '{$project_created_at}' => $project['created_at'],
                    '{$project_updated_at}' => $project['updated_at'],
                    '{$project_start_at}' => $project['start_at'],
                    '{$project_end_at}' => $project['end_at'],

                    '{$file_name}' => $file['title'],
                    '{$file_date}' => $file['uploaded_at'],

                    '{$uploaded_by_full_name}' => $file_owner_name,
                    '{$uploaded_by_email}' => $file_owner_email,
                    '{$uploaded_by_first_name}' => $file_owner['first_name'],
                    '{$uploaded_by_last_name}' => $file_owner['last_name'],
                    '{$uploaded_by_user_name}' => $file_owner['user_name'],

                    '{$full_name}' => trim($to_name),
                    '{$email}' => $to_email,
                    '{$first_name}' => trim($user_data['first_name']),
                    '{$last_name}' => trim($user_data['last_name']),
                    '{$user_name}' => trim($user_data['user_name']),
                ),
                false
            );
        }

        return (boolean) $status;
    }

    /**
     * New Message Email Notification
     *
     * <code>
     * // Meta key
     * _new_message_tpl
     *
     * // Available filters
     * {$login_url}
     * {$home_url}
     * {$to_full_name}
     * {$to_email}
     * {$to_first_name}
     * {$to_last_name}
     * {$to_user_name}
     * {$from_full_name}
     * {$from_email}
     * {$from_first_name}
     * {$from_last_name}
     * {$from_user_name}
     *
     * # Method args
     * $data
     *  - 'to_user_id' => Int,
     *  - 'from_user_id' => Int,
     * </code>
     *
     * <code>
     *      $this->timber->notify->setMailerCron(array(
     *          'method_name' => 'messageEmailNotifier',
     *          'to_user_id' => 2,
     *          'from_user_id' => 2,
     *      ));
     * </code>
     *
     * @since 1.0
     * @access private
     * @param array $data
     * @return boolean
     */
    private function messageEmailNotifier($data)
    {

        $to_user_data = $this->timber->user_model->getUserById($data['to_user_id']);
        $from_user_data = $this->timber->user_model->getUserById($data['from_user_id']);

        if( (false !== $to_user_data) && (is_object($to_user_data)) && (false !== $from_user_data) && (is_object($from_user_data)) ){
            $to_user_data = $to_user_data->as_array();
            $from_user_data = $from_user_data->as_array();
        }else{
            return false;
        }

        $to_name = (!empty($to_user_data['first_name']) || !empty($to_user_data['last_name'])) ? $to_user_data['first_name'] . " " . $to_user_data['last_name'] : $to_user_data['user_name'];
        $to_email = $to_user_data['email'];

        $from_name = (!empty($from_user_data['first_name']) || !empty($from_user_data['last_name'])) ? $from_user_data['first_name'] . " " . $from_user_data['last_name'] : $from_user_data['user_name'];
        $from_email = $from_user_data['email'];

        return (boolean) $this->timber->mailer->mail($this->timber->config('_site_title'),
            trim($to_name),
            $to_email,
            false,
            '_new_message_tpl',
            array(
                '{$login_url}' => $this->timber->config('request_url') . '/login',
                '{$home_url}' => $this->timber->config('request_url'),

                '{$to_full_name}' => trim($to_name),
                '{$to_email}' => $to_email,
                '{$to_first_name}' => trim($to_user_data['first_name']),
                '{$to_last_name}' => trim($to_user_data['last_name']),
                '{$to_user_name}' => trim($to_user_data['user_name']),

                '{$from_full_name}' => trim($from_name),
                '{$from_email}' => $from_email,
                '{$from_first_name}' => trim($from_user_data['first_name']),
                '{$from_last_name}' => trim($from_user_data['last_name']),
                '{$from_user_name}' => trim($from_user_data['user_name']),
            ),
            false
        );
    }

    /**
     * New Quotation Email Notification
     *
     * <code>
     * // Meta key
     * _new_quotation_tpl
     *
     * // Available filters
     * {$login_url}
     * {$home_url}
     * {$quotation_title}
     * {$quotation_reference}
     * {$quotation_ref_id}
     * {$quotation_created_at}
     * {$full_name}
     * {$email}
     * {$first_name}
     * {$last_name}
     * {$user_name}
     *
     * # Method args
     * $data
     *  - 'qu_id'
     *  - 'user_id'
     * </code>
     *
     * <code>
     *      $this->timber->notify->setMailerCron(array(
     *          'method_name' => 'newQuotationEmailNotifier',
     *          'qu_id' => 2,
     *          'user_id' => 2,
     *      ));
     * </code>
     *
     * @since 1.0
     * @access private
     * @param array $data
     * @return boolean
     */
    private function newQuotationEmailNotifier($data)
    {
        $quotation_data = $this->timber->quotation_model->getQuotationById($data['qu_id']);

        if( (false !== $quotation_data) && (is_object($quotation_data)) ){
            $quotation_data = $quotation_data->as_array();
        }else{
            return false;
        }

        $user_data = $this->timber->user_model->getUserById($data['user_id']);

        if( (false !== $user_data) && (is_object($user_data)) ){
            $user_data = $user_data->as_array();
        }else{
            return false;
        }

        $to_name = (!empty($user_data['first_name']) || !empty($user_data['last_name'])) ? $user_data['first_name'] . " " . $user_data['last_name'] : $user_data['user_name'];
        $to_email = $user_data['email'];

        return (boolean) $this->timber->mailer->mail($this->timber->config('_site_title'),
            trim($to_name),
            $to_email,
            false,
            '_new_quotation_tpl',
            array(
                '{$login_url}' => $this->timber->config('request_url') . '/login',
                '{$home_url}' => $this->timber->config('request_url'),

                '{$quotation_title}' => $quotation_data['title'],
                '{$quotation_reference}' => $quotation_data['reference'],
                '{$quotation_ref_id}' => "QUO-" . str_pad($quotation_data['qu_id'], 8, '0', STR_PAD_LEFT),
                '{$quotation_created_at}' => $quotation_data['created_at'],

                '{$full_name}' => trim($to_name),
                '{$email}' => $to_email,
                '{$first_name}' => trim($user_data['first_name']),
                '{$last_name}' => trim($user_data['last_name']),
                '{$user_name}' => trim($user_data['user_name']),
            ),
            false
        );
    }

    /**
     * New Public Quotation Email Notification
     *
     * <code>
     * // Meta key
     * _new_public_quotation_tpl
     *
     * // Available filters
     * {$quotation_url}
     * {$quotation_title}
     * {$quotation_reference}
     * {$quotation_ref_id}
     * {$quotation_created_at}
     * {$email}
     *
     * # Method args
     * $data
     *  - 'qu_id'
     *  - 'email'
     * </code>
     *
     * <code>
     *      $this->timber->notify->setMailerCron(array(
     *          'method_name' => 'newPublicQuotationEmailNotifier',
     *          'qu_id' => 2,
     *          'email' => 2,
     *      ));
     * </code>
     *
     * @since 1.0
     * @access private
     * @param array $data
     * @return boolean
     */
    private function newPublicQuotationEmailNotifier($data)
    {
        $quotation_data = $this->timber->quotation_model->getQuotationById($data['qu_id']);

        if( (false !== $quotation_data) && (is_object($quotation_data)) ){
            $quotation_data = $quotation_data->as_array();
        }else{
            return false;
        }

        $to_email = $data['email'];

        return (boolean) $this->timber->mailer->mail($this->timber->config('_site_title'),
            "Client",
            $to_email,
            false,
            '_new_public_quotation_tpl',
            array(
                '{$quotation_url}' => $this->timber->config('request_url') . "/admin/quotations/pubsubmit/" . $data['qu_id'] . "/"  . $to_email,

                '{$quotation_title}' => $quotation_data['title'],
                '{$quotation_reference}' => $quotation_data['reference'],
                '{$quotation_ref_id}' => "QUO-" . str_pad($quotation_data['qu_id'], 8, '0', STR_PAD_LEFT),
                '{$quotation_created_at}' => $quotation_data['created_at'],

                '{$email}' => $to_email,
            ),
            false
        );
    }

    /**
     * New Subscription Email Notification
     *
     * <code>
     * // Meta key
     * _new_subscription_tpl
     *
     * // Available filters
     * {$login_url}
     * {$home_url}
     * {$site_currency}
     *
     * {$subscription_reference}
     * {$subscription_ref_id}
     * {$subscription_total}
     * {$subscription_begin_at}
     * {$subscription_end_at}
     * {$subscription_created_at}
     *
     * {$full_name}
     * {$email}
     * {$first_name}
     * {$last_name}
     * {$user_name}
     *
     * # Method args
     * $data
     *  - 'sub_id'
     * </code>
     *
     * <code>
     *      $this->timber->notify->setMailerCron(array(
     *          'method_name' => 'newSubscriptionEmailNotifier',
     *          'sub_id' => 2,
     *      ));
     * </code>
     *
     * @since 1.0
     * @access private
     * @param array $data
     * @return boolean
     */
    private function newSubscriptionEmailNotifier($data)
    {
        $site_currency = $this->timber->config('_site_currency_symbol');

        $subscription_data = $this->timber->subscription_model->getSubscriptionById($data['sub_id']);

        if( (false !== $subscription_data) && (is_object($subscription_data)) ){
            $subscription_data = $subscription_data->as_array();
        }else{
            return false;
        }

        $user_data = $this->timber->user_model->getUserById($subscription_data['client_id']);

        if( (false !== $user_data) && (is_object($user_data)) ){
            $user_data = $user_data->as_array();
        }else{
            return false;
        }

        $to_name = (!empty($user_data['first_name']) || !empty($user_data['last_name'])) ? $user_data['first_name'] . " " . $user_data['last_name'] : $user_data['user_name'];
        $to_email = $user_data['email'];

        return (boolean) $this->timber->mailer->mail($this->timber->config('_site_title'),
            trim($to_name),
            $to_email,
            false,
            '_new_subscription_tpl',
            array(
                '{$login_url}' => $this->timber->config('request_url') . '/login',
                '{$home_url}' => $this->timber->config('request_url'),

                '{$site_currency}' => $site_currency,

                '{$subscription_reference}' => $subscription_data['reference'],
                '{$subscription_ref_id}' => "SUB-" . str_pad($subscription_data['su_id'], 8, '0', STR_PAD_LEFT),
                '{$subscription_total}' => $subscription_data['total'],
                '{$subscription_begin_at}' => $subscription_data['begin_at'],
                '{$subscription_end_at}' => $subscription_data['end_at'],
                '{$subscription_created_at}' => $subscription_data['created_at'],

                '{$full_name}' => trim($to_name),
                '{$email}' => $to_email,
                '{$first_name}' => trim($user_data['first_name']),
                '{$last_name}' => trim($user_data['last_name']),
                '{$user_name}' => trim($user_data['user_name']),
            ),
            false
        );
    }

    /**
     * New Invoice Email Notification
     *
     * <code>
     * // Meta key
     * _new_invoice_tpl
     *
     * // Available filters
     * {$login_url}
     * {$home_url}
     * {$site_currency}
     *
     * {$invoice_reference}
     * {$invoice_ref_id}
     * {$invoice_total}
     * {$invoice_issue_date}
     * {$invoice_due_date}
     * {$invoice_created_at}
     *
     * {$full_name}
     * {$email}
     * {$first_name}
     * {$last_name}
     * {$user_name}
     *
     * # Method args
     * $data
     *  - 'inv_id'
     * </code>
     *
     * <code>
     *      $this->timber->notify->setMailerCron(array(
     *          'method_name' => 'newInvoiceEmailNotifier',
     *          'inv_id' => 2,
     *      ));
     * </code>
     *
     * @since 1.0
     * @access private
     * @param array $data
     * @return boolean
     */
    private function newInvoiceEmailNotifier($data)
    {
        $site_currency = $this->timber->config('_site_currency_symbol');

        $invoice_data = $this->timber->invoice_model->getInvoiceById($data['inv_id']);

        if( (false !== $invoice_data) && (is_object($invoice_data)) ){
            $invoice_data = $invoice_data->as_array();
        }else{
            return false;
        }

        $user_data = $this->timber->user_model->getUserById($invoice_data['client_id']);

        if( (false !== $user_data) && (is_object($user_data)) ){
            $user_data = $user_data->as_array();
        }else{
            return false;
        }

        $to_name = (!empty($user_data['first_name']) || !empty($user_data['last_name'])) ? $user_data['first_name'] . " " . $user_data['last_name'] : $user_data['user_name'];
        $to_email = $user_data['email'];

        return (boolean) $this->timber->mailer->mail($this->timber->config('_site_title'),
            trim($to_name),
            $to_email,
            false,
            '_new_invoice_tpl',
            array(
                '{$login_url}' => $this->timber->config('request_url') . '/login',
                '{$home_url}' => $this->timber->config('request_url'),

                '{$site_currency}' => $site_currency,

                '{$invoice_reference}' => $invoice_data['reference'],
                '{$invoice_ref_id}' => "INV-" . str_pad($invoice_data['in_id'], 8, '0', STR_PAD_LEFT),
                '{$invoice_total}' => $invoice_data['total'],
                '{$invoice_issue_date}' => $invoice_data['issue_date'],
                '{$invoice_due_date}' => $invoice_data['due_date'],
                '{$invoice_created_at}' => $invoice_data['created_at'],

                '{$full_name}' => trim($to_name),
                '{$email}' => $to_email,
                '{$first_name}' => trim($user_data['first_name']),
                '{$last_name}' => trim($user_data['last_name']),
                '{$user_name}' => trim($user_data['user_name']),
            ),
            false
        );
    }

    /**
     * New Estimate Email Notification
     *
     * <code>
     * // Meta key
     * _new_estimate_tpl
     *
     * // Available filters
     * {$login_url}
     * {$home_url}
     * {$site_currency}
     *
     * {$estimate_reference}
     * {$estimate_ref_id}
     * {$estimate_total}
     * {$estimate_issue_date}
     * {$estimate_due_date}
     * {$estimate_created_at}
     *
     * {$full_name}
     * {$email}
     * {$first_name}
     * {$last_name}
     * {$user_name}
     *
     * # Method args
     * $data
     *  - 'est_id'
     * </code>
     *
     * <code>
     *      $this->timber->notify->setMailerCron(array(
     *          'method_name' => 'newEstimateEmailNotifier',
     *          'est_id' => 2,
     *      ));
     * </code>
     *
     * @since 1.0
     * @access private
     * @param array $data
     * @return boolean
     */
    private function newEstimateEmailNotifier($data)
    {
        $site_currency = $this->timber->config('_site_currency_symbol');

        $estimate_data = $this->timber->invoice_model->getInvoiceById($data['est_id']);

        if( (false !== $estimate_data) && (is_object($estimate_data)) ){
            $estimate_data = $estimate_data->as_array();
        }else{
            return false;
        }

        $user_data = $this->timber->user_model->getUserById($estimate_data['client_id']);

        if( (false !== $user_data) && (is_object($user_data)) ){
            $user_data = $user_data->as_array();
        }else{
            return false;
        }

        $to_name = (!empty($user_data['first_name']) || !empty($user_data['last_name'])) ? $user_data['first_name'] . " " . $user_data['last_name'] : $user_data['user_name'];
        $to_email = $user_data['email'];

        return (boolean) $this->timber->mailer->mail($this->timber->config('_site_title'),
            trim($to_name),
            $to_email,
            false,
            '_new_estimate_tpl',
            array(
                '{$login_url}' => $this->timber->config('request_url') . '/login',
                '{$home_url}' => $this->timber->config('request_url'),

                '{$site_currency}' => $site_currency,

                '{$estimate_reference}' => $estimate_data['reference'],
                '{$estimate_ref_id}' => "EST-" . str_pad($estimate_data['in_id'], 8, '0', STR_PAD_LEFT),
                '{$estimate_total}' => $estimate_data['total'],
                '{$estimate_issue_date}' => $estimate_data['issue_date'],
                '{$estimate_due_date}' => $estimate_data['due_date'],
                '{$estimate_created_at}' => $estimate_data['created_at'],

                '{$full_name}' => trim($to_name),
                '{$email}' => $to_email,
                '{$first_name}' => trim($user_data['first_name']),
                '{$last_name}' => trim($user_data['last_name']),
                '{$user_name}' => trim($user_data['user_name']),
            ),
            false
        );
    }
}