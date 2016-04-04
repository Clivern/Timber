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
 * Subscriptions Data Services
 *
 * @since 1.0
 */
class SubscriptionsData extends \Timber\Services\Base {


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
     * Get Subscriptions Data
     *
     * @since 1.0
     * @access public
     * @return array
     */
    public function listData()
    {
        $data = array();

        # Bind Actions
        $data['add_subscription_action'] = $this->timber->config('request_url') . '/request/backend/ajax/subscriptions/add';
        $data['edit_subscription_action'] = $this->timber->config('request_url') . '/request/backend/ajax/subscriptions/edit';
        $data['delete_subscription_action'] = $this->timber->config('request_url') . '/request/backend/ajax/subscriptions/delete';
        $data['mark_subscription_action'] = $this->timber->config('request_url') . '/request/backend/ajax/subscriptions/mark';
        $data['mark_invoice_action'] = $this->timber->config('request_url') . '/request/backend/ajax/invoices/mark';
        $data['invoice_subscription_action'] = $this->timber->config('request_url') . '/request/backend/ajax/subscriptions/invoice';
        $data['site_currency'] = $this->timber->config('_site_currency_symbol');

        $user_id = $this->timber->security->getId();
        $user = $this->timber->user_model->getUserById( $user_id );

        if( (false === $user) || !(is_object($user)) ){
            $this->timber->redirect( $this->timber->config('request_url') . '/500' );
        }

        $user = $user->as_array();

        # Admin
        if( ('1' == $user['access_rule']) || ('2' == $user['access_rule']) ){
            $subscriptions = $this->timber->subscription_model->getSubscriptions( false, false, 'desc', 'created_at' );
        }

        # Client
        if( '3' == $user['access_rule'] ){
            $subscriptions = $this->timber->subscription_model->getSubscriptionsBy( array('client_id' => $user_id), false, false, 'desc', 'created_at' );
        }

        $i = 0;
        $data['subscriptions'] = array();

        foreach ($subscriptions as $key => $subscription) {

            # Admin
            if( ('1' == $user['access_rule']) || ('2' == $user['access_rule']) ){
                $client_data = $this->timber->user_model->getUserById( $subscription['client_id'] );
                if( (false === $client_data) || !(is_object($client_data)) ){ continue; }

                $data['subscriptions'][$i]['client_email'] = $client_data['email'];
                $data['subscriptions'][$i]['client_grav_id'] = $client_data['grav_id'];
                $data['subscriptions'][$i]['client_user_name'] = $client_data['user_name'];
                $data['subscriptions'][$i]['client_first_name'] = $client_data['first_name'];
                $data['subscriptions'][$i]['client_last_name'] = $client_data['last_name'];
                $data['subscriptions'][$i]['client_full_name'] = trim( $client_data['first_name'] . " " . $client_data['last_name'] );
                $data['subscriptions'][$i]['client_company'] = $client_data['company'];
                $data['subscriptions'][$i]['client_job'] = $client_data['job'];
            }

            # Client
            if( '3' == $user['access_rule'] ){

                $data['subscriptions'][$i]['client_email'] = $user['email'];
                $data['subscriptions'][$i]['client_grav_id'] = $user['grav_id'];
                $data['subscriptions'][$i]['client_user_name'] = $user['user_name'];
                $data['subscriptions'][$i]['client_first_name'] = $user['first_name'];
                $data['subscriptions'][$i]['client_last_name'] = $user['last_name'];
                $data['subscriptions'][$i]['client_full_name'] = trim( $user['first_name'] . " " . $user['last_name'] );
                $data['subscriptions'][$i]['client_company'] = $user['company'];
                $data['subscriptions'][$i]['client_job'] = $user['job'];
            }


            $data['subscriptions'][$i]['su_id'] = $subscription['su_id'];
            $data['subscriptions'][$i]['reference'] = $subscription['reference'];
            $data['subscriptions'][$i]['ref_id'] = "SUB-" . str_pad($subscription['su_id'], 8, '0', STR_PAD_LEFT);
            $data['subscriptions'][$i]['owner_id'] = $subscription['owner_id'];
            $data['subscriptions'][$i]['client_id'] = $subscription['client_id'];
            $data['subscriptions'][$i]['status'] = $subscription['status'];
            $data['subscriptions'][$i]['nice_status'] = str_replace(array('1','2','3'), array($this->timber->translator->trans('Paid'), $this->timber->translator->trans('Partially Paid'), $this->timber->translator->trans('Unpaid')), $subscription['status']);
            $data['subscriptions'][$i]['frequency'] = $subscription['frequency'];
            # $data['subscriptions'][$i]['terms'] = unserialize($subscription['terms']);
            $data['subscriptions'][$i]['tax'] = $subscription['tax'];
            $data['subscriptions'][$i]['discount'] = $subscription['discount'];
            $data['subscriptions'][$i]['total'] = $subscription['total'];
            $data['subscriptions'][$i]['attach'] = $subscription['attach'];
            $data['subscriptions'][$i]['begin_at'] = $subscription['begin_at'];
            $data['subscriptions'][$i]['end_at'] = $subscription['end_at'];
            $data['subscriptions'][$i]['created_at'] = $subscription['created_at'];
            $data['subscriptions'][$i]['updated_at'] = $subscription['updated_at'];

            $data['subscriptions'][$i]['edit_link'] = $this->timber->config('request_url') . '/admin/subscriptions/edit/' . $subscription['su_id'];
            $data['subscriptions'][$i]['view_link'] = $this->timber->config('request_url') . '/admin/subscriptions/view/' . $subscription['su_id'];
            $data['subscriptions'][$i]['trash_link'] = $this->timber->config('request_url') . '/request/backend/ajax/subscriptions/delete';

            $i += 1;
        }

        return $data;
    }

