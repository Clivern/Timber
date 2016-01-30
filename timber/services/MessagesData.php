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
 * Messages Data Services
 *
 * @since 1.0
 */
class MessagesData extends \Timber\Services\Base {

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
     * List Messages Data Binding
     *
     * @since 1.0
     * @access public
     * @return array
     */
    public function listData()
    {
        $data = array();

        $group = ( (isset($_GET['group'])) && (in_array($_GET['group'], array( 'index', 'favorite', 'sent', 'trash' ))) ) ? $_GET['group'] : 'index';
        $user_id = $this->timber->security->getId();

        # Bind Actions
        $data['add_msg_action'] = $this->timber->config('request_url') . '/request/backend/ajax/messages/add_message';
        $data['reply_msg_action'] = $this->timber->config('request_url') . '/request/backend/ajax/messages/reply_to_message';
        $data['mark_msg_action'] = $this->timber->config('request_url') . '/request/backend/ajax/messages/mark_message';

        $data['messages'] = array();

        $criteria = array(
            array( 'sender_id' => $user_id, 'parent_id' => '0' ),
            array( 'receiver_id' => $user_id, 'parent_id' => '0' )
        );

        $messages = $this->timber->message_model->getAnyMessagesBy($criteria, false, false, 'desc', 'created_at');

        $i = 0;
        foreach ($messages as $key => $message) {

            # DUMB message and its meta
            if( ($message['rece_hide'] == 'on') && ($message['send_hide'] == 'on') ){
                $this->timber->message_model->dumpMessage($message['ms_id']);
                $this->timber->message_model->dumpMessage(false, $message['ms_id']);
                $this->timber->meta_model->dumpMetas(false, $message['ms_id'], 5);
                continue;
            }

            if( ($message['rece_hide'] == 'on') && ( $user_id == $message['receiver_id'] ) ){ continue; }
            if( ($message['send_hide'] == 'on') && ( $user_id == $message['sender_id'] ) ){ continue; }

            if( ( 'index' == $group ) && ( ( $user_id != $message['receiver_id'] ) || ( 'trash' == $message['rece_cat'] ) ) ){ continue; }
            if( ( 'sent' == $group ) && ( ( $user_id != $message['sender_id'] ) || ( 'trash' == $message['send_cat'] ) ) ){ continue; }

            if( ( 'favorite' == $group ) && ( $user_id == $message['sender_id'] ) && ( 'favorite' != $message['send_cat'] ) ){ continue; }
            if( ( 'favorite' == $group ) && ( $user_id == $message['receiver_id'] ) && ( 'favorite' != $message['rece_cat'] ) ){ continue; }

            if( ( 'trash' == $group ) && ( $user_id == $message['sender_id'] ) && ( 'trash' != $message['send_cat'] ) ){ continue; }
            if( ( 'trash' == $group ) && ( $user_id == $message['receiver_id'] ) && ( 'trash' != $message['rece_cat'] ) ){ continue; }

            $sender_data = $this->timber->user_model->getUserById($message['sender_id']);
            $receiver_data = $this->timber->user_model->getUserById($message['receiver_id']);

            if( (false === $sender_data) || !(is_object($sender_data)) || (false === $receiver_data) || !(is_object($receiver_data)) ){
                $this->timber->message_model->dumpMessage($message['ms_id']);
                $this->timber->message_model->dumpMessage(false, $message['ms_id']);
                $this->timber->meta_model->dumpMetas(false, $message['ms_id'], 5);
                continue;
            }

            // Get Message
            $sender_data = $sender_data->as_array();
            $receiver_data = $receiver_data->as_array();

            $data['messages'][$i]['ms_id'] = $message['ms_id'];
            $data['messages'][$i]['sender_id'] = $message['sender_id'];
            $data['messages'][$i]['receiver_id'] = $message['receiver_id'];

            $data['messages'][$i]['sender_name'] = trim($sender_data['first_name'] . ' ' . $sender_data['last_name']);
            $data['messages'][$i]['receiver_name'] = trim($receiver_data['first_name'] . ' ' . $receiver_data['last_name']);
            $data['messages'][$i]['sender_receiver_name'] = ( $user_id == $message['sender_id'] ) ? $data['messages'][$i]['receiver_name'] : $data['messages'][$i]['sender_name'];
            $data['messages'][$i]['sender_receiver_job'] =  ( $user_id == $message['sender_id'] ) ? $receiver_data['job'] : $sender_data['job'];
            $data['messages'][$i]['sender_receiver_grav_id'] =  ( $user_id == $message['sender_id'] ) ? $receiver_data['grav_id'] : $sender_data['grav_id'];
            $data['messages'][$i]['sender_receiver_email'] =  ( $user_id == $message['sender_id'] ) ? $receiver_data['email'] : $sender_data['email'];

            if( ( $user_id == $message['sender_id'] ) && ( 'favorite' == $message['send_cat'] ) ){
                $data['messages'][$i]['favorite'] = 'on';
            }else{
                $data['messages'][$i]['favorite'] = 'off';
            }

            if( ( $user_id == $message['receiver_id'] ) && ( 'favorite' == $message['rece_cat'] ) ){
                $data['messages'][$i]['favorite'] = 'on';
            }else{
                $data['messages'][$i]['favorite'] = 'off';
            }

            $data['messages'][$i]['parent_id'] = $message['parent_id'];
            $data['messages'][$i]['rece_cat'] = $message['rece_cat'];
            $data['messages'][$i]['send_cat'] = $message['send_cat'];
            $data['messages'][$i]['rece_hide'] = $message['rece_hide'];
            $data['messages'][$i]['send_hide'] = $message['send_hide'];
            $data['messages'][$i]['subject'] = $message['subject'];
            $data['messages'][$i]['content'] = $message['content'];
            $data['messages'][$i]['attach'] = $message['attach'];
            $data['messages'][$i]['created_at'] = $message['created_at'];
            $data['messages'][$i]['updated_at'] = $message['updated_at'];
            $data['messages'][$i]['sent_at'] = $message['sent_at'];
            $data['messages'][$i]['attachments'] = array();
            $data['messages'][$i]['attachments_ids'] = array();

            if( $message['attach'] == 'on' ){
                $attachments_ids = $this->timber->meta_model->getMetaByMultiple(array(
                    'rec_id' => $message['ms_id'],
                    'rec_type' => 5,
                    'me_key' => 'message_attachments_data'
                ));
                $attachments_ids = $attachments_ids->as_array();
                $data['messages'][$i]['attachments_ids'] = unserialize($attachments_ids['me_value']);

                foreach ($data['messages'][$i]['attachments_ids'] as $key => $value) {
                    $file = $this->timber->file_model->getFileById($value);
                    $data['messages'][$i]['attachments'][] = $file->as_array();
                }
            }

            $i += 1;
        }

        $data['group'] = $group;
        $data['group_name'] = str_replace(
            array( 'index', 'favorite', 'sent', 'trash' ),
            array( $this->timber->translator->trans('Index'), $this->timber->translator->trans('Favorite'), $this->timber->translator->trans('Sent'), $this->timber->translator->trans('Trash') ),
            $group
        );
        $data['group_count'] = count($data['messages']);

        return $data;
    }

