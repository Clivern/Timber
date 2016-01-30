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
 * Invoices Data Services
 *
 * @since 1.0
 */
class InvoicesData extends \Timber\Services\Base {

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
     * Get Invoices Data
     *
     * @since 1.0
     * @access public
     * @return array
     */
    public function listData()
    {
        $data = array();

        # Bind Actions
        $data['add_invoice_action'] = $this->timber->config('request_url') . '/request/backend/ajax/invoices/add';
        $data['edit_invoice_action'] = $this->timber->config('request_url') . '/request/backend/ajax/invoices/edit';
        $data['delete_invoice_action'] = $this->timber->config('request_url') . '/request/backend/ajax/invoices/delete';
        $data['mark_invoice_action'] = $this->timber->config('request_url') . '/request/backend/ajax/invoices/mark';
        $data['site_currency'] = $this->timber->config('_site_currency_symbol');

        $user_id = $this->timber->security->getId();
        $user = $this->timber->user_model->getUserById( $user_id );

        if( (false === $user) || !(is_object($user)) ){
            $this->timber->redirect( $this->timber->config('request_url') . '/500' );
        }

        $user = $user->as_array();

        # Admin
        if( ('1' == $user['access_rule']) || ('2' == $user['access_rule']) ){
            $invoices = $this->timber->invoice_model->getInvoicesBy( array('type' => 1), false, false, 'desc', 'created_at' );
        }

        # Client
        if( '3' == $user['access_rule'] ){
            $invoices = $this->timber->invoice_model->getInvoicesBy( array('type' => 1, 'client_id' => $user_id), false, false, 'desc', 'created_at' );
        }

        $i = 0;
        $data['invoices'] = array();

        foreach ($invoices as $key => $invoice) {

            # Admin
            if( ('1' == $user['access_rule']) || ('2' == $user['access_rule']) ){
                $client_data = $this->timber->user_model->getUserById( $invoice['client_id'] );
                if( (false === $client_data) || !(is_object($client_data)) ){ continue; }

                $data['invoices'][$i]['client_email'] = $client_data['email'];
                $data['invoices'][$i]['client_grav_id'] = $client_data['grav_id'];
                $data['invoices'][$i]['client_user_name'] = $client_data['user_name'];
                $data['invoices'][$i]['client_first_name'] = $client_data['first_name'];
                $data['invoices'][$i]['client_last_name'] = $client_data['last_name'];
                $data['invoices'][$i]['client_full_name'] = trim( $client_data['first_name'] . " " . $client_data['last_name'] );
                $data['invoices'][$i]['client_company'] = $client_data['company'];
                $data['invoices'][$i]['client_job'] = $client_data['job'];
            }

            $data['invoices'][$i]['checkout'] = false;

            # Client
            if( '3' == $user['access_rule'] ){

                $data['invoices'][$i]['client_email'] = $user['email'];
                $data['invoices'][$i]['client_grav_id'] = $user['grav_id'];
                $data['invoices'][$i]['client_user_name'] = $user['user_name'];
                $data['invoices'][$i]['client_first_name'] = $user['first_name'];
                $data['invoices'][$i]['client_last_name'] = $user['last_name'];
                $data['invoices'][$i]['client_full_name'] = trim( $user['first_name'] . " " . $user['last_name'] );
                $data['invoices'][$i]['client_company'] = $user['company'];
                $data['invoices'][$i]['client_job'] = $user['job'];

                if( $this->timber->cookie->exist('_checkout_invoices') ){
                    $invoices = $this->timber->cookie->get('_checkout_invoices', '');
                    $invoices = $this->timber->encrypter->decrypt($invoices, RANDOM_HASH);
                    $invoices = explode(',', $invoices);
                    if( in_array($invoice['in_id'], $invoices) ){
                        $data['invoices'][$i]['checkout'] = true;
                    }
                }
            }

            $data['invoices'][$i]['in_id'] = $invoice['in_id'];
            $data['invoices'][$i]['reference'] = $invoice['reference'];
            $data['invoices'][$i]['ref_id'] = "INV-" . str_pad($invoice['in_id'], 8, '0', STR_PAD_LEFT);
            $data['invoices'][$i]['owner_id'] = $invoice['owner_id'];
            $data['invoices'][$i]['client_id'] = $invoice['client_id'];
            $data['invoices'][$i]['status'] = $invoice['status'];
            $data['invoices'][$i]['nice_status'] = str_replace(array('1','2','3'), array($this->timber->translator->trans('Paid'), $this->timber->translator->trans('Partially Paid'), $this->timber->translator->trans('Unpaid')), $invoice['status']);
            $data['invoices'][$i]['type'] = $invoice['type'];
            # $data['invoices'][$i]['terms'] = unserialize($invoice['terms']);
            $data['invoices'][$i]['tax'] = $invoice['tax'];
            $data['invoices'][$i]['discount'] = $invoice['discount'];
            $data['invoices'][$i]['total'] = $invoice['total'];
            $data['invoices'][$i]['attach'] = $invoice['attach'];
            $data['invoices'][$i]['rec_type'] = $invoice['rec_type'];
            $data['invoices'][$i]['rec_id'] = $invoice['rec_id'];
            $data['invoices'][$i]['due_date'] = $invoice['due_date'];
            $data['invoices'][$i]['issue_date'] = $invoice['issue_date'];
            $data['invoices'][$i]['created_at'] = $invoice['created_at'];
            $data['invoices'][$i]['updated_at'] = $invoice['updated_at'];
            $data['invoices'][$i]['edit_link'] = $this->timber->config('request_url') . '/admin/invoices/edit/' . $invoice['in_id'];
            $data['invoices'][$i]['view_link'] = $this->timber->config('request_url') . '/admin/invoices/view/' . $invoice['in_id'];
            $data['invoices'][$i]['trash_link'] = $this->timber->config('request_url') . '/request/backend/ajax/invoices/delete';

            $i += 1;
        }

        return $data;
    }