    /**
     * Add Subscriptions Data
     *
     * @since 1.0
     * @access public
     * @return array
     */
    public function addData()
    {
        $data = array();

        # Bind Actions
        $data['add_subscription_action'] = $this->timber->config('request_url') . '/request/backend/ajax/subscriptions/add';
        $data['edit_subscription_action'] = $this->timber->config('request_url') . '/request/backend/ajax/subscriptions/edit';
        $data['delete_subscription_action'] = $this->timber->config('request_url') . '/request/backend/ajax/subscriptions/delete';
        $data['mark_subscription_action'] = $this->timber->config('request_url') . '/request/backend/ajax/subscriptions/mark';
        $data['mark_invoice_action'] = $this->timber->config('request_url') . '/request/backend/ajax/invoices/mark';
        $data['invoice_subscription_action'] = $this->timber->config('request_url') . '/request/backend/ajax/subscriptions/invoice';
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
     * Edit Subscriptions Data
     *
     * @since 1.0
     * @access public
     * @return array
     */
    public function editData($subscription_id)
    {
        $subscription_id = ( (boolean) filter_var($subscription_id, FILTER_VALIDATE_INT) ) ? filter_var($subscription_id, FILTER_SANITIZE_NUMBER_INT) : false;
        if( false === $subscription_id){
            $this->timber->redirect( $this->timber->config('request_url') . '/404' );
        }
        $data = array();

        # Bind Actions
        $data['add_subscription_action'] = $this->timber->config('request_url') . '/request/backend/ajax/subscriptions/add';
        $data['edit_subscription_action'] = $this->timber->config('request_url') . '/request/backend/ajax/subscriptions/edit';
        $data['delete_subscription_action'] = $this->timber->config('request_url') . '/request/backend/ajax/subscriptions/delete';
        $data['mark_subscription_action'] = $this->timber->config('request_url') . '/request/backend/ajax/subscriptions/mark';
        $data['mark_invoice_action'] = $this->timber->config('request_url') . '/request/backend/ajax/invoices/mark';
        $data['invoice_subscription_action'] = $this->timber->config('request_url') . '/request/backend/ajax/subscriptions/invoice';
        $data['site_currency'] = $this->timber->config('_site_currency_symbol');

        $user_id = $this->timber->security->getId();
        $subscription = $this->timber->subscription_model->getSubscriptionByMultiple( array('type' => 1, 'su_id' => $subscription_id) );

        if( (false === $subscription) || !(is_object($subscription)) ){
            $this->timber->redirect( $this->timber->config('request_url') . '/404' );
        }

        $subscription = $subscription->as_array();

        $data['subscription_su_id'] = $subscription['su_id'];
        $data['subscription_reference'] = $subscription['reference'];
        $data['subscription_ref_id'] = "SUB-" . str_pad($subscription['su_id'], 8, '0', STR_PAD_LEFT);

        if( !empty($data['subscription_ref_id']) ){
            $data['site_sub_page'] = $data['subscription_ref_id']  . " | ";
        }

        $data['subscription_owner_id'] = $subscription['owner_id'];
        $data['subscription_client_id'] = $subscription['client_id'];
        $data['subscription_status'] = $subscription['status'];
        $data['subscription_nice_status'] = str_replace(array('1','2','3'), array($this->timber->translator->trans('Paid'), $this->timber->translator->trans('Partially Paid'), $this->timber->translator->trans('Unpaid')), $subscription['status']);
        $data['subscription_frequency'] = explode("-", $subscription['frequency']);
        $data['subscription_frequency_value'] = $data['subscription_frequency'][0];
        $data['subscription_frequency_type'] =$data['subscription_frequency'][1];
        $data['subscription_terms'] = unserialize($subscription['terms']);
        $data['subscription_tax'] = $subscription['tax'];
        $data['subscription_discount'] = $subscription['discount'];
        $data['subscription_total'] = $subscription['total'];
        $data['subscription_attach'] = $subscription['attach'];
        $data['subscription_begin_at'] = $subscription['begin_at'];
        $data['subscription_end_at'] = $subscription['end_at'];
        $data['subscription_created_at'] = $subscription['created_at'];
        $data['subscription_updated_at'] = $subscription['updated_at'];

        $data['subscription_attachments'] = array();
        $data['subscription_attachments_ids'] = array();
        $data['subscription_attachments_count'] = 0;

        # Attachments
        if( $subscription['attach'] == 'on' ){
            $attachments_ids = $this->timber->meta_model->getMetaByMultiple(array(
                'rec_id' => $subscription['su_id'],
                'rec_type' => 8,
                'me_key' => 'subscription_attachments_data'
            ));

            if( (false !== $attachments_ids) && (is_object($attachments_ids)) ){
                $attachments_ids = $attachments_ids->as_array();
                $data['subscription_attachments_ids'] = unserialize($attachments_ids['me_value']);

                foreach ($data['subscription_attachments_ids'] as $key => $value) {
                    $file = $this->timber->file_model->getFileById($value);
                    $data['subscription_attachments'][] = $file->as_array();
                }
                $data['subscription_attachments_count'] = count($data['subscription_attachments']);
            }
        }
        $data['subscription_attachments_ids'] = implode(',', $data['subscription_attachments_ids']);

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
     * View Subscriptions Data
     *
     * @since 1.0
     * @access public
     * @return array
     */
    public function viewData($subscription_id)
    {
        $subscription_id = ( (boolean) filter_var($subscription_id, FILTER_VALIDATE_INT) ) ? filter_var($subscription_id, FILTER_SANITIZE_NUMBER_INT) : false;

        if( false === $subscription_id){
            $this->timber->redirect( $this->timber->config('request_url') . '/404' );
        }

        $data = array();

        # Bind Actions
        $data['add_subscription_action'] = $this->timber->config('request_url') . '/request/backend/ajax/subscriptions/add';
        $data['edit_subscription_action'] = $this->timber->config('request_url') . '/request/backend/ajax/subscriptions/edit';
        $data['delete_subscription_action'] = $this->timber->config('request_url') . '/request/backend/ajax/subscriptions/delete';
        $data['mark_subscription_action'] = $this->timber->config('request_url') . '/request/backend/ajax/subscriptions/mark';
        $data['mark_invoice_action'] = $this->timber->config('request_url') . '/request/backend/ajax/invoices/mark';
        $data['invoice_subscription_action'] = $this->timber->config('request_url') . '/request/backend/ajax/subscriptions/invoice';
        $data['site_currency'] = $this->timber->config('_site_currency_symbol');

        $user_id = $this->timber->security->getId();
        $subscription = $this->timber->subscription_model->getSubscriptionByMultiple( array('su_id' => $subscription_id) );

        if( (false === $subscription) || !(is_object($subscription)) ){
            $this->timber->redirect( $this->timber->config('request_url') . '/404' );
        }
        $subscription = $subscription->as_array();

        if( ($this->timber->access->getRule() == 'client') && ($user_id != $subscription['client_id']) ){
            $this->timber->redirect( $this->timber->config('request_url') . '/404' );
        }

        $client_data = $this->timber->user_model->getUserById( $subscription['client_id'] );
        if( (false === $client_data) || !(is_object($client_data)) ){
            $this->timber->redirect( $this->timber->config('request_url') . '/500' );
        }

        $client_data = $client_data->as_array();

        $data['subscription_client_email'] = $client_data['email'];
        $data['subscription_client_grav_id'] = $client_data['grav_id'];
        $data['subscription_client_user_name'] = $client_data['user_name'];
        $data['subscription_client_first_name'] = $client_data['first_name'];
        $data['subscription_client_last_name'] = $client_data['last_name'];
        $data['subscription_client_full_name'] = trim( $client_data['first_name'] . " " . $client_data['last_name'] );
        $data['subscription_client_company'] = $client_data['company'];
        $data['subscription_client_job'] = $client_data['job'];
        $data['subscription_client_website'] = $client_data['website'];
        $data['subscription_client_phone_num'] = $client_data['phone_num'];
        $data['subscription_client_zip_code'] = $client_data['zip_code'];
        $data['subscription_client_vat_nubmer'] = $client_data['vat_nubmer'];
        $data['subscription_client_country'] = $client_data['country'];
        $data['subscription_client_city'] = $client_data['city'];
        $data['subscription_client_address1'] = $client_data['address1'];
        $data['subscription_client_address2'] = $client_data['address2'];

        $data['subscription_su_id'] = $subscription['su_id'];
        $data['subscription_reference'] = $subscription['reference'];
        $data['subscription_ref_id'] = "SUB-" . str_pad($subscription['su_id'], 8, '0', STR_PAD_LEFT);

        if( !empty($data['subscription_ref_id']) ){
            $data['site_sub_page'] = $data['subscription_ref_id']  . " | ";
        }

        $data['subscription_owner_id'] = $subscription['owner_id'];
        $data['subscription_client_id'] = $subscription['client_id'];
        $data['subscription_status'] = $subscription['status'];
        $data['subscription_nice_status'] = str_replace(array('1','2','3'), array($this->timber->translator->trans('Paid'), $this->timber->translator->trans('Partially Paid'), $this->timber->translator->trans('Unpaid')), $subscription['status']);

        $data['subscription_frequency'] = explode("-", $subscription['frequency']);
        $data['subscription_frequency_value'] = $data['subscription_frequency'][0];
        $data['subscription_frequency_type'] = $data['subscription_frequency'][1];

        $frequency_nice_types = array(
            'd' => $this->timber->translator->trans('Day'),
            'w' => $this->timber->translator->trans('Week'),
            'm' => $this->timber->translator->trans('Month'),
            'y' => $this->timber->translator->trans('Year')
        );

        $data['subscription_frequency_nice_type'] = $frequency_nice_types[$data['subscription_frequency_type']];

        $data['subscription_terms'] = unserialize($subscription['terms']);
        $data['subscription_tax'] = $subscription['tax'];
        $data['subscription_discount'] = $subscription['discount'];

        $data['subscription_tax_currency'] = ($data['subscription_terms']['overall']['tax_type'] == 'percent') ? "%" : $this->timber->config('_site_currency_symbol');
        $data['subscription_discount_currency'] = ($data['subscription_terms']['overall']['discount_type'] == 'percent') ? "%" : $this->timber->config('_site_currency_symbol');

        $data['subscription_total'] = $subscription['total'];
        $data['subscription_attach'] = $subscription['attach'];
        $data['subscription_due_date'] = $subscription['begin_at'];
        $data['subscription_end_at'] = $subscription['end_at'];
        $data['subscription_created_at'] = $subscription['created_at'];
        $data['subscription_updated_at'] = $subscription['updated_at'];

        $data['subscription_edit_link'] = $this->timber->config('request_url') . '/admin/subscriptions/edit/' . $subscription['su_id'];
        $data['subscription_view_link'] = $this->timber->config('request_url') . '/admin/subscriptions/view/' . $subscription['su_id'];

        $data['subscription_attachments'] = array();
        $data['subscription_attachments_ids'] = array();
        $data['subscription_attachments_count'] = 0;

        # Attachments
        if( $subscription['attach'] == 'on' ){
            $attachments_ids = $this->timber->meta_model->getMetaByMultiple(array(
                'rec_id' => $subscription['su_id'],
                'rec_type' => 8,
                'me_key' => 'subscription_attachments_data'
            ));

            if( (false !== $attachments_ids) && (is_object($attachments_ids)) ){
                $attachments_ids = $attachments_ids->as_array();
                $data['subscription_attachments_ids'] = unserialize($attachments_ids['me_value']);

                foreach ($data['subscription_attachments_ids'] as $key => $value) {
                    $file = $this->timber->file_model->getFileById($value);
                    $data['subscription_attachments'][] = $file->as_array();
                }
                $data['subscription_attachments_count'] = count($data['subscription_attachments']);
            }
        }
        $data['subscription_attachments_ids'] = implode(',', $data['subscription_attachments_ids']);


        $invoices = $this->timber->invoice_model->getInvoicesBy( array('type' => 1, 'rec_type' => '8', 'rec_id' => $subscription_id), false, false, 'desc', 'created_at' );

        $i = 0;
        $data['subscription_invoices'] = array();

        foreach ($invoices as $key => $invoice) {

            $data['subscription_invoices'][$i]['in_id'] = $invoice['in_id'];
            $data['subscription_invoices'][$i]['reference'] = $invoice['reference'];
            $data['subscription_invoices'][$i]['ref_id'] = "INV-" . str_pad($invoice['in_id'], 8, '0', STR_PAD_LEFT);
            $data['subscription_invoices'][$i]['owner_id'] = $invoice['owner_id'];
            $data['subscription_invoices'][$i]['client_id'] = $invoice['client_id'];
            $data['subscription_invoices'][$i]['status'] = $invoice['status'];
            $data['subscription_invoices'][$i]['nice_status'] = str_replace(array('1','2','3'), array($this->timber->translator->trans('Paid'), $this->timber->translator->trans('Partially Paid'), $this->timber->translator->trans('Unpaid')), $invoice['status']);
            $data['subscription_invoices'][$i]['type'] = $invoice['type'];
            # $data['subscription_invoices'][$i]['terms'] = unserialize($invoice['terms']);
            $data['subscription_invoices'][$i]['tax'] = $invoice['tax'];
            $data['subscription_invoices'][$i]['discount'] = $invoice['discount'];
            $data['subscription_invoices'][$i]['total'] = $invoice['total'];
            $data['subscription_invoices'][$i]['attach'] = $invoice['attach'];
            $data['subscription_invoices'][$i]['rec_type'] = $invoice['rec_type'];
            $data['subscription_invoices'][$i]['rec_id'] = $invoice['rec_id'];
            $data['subscription_invoices'][$i]['due_date'] = $invoice['due_date'];
            $data['subscription_invoices'][$i]['issue_date'] = $invoice['issue_date'];
            $data['subscription_invoices'][$i]['created_at'] = $invoice['created_at'];
            $data['subscription_invoices'][$i]['updated_at'] = $invoice['updated_at'];
            $data['subscription_invoices'][$i]['edit_link'] = $this->timber->config('request_url') . '/admin/invoices/edit/' . $invoice['in_id'];
            $data['subscription_invoices'][$i]['view_link'] = $this->timber->config('request_url') . '/admin/invoices/view/' . $invoice['in_id'];
            $data['subscription_invoices'][$i]['trash_link'] = $this->timber->config('request_url') . '/request/backend/ajax/invoices/delete';

            $i += 1;
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