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
 * Expenses Data Services
 *
 * @since 1.0
 */
class ExpensesData extends \Timber\Services\Base {

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
	 * Get Expenses Data
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function listData()
	{

		$data = array();

		# Bind Actions
		$data['add_expense_action'] = $this->timber->config('request_url') . '/request/backend/ajax/expenses/add';
		$data['edit_expense_action'] = $this->timber->config('request_url') . '/request/backend/ajax/expenses/edit';
		$data['delete_expense_action'] = $this->timber->config('request_url') . '/request/backend/ajax/expenses/delete';
		$data['mark_expense_action'] = $this->timber->config('request_url') . '/request/backend/ajax/expenses/mark';
		$data['site_currency'] = $this->timber->config('_site_currency_symbol');

		$user_id = $this->timber->security->getId();
		$user = $this->timber->user_model->getUserById( $user_id );

		if( (false === $user) || !(is_object($user)) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/500' );
		}

		$user = $user->as_array();

		$expenses = $this->timber->invoice_model->getInvoicesBy( array('type' => 3), false, false, 'desc', 'created_at' );

		$i = 0;
		$data['expenses'] = array();

		foreach ($expenses as $key => $expense) {

			$data['expenses'][$i]['in_id'] = $expense['in_id'];
			$data['expenses'][$i]['reference'] = $expense['reference'];
			$data['expenses'][$i]['ref_id'] = "EXP-" . str_pad($expense['in_id'], 8, '0', STR_PAD_LEFT);
			$data['expenses'][$i]['owner_id'] = $expense['owner_id'];
			$data['expenses'][$i]['client_id'] = $expense['client_id'];
			$data['expenses'][$i]['status'] = $expense['status'];
			$data['expenses'][$i]['nice_status'] = str_replace(array('1','2'), array($this->timber->translator->trans('Payment'), $this->timber->translator->trans('Refund')), $expense['status']);
			$data['expenses'][$i]['type'] = $expense['type'];
			$data['expenses'][$i]['terms'] = unserialize($expense['terms']);
			$data['expenses'][$i]['tax'] = $expense['tax'];
			$data['expenses'][$i]['discount'] = $expense['discount'];
			$data['expenses'][$i]['total'] = $expense['total'];
			$data['expenses'][$i]['attach'] = $expense['attach'];
			$data['expenses'][$i]['rec_type'] = $expense['rec_type'];
			$data['expenses'][$i]['rec_id'] = $expense['rec_id'];
			$data['expenses'][$i]['due_date'] = $expense['due_date'];
			$data['expenses'][$i]['issue_date'] = $expense['issue_date'];
			$data['expenses'][$i]['created_at'] = $expense['created_at'];
			$data['expenses'][$i]['updated_at'] = $expense['updated_at'];
			$data['expenses'][$i]['edit_link'] = $this->timber->config('request_url') . '/admin/expenses/edit/' . $expense['in_id'];
			$data['expenses'][$i]['view_link'] = $this->timber->config('request_url') . '/admin/expenses/view/' . $expense['in_id'];
			$data['expenses'][$i]['trash_link'] = $this->timber->config('request_url') . '/request/backend/ajax/expenses/delete';

			$i += 1;
		}

		return $data;


	}