    /**
     * Add Invoices Data
     *
     * @since 1.0
     * @access public
     * @return array
     */
    public function addData()
    {
        $data = array();

        # Bind Actions
        $data['add_invoice_action'] = $this->timber->config('request_url') . '/request/backend/ajax/invoices/add';
        $data['edit_invoice_action'] = $this->timber->config('request_url') . '/request/backend/ajax/invoices/edit';
        $data['delete_invoice_action'] = $this->timber->config('request_url') . '/request/backend/ajax/invoices/delete';
        $data['mark_invoice_action'] = $this->timber->config('request_url') . '/request/backend/ajax/invoices/mark';
        $data['site_currency'] = $this->timber->config('_site_currency_symbol');

        $user_id = $this->timber->security->getId();

        $data['members_list'] = array();
        $data['projects_list'] = array();
        $data['items_list'] = array();
        $data['taxes_list'] = unserialize($this->timber->config('_site_tax_rates'));

        $users = $this->timber->user_model->getUsersBy( array('access_rule' => 3) );
        $projects = $this->timber->project_model->getProjects();
        $items = $this->timber->item_model->getItems();

        $i = 0;
        foreach( $projects as $key => $project ) {

            $data['projects_list'][$i]['pr_id'] = $project['pr_id'];
            $data['projects_list'][$i]['title'] = $project['title'];
            $data['projects_list'][$i]['reference'] = $project['reference'];

            $i += 1;
        }

        $i = 0;
        foreach ($users as $key => $user) {

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

        $i = 0;
        foreach ($items as $key => $item) {

            $data['items_list'][$i]['it_id'] = $item['it_id'];
            $data['items_list'][$i]['title'] = $item['title'];
            $data['items_list'][$i]['description'] = $item['description'];
            $data['items_list'][$i]['cost'] = $item['cost'];

            $i += 1;
        }

        return $data;
    }

    /**
     * Edit Invoices Data
     *
     * @since 1.0
     * @access public
     * @return array
     */
    public function editData($invoice_id)
    {
        $invoice_id = ( (boolean) filter_var($invoice_id, FILTER_VALIDATE_INT) ) ? filter_var($invoice_id, FILTER_SANITIZE_NUMBER_INT) : false;
        if( false === $invoice_id){
            $this->timber->redirect( $this->timber->config('request_url') . '/404' );
        }
        $data = array();

        # Bind Actions
        $data['add_invoice_action'] = $this->timber->config('request_url') . '/request/backend/ajax/invoices/add';
        $data['edit_invoice_action'] = $this->timber->config('request_url') . '/request/backend/ajax/invoices/edit';
        $data['delete_invoice_action'] = $this->timber->config('request_url') . '/request/backend/ajax/invoices/delete';
        $data['mark_invoice_action'] = $this->timber->config('request_url') . '/request/backend/ajax/invoices/mark';
        $data['site_currency'] = $this->timber->config('_site_currency_symbol');

        $user_id = $this->timber->security->getId();
        $invoice = $this->timber->invoice_model->getInvoiceByMultiple( array('type' => 1, 'in_id' => $invoice_id) );

        if( (false === $invoice) || !(is_object($invoice)) ){
            $this->timber->redirect( $this->timber->config('request_url') . '/404' );
        }

        $invoice = $invoice->as_array();

        $data['invoice_in_id'] = $invoice['in_id'];
        $data['invoice_reference'] = $invoice['reference'];
        $data['invoice_ref_id'] = "INV-" . str_pad($invoice['in_id'], 8, '0', STR_PAD_LEFT);

        if( !empty($data['invoice_ref_id']) ){
            $data['site_sub_page'] = $data['invoice_ref_id']  . " | ";
        }

        $data['invoice_owner_id'] = $invoice['owner_id'];
        $data['invoice_client_id'] = $invoice['client_id'];
        $data['invoice_status'] = $invoice['status'];
        $data['invoice_nice_status'] = str_replace(array('1','2','3'), array($this->timber->translator->trans('Paid'), $this->timber->translator->trans('Partially Paid'), $this->timber->translator->trans('Unpaid')), $invoice['status']);
        $data['invoice_type'] = $invoice['type'];
        $data['invoice_terms'] = unserialize($invoice['terms']);
        $data['invoice_tax'] = $invoice['tax'];
        $data['invoice_discount'] = $invoice['discount'];
        $data['invoice_total'] = $invoice['total'];
        $data['invoice_attach'] = $invoice['attach'];
        $data['invoice_rec_type'] = $invoice['rec_type'];
        $data['invoice_rec_id'] = $invoice['rec_id'];
        $data['invoice_due_date'] = $invoice['due_date'];
        $data['invoice_issue_date'] = $invoice['issue_date'];
        $data['invoice_created_at'] = $invoice['created_at'];
        $data['invoice_updated_at'] = $invoice['updated_at'];

        $data['invoice_attachments'] = array();
        $data['invoice_attachments_ids'] = array();
        $data['invoice_attachments_count'] = 0;

        # Attachments
        if( $invoice['attach'] == 'on' ){
            $attachments_ids = $this->timber->meta_model->getMetaByMultiple(array(
                'rec_id' => $invoice['in_id'],
                'rec_type' => 3,
                'me_key' => 'invoice_attachments_data'
            ));

            if( (false !== $attachments_ids) && (is_object($attachments_ids)) ){
                $attachments_ids = $attachments_ids->as_array();
                $data['invoice_attachments_ids'] = unserialize($attachments_ids['me_value']);

                foreach ($data['invoice_attachments_ids'] as $key => $value) {
                    $file = $this->timber->file_model->getFileById($value);
                    $data['invoice_attachments'][] = $file->as_array();
                }
                $data['invoice_attachments_count'] = count($data['invoice_attachments']);
            }
        }
        $data['invoice_attachments_ids'] = implode(',', $data['invoice_attachments_ids']);


        $data['members_list'] = array();
        $data['projects_list'] = array();
        $data['items_list'] = array();
        $data['taxes_list'] = unserialize($this->timber->config('_site_tax_rates'));

        $users = $this->timber->user_model->getUsersBy( array('access_rule' => 3) );
        $projects = $this->timber->project_model->getProjects();
        $items = $this->timber->item_model->getItems();

        $i = 0;
        foreach( $projects as $key => $project ) {

            $data['projects_list'][$i]['pr_id'] = $project['pr_id'];
            $data['projects_list'][$i]['title'] = $project['title'];
            $data['projects_list'][$i]['reference'] = $project['reference'];

            $i += 1;
        }

        $i = 0;
        foreach ($users as $key => $user) {

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

        $i = 0;
        foreach ($items as $key => $item) {

            $data['items_list'][$i]['it_id'] = $item['it_id'];
            $data['items_list'][$i]['title'] = $item['title'];
            $data['items_list'][$i]['description'] = $item['description'];
            $data['items_list'][$i]['cost'] = $item['cost'];

            $i += 1;
        }

        return $data;
    }

    /**
     * View Invoices Data
     *
     * @since 1.0
     * @access public
     * @return array
     */
    public function viewData($invoice_id)
    {
        $invoice_id = ( (boolean) filter_var($invoice_id, FILTER_VALIDATE_INT) ) ? filter_var($invoice_id, FILTER_SANITIZE_NUMBER_INT) : false;
        if( false === $invoice_id){
            $this->timber->redirect( $this->timber->config('request_url') . '/404' );
        }

        $data = array();

        # Bind Actions
        $data['add_invoice_action'] = $this->timber->config('request_url') . '/request/backend/ajax/invoices/add';
        $data['edit_invoice_action'] = $this->timber->config('request_url') . '/request/backend/ajax/invoices/edit';
        $data['delete_invoice_action'] = $this->timber->config('request_url') . '/request/backend/ajax/invoices/delete';
        $data['mark_invoice_action'] = $this->timber->config('request_url') . '/request/backend/ajax/invoices/mark';
        $data['site_currency'] = $this->timber->config('_site_currency_symbol');

        $user_id = $this->timber->security->getId();
        $invoice = $this->timber->invoice_model->getInvoiceByMultiple( array('type' => 1, 'in_id' => $invoice_id) );

        if( (false === $invoice) || !(is_object($invoice)) ){
            $this->timber->redirect( $this->timber->config('request_url') . '/404' );
        }
        $invoice = $invoice->as_array();

        $client_data = $this->timber->user_model->getUserById( $invoice['client_id'] );
        if( (false === $client_data) || !(is_object($client_data)) ){
            $this->timber->redirect( $this->timber->config('request_url') . '/500' );
        }

        if( ($this->timber->access->getRule() == 'client') && ($user_id != $invoice['client_id']) ){
            $this->timber->redirect( $this->timber->config('request_url') . '/404' );
        }

        $client_data = $client_data->as_array();

        $data['invoice_client_email'] = $client_data['email'];
        $data['invoice_client_grav_id'] = $client_data['grav_id'];
        $data['invoice_client_user_name'] = $client_data['user_name'];
        $data['invoice_client_first_name'] = $client_data['first_name'];
        $data['invoice_client_last_name'] = $client_data['last_name'];
        $data['invoice_client_full_name'] = trim( $client_data['first_name'] . " " . $client_data['last_name'] );
        $data['invoice_client_company'] = $client_data['company'];
        $data['invoice_client_job'] = $client_data['job'];
        $data['invoice_client_website'] = $client_data['website'];
        $data['invoice_client_phone_num'] = $client_data['phone_num'];
        $data['invoice_client_zip_code'] = $client_data['zip_code'];
        $data['invoice_client_vat_nubmer'] = $client_data['vat_nubmer'];
        $data['invoice_client_country'] = $client_data['country'];
        $data['invoice_client_city'] = $client_data['city'];
        $data['invoice_client_address1'] = $client_data['address1'];
        $data['invoice_client_address2'] = $client_data['address2'];

        $data['invoice_in_id'] = $invoice['in_id'];
        $data['invoice_reference'] = $invoice['reference'];
        $data['invoice_ref_id'] = "INV-" . str_pad($invoice['in_id'], 8, '0', STR_PAD_LEFT);

        if( !empty($data['invoice_ref_id']) ){
            $data['site_sub_page'] = $data['invoice_ref_id']  . " | ";
        }

        $data['invoice_owner_id'] = $invoice['owner_id'];
        $data['invoice_client_id'] = $invoice['client_id'];
        $data['invoice_status'] = $invoice['status'];
        $data['invoice_nice_status'] = str_replace(array('1','2','3'), array($this->timber->translator->trans('Paid'), $this->timber->translator->trans('Partially Paid'), $this->timber->translator->trans('Unpaid')), $invoice['status']);
        $data['invoice_type'] = $invoice['type'];
        $data['invoice_terms'] = unserialize($invoice['terms']);
        $data['invoice_tax'] = $invoice['tax'];
        $data['invoice_discount'] = $invoice['discount'];

        $data['invoice_tax_currency'] = ($data['invoice_terms']['overall']['tax_type'] == 'percent') ? "%" : $this->timber->config('_site_currency_symbol');
        $data['invoice_discount_currency'] = ($data['invoice_terms']['overall']['discount_type'] == 'percent') ? "%" : $this->timber->config('_site_currency_symbol');

        $data['invoice_total'] = $invoice['total'];
        $data['invoice_attach'] = $invoice['attach'];
        $data['invoice_rec_type'] = $invoice['rec_type'];
        $data['invoice_rec_id'] = $invoice['rec_id'];
        $data['invoice_due_date'] = $invoice['due_date'];
        $data['invoice_issue_date'] = $invoice['issue_date'];
        $data['invoice_created_at'] = $invoice['created_at'];
        $data['invoice_updated_at'] = $invoice['updated_at'];

        $data['invoice_edit_link'] = $this->timber->config('request_url') . '/admin/invoices/edit/' . $invoice['in_id'];
        $data['invoice_view_link'] = $this->timber->config('request_url') . '/admin/invoices/view/' . $invoice['in_id'];

        $data['invoice_attachments'] = array();
        $data['invoice_attachments_ids'] = array();
        $data['invoice_attachments_count'] = 0;

        # Attachments
        if( $invoice['attach'] == 'on' ){
            $attachments_ids = $this->timber->meta_model->getMetaByMultiple(array(
                'rec_id' => $invoice['in_id'],
                'rec_type' => 3,
                'me_key' => 'invoice_attachments_data'
            ));

            if( (false !== $attachments_ids) && (is_object($attachments_ids)) ){
                $attachments_ids = $attachments_ids->as_array();
                $data['invoice_attachments_ids'] = unserialize($attachments_ids['me_value']);

                foreach ($data['invoice_attachments_ids'] as $key => $value) {
                    $file = $this->timber->file_model->getFileById($value);
                    $data['invoice_attachments'][] = $file->as_array();
                }
                $data['invoice_attachments_count'] = count($data['invoice_attachments']);
            }
        }
        $data['invoice_attachments_ids'] = implode(',', $data['invoice_attachments_ids']);


        $data['company_name'] = $this->timber->config('_site_title');
        $data['company_country'] = $this->timber->config('_site_country');
        $data['company_city'] = $this->timber->config('_site_city');
        $data['company_address_line1'] = $this->timber->config('_site_address_line1');
        $data['company_address_line2'] = $this->timber->config('_site_address_line2');
        $data['company_vat_number'] = $this->timber->config('_site_vat_number');
        $data['company_phone'] = $this->timber->config('_site_phone');

        return $data;
    }

    /**
     * Checkout data
     *
     * @since 1.0
     * @access public
     * @return array
     */
    public function checkoutData()
    {
        $data = array();

        if( !($this->timber->cookie->exist('_checkout_invoices')) || ($this->timber->access->getRule() != 'client') ){
            $this->timber->cookie->delete('_checkout_invoices');
            $this->timber->redirect( $this->timber->config('request_url') . '/admin/invoices' );
        }

        $user_id = $this->timber->security->getId();

        $data['checkout_invoices_paypal'] = $this->timber->config('request_url') . '/request/backend/direct/pay/paypal';
        $data['checkout_invoices_stripe'] = $this->timber->config('request_url') . '/request/backend/direct/pay/stripe';
        $data['mark_invoice_action'] = $this->timber->config('request_url') . '/request/backend/ajax/invoices/mark';
        $data['site_currency'] = $this->timber->config('_site_currency_symbol');
        $data['bank_transfer_details'] = unserialize($this->timber->config('_bank_transfer_details'));

        $invoices = $this->timber->cookie->get('_checkout_invoices', '');
        $invoices = $this->timber->encrypter->decrypt($invoices, RANDOM_HASH);

        $invoices = explode(',', $invoices);
        $new_invoices = array();

        foreach( $invoices as $invoice ) {
            $new_invoices[] = ( (boolean) filter_var($invoice, FILTER_VALIDATE_INT) ) ? filter_var($invoice, FILTER_SANITIZE_NUMBER_INT) : false;
        }

        if( count($new_invoices) <= 0 ){
            $this->timber->cookie->delete('_checkout_invoices');
            $this->timber->redirect( $this->timber->config('request_url') . '/admin/invoices' );
        }

        $price = 0;
        $i = 0;
        $invoices_ids = array();
        $data['invoices'] = array();

        foreach ($new_invoices as $invoice_id ) {

            $invoice = $this->timber->invoice_model->getInvoiceByMultiple( array('type' => 1, 'in_id' => $invoice_id) );

            if( (false === $invoice) || !(is_object($invoice)) ){ continue; }
            if( $invoice['client_id'] != $user_id ){ continue; }
            if( $invoice['status'] == '1' ){ continue; }

            $invoice = $invoice->as_array();
            $invoices_ids[] = $invoice['in_id'];

            $data['invoices'][$i]['in_id'] = $invoice['in_id'];
            $data['invoices'][$i]['reference'] = $invoice['reference'];
            $data['invoices'][$i]['ref_id'] = "INV-" . str_pad($invoice['in_id'], 8, '0', STR_PAD_LEFT);
            $data['invoices'][$i]['owner_id'] = $invoice['owner_id'];
            $data['invoices'][$i]['client_id'] = $invoice['client_id'];
            $data['invoices'][$i]['status'] = $invoice['status'];
            $data['invoices'][$i]['type'] = $invoice['type'];
            $data['invoices'][$i]['terms'] = unserialize($invoice['terms']);
            $data['invoices'][$i]['tax'] = $invoice['tax'];
            $data['invoices'][$i]['discount'] = $invoice['discount'];
            $data['invoices'][$i]['total'] = $invoice['total'];
            $data['invoices'][$i]['attach'] = $invoice['attach'];
            $data['invoices'][$i]['rec_type'] = $invoice['rec_type'];
            $data['invoices'][$i]['rec_id'] = $invoice['rec_id'];
            $data['invoices'][$i]['due_date'] = $invoice['due_date'];
            $data['invoices'][$i]['issue_date'] = $invoice['issue_date'];
            $data['invoices'][$i]['created_at'] = $invoice['created_at'];
            $data['invoices'][$i]['updated_at'] = $invoice['updated_at'];

            $price += ($invoice['total'] - $data['invoices'][$i]['terms']['overall']['paid_value']);
            $data['invoices'][$i]['total'] = $invoice['total'] - $data['invoices'][$i]['terms']['overall']['paid_value'];
            $i += 1;
        }

        if( $price == 0 ){
            $this->timber->cookie->delete('_checkout_invoices');
            $this->timber->redirect( $this->timber->config('request_url') . '/admin/invoices' );
        }

        $data['total_price'] = number_format($price, 2);

        $this->timber->cookie->set('_checkout_invoices', $this->timber->encrypter->encrypt(implode(',', $invoices_ids),RANDOM_HASH), 1);

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