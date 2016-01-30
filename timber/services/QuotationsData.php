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
 * Quotations Data Services
 *
 * @since 1.0
 */
class QuotationsData extends \Timber\Services\Base {


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
	 * Get Quotations Data
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function listData()
	{
		$data = array();

		# Bind Actions
		$data['add_quotation_action'] = $this->timber->config('request_url') . '/request/backend/ajax/quotations/add';
		$data['delete_quotation_action'] = $this->timber->config('request_url') . '/request/backend/ajax/quotations/delete';
		$data['mark_quotation_action'] = $this->timber->config('request_url') . '/request/backend/ajax/quotations/mark';

		$user_id = $this->timber->security->getId();
		$user = $this->timber->user_model->getUserById( $user_id );

		if( (false === $user) || !(is_object($user)) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/500' );
		}

		$user = $user->as_array();

		$quotations = $this->timber->quotation_model->getQuotations( false, false, 'desc', 'created_at' );

		$i = 0;
		$data['quotations'] = array();

		foreach ($quotations as $key => $quotation) {

			if( '3' == $user['access_rule'] ){

				$client_access = $this->timber->meta_model->getMetaByMultiple(array(
					'rec_id' => $quotation['qu_id'],
					'rec_type' => 7,
					'me_key' => 'quotation_available_access'
				));
				$clients = array();

				if( (false !== $client_access) && (is_object($client_access)) ){
					$client_access = $client_access->as_array();
					$clients = unserialize($client_access['me_value']);
				}

				if( !in_array($user_id, $clients) ){
					continue;
				}
			}

			$data['quotations'][$i]['qu_id'] = $quotation['qu_id'];
			$data['quotations'][$i]['title'] = $quotation['title'];
			$data['quotations'][$i]['reference'] = $quotation['reference'];
			$data['quotations'][$i]['ref_id'] = "QUO-" . str_pad($quotation['qu_id'], 8, '0', STR_PAD_LEFT);
			$data['quotations'][$i]['owner_id'] = $quotation['owner_id'];
			# $data['quotations'][$i]['terms'] = $quotation['terms'];
			$data['quotations'][$i]['created_at'] = $quotation['created_at'];
			$data['quotations'][$i]['updated_at'] = $quotation['updated_at'];

			$data['quotations'][$i]['submit_link'] = $this->timber->config('request_url') . '/admin/quotations/submit/' . $quotation['qu_id'];
			$data['quotations'][$i]['view_link'] = $this->timber->config('request_url') . '/admin/quotations/view/' . $quotation['qu_id'];
			$data['quotations'][$i]['trash_link'] = $this->timber->config('request_url') . '/request/backend/ajax/quotations/delete';

			$i += 1;
		}

		return $data;
	}