    /**
     * Add Message Data Bending
     *
     * @since 1.0
     * @access public
     * @return array
     */
    public function addData()
    {
        # Get & Bind Members
        $data = array();

        $user_id = $this->timber->security->getId();

        # Bind Actions
        $data['add_msg_action'] = $this->timber->config('request_url') . '/request/backend/ajax/messages/add_message';
        $data['reply_msg_action'] = $this->timber->config('request_url') . '/request/backend/ajax/messages/reply_to_message';
        $data['mark_msg_action'] = $this->timber->config('request_url') . '/request/backend/ajax/messages/mark_message';

        $data['members_list'] = array();

        $users = $this->timber->user_model->getUsers();

        $i = 0;

        foreach ($users as $key => $user) {

            # Exclude yourself
            if( ($user['us_id'] == $user_id) ){ continue; }
            if( ($this->timber->access->getRule() == 'client') && !($this->timber->access->checkPermission('message.admins')) && ($user['access_rule'] == '1') ){ continue; }
            if( ($this->timber->access->getRule() == 'client') && !($this->timber->access->checkPermission('message.staff')) && ($user['access_rule'] == '2') ){ continue; }
            if( ($this->timber->access->getRule() == 'client') && !($this->timber->access->checkPermission('message.clients')) && ($user['access_rule'] == '3') ){ continue; }

            $data['members_list'][$i]['us_id'] = $user['us_id'];
            $data['members_list'][$i]['user_name'] = $user['user_name'];
            $data['members_list'][$i]['first_name'] = $user['first_name'];
            $data['members_list'][$i]['last_name'] = $user['last_name'];
            $data['members_list'][$i]['full_name'] = trim($user['first_name'] . ' ' . $user['last_name']);
            $data['members_list'][$i]['access_rule'] = $user['access_rule'];
            $data['members_list'][$i]['status'] = $user['status'];
            $data['members_list'][$i]['grav_id'] = $user['grav_id'];
            $data['members_list'][$i]['email'] = $user['email'];

            $i += 1;
        }

        return $data;
    }

