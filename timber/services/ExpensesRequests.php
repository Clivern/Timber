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
 * Expenses Requests Services
 *
 * @since 1.0
 */
class ExpensesRequests extends \Timber\Services\Base {

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
	 * Add New Expense
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function addExpense()
	{
		$expense_data = $this->timber->validator->clear(array(
			# 1-payment  2-refund
			'exp_status' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vinarray:1,2',
				'default' => '1',
				'errors' => array(),
			),
			'exp_issue_date' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vdate',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Issue date is invalid.'),
					'vdate' => $this->timber->translator->trans('Issue date is invalid.'),
				),
			),
			'exp_terms' => array(
				'req' => 'post',
				'sanit' => 'sexpen',
				'valid' => 'vexpen',
				'default' => '',
				'errors' => array(
					'vexpen' =>  $this->timber->translator->trans('Provided expense data are invalid.'),
				),
			),
			'exp_rec_id' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '0',
				'errors' => array(),
			),
			'exp_attachments' => array(
				'req' => 'post',
				'sanit' => 'sfiles',
				'valid' => 'vnotempty&vfiles:' . implode(',' , unserialize($this->timber->config('_allowed_upload_extensions'))),
				'default' => '',
				'errors' => array(),
			),

		));

		if( true === $expense_data['error_status'] ){
			$this->response['data'] = $expense_data['error_text'];
			return false;
		}

		if( !($this->timber->access->checkPermission('add.expenses')) ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		$new_expense_data = array();
		$new_expense_data['reference'] = $this->timber->invoice_model->newReference("EXP");
		$new_expense_data['owner_id'] = $this->timber->security->getId();
		$new_expense_data['client_id'] = 0;

		$new_expense_data['status'] = $expense_data['exp_status']['value'];
		$new_expense_data['type'] = 3;

		$new_expense_data['terms'] = serialize(array(
			'title' => $expense_data['exp_terms']['value']['title'],
			'description' => $expense_data['exp_terms']['value']['description'],
			'sub_total' => $expense_data['exp_terms']['value']['sub_total'],
			'tax_type' => $expense_data['exp_terms']['value']['tax_type'],
			'tax_select' => $expense_data['exp_terms']['value']['tax_select'],
			'tax_value' => $expense_data['exp_terms']['value']['tax_value'],
			'discount_type' => $expense_data['exp_terms']['value']['discount_type'],
			'discount_value' => $expense_data['exp_terms']['value']['discount_value'],
			'total_value' => $expense_data['exp_terms']['value']['total_value']
		));

		/*array(
			'title' => "",
			'description' => "",
			'sub_total' => '',
			'tax_type' => '',
			'tax_select' => '',
			'tax_value' => '',
			'discount_type' => '',
			'discount_value' => '',
			'total_value' => '',
		)*/

		$new_expense_data['tax'] = $expense_data['exp_terms']['value']['tax_value'];
		$new_expense_data['discount'] = $expense_data['exp_terms']['value']['discount_value'];
		$new_expense_data['total'] = $expense_data['exp_terms']['value']['total_value'];

		$new_expense_data['rec_type'] = (empty($expense_data['exp_rec_id']['value'])) ? 12 : 11;
		$new_expense_data['rec_id'] = $expense_data['exp_rec_id']['value'];

		$new_expense_data['due_date'] = '';
		$new_expense_data['issue_date'] = $expense_data['exp_issue_date']['value'];

		$new_expense_data['attach'] = 'off';
		$new_expense_data['created_at'] = $this->timber->time->getCurrentDate(true);
		$new_expense_data['updated_at'] = $this->timber->time->getCurrentDate(true);

		# Add Attachments
		$exp_attachments = $expense_data['exp_attachments']['value'];

		$files_ids = array();
		if( (is_array($exp_attachments)) && (count($exp_attachments) > 0) ){
			foreach( $exp_attachments as $exp_attachment ) {
				$exp_attachment = explode('--||--', $exp_attachment);
				$files_ids[] = $this->timber->file_model->addFile(array(
					'title' => $exp_attachment[1],
					'hash' => $exp_attachment[0],
					'owner_id' => $this->timber->security->getId(),
					'description' => "Invoice Attachments",
					'storage' => 2,
					'type' => pathinfo($exp_attachment[1], PATHINFO_EXTENSION),
					'uploaded_at' => $this->timber->time->getCurrentDate(true),
				));
			}
			$new_expense_data['attach'] = 'on';
		}

		$expense_id = $this->timber->invoice_model->addInvoice($new_expense_data);

		# Add Metas
		$meta_status = true;

		$meta_status &= (boolean) $this->timber->meta_model->addMeta(array(
			'rec_id' => $expense_id,
			'rec_type' => 3,
			'me_key' => 'invoice_attachments_data',
			'me_value' => serialize($files_ids),
		));

		if( $expense_id && $meta_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Expense created successfully.');
			$this->response['next_link'] = $this->timber->config('request_url') . '/admin/expenses/view/' . $expense_id;
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}

	/**
	 * Edit Expense
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function editExpense()
	{
		$expense_data = $this->timber->validator->clear(array(
			'exp_id' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Invalid Request.'),
					'vint' => $this->timber->translator->trans('Invalid Request.')
				),
			),
			# 1-payment  2-refund
			'exp_status' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vinarray:1,2',
				'default' => '1',
				'errors' => array(),
			),
			'exp_issue_date' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vdate',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Issue date is invalid.'),
					'vdate' => $this->timber->translator->trans('Issue date is invalid.'),
				),
			),
			'exp_terms' => array(
				'req' => 'post',
				'sanit' => 'sexpen',
				'valid' => 'vexpen',
				'default' => '',
				'errors' => array(
					'vexpen' =>  $this->timber->translator->trans('Provided expense data are invalid.'),
				),
			),
			'exp_rec_id' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '0',
				'errors' => array(),
			),
			'exp_attachments' => array(
				'req' => 'post',
				'sanit' => 'sfiles',
				'valid' => 'vnotempty&vfiles:' . implode(',' , unserialize($this->timber->config('_allowed_upload_extensions'))),
				'default' => '',
				'errors' => array(),
			),
			'exp_old_attachments' => array(
				'req' => 'post',
				'sanit' => 'sfilesids',
				'valid' => 'vfilesids',
				'default' => array(),
				'errors' => array(),
			),
		));

		if( true === $expense_data['error_status'] ){
			$this->response['data'] = $expense_data['error_text'];
			return false;
		}

		if( !($this->timber->access->checkPermission('edit.expenses')) ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		$expense = $this->timber->invoice_model->getInvoiceById($expense_data['exp_id']['value']);

		if( (false === $expense) || !(is_object($expense)) ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		$expense = $expense->as_array();

		if( 3 !=  $expense['type'] ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		$new_expense_data = array();
		$new_expense_data['in_id'] = $expense_data['exp_id']['value'];
		$new_expense_data['client_id'] = 0;
		$new_expense_data['status'] = $expense_data['exp_status']['value'];
		$new_expense_data['type'] = 3;

		$new_expense_data['terms'] = serialize(array(
			'title' => $expense_data['exp_terms']['value']['title'],
			'description' => $expense_data['exp_terms']['value']['description'],
			'sub_total' => $expense_data['exp_terms']['value']['sub_total'],
			'tax_type' => $expense_data['exp_terms']['value']['tax_type'],
			'tax_select' => $expense_data['exp_terms']['value']['tax_select'],
			'tax_value' => $expense_data['exp_terms']['value']['tax_value'],
			'discount_type' => $expense_data['exp_terms']['value']['discount_type'],
			'discount_value' => $expense_data['exp_terms']['value']['discount_value'],
			'total_value' => $expense_data['exp_terms']['value']['total_value']
		));

		/*array(
			'title' => "",
			'description' => "",
			'sub_total' => '',
			'tax_type' => '',
			'tax_select' => '',
			'tax_value' => '',
			'discount_type' => '',
			'discount_value' => '',
			'total_value' => '',
		)*/

		$new_expense_data['tax'] = $expense_data['exp_terms']['value']['tax_value'];
		$new_expense_data['discount'] = $expense_data['exp_terms']['value']['discount_value'];
		$new_expense_data['total'] = $expense_data['exp_terms']['value']['total_value'];


		# (8) subscription (11) project (12) none
		if( $expense['rec_type'] != 8 ){
			$new_expense_data['rec_type'] = (empty($expense_data['exp_rec_id']['value'])) ? 12 : 11;
			$new_expense_data['rec_id'] = $expense_data['exp_rec_id']['value'];
		}

		$new_expense_data['due_date'] = '';
		$new_expense_data['issue_date'] = $expense_data['exp_issue_date']['value'];

		$new_expense_data['attach'] = 'off';
		$new_expense_data['updated_at'] = $this->timber->time->getCurrentDate(true);

		# Add Attachments
		$exp_attachments = $expense_data['exp_attachments']['value'];

		$files_ids = array();
		if( $expense['attach'] == 'on' ){
			$attachments_ids = $this->timber->meta_model->getMetaByMultiple(array(
				'rec_id' => $expense_data['exp_id']['value'],
				'rec_type' => 3,
				'me_key' => 'invoice_attachments_data'
			));
			if( (false !== $attachments_ids) && (is_object($attachments_ids)) ){
				$attachments_ids = $attachments_ids->as_array();
				$files_ids = unserialize($attachments_ids['me_value']);

				foreach ($files_ids as $key => $value) {
					if( !in_array( $value, $expense_data['exp_old_attachments']['value'] ) ){
						unset($files_ids[$key]);
					}
				}
			}
		}

		if( (is_array($exp_attachments)) && (count($exp_attachments) > 0) ){
			foreach( $exp_attachments as $exp_attachment ) {
				$exp_attachment = explode('--||--', $exp_attachment);
				$files_ids[] = $this->timber->file_model->addFile(array(
					'title' => $exp_attachment[1],
					'hash' => $exp_attachment[0],
					'owner_id' => $this->timber->security->getId(),
					'description' => "Invoice Attachments",
					'storage' => 2,
					'type' => pathinfo($exp_attachment[1], PATHINFO_EXTENSION),
					'uploaded_at' => $this->timber->time->getCurrentDate(true),
				));
			}
		}

		if( count($files_ids) > 0 ){
			$new_expense_data['attach'] = 'on';
		}

		$action_status = (boolean) $this->timber->invoice_model->updateInvoiceById($new_expense_data);


		$action_status &= (boolean) $this->timber->meta_model->updateMetaByMultiple(array(
			'rec_id' => $expense_data['exp_id']['value'],
			'rec_type' => 3,
			'me_key' => 'invoice_attachments_data',
			'me_value' => serialize($files_ids),
		));

		if( $action_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Expense updated successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}

	/**
	 * Delete Expense
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function deleteExpense()
	{
		$expense_id = ( (isset($_POST['expense_id'])) && ((boolean) filter_var($_POST['expense_id'], FILTER_VALIDATE_INT)) ) ? filter_var($_POST['expense_id'], FILTER_SANITIZE_NUMBER_INT) : false;

		if( !($this->timber->access->checkPermission('delete.expenses')) ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		if( $expense_id === false ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		$action_status  = (boolean) $this->timber->invoice_model->deleteInvoiceById($expense_id);
		$action_status &= (boolean) $this->timber->meta_model->dumpMetas(false, $expense_id, 3);

		if( $action_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Expense deleted successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}

	/**
	 * Mark Expense
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function markExpense()
	{
		/*
		$expense_id = ( (isset($_POST['expense_id'])) && ((boolean) filter_var($_POST['expense_id'], FILTER_VALIDATE_INT)) ) ? filter_var($_POST['expense_id'], FILTER_SANITIZE_NUMBER_INT) : false;
		$action = ( (isset($_POST['action'])) && (in_array($_POST['action'], array('test'))) ) ? $_POST['action'] : false;

		if( !($this->timber->access->checkPermission('edit.expenses')) ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		if( 'test' == $action ){
			$action_status = (boolean) $this->timber->invoice_model->updateInvoiceById(array(
				'in_id' => $expense_id,
				'status' => '1'
			));
		}

		if( $action_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Expense updated successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
		*/
	}
}