	/**
	 * Add Expenses Data
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function addData()
	{

		$data = array();

		# Bind Actions
		$data['add_expense_action'] = $this->timber->config('request_url') . '/request/backend/ajax/expenses/add';
		$data['edit_expense_action'] = $this->timber->config('request_url') . '/request/backend/ajax/expenses/edit';
		$data['delete_expense_action'] = $this->timber->config('request_url') . '/request/backend/ajax/expenses/delete';
		$data['mark_expense_action'] = $this->timber->config('request_url') . '/request/backend/ajax/expenses/mark';
		$data['site_currency'] = $this->timber->config('_site_currency_symbol');

		$user_id = $this->timber->security->getId();

		$data['projects_list'] = array();
		$data['taxes_list'] = unserialize($this->timber->config('_site_tax_rates'));

		$projects = $this->timber->project_model->getProjects();

		$i = 0;
		foreach( $projects as $key => $project ) {

			$data['projects_list'][$i]['pr_id'] = $project['pr_id'];
			$data['projects_list'][$i]['title'] = $project['title'];
			$data['projects_list'][$i]['reference'] = $project['reference'];

			$i += 1;
		}

		return $data;
	}

	/**
	 * Edit Expenses Data
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function editData($expense_id)
	{

		$expense_id = ( (boolean) filter_var($expense_id, FILTER_VALIDATE_INT) ) ? filter_var($expense_id, FILTER_SANITIZE_NUMBER_INT) : false;
		if( false === $expense_id){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}
		$data = array();

		# Bind Actions
		$data['add_expense_action'] = $this->timber->config('request_url') . '/request/backend/ajax/expenses/add';
		$data['edit_expense_action'] = $this->timber->config('request_url') . '/request/backend/ajax/expenses/edit';
		$data['delete_expense_action'] = $this->timber->config('request_url') . '/request/backend/ajax/expenses/delete';
		$data['mark_expense_action'] = $this->timber->config('request_url') . '/request/backend/ajax/expenses/mark';
		$data['site_currency'] = $this->timber->config('_site_currency_symbol');

		$user_id = $this->timber->security->getId();
		$expense = $this->timber->invoice_model->getInvoiceByMultiple( array('type' => 3, 'in_id' => $expense_id) );

		if( (false === $expense) || !(is_object($expense)) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		$expense = $expense->as_array();

		$data['expense_in_id'] = $expense['in_id'];
		$data['expense_reference'] = $expense['reference'];
		$data['expense_ref_id'] = "EXP-" . str_pad($expense['in_id'], 8, '0', STR_PAD_LEFT);

        if( !empty($data['expense_ref_id']) ){
            $data['site_sub_page'] = $data['expense_ref_id']  . " | ";
        }

		$data['expense_owner_id'] = $expense['owner_id'];
		$data['expense_client_id'] = $expense['client_id'];
		$data['expense_status'] = $expense['status'];
		$data['expense_nice_status'] = str_replace(array('1','2'), array($this->timber->translator->trans('Payment'), $this->timber->translator->trans('Refund')), $expense['status']);
		$data['expense_type'] = $expense['type'];
		$data['expense_terms'] = unserialize($expense['terms']);
		$data['expense_tax'] = $expense['tax'];
		$data['expense_discount'] = $expense['discount'];
		$data['expense_total'] = $expense['total'];
		$data['expense_attach'] = $expense['attach'];
		$data['expense_rec_type'] = $expense['rec_type'];
		$data['expense_rec_id'] = $expense['rec_id'];
		$data['expense_due_date'] = $expense['due_date'];
		$data['expense_issue_date'] = $expense['issue_date'];
		$data['expense_created_at'] = $expense['created_at'];
		$data['expense_updated_at'] = $expense['updated_at'];

		$data['expense_attachments'] = array();
		$data['expense_attachments_ids'] = array();
		$data['expense_attachments_count'] = 0;

		# Attachments
		if( $expense['attach'] == 'on' ){
			$attachments_ids = $this->timber->meta_model->getMetaByMultiple(array(
				'rec_id' => $expense['in_id'],
				'rec_type' => 3,
				'me_key' => 'invoice_attachments_data'
			));

			if( (false !== $attachments_ids) && (is_object($attachments_ids)) ){
				$attachments_ids = $attachments_ids->as_array();
				$data['expense_attachments_ids'] = unserialize($attachments_ids['me_value']);

				foreach ($data['expense_attachments_ids'] as $key => $value) {
					$file = $this->timber->file_model->getFileById($value);
					$data['expense_attachments'][] = $file->as_array();
				}
				$data['expense_attachments_count'] = count($data['expense_attachments']);
			}
		}
		$data['expense_attachments_ids'] = implode(',', $data['expense_attachments_ids']);


		$data['projects_list'] = array();
		$data['taxes_list'] = unserialize($this->timber->config('_site_tax_rates'));

		$projects = $this->timber->project_model->getProjects();

		$i = 0;
		foreach( $projects as $key => $project ) {

			$data['projects_list'][$i]['pr_id'] = $project['pr_id'];
			$data['projects_list'][$i]['title'] = $project['title'];
			$data['projects_list'][$i]['reference'] = $project['reference'];

			$i += 1;
		}

		return $data;
	}

	/**
	 * View Expenses Data
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function viewData($expense_id)
	{

		$expense_id = ( (boolean) filter_var($expense_id, FILTER_VALIDATE_INT) ) ? filter_var($expense_id, FILTER_SANITIZE_NUMBER_INT) : false;
		if( false === $expense_id){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		$data = array();

		# Bind Actions
		$data['add_expense_action'] = $this->timber->config('request_url') . '/request/backend/ajax/expenses/add';
		$data['edit_expense_action'] = $this->timber->config('request_url') . '/request/backend/ajax/expenses/edit';
		$data['delete_expense_action'] = $this->timber->config('request_url') . '/request/backend/ajax/expenses/delete';
		$data['mark_expense_action'] = $this->timber->config('request_url') . '/request/backend/ajax/expenses/mark';
		$data['site_currency'] = $this->timber->config('_site_currency_symbol');

		$user_id = $this->timber->security->getId();
		$expense = $this->timber->invoice_model->getInvoiceByMultiple( array('type' => 3, 'in_id' => $expense_id) );

		if( (false === $expense) || !(is_object($expense)) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}
		$expense = $expense->as_array();

		$data['expense_in_id'] = $expense['in_id'];
		$data['expense_reference'] = $expense['reference'];
		$data['expense_ref_id'] = "EXP-" . str_pad($expense['in_id'], 8, '0', STR_PAD_LEFT);

        if( !empty($data['expense_ref_id']) ){
            $data['site_sub_page'] = $data['expense_ref_id']  . " | ";
        }

		$data['expense_owner_id'] = $expense['owner_id'];
		$data['expense_client_id'] = $expense['client_id'];
		$data['expense_status'] = $expense['status'];
		$data['expense_nice_status'] = str_replace(array('1','2'), array($this->timber->translator->trans('Payment'), $this->timber->translator->trans('Refund')), $expense['status']);
		$data['expense_type'] = $expense['type'];
		$data['expense_terms'] = unserialize($expense['terms']);
		$data['expense_tax'] = $expense['tax'];
		$data['expense_discount'] = $expense['discount'];

		$data['expense_tax_currency'] = ($data['expense_terms']['tax_type'] == 'percent') ? "%" : $this->timber->config('_site_currency_symbol');
		$data['expense_discount_currency'] = ($data['expense_terms']['discount_type'] == 'percent') ? "%" : $this->timber->config('_site_currency_symbol');

		$data['expense_total'] = $expense['total'];
		$data['expense_attach'] = $expense['attach'];
		$data['expense_rec_type'] = $expense['rec_type'];
		$data['expense_rec_id'] = $expense['rec_id'];
		$data['expense_due_date'] = $expense['due_date'];
		$data['expense_issue_date'] = $expense['issue_date'];
		$data['expense_created_at'] = $expense['created_at'];
		$data['expense_updated_at'] = $expense['updated_at'];

		$data['expense_edit_link'] = $this->timber->config('request_url') . '/admin/expenses/edit/' . $expense['in_id'];
		$data['expense_view_link'] = $this->timber->config('request_url') . '/admin/expenses/view/' . $expense['in_id'];

		$data['expense_attachments'] = array();
		$data['expense_attachments_ids'] = array();
		$data['expense_attachments_count'] = 0;

		# Attachments
		if( $expense['attach'] == 'on' ){
			$attachments_ids = $this->timber->meta_model->getMetaByMultiple(array(
				'rec_id' => $expense['in_id'],
				'rec_type' => 3,
				'me_key' => 'invoice_attachments_data'
			));

			if( (false !== $attachments_ids) && (is_object($attachments_ids)) ){
				$attachments_ids = $attachments_ids->as_array();
				$data['expense_attachments_ids'] = unserialize($attachments_ids['me_value']);

				foreach ($data['expense_attachments_ids'] as $key => $value) {
					$file = $this->timber->file_model->getFileById($value);
					$data['expense_attachments'][] = $file->as_array();
				}
				$data['expense_attachments_count'] = count($data['expense_attachments']);
			}
		}
		$data['expense_attachments_ids'] = implode(',', $data['expense_attachments_ids']);

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