    /**
     * View Message Data Bending
     *
     * @since 1.0
     * @access public
     * @param integer $message_id
     * @return array
     */
    public function viewData($message_id)
    {
        $data = array();

        $user_id = $this->timber->security->getId();
        $message_id = ((boolean) filter_var($message_id, FILTER_VALIDATE_INT)) ? filter_var($message_id, FILTER_SANITIZE_NUMBER_INT) : false;

        if( $message_id === false ){
            $this->timber->redirect( $this->timber->config('request_url') . '/404' );
        }

        $message_data = $this->timber->message_model->getMessageByMultiple( array( 'ms_id' => $message_id, 'parent_id' => '0' ) );
        if( (false === $message_data) || !(is_object($message_data)) ){
            $this->timber->redirect( $this->timber->config('request_url') . '/404' );
        }

        // Get Message
        $message_data = $message_data->as_array();

        if( ($message_data['sender_id'] != $user_id) && ($message_data['receiver_id'] != $user_id) ){
            $this->timber->redirect( $this->timber->config('request_url') . '/404' );
        }
        if( (($message_data['receiver_id'] == $user_id) && ($message_data['rece_hide'] == 'on')) || (($message_data['sender_id'] == $user_id) && ($message_data['send_hide'] == 'on')) ){
            $this->timber->redirect( $this->timber->config('request_url') . '/404' );
        }

        $sender_data = $this->timber->user_model->getUserById($message_data['sender_id']);
        $receiver_data = $this->timber->user_model->getUserById($message_data['receiver_id']);

        if( (false === $sender_data) || !(is_object($sender_data)) || (false === $receiver_data) || !(is_object($receiver_data)) ){
            $this->timber->message_model->dumpMessage($message_data['ms_id']);
            $this->timber->message_model->dumpMessage(false, $message_data['ms_id']);
            $this->timber->meta_model->dumpMetas(false, $message_data['ms_id'], 5);
            $this->timber->redirect( $this->timber->config('request_url') . '/404' );
        }

        $data['sender_name'] = trim($sender_data['first_name'] . ' ' . $sender_data['last_name']);
        $data['receiver_name'] = trim($receiver_data['first_name'] . ' ' . $receiver_data['last_name']);

        $data['sender_grav_id'] = $sender_data['grav_id'];
        $data['receiver_grav_id'] = $receiver_data['grav_id'];
        $data['sender_email'] = $sender_data['email'];
        $data['receiver_email'] = $receiver_data['email'];


        $data['sender_receiver_name'] = ( $user_id == $message_data['sender_id'] ) ? $data['receiver_name'] : $data['sender_name'];
        $data['sender_receiver_id'] = ( $user_id == $message_data['sender_id'] ) ? $message_data['receiver_id'] : $message_data['sender_id'];


        $data['add_msg_action'] = $this->timber->config('request_url') . '/request/backend/ajax/messages/add_message';
        $data['reply_msg_action'] = $this->timber->config('request_url') . '/request/backend/ajax/messages/reply_to_message';
        $data['mark_msg_action'] = $this->timber->config('request_url') . '/request/backend/ajax/messages/mark_message';


        $data['ms_id'] = $message_data['ms_id'];
        $data['sender_id'] = $message_data['sender_id'];
        $data['receiver_id'] = $message_data['receiver_id'];
        $data['parent_id'] = $message_data['parent_id'];
        $data['subject'] = $message_data['subject'];

        if( !empty($data['subject']) ){
            $data['site_sub_page'] = $data['subject']  . " | ";
        }

        $data['rece_cat'] = $message_data['rece_cat'];
        $data['send_cat'] = $message_data['send_cat'];
        $data['rece_hide'] = $message_data['rece_hide'];
        $data['send_hide'] = $message_data['send_hide'];
        $data['content'] = $message_data['content'];
        $data['attach'] = $message_data['attach'];
        $data['created_at'] = $message_data['created_at'];
        $data['updated_at'] = $message_data['updated_at'];
        $data['sent_at'] = $message_data['sent_at'];
        $data['attachments'] = array();
        $data['attachments_count'] = 0;
        $data['attachments_ids'] = array();
        $data['replies'] = array();

        # Attachments
        if( $data['attach'] == 'on' ){
            $attachments_ids = $this->timber->meta_model->getMetaByMultiple(array(
                'rec_id' => $message_data['ms_id'],
                'rec_type' => 5,
                'me_key' => 'message_attachments_data'
            ));
            $attachments_ids = $attachments_ids->as_array();
            $data['attachments_ids'] = unserialize($attachments_ids['me_value']);

            foreach ($data['attachments_ids'] as $key => $value) {
                $file = $this->timber->file_model->getFileById($value);
                $data['attachments'][] = $file->as_array();
            }
            $data['attachments_count'] = count($data['attachments']);
        }


        // Get Replies
        $message_replies = $this->timber->message_model->getMessagesBy(array('parent_id' => $message_data['ms_id']), false, false, 'asc', 'created_at');

        $i = 0;
        foreach ($message_replies as $key => $reply) {

            $sender_data = $this->timber->user_model->getUserById($reply['sender_id']);
            $receiver_data = $this->timber->user_model->getUserById($reply['receiver_id']);

            if( (false === $sender_data) || !(is_object($sender_data)) || (false === $receiver_data) || !(is_object($receiver_data)) ){
                $this->timber->message_model->dumpMessage($reply['ms_id']);
                $this->timber->message_model->dumpMessage(false, $reply['ms_id']);
                $this->timber->meta_model->dumpMetas(false, $reply['ms_id'], 5);
                continue;
            }

            $data['replies'][$i]['sender_name'] = trim($sender_data['first_name'] . ' ' . $sender_data['last_name']);
            $data['replies'][$i]['receiver_name'] = trim($receiver_data['first_name'] . ' ' . $receiver_data['last_name']);

            $data['replies'][$i]['sender_grav_id'] = $sender_data['grav_id'];
            $data['replies'][$i]['receiver_grav_id'] = $receiver_data['grav_id'];
            $data['replies'][$i]['sender_email'] = $sender_data['email'];
            $data['replies'][$i]['receiver_email'] = $receiver_data['email'];

            $data['replies'][$i]['sender_receiver_name'] = ( $user_id == $message_data['sender_id'] ) ? $data['replies'][$i]['receiver_name'] : $data['replies'][$i]['sender_name'];

            $data['replies'][$i]['ms_id'] = $reply['ms_id'];
            $data['replies'][$i]['sender_id'] = $reply['sender_id'];
            $data['replies'][$i]['receiver_id'] = $reply['receiver_id'];
            $data['replies'][$i]['parent_id'] = $reply['parent_id'];
            $data['replies'][$i]['subject'] = $reply['subject'];
            $data['replies'][$i]['rece_cat'] = $reply['rece_cat'];
            $data['replies'][$i]['send_cat'] = $reply['send_cat'];
            $data['replies'][$i]['rece_hide'] = $reply['rece_hide'];
            $data['replies'][$i]['send_hide'] = $reply['send_hide'];
            $data['replies'][$i]['subject'] = $reply['subject'];
            $data['replies'][$i]['content'] = $reply['content'];
            $data['replies'][$i]['attach'] = $reply['attach'];
            $data['replies'][$i]['created_at'] = $reply['created_at'];
            $data['replies'][$i]['updated_at'] = $reply['updated_at'];
            $data['replies'][$i]['sent_at'] = $reply['sent_at'];
            $data['replies'][$i]['attachments'] = array();
            $data['replies'][$i]['attachments_count'] = 0;
            $data['replies'][$i]['attachments_ids'] = array();

            # Attachments
            if( $reply['attach'] == 'on' ){
                $attachments_ids = $this->timber->meta_model->getMetaByMultiple(array(
                    'rec_id' => $reply['ms_id'],
                    'rec_type' => 5,
                    'me_key' => 'message_attachments_data'
                ));

                $attachments_ids = $attachments_ids->as_array();
                $data['replies'][$i]['attachments_ids'] = unserialize($attachments_ids['me_value']);

                foreach ($data['replies'][$i]['attachments_ids'] as $key => $value) {
                    $file = $this->timber->file_model->getFileById($value);
                    $data['replies'][$i]['attachments'][] = $file->as_array();
                }
                $data['replies'][$i]['attachments_count'] = count($data['replies'][$i]['attachments']);
            }

            $i += 1;
        }

        return $data;
    }

