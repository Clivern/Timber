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
 * Estimates Requests Services
 *
 * @since 1.0
 */
class EstimatesRequests extends \Timber\Services\Base {

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
	 * Add New Estimate
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function addEstimate()
	{

		$estimate_data = $this->timber->validator->clear(array(
			'est_client_id' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Please select client.'),
					'vint' => $this->timber->translator->trans('Please select client.')
				),
			),
			# 1-opened  2-sent to client  3-accepted from client  4-rejected from client  5-invoiced  6-closed
			'est_status' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vinarray:1,2,3,4,5,6',
				'default' => '1',
				'errors' => array(),
			),
			'est_due_date' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vdate',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Due date is invalid.'),
					'vdate' => $this->timber->translator->trans('Due date is invalid.'),
				),
			),
			'est_issue_date' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vdate',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Issue date is invalid.'),
					'vdate' => $this->timber->translator->trans('Issue date is invalid.'),
				),
			),
			'est_terms' => array(
				'req' => 'post',
				'sanit' => 'sestim',
				'valid' => 'vestim',
				'default' => '',
				'errors' => array(
					'vinvs' =>  $this->timber->translator->trans('Invalid estimate items detected or estimate overall data is invalid.'),
				),
			),
			'est_rec_id' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '0',
				'errors' => array(),
			),
			'est_attachments' => array(
				'req' => 'post',
				'sanit' => 'sfiles',
				'valid' => 'vnotempty&vfiles:' . implode(',' , unserialize($this->timber->config('_allowed_upload_extensions'))),
				'default' => '',
				'errors' => array(),
			),
		));

		if( true === $estimate_data['error_status'] ){
			$this->response['data'] = $estimate_data['error_text'];
			return false;
		}

		if( !($this->timber->access->checkPermission('add.estimate')) ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		if( !($estimate_data['est_due_date']['value'] >= $estimate_data['est_issue_date']['value']) ){
			$this->response['data'] = $this->timber->translator->trans('Due date must not be less than issue date.');
			return false;
		}

		$new_estimate_data = array();
		$new_estimate_data['reference'] = $this->timber->invoice_model->newReference("EST");
		$new_estimate_data['owner_id'] = $this->timber->security->getId();
		$new_estimate_data['client_id'] = $estimate_data['est_client_id']['value'];
		$new_estimate_data['status'] = $estimate_data['est_status']['value'];

		$new_estimate_data['type'] = 2;

		$new_estimate_data['terms'] = serialize(array(
			'notes' => $estimate_data['est_terms']['value']['notes'],
			'items' => $estimate_data['est_terms']['value']['items'],
			'overall' =>  $estimate_data['est_terms']['value']['overall']
		));

		/*array(
			'notes' => "",
			'items' => array(
				array( 'item_title', 'item_description', 'item_quantity', 'item_unit_price', 'item_sub_total' ),
				array( 'item_title', 'item_description', 'item_quantity', 'item_unit_price', 'item_sub_total' ),
				......
			)
			'overall' => array(
				'sub_total' => '',
				'tax_type' => '',
				'tax_value' => '',
				'discount_type' => '',
				'discount_value' => '',
				'total_value' => '',
				'paid_value' =>  '',
			)
		)*/

		$new_estimate_data['tax'] = $estimate_data['est_terms']['value']['overall']['tax_value'];
		$new_estimate_data['discount'] = $estimate_data['est_terms']['value']['overall']['discount_value'];
		$new_estimate_data['total'] = $estimate_data['est_terms']['value']['overall']['total_value'];

		# (8) subscription (11) project (12) none
		$new_estimate_data['rec_type'] = (empty($estimate_data['est_rec_id']['value'])) ? 12 : 11;
		$new_estimate_data['rec_id'] = $estimate_data['est_rec_id']['value'];

		$new_estimate_data['due_date'] = $estimate_data['est_due_date']['value'];
		$new_estimate_data['issue_date'] = $estimate_data['est_issue_date']['value'];

		$new_estimate_data['attach'] = 'off';
		$new_estimate_data['created_at'] = $this->timber->time->getCurrentDate(true);
		$new_estimate_data['updated_at'] = $this->timber->time->getCurrentDate(true);

		# Add Attachments
		$est_attachments = $estimate_data['est_attachments']['value'];

		$files_ids = array();
		if( (is_array($est_attachments)) && (count($est_attachments) > 0) ){
			foreach( $est_attachments as $est_attachment ) {
				$est_attachment = explode('--||--', $est_attachment);
				$files_ids[] = $this->timber->file_model->addFile(array(
					'title' => $est_attachment[1],
					'hash' => $est_attachment[0],
					'owner_id' => $this->timber->security->getId(),
					'description' => "Invoice Attachments",
					'storage' => 2,
					'type' => pathinfo($est_attachment[1], PATHINFO_EXTENSION),
					'uploaded_at' => $this->timber->time->getCurrentDate(true),
				));
			}
			$new_estimate_data['attach'] = 'on';
		}

		$estimate_id = $this->timber->invoice_model->addInvoice($new_estimate_data);

		# Add Metas
		$meta_status = true;

		$meta_status &= (boolean) $this->timber->meta_model->addMeta(array(
			'rec_id' => $estimate_id,
			'rec_type' => 3,
			'me_key' => 'invoice_attachments_data',
			'me_value' => serialize($files_ids),
		));

		# Estimate Notification
		$this->timber->notify->increment('estimates_notif', $new_estimate_data['client_id']);
		$this->timber->notify->setMailerCron(array(
			'method_name' => 'newEstimateEmailNotifier',
			'est_id' => $estimate_id,
		));

		if( $estimate_id && $meta_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Estimate created successfully.');
			$this->response['next_link'] = $this->timber->config('request_url') . '/admin/estimates/view/' . $estimate_id;
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}

	/**
	 * Edit Estimate
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function editEstimate()
	{
		$estimate_data = $this->timber->validator->clear(array(
			'est_id' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Invalid Request.'),
					'vint' => $this->timber->translator->trans('Invalid Request.')
				),
			),
			'est_client_id' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Please select client.'),
					'vint' => $this->timber->translator->trans('Please select client.')
				),
			),
			# 1-opened  2-sent to client  3-accepted from client  4-rejected from client  5-invoiced  6-closed
			'est_status' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vinarray:1,2,3,4,5,6',
				'default' => '1',
				'errors' => array(),
			),
			'est_due_date' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vdate',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Due date is invalid.'),
					'vdate' => $this->timber->translator->trans('Due date is invalid.'),
				),
			),
			'est_issue_date' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vdate',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Issue date is invalid.'),
					'vdate' => $this->timber->translator->trans('Issue date is invalid.'),
				),
			),
			'est_terms' => array(
				'req' => 'post',
				'sanit' => 'sestim',
				'valid' => 'vestim',
				'default' => '',
				'errors' => array(
					'vinvs' =>  $this->timber->translator->trans('Invalid estimate items detected or estimate overall data is invalid.'),
				),
			),
			'est_rec_id' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '0',
				'errors' => array(),
			),
			'est_attachments' => array(
				'req' => 'post',
				'sanit' => 'sfiles',
				'valid' => 'vnotempty&vfiles:' . implode(',' , unserialize($this->timber->config('_allowed_upload_extensions'))),
				'default' => '',
				'errors' => array(),
			),
			'est_old_attachments' => array(
				'req' => 'post',
				'sanit' => 'sfilesids',
				'valid' => 'vfilesids',
				'default' => array(),
				'errors' => array(),
			),
		));

		if( true === $estimate_data['error_status'] ){
			$this->response['data'] = $estimate_data['error_text'];
			return false;
		}

		if( !($this->timber->access->checkPermission('edit.estimate')) ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		$estimate = $this->timber->invoice_model->getInvoiceById($estimate_data['est_id']['value']);

		if( (false === $estimate) || !(is_object($estimate)) ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		$estimate = $estimate->as_array();

		if( 2 !=  $estimate['type'] ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		if( !($estimate_data['est_due_date']['value'] >= $estimate_data['est_issue_date']['value']) ){
			$this->response['data'] = $this->timber->translator->trans('Due date must not be less than issue date.');
			return false;
		}

		$new_estimate_data = array();
		$new_estimate_data['in_id'] = $estimate_data['est_id']['value'];
		$new_estimate_data['client_id'] = $estimate_data['est_client_id']['value'];

		$new_estimate_data['status'] = $estimate_data['est_status']['value'];

		$new_estimate_data['terms'] = serialize(array(
			'notes' => $estimate_data['est_terms']['value']['notes'],
			'items' => $estimate_data['est_terms']['value']['items'],
			'overall' => $estimate_data['est_terms']['value']['overall']
		));

		/*array(
			'notes' => "",
			'items' => array(
				array( 'item_title', 'item_description', 'item_quantity', 'item_unit_price', 'item_sub_total' ),
				array( 'item_title', 'item_description', 'item_quantity', 'item_unit_price', 'item_sub_total' ),
				......
			)
			'overall' => array(
				'sub_total' => '',
				'tax_type' => '',
				'tax_value' => '',
				'discount_type' => '',
				'discount_value' => '',
				'total_value' => '',
				'paid_value' =>  '',
			)
		)*/

		$new_estimate_data['tax'] = $estimate_data['est_terms']['value']['overall']['tax_value'];
		$new_estimate_data['discount'] = $estimate_data['est_terms']['value']['overall']['discount_value'];
		$new_estimate_data['total'] = $estimate_data['est_terms']['value']['overall']['total_value'];

		# (8) subscription (11) project (12) none
		if( $estimate['rec_type'] != 8 ){
			$new_estimate_data['rec_type'] = (empty($estimate_data['est_rec_id']['value'])) ? 12 : 11;
			$new_estimate_data['rec_id'] = $estimate_data['est_rec_id']['value'];
		}

		$new_estimate_data['due_date'] = $estimate_data['est_due_date']['value'];
		$new_estimate_data['issue_date'] = $estimate_data['est_issue_date']['value'];

		$new_estimate_data['attach'] = 'off';
		$new_estimate_data['updated_at'] = $this->timber->time->getCurrentDate(true);

		# Add Attachments
		$est_attachments = $estimate_data['est_attachments']['value'];

		$files_ids = array();
		if( $estimate['attach'] == 'on' ){
			$attachments_ids = $this->timber->meta_model->getMetaByMultiple(array(
				'rec_id' => $estimate_data['est_id']['value'],
				'rec_type' => 3,
				'me_key' => 'invoice_attachments_data'
			));
			if( (false !== $attachments_ids) && (is_object($attachments_ids)) ){
				$attachments_ids = $attachments_ids->as_array();
				$files_ids = unserialize($attachments_ids['me_value']);

				foreach ($files_ids as $key => $value) {
					if( !in_array( $value, $estimate_data['est_old_attachments']['value'] ) ){
						unset($files_ids[$key]);
					}
				}
			}
		}

		if( (is_array($est_attachments)) && (count($est_attachments) > 0) ){
			foreach( $est_attachments as $est_attachment ) {
				$est_attachment = explode('--||--', $est_attachment);
				$files_ids[] = $this->timber->file_model->addFile(array(
					'title' => $est_attachment[1],
					'hash' => $est_attachment[0],
					'owner_id' => $this->timber->security->getId(),
					'description' => "Invoice Attachments",
					'storage' => 2,
					'type' => pathinfo($est_attachment[1], PATHINFO_EXTENSION),
					'uploaded_at' => $this->timber->time->getCurrentDate(true),
				));
			}
		}

		if( count($files_ids) > 0 ){
			$new_estimate_data['attach'] = 'on';
		}

		$action_status = (boolean) $this->timber->invoice_model->updateInvoiceById($new_estimate_data);


		$action_status &= (boolean) $this->timber->meta_model->updateMetaByMultiple(array(
			'rec_id' => $estimate_data['est_id']['value'],
			'rec_type' => 3,
			'me_key' => 'invoice_attachments_data',
			'me_value' => serialize($files_ids),
		));

		if( $action_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Estimate updated successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}
	/**
	 * Delete Estimate
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function deleteEstimate()
	{
		$estimate_id = ( (isset($_POST['estimate_id'])) && ((boolean) filter_var($_POST['estimate_id'], FILTER_VALIDATE_INT)) ) ? filter_var($_POST['estimate_id'], FILTER_SANITIZE_NUMBER_INT) : false;

		if( $estimate_id === false ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		if( !($this->timber->access->checkPermission('delete.estimate')) ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		$action_status  = (boolean) $this->timber->invoice_model->deleteInvoiceById($estimate_id);
		$action_status &= (boolean) $this->timber->meta_model->dumpMetas(false, $estimate_id, 3);

		if( $action_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Estimate deleted successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}

	/**
	 * Mark Estimate
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function markEstimate()
	{
		$estimate_id = ( (isset($_POST['estimate_id'])) && ((boolean) filter_var($_POST['estimate_id'], FILTER_VALIDATE_INT)) ) ? filter_var($_POST['estimate_id'], FILTER_SANITIZE_NUMBER_INT) : false;
		$action = ( (isset($_POST['action'])) && (in_array($_POST['action'], array('open', 'send', 'accept', 'reject', 'invoice', 'close'))) ) ? $_POST['action'] : false;

		# 1-opened  2-sent to client  3-accepted from client  4-rejected from client  5-invoiced  6-closed
		if( 'open' == $action ){
			$action_status = (boolean) $this->timber->invoice_model->updateInvoiceById(array(
				'in_id' => $estimate_id,
				'status' => 1
			));
		}

		if( 'send' == $action ){
			$action_status = (boolean) $this->timber->invoice_model->updateInvoiceById(array(
				'in_id' => $estimate_id,
				'status' => 2
			));
		}

		if( 'accept' == $action ){
			$action_status = (boolean) $this->timber->invoice_model->updateInvoiceById(array(
				'in_id' => $estimate_id,
				'status' => 3
			));
		}

		if( 'reject' == $action ){
			$action_status = (boolean) $this->timber->invoice_model->updateInvoiceById(array(
				'in_id' => $estimate_id,
				'status' => 4
			));
		}

		if( 'invoice' == $action ){
			$action_status = (boolean) $this->timber->invoice_model->updateInvoiceById(array(
				'in_id' => $estimate_id,
				'status' => 5,
				'type' => 2
			));
		}

		if( 'close' == $action ){
			$action_status = (boolean) $this->timber->invoice_model->updateInvoiceById(array(
				'in_id' => $estimate_id,
				'status' => 6
			));
		}

		if( $action_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Estimate updated successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}
}