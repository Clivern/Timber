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
 * Messages Requests Services
 *
 * @since 1.0
 */
class MessagesRequests extends \Timber\Services\Base {

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
	 * Add New Message
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function addMessage()
	{

		$message_data = $this->timber->validator->clear(array(
			'msg_receiver_id' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Please select message receiver.'),
					'vint' => $this->timber->translator->trans('Please select message receiver.')
				),
			),
			'msg_subject' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vstrlenbetween:2,140',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('The message subject must not be empty.'),
					'vstrlenbetween' => $this->timber->translator->trans('The message subject lenght is invalid.')
				)
			),
			'msg_content' => array(
				'req' => 'post',
				'sanit' => '',
				'valid' => 'vnotempty&vstrlenbetween:2,15000',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('The message content must not be empty.'),
					'vstrlenbetween' => $this->timber->translator->trans('The message content lenght is invalid.')
				),
			),
			'msg_attachments' => array(
				'req' => 'post',
				'sanit' => 'sfiles',
				'valid' => 'vnotempty&vfiles:' . implode(',' , unserialize($this->timber->config('_allowed_upload_extensions'))),
				'default' => '',
				'errors' => array(),
			)
		));

		if( true === $message_data['error_status'] ){
			$this->response['data'] = $message_data['error_text'];
			return false;
		}


		$new_message_data = array();
		$new_message_data['sender_id'] = $this->timber->security->getId();
		$new_message_data['receiver_id'] = $message_data['msg_receiver_id']['value'];
		$new_message_data['parent_id'] = '0';
		$new_message_data['rece_cat'] = 'index';
		$new_message_data['send_cat'] = 'sent';
		$new_message_data['rece_hide'] = 'off';
		$new_message_data['send_hide'] = 'off';
		$new_message_data['subject'] = $message_data['msg_subject']['value'];
		$new_message_data['content'] = $message_data['msg_content']['value'];
		$new_message_data['attach'] = 'off';
		$new_message_data['created_at'] = $this->timber->time->getCurrentDate(true);
		$new_message_data['updated_at'] = $this->timber->time->getCurrentDate(true);
		$new_message_data['sent_at'] = $this->timber->time->getCurrentDate(true);

		# Add Attachments
		$msg_attachments = $message_data['msg_attachments']['value'];

		$files_ids = array();
		if( (is_array($msg_attachments)) && (count($msg_attachments) > 0) ){
			foreach( $msg_attachments as $msg_attachment ) {
				$msg_attachment = explode('--||--', $msg_attachment);
				$files_ids[] = $this->timber->file_model->addFile(array(
					'title' => $msg_attachment[1],
					'hash' => $msg_attachment[0],
					'owner_id' => $this->timber->security->getId(),
					'description' => "Message Attachments",
					'storage' => 2,
					'type' => pathinfo($msg_attachment[1], PATHINFO_EXTENSION),
					'uploaded_at' => $this->timber->time->getCurrentDate(true),
				));
			}
			$new_message_data['attach'] = 'on';
		}

		$message_id = $this->timber->message_model->addMessage($new_message_data);

		# Add Metas
		$meta_status = true;

		$meta_status &= (boolean) $this->timber->meta_model->addMeta(array(
			'rec_id' => $message_id,
			'rec_type' => 5,
			'me_key' => 'message_attachments_data',
			'me_value' => serialize($files_ids),
		));

        $this->timber->notify->setMailerCron(array(
            'method_name' => 'messageEmailNotifier',
            'to_user_id' => $new_message_data['receiver_id'],
            'from_user_id' => $new_message_data['sender_id'],
        ));

        $this->timber->notify->increment('messages_notif', $new_message_data['receiver_id']);
        $this->timber->notify->increment('messages_index_notif', $new_message_data['receiver_id']);

		if( $message_id && $meta_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Message sent successfully.');
			$this->response['next_link'] = $this->timber->config('request_url') . '/admin/messages/view/' . $message_id;
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}

	/**
	 * Reply To Message
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function addReply()
	{

		$message_data = $this->timber->validator->clear(array(
			'msg_receiver_id' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Please select message receiver.'),
					'vint' => $this->timber->translator->trans('Please select message receiver.')
				),
			),
			'msg_parent_id' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Invalid Request.'),
					'vint' => $this->timber->translator->trans('Invalid Request.')
				),
			),
			'msg_content' => array(
				'req' => 'post',
				'sanit' => '',
				'valid' => 'vnotempty&vstrlenbetween:2,15000',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('The message content must not be empty.'),
					'vstrlenbetween' => $this->timber->translator->trans('The message content lenght is invalid.')
				),
			),
			'msg_attachments' => array(
				'req' => 'post',
				'sanit' => 'sfiles',
				'valid' => 'vnotempty&vfiles:' . implode(',' , unserialize($this->timber->config('_allowed_upload_extensions'))),
				'default' => '',
				'errors' => array(),
			),
		));

		if( true === $message_data['error_status'] ){
			$this->response['data'] = $message_data['error_text'];
			return false;
		}

		$new_message_data = array();
		$new_message_data['sender_id'] = $this->timber->security->getId();
		$new_message_data['receiver_id'] = $message_data['msg_receiver_id']['value'];
		$new_message_data['parent_id'] = $message_data['msg_parent_id']['value'];
		$new_message_data['rece_cat'] = '';
		$new_message_data['send_cat'] = '';
		$new_message_data['rece_hide'] = 'off';
		$new_message_data['send_hide'] = 'off';
		$new_message_data['subject'] = '';
		$new_message_data['content'] = $message_data['msg_content']['value'];
		$new_message_data['attach'] = 'off';
		$new_message_data['created_at'] = $this->timber->time->getCurrentDate(true);
		$new_message_data['updated_at'] = $this->timber->time->getCurrentDate(true);
		$new_message_data['sent_at'] = $this->timber->time->getCurrentDate(true);

		# Add Attachments
		$msg_attachments = $message_data['msg_attachments']['value'];

		$files_ids = array();
		if( (is_array($msg_attachments)) && (count($msg_attachments) > 0) ){
			foreach( $msg_attachments as $msg_attachment ) {
				$msg_attachment = explode('--||--', $msg_attachment);
				$files_ids[] = $this->timber->file_model->addFile(array(
					'title' => $msg_attachment[1],
					'hash' => $msg_attachment[0],
					'owner_id' => $this->timber->security->getId(),
					'description' => "Message Attachments",
					'storage' => 2,
					'type' => pathinfo($msg_attachment[1], PATHINFO_EXTENSION),
					'uploaded_at' => $this->timber->time->getCurrentDate(true),
				));
			}
			$new_message_data['attach'] = 'on';
		}

		$message_id = $this->timber->message_model->addMessage($new_message_data);

		$meta_status = true;

		$meta_status &= (boolean) $this->timber->meta_model->addMeta(array(
			'rec_id' => $message_id,
			'rec_type' => 5,
			'me_key' => 'message_attachments_data',
			'me_value' => serialize($files_ids),
		));

        $this->timber->notify->increment('messages_notif', $new_message_data['receiver_id']);
        $old_message_data = $this->timber->message_model->getMessageByMultiple( array( 'ms_id' => $message_data['msg_parent_id']['value'], 'parent_id' => '0' ) );
        if( (false !== $old_message_data) && (is_object($old_message_data)) ){

            $old_message_data = $old_message_data->as_array();

            if( ($old_message_data['sender_id'] == $new_message_data['receiver_id']) && ($old_message_data['send_cat'] == 'index') ){
                $this->timber->notify->increment('messages_index_notif', $new_message_data['receiver_id']);
            }
            if( ($old_message_data['sender_id'] == $new_message_data['receiver_id']) && ($old_message_data['send_cat'] == 'favorite') ){
                $this->timber->notify->increment('messages_favorite_notif', $new_message_data['receiver_id']);
            }
            if( ($old_message_data['sender_id'] == $new_message_data['receiver_id']) && ($old_message_data['send_cat'] == 'sent') ){
                $this->timber->notify->increment('messages_sent_notif', $new_message_data['receiver_id']);
            }

            if( ($old_message_data['receiver_id'] == $new_message_data['receiver_id']) && ($old_message_data['rece_cat'] == 'index') ){
                $this->timber->notify->increment('messages_index_notif', $new_message_data['receiver_id']);
            }
            if( ($old_message_data['receiver_id'] == $new_message_data['receiver_id']) && ($old_message_data['rece_cat'] == 'favorite') ){
                $this->timber->notify->increment('messages_favorite_notif', $new_message_data['receiver_id']);
            }
            if( ($old_message_data['receiver_id'] == $new_message_data['receiver_id']) && ($old_message_data['rece_cat'] == 'sent') ){
                $this->timber->notify->increment('messages_sent_notif', $new_message_data['receiver_id']);
            }
        }

		if( $message_id && $meta_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Reply sent successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}

	/**
	 * Mark Message
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function markMessage()
	{
		$message_id = ( (isset($_POST['message_id'])) && ((boolean) filter_var($_POST['message_id'], FILTER_VALIDATE_INT)) ) ? filter_var($_POST['message_id'], FILTER_SANITIZE_NUMBER_INT) : false;
		$action = ( (isset($_POST['action'])) && (in_array($_POST['action'], array('delete', 'trash','untrash', 'favorite', 'unfavorite'))) ) ? $_POST['action'] : false;

		$message_data = $this->timber->message_model->getMessageById( $message_id );
		if( (false === $message_data) || !(is_object($message_data)) ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		// Get Message
		$message_data = $message_data->as_array();
		$user_id = $this->timber->security->getId();

		if( ($message_data['sender_id'] != $user_id) && ($message_data['receiver_id'] != $user_id) ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		$action_status = true;
		$new_message_data = array(
			'ms_id' => $message_data['ms_id']
		);

		if( 'delete' == $action ){

			if($message_data['receiver_id'] == $user_id){ $new_message_data['rece_hide'] = 'on'; }
			if($message_data['sender_id'] == $user_id){ $new_message_data['send_hide'] = 'on'; }

			$action_status &= (boolean) $this->timber->message_model->updateMessageById($new_message_data);
		}elseif ( 'trash' == $action ){

			if($message_data['receiver_id'] == $user_id){ $new_message_data['rece_cat'] = 'trash'; }
			if($message_data['sender_id'] == $user_id){ $new_message_data['send_cat'] = 'trash'; }

			$action_status &= (boolean) $this->timber->message_model->updateMessageById($new_message_data);
		}elseif ( 'untrash' == $action ){

			if($message_data['receiver_id'] == $user_id){ $new_message_data['rece_cat'] = 'index'; }
			if($message_data['sender_id'] == $user_id){ $new_message_data['send_cat'] = 'sent'; }

			$action_status &= (boolean) $this->timber->message_model->updateMessageById($new_message_data);
		}elseif ( 'favorite' == $action ){

			if($message_data['receiver_id'] == $user_id){ $new_message_data['rece_cat'] = 'favorite'; }
			if($message_data['sender_id'] == $user_id){ $new_message_data['send_cat'] = 'favorite'; }

			$action_status &= (boolean) $this->timber->message_model->updateMessageById($new_message_data);
		}elseif ( 'unfavorite' == $action ){

			if($message_data['receiver_id'] == $user_id){ $new_message_data['rece_cat'] = 'index'; }
			if($message_data['sender_id'] == $user_id){ $new_message_data['send_cat'] = 'sent'; }

			$action_status &= (boolean) $this->timber->message_model->updateMessageById($new_message_data);
		}

		if( $action_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Message updated successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}
}