    /**
     * Message Meta Data
     *
     * @since 1.0
     * @access public
     * @return array
     */
    public function messagesMeta()
    {
        $data = array();

        $group = ( (isset($_GET['group'])) && (in_array($_GET['group'], array( 'index', 'favorite', 'sent', 'trash' ))) ) ? $_GET['group'] : 'index';

        $data['inbox_alerts'] = 0;
        $data['favorite_alerts'] = 0;
        $data['sent_alerts'] = 0;

        $data['check_button'] = 'off';
        $data['star_button'] = 'off';
        $data['un_star_button'] = 'off';
        $data['trash_button'] = 'off';
        $data['un_trash_button'] = 'off';
        $data['delete_button'] = 'off';

        if( 'index' == $group ){

            $data['check_button'] = 'on';
            $data['star_button'] = 'on';
            $data['trash_button'] = 'on';

        }elseif( 'favorite' == $group ){

            $data['check_button'] = 'on';
            $data['un_star_button'] = 'on';
            $data['trash_button'] = 'on';

        }elseif( 'sent' == $group ){

            $data['check_button'] = 'on';
            $data['star_button'] = 'on';
            $data['trash_button'] = 'on';

        }elseif( 'trash' == $group ){

            $data['un_trash_button'] = 'on';
            $data['delete_button'] = 'on';

        }

        return $data;
    }

    /**
     * Get Uploader Info
     *
     * @since 1.0
     * @access public
     * @return array
     */
    public function uploaderInfo()
    {
        $data = array();

        $allowed_extensions = unserialize($this->timber->config('_allowed_upload_extensions'));
        $allowed_extensions = array_map('strtolower', $allowed_extensions);
        $allowed_extensions = implode(', ', $allowed_extensions);
        # $allowed_extensions = ".png, .jpg, .gif";
        $max_file_size = $this->timber->config('_max_upload_size');

        $data['page_uploaders'][] = array(
            'id' => 'uploadModal',
            'title' => $this->timber->translator->trans('Media Uploader'),
            'description' => sprintf( $this->timber->translator->trans('Allowed extensions (%1$s) and Max file size (%2$s MB).'), $allowed_extensions , $max_file_size ),
            'uploader_id' => 'dropzone_uploader',
            'uploader_class' => 'dropzone',
            'field_id' => 'uploader_files',
            'field_name' => 'uploaded_files',
        );

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