	/**
	 * Add Quotations Data
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function addData()
	{
		$data = array();

		# Bind Actions
		$data['add_quotation_action'] = $this->timber->config('request_url') . '/request/backend/ajax/quotations/add';
		$data['delete_quotation_action'] = $this->timber->config('request_url') . '/request/backend/ajax/quotations/delete';
		$data['mark_quotation_action'] = $this->timber->config('request_url') . '/request/backend/ajax/quotations/mark';

		return $data;
	}

	/**
	 * View Quotations Data
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function viewData($quotation_id)
	{
		$quotation_id = ( (boolean) filter_var($quotation_id, FILTER_VALIDATE_INT) ) ? filter_var($quotation_id, FILTER_SANITIZE_NUMBER_INT) : false;
		if( false === $quotation_id){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		$data = array();

		# Bind Actions
		$data['add_quotation_action'] = $this->timber->config('request_url') . '/request/backend/ajax/quotations/add';
		$data['delete_quotation_action'] = $this->timber->config('request_url') . '/request/backend/ajax/quotations/delete';
		$data['mark_quotation_action'] = $this->timber->config('request_url') . '/request/backend/ajax/quotations/mark';

		$quotation = $this->timber->quotation_model->getQuotationById( $quotation_id );

		if( (false === $quotation) || !(is_object($quotation)) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}
		$quotation = $quotation->as_array();

		$data['quotation_qu_id'] = $quotation['qu_id'];
		$data['quotation_title'] = $quotation['title'];

        if( !empty($data['quotation_title']) ){
            $data['site_sub_page'] = $data['quotation_title']  . " | ";
        }

		$data['quotation_reference'] = $quotation['reference'];
		$data['quotation_ref_id'] = "QUO-" . str_pad($quotation['qu_id'], 8, '0', STR_PAD_LEFT);
		$data['quotation_owner_id'] = $quotation['owner_id'];
		$data['quotation_terms'] = $quotation['terms'];
		$data['quotation_created_at'] = $quotation['created_at'];
		$data['quotation_updated_at'] = $quotation['updated_at'];

		$data['members_list'] = array();

		$users = $this->timber->user_model->getUsersBy( array('access_rule' => 3) );

		$i = 0;
		foreach ($users as $key => $user) {

			if( $user['access_rule'] != '3' ){
				continue;
			}

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

		$data['quotation_submissions'] = array();
		$submissions = $this->timber->meta_model->getMetasBy(array(
			'rec_id' => $quotation['qu_id'],
			'rec_type' => 7,
			'me_key' => 'quotation_submission'
		));

		$i = 0;
		foreach ($submissions as $key => $submission) {

			$submission['me_value'] = unserialize($submission['me_value']);
			$data['quotation_submissions'][$i]['id'] = $submission['me_id'];
			$data['quotation_submissions'][$i]['submitter'] = $this->timber->translator->trans("Not Exist");
			$data['quotation_submissions'][$i]['created_at'] = $submission['me_value']['created_at'];
			$data['quotation_submissions'][$i]['values'] = $submission['me_value']['values'];

			if( is_integer($submission['me_value']['submitter']) ){
				$submitter_data = $this->timber->user_model->getUserById( $submission['me_value']['submitter']);
				if( (false !== $submitter_data) && (is_object($submitter_data)) ){
					$submitter_data = $submitter_data->as_array();
					$data['quotation_submissions'][$i]['submitter'] = trim( $submitter_data['first_name'] . " " . $submitter_data['last_name'] );
				}
			}else{
				$data['quotation_submissions'][$i]['submitter'] = $submission['me_value']['submitter'];
			}

			$i += 1;
		}

		return $data;
	}

	/**
	 * Submit Quotations Data
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function submitData($quotation_id)
	{
		$quotation_id = ( (boolean) filter_var($quotation_id, FILTER_VALIDATE_INT) ) ? filter_var($quotation_id, FILTER_SANITIZE_NUMBER_INT) : false;
		if( false === $quotation_id){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		$data = array();

		$data['submit_quotation_action'] = $this->timber->config('request_url') . '/request/backend/ajax/quotations/submit';

		$user_id = $this->timber->security->getId();
		$user = $this->timber->user_model->getUserById( $user_id );

		if( (false === $user) || !(is_object($user)) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/500' );
		}

		$user = $user->as_array();

		$quotation = $this->timber->quotation_model->getQuotationById( $quotation_id );

		if( (false === $quotation) || !(is_object($quotation)) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		if( '3' == $user['access_rule'] ){

			$client_access = $this->timber->meta_model->getMetaByMultiple(array(
				'rec_id' => $quotation_id,
				'rec_type' => 7,
				'me_key' => 'quotation_available_access'
			));
			$clients = array();

			if( (false !== $client_access) && (is_object($client_access)) ){
				$client_access = $client_access->as_array();
				$clients = unserialize($client_access['me_value']);
			}

			if( !in_array($user_id, $clients) ){
				$this->timber->redirect( $this->timber->config('request_url') . '/404' );
			}
		}

		$quotation = $quotation->as_array();

		$data['quotation_qu_id'] = $quotation['qu_id'];
		$data['quotation_title'] = $quotation['title'];

        if( !empty($data['quotation_title']) ){
            $data['site_sub_page'] = $data['quotation_title']  . " | ";
        }

		$data['quotation_reference'] = $quotation['reference'];
		$data['quotation_ref_id'] = "QUO-" . str_pad($quotation['qu_id'], 8, '0', STR_PAD_LEFT);
		$data['quotation_owner_id'] = $quotation['owner_id'];
		$data['quotation_terms'] = unserialize($quotation['terms']);
		$data['quotation_created_at'] = $quotation['created_at'];
		$data['quotation_updated_at'] = $quotation['updated_at'];

		return $data;
	}

	/**
	 * Public Submit Quotations Data
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function publicSubmitData($quotation_id, $email)
	{
		$quotation_id = ( (boolean) filter_var($quotation_id, FILTER_VALIDATE_INT) ) ? filter_var($quotation_id, FILTER_SANITIZE_NUMBER_INT) : false;
		$email = ( (boolean) filter_var($email, FILTER_VALIDATE_EMAIL) ) ? filter_var($email, FILTER_SANITIZE_EMAIL) : false;

		if( (false === $quotation_id) || (false === $email) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		$data = array();

		$data['submit_quotation_action'] = $this->timber->config('request_url') . '/request/backend/ajax/quotations/pubsubmit';

		$quotation = $this->timber->quotation_model->getQuotationById( $quotation_id );

		if( (false === $quotation) || !(is_object($quotation)) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		$client_access = $this->timber->meta_model->getMetaByMultiple(array(
			'rec_id' => $quotation_id,
			'rec_type' => 7,
			'me_key' => 'quotation_available_access'
		));
		$clients = array();

		if( (false !== $client_access) && (is_object($client_access)) ){
			$client_access = $client_access->as_array();
			$clients = unserialize($client_access['me_value']);
		}

		if( !in_array($email, $clients) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		$quotation = $quotation->as_array();

		$data['quotation_qu_id'] = $quotation['qu_id'];
		$data['quotation_title'] = $quotation['title'];

        if( !empty($data['quotation_title']) ){
            $data['site_sub_page'] = $data['quotation_title']  . " | ";
        }

		$data['quotation_reference'] = $quotation['reference'];
		$data['quotation_ref_id'] = "QUO-" . str_pad($quotation['qu_id'], 8, '0', STR_PAD_LEFT);
		$data['quotation_owner_id'] = $quotation['owner_id'];
		$data['quotation_terms'] = unserialize($quotation['terms']);
		$data['quotation_created_at'] = $quotation['created_at'];
		$data['quotation_updated_at'] = $quotation['updated_at'];


		$data['submitter_email'] = $email;

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