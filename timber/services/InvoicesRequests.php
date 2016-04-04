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
 * Invoices Requests Services
 *
 * @since 1.0
 */
class InvoicesRequests extends \Timber\Services\Base {

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
	 * Add New Invoice
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function addInvoice()
	{
		$invoice_data = $this->timber->validator->clear(array(
			'inv_client_id' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Please select client.'),
					'vint' => $this->timber->translator->trans('Please select client.')
				),
			),
			# 1-Paid 2-Partially Paid 3-Unpaid
			'inv_status' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vinarray:1,2,3',
				'default' => '1',
				'errors' => array(),
			),
			'inv_due_date' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vdate',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Due date is invalid.'),
					'vdate' => $this->timber->translator->trans('Due date is invalid.'),
				),
			),
			'inv_issue_date' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vdate',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Issue date is invalid.'),
					'vdate' => $this->timber->translator->trans('Issue date is invalid.'),
				),
			),
			'inv_terms' => array(
				'req' => 'post',
				'sanit' => 'sinvs',
				'valid' => 'vinvs',
				'default' => '',
				'errors' => array(
					'vinvs' =>  $this->timber->translator->trans('Invalid invoice items detected or invoice overall data is invalid.'),
				),
			),
			'inv_rec_id' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '0',
				'errors' => array(),
			),
			'inv_attachments' => array(
				'req' => 'post',
				'sanit' => 'sfiles',
				'valid' => 'vnotempty&vfiles:' . implode(',' , unserialize($this->timber->config('_allowed_upload_extensions'))),
				'default' => '',
				'errors' => array(),
			),
		));

		if( true === $invoice_data['error_status'] ){
			$this->response['data'] = $invoice_data['error_text'];
			return false;
		}

		if( !($this->timber->access->checkPermission('add.invoices')) ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		if( !($invoice_data['inv_due_date']['value'] >= $invoice_data['inv_issue_date']['value']) ){
			$this->response['data'] = $this->timber->translator->trans('Due date must not be less than issue date.');
			return false;
		}

		$new_invoice_data = array();
		$new_invoice_data['reference'] = $this->timber->invoice_model->newReference("INV");
		$new_invoice_data['owner_id'] = $this->timber->security->getId();
		$new_invoice_data['client_id'] = $invoice_data['inv_client_id']['value'];

		# 1-Paid 2-Partially Paid 3-Unpaid
		if( $invoice_data['inv_terms']['value']['overall']['total_value'] == $invoice_data['inv_terms']['value']['overall']['paid_value'] ){
			$new_invoice_data['status'] = 1;
		}
		if( ($invoice_data['inv_terms']['value']['overall']['total_value'] > $invoice_data['inv_terms']['value']['overall']['paid_value']) && ($invoice_data['inv_terms']['value']['overall']['paid_value'] > 0) ){
			$new_invoice_data['status'] = 2;
		}
		if( ($invoice_data['inv_terms']['value']['overall']['total_value'] > $invoice_data['inv_terms']['value']['overall']['paid_value']) && ($invoice_data['inv_terms']['value']['overall']['paid_value'] == 0) ){
			$new_invoice_data['status'] = 3;
		}

		$new_invoice_data['type'] = 1;

		$new_invoice_data['terms'] = serialize(array(
			'notes' => $invoice_data['inv_terms']['value']['notes'],
			'items' => $invoice_data['inv_terms']['value']['items'],
			'overall' =>  $invoice_data['inv_terms']['value']['overall']
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

		$new_invoice_data['tax'] = $invoice_data['inv_terms']['value']['overall']['tax_value'];
		$new_invoice_data['discount'] = $invoice_data['inv_terms']['value']['overall']['discount_value'];
		$new_invoice_data['total'] = $invoice_data['inv_terms']['value']['overall']['total_value'];

		# (8) subscription (11) project (12) none
		$new_invoice_data['rec_type'] = (empty($invoice_data['inv_rec_id']['value'])) ? 12 : 11;
		$new_invoice_data['rec_id'] = $invoice_data['inv_rec_id']['value'];

		$new_invoice_data['due_date'] = $invoice_data['inv_due_date']['value'];
		$new_invoice_data['issue_date'] = $invoice_data['inv_issue_date']['value'];

		$new_invoice_data['attach'] = 'off';
		$new_invoice_data['created_at'] = $this->timber->time->getCurrentDate(true);
		$new_invoice_data['updated_at'] = $this->timber->time->getCurrentDate(true);

		# Add Attachments
		$inv_attachments = $invoice_data['inv_attachments']['value'];

		$files_ids = array();
		if( (is_array($inv_attachments)) && (count($inv_attachments) > 0) ){
			foreach( $inv_attachments as $inv_attachment ) {
				$inv_attachment = explode('--||--', $inv_attachment);
				$files_ids[] = $this->timber->file_model->addFile(array(
					'title' => $inv_attachment[1],
					'hash' => $inv_attachment[0],
					'owner_id' => $this->timber->security->getId(),
					'description' => "Invoice Attachments",
					'storage' => 2,
					'type' => pathinfo($inv_attachment[1], PATHINFO_EXTENSION),
					'uploaded_at' => $this->timber->time->getCurrentDate(true),
				));
			}
			$new_invoice_data['attach'] = 'on';
		}

		$invoice_id = $this->timber->invoice_model->addInvoice($new_invoice_data);

		# Add Metas
		$meta_status = true;

		$meta_status &= (boolean) $this->timber->meta_model->addMeta(array(
			'rec_id' => $invoice_id,
			'rec_type' => 3,
			'me_key' => 'invoice_attachments_data',
			'me_value' => serialize($files_ids),
		));

		# New Invoice Notification
		$this->timber->notify->increment('invoices_notif', $new_invoice_data['client_id']);
		$this->timber->notify->setMailerCron(array(
			'method_name' => 'newInvoiceEmailNotifier',
			'inv_id' => $invoice_id,
		));

		if( $invoice_id && $meta_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Invoice created successfully.');
			$this->response['next_link'] = $this->timber->config('request_url') . '/admin/invoices/view/' . $invoice_id;
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}

	}

	/**
	 * Edit Invoice
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function editInvoice()
	{
		$invoice_data = $this->timber->validator->clear(array(
			'inv_id' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Invalid Request.'),
					'vint' => $this->timber->translator->trans('Invalid Request.')
				),
			),
			'inv_client_id' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Please select client.'),
					'vint' => $this->timber->translator->trans('Please select client.')
				),
			),
			# 1-Paid 2-Partially Paid 3-Unpaid
			'inv_status' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vinarray:1,2,3',
				'default' => '1',
				'errors' => array(),
			),
			'inv_due_date' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vdate',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Due date is invalid.'),
					'vdate' => $this->timber->translator->trans('Due date is invalid.'),
				),
			),
			'inv_issue_date' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vdate',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Issue date is invalid.'),
					'vdate' => $this->timber->translator->trans('Issue date is invalid.'),
				),
			),
			'inv_terms' => array(
				'req' => 'post',
				'sanit' => 'sinvs',
				'valid' => 'vinvs',
				'default' => '',
				'errors' => array(
					'vinvs' =>  $this->timber->translator->trans('Invalid invoice items detected or invoice overall data is invalid.'),
				),
			),
			'inv_rec_id' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '0',
				'errors' => array(),
			),
			'inv_attachments' => array(
				'req' => 'post',
				'sanit' => 'sfiles',
				'valid' => 'vnotempty&vfiles:' . implode(',' , unserialize($this->timber->config('_allowed_upload_extensions'))),
				'default' => '',
				'errors' => array(),
			),
			'inv_old_attachments' => array(
				'req' => 'post',
				'sanit' => 'sfilesids',
				'valid' => 'vfilesids',
				'default' => array(),
				'errors' => array(),
			),
		));

		if( true === $invoice_data['error_status'] ){
			$this->response['data'] = $invoice_data['error_text'];
			return false;
		}

		if( !($this->timber->access->checkPermission('edit.invoices')) ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		$invoice = $this->timber->invoice_model->getInvoiceById($invoice_data['inv_id']['value']);

		if( (false === $invoice) || !(is_object($invoice)) ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		$invoice = $invoice->as_array();

		if( 1 !=  $invoice['type'] ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		if( !($invoice_data['inv_due_date']['value'] >= $invoice_data['inv_issue_date']['value']) ){
			$this->response['data'] = $this->timber->translator->trans('Due date must not be less than issue date.');
			return false;
		}

		$new_invoice_data = array();
		$new_invoice_data['in_id'] = $invoice_data['inv_id']['value'];
		$new_invoice_data['client_id'] = $invoice_data['inv_client_id']['value'];

		# 1-Paid 2-Partially Paid 3-Unpaid
		if( $invoice_data['inv_terms']['value']['overall']['total_value'] == $invoice_data['inv_terms']['value']['overall']['paid_value'] ){
			$new_invoice_data['status'] = 1;
		}
		if( ($invoice_data['inv_terms']['value']['overall']['total_value'] > $invoice_data['inv_terms']['value']['overall']['paid_value']) && ($invoice_data['inv_terms']['value']['overall']['paid_value'] > 0) ){
			$new_invoice_data['status'] = 2;
		}
		if( ($invoice_data['inv_terms']['value']['overall']['total_value'] > $invoice_data['inv_terms']['value']['overall']['paid_value']) && ($invoice_data['inv_terms']['value']['overall']['paid_value'] == 0) ){
			$new_invoice_data['status'] = 3;
		}

		$new_invoice_data['terms'] = serialize(array(
			'notes' => $invoice_data['inv_terms']['value']['notes'],
			'items' => $invoice_data['inv_terms']['value']['items'],
			'overall' => $invoice_data['inv_terms']['value']['overall']
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

		$new_invoice_data['tax'] = $invoice_data['inv_terms']['value']['overall']['tax_value'];
		$new_invoice_data['discount'] = $invoice_data['inv_terms']['value']['overall']['discount_value'];
		$new_invoice_data['total'] = $invoice_data['inv_terms']['value']['overall']['total_value'];

		# (8) subscription (11) project (12) none
		if( $invoice['rec_type'] != 8 ){
			$new_invoice_data['rec_type'] = (empty($invoice_data['inv_rec_id']['value'])) ? 12 : 11;
			$new_invoice_data['rec_id'] = $invoice_data['inv_rec_id']['value'];
		}

		$new_invoice_data['due_date'] = $invoice_data['inv_due_date']['value'];
		$new_invoice_data['issue_date'] = $invoice_data['inv_issue_date']['value'];

		$new_invoice_data['attach'] = 'off';
		$new_invoice_data['updated_at'] = $this->timber->time->getCurrentDate(true);

		# Add Attachments
		$inv_attachments = $invoice_data['inv_attachments']['value'];

		$files_ids = array();
		if( $invoice['attach'] == 'on' ){
			$attachments_ids = $this->timber->meta_model->getMetaByMultiple(array(
				'rec_id' => $invoice_data['inv_id']['value'],
				'rec_type' => 3,
				'me_key' => 'invoice_attachments_data'
			));
			if( (false !== $attachments_ids) && (is_object($attachments_ids)) ){
				$attachments_ids = $attachments_ids->as_array();
				$files_ids = unserialize($attachments_ids['me_value']);

				foreach ($files_ids as $key => $value) {
					if( !in_array( $value, $invoice_data['inv_old_attachments']['value'] ) ){
						unset($files_ids[$key]);
					}
				}
			}
		}

		if( (is_array($inv_attachments)) && (count($inv_attachments) > 0) ){
			foreach( $inv_attachments as $inv_attachment ) {
				$inv_attachment = explode('--||--', $inv_attachment);
				$files_ids[] = $this->timber->file_model->addFile(array(
					'title' => $inv_attachment[1],
					'hash' => $inv_attachment[0],
					'owner_id' => $this->timber->security->getId(),
					'description' => "Invoice Attachments",
					'storage' => 2,
					'type' => pathinfo($inv_attachment[1], PATHINFO_EXTENSION),
					'uploaded_at' => $this->timber->time->getCurrentDate(true),
				));
			}
		}

		if( count($files_ids) > 0 ){
			$new_invoice_data['attach'] = 'on';
		}

		$action_status = (boolean) $this->timber->invoice_model->updateInvoiceById($new_invoice_data);


		$action_status &= (boolean) $this->timber->meta_model->updateMetaByMultiple(array(
			'rec_id' => $invoice_data['inv_id']['value'],
			'rec_type' => 3,
			'me_key' => 'invoice_attachments_data',
			'me_value' => serialize($files_ids),
		));

		if( $action_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Invoice updated successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}

	/**
	 * Delete Invoice
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function deleteInvoice()
	{
		$invoice_id = ( (isset($_POST['invoice_id'])) && ((boolean) filter_var($_POST['invoice_id'], FILTER_VALIDATE_INT)) ) ? filter_var($_POST['invoice_id'], FILTER_SANITIZE_NUMBER_INT) : false;

		if( $invoice_id === false ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		if( !($this->timber->access->checkPermission('delete.invoices')) ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		# Delete Invoice and its meta data
		$action_status = (boolean) $this->timber->invoice_model->deleteInvoiceById($invoice_id);
		$action_status &= (boolean) $this->timber->meta_model->dumpMetas(false, $invoice_id, 3);

		if( $action_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Invoice deleted successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}

	/**
	 * Mark Invoice
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function markInvoice()
	{
		$invoice_id = ( (isset($_POST['invoice_id'])) && ((boolean) filter_var($_POST['invoice_id'], FILTER_VALIDATE_INT)) ) ? filter_var($_POST['invoice_id'], FILTER_SANITIZE_NUMBER_INT) : false;
		$action = ( (isset($_POST['action'])) && (in_array($_POST['action'], array('checkout', 'un_checkout', 'un_paid', 'paid'))) ) ? $_POST['action'] : false;

		# Add to checkout cart
		if( 'checkout' == $action ){
			$new_invoices = $invoice_id;

			if( $this->timber->cookie->exist('_checkout_invoices') ){
				$invoices = $this->timber->cookie->get('_checkout_invoices', '');
				$invoices = $this->timber->encrypter->decrypt($invoices, RANDOM_HASH);
				$new_invoices = (empty($invoices)) ? $new_invoices : "{$invoices},{$new_invoices}";
			}

			$action_status = (boolean) $this->timber->cookie->set('_checkout_invoices', $this->timber->encrypter->encrypt($new_invoices,RANDOM_HASH), 1);

			$this->response['data'] = $this->timber->translator->trans('Invoice added to cart.');
		}

		# Remove from checkout cart
		if( 'un_checkout' == $action ){
			if( $this->timber->cookie->exist('_checkout_invoices') ){
				$invoices = $this->timber->cookie->get('_checkout_invoices', '');
				$invoices = $this->timber->encrypter->decrypt($invoices, RANDOM_HASH);
				$invoices = explode(',', $invoices);
				$new_invoices = '';
				foreach ($invoices as $invoice) {
					if( empty($invoice) || ($invoice == $invoice_id) || !((boolean) filter_var($invoice, FILTER_VALIDATE_INT)) ){ continue; }
					$new_invoices = "{$new_invoices},{$invoice}";
				}
				$action_status = (boolean) $this->timber->cookie->set('_checkout_invoices', $this->timber->encrypter->encrypt(trim($new_invoices, ','),RANDOM_HASH), 1);
			}else{
				$action_status = false;
			}

			$this->response['data'] = $this->timber->translator->trans('Invoice removed from cart.');
		}

		if( $action_status ){
			$this->response['status'] = 'success';
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}
}