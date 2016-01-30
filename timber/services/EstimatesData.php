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
 * Estimates Data Services
 *
 * @since 1.0
 */
class EstimatesData extends \Timber\Services\Base {

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
        $data['add_estimate_action'] = $this->timber->config('request_url') . '/request/backend/ajax/estimates/add';
        $data['edit_estimate_action'] = $this->timber->config('request_url') . '/request/backend/ajax/estimates/edit';
        $data['delete_estimate_action'] = $this->timber->config('request_url') . '/request/backend/ajax/estimates/delete';
        $data['mark_estimate_action'] = $this->timber->config('request_url') . '/request/backend/ajax/estimates/mark';
        $data['site_currency'] = $this->timber->config('_site_currency_symbol');

        $user_id = $this->timber->security->getId();
        $user = $this->timber->user_model->getUserById( $user_id );

        if( (false === $user) || !(is_object($user)) ){
              $this->timber->redirect( $this->timber->config('request_url') . '/500' );
        }

        $user = $user->as_array();

        # Admin
        if( ('1' == $user['access_rule']) || ('2' == $user['access_rule']) ){
            $estimates = $this->timber->invoice_model->getInvoicesBy( array('type' => 2), false, false, 'desc', 'created_at' );
        }

        # Client
        if( '3' == $user['access_rule'] ){
            $estimates = $this->timber->invoice_model->getInvoicesBy( array('type' => 2, 'client_id' => $user_id), false, false, 'desc', 'created_at' );
        }

        $i = 0;
        $data['estimates'] = array();

        foreach ($estimates as $key => $estimate) {

            # Admin
            if( ('1' == $user['access_rule']) || ('2' == $user['access_rule']) ){
                $client_data = $this->timber->user_model->getUserById( $estimate['client_id'] );
                if( (false === $client_data) || !(is_object($client_data)) ){ continue; }

                $data['estimates'][$i]['client_email'] = $client_data['email'];
                $data['estimates'][$i]['client_grav_id'] = $client_data['grav_id'];
                $data['estimates'][$i]['client_user_name'] = $client_data['user_name'];
                $data['estimates'][$i]['client_first_name'] = $client_data['first_name'];
                $data['estimates'][$i]['client_last_name'] = $client_data['last_name'];
                $data['estimates'][$i]['client_full_name'] = trim( $client_data['first_name'] . " " . $client_data['last_name'] );
                $data['estimates'][$i]['client_company'] = $client_data['company'];
                $data['estimates'][$i]['client_job'] = $client_data['job'];
            }

            # Client
            if( '3' == $user['access_rule'] ){
                $data['estimates'][$i]['client_email'] = $user['email'];
                $data['estimates'][$i]['client_grav_id'] = $user['grav_id'];
                $data['estimates'][$i]['client_user_name'] = $user['user_name'];
                $data['estimates'][$i]['client_first_name'] = $user['first_name'];
                $data['estimates'][$i]['client_last_name'] = $user['last_name'];
                $data['estimates'][$i]['client_full_name'] = trim( $user['first_name'] . " " . $user['last_name'] );
                $data['estimates'][$i]['client_company'] = $user['company'];
                $data['estimates'][$i]['client_job'] = $user['job'];
            }

            $data['estimates'][$i]['in_id'] = $estimate['in_id'];
            $data['estimates'][$i]['reference'] = $estimate['reference'];
            $data['estimates'][$i]['ref_id'] = "EST-" . str_pad($estimate['in_id'], 8, '0', STR_PAD_LEFT);
            $data['estimates'][$i]['owner_id'] = $estimate['owner_id'];
            $data['estimates'][$i]['client_id'] = $estimate['client_id'];
            $data['estimates'][$i]['status'] = $estimate['status'];

            $data['estimates'][$i]['nice_status'] = str_replace(
                array('1','2','3','4','5','6'),
                array($this->timber->translator->trans('Opened'), $this->timber->translator->trans('Sent'), $this->timber->translator->trans('Accepted'), $this->timber->translator->trans('Rejected'), $this->timber->translator->trans('Invoiced'), $this->timber->translator->trans('Closed')), $estimate['status']
            );

            $data['estimates'][$i]['type'] = $estimate['type'];
            # $data['estimates'][$i]['terms'] = unserialize($estimate['terms']);
            $data['estimates'][$i]['tax'] = $estimate['tax'];
            $data['estimates'][$i]['discount'] = $estimate['discount'];
            $data['estimates'][$i]['total'] = $estimate['total'];
            $data['estimates'][$i]['attach'] = $estimate['attach'];
            $data['estimates'][$i]['rec_type'] = $estimate['rec_type'];
            $data['estimates'][$i]['rec_id'] = $estimate['rec_id'];
            $data['estimates'][$i]['due_date'] = $estimate['due_date'];
            $data['estimates'][$i]['issue_date'] = $estimate['issue_date'];
            $data['estimates'][$i]['created_at'] = $estimate['created_at'];
            $data['estimates'][$i]['updated_at'] = $estimate['updated_at'];
            $data['estimates'][$i]['edit_link'] = $this->timber->config('request_url') . '/admin/estimates/edit/' . $estimate['in_id'];
            $data['estimates'][$i]['view_link'] = $this->timber->config('request_url') . '/admin/estimates/view/' . $estimate['in_id'];
            $data['estimates'][$i]['trash_link'] = $this->timber->config('request_url') . '/request/backend/ajax/estimates/delete';


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
        $data['add_estimate_action'] = $this->timber->config('request_url') . '/request/backend/ajax/estimates/add';
        $data['edit_estimate_action'] = $this->timber->config('request_url') . '/request/backend/ajax/estimates/edit';
        $data['delete_estimate_action'] = $this->timber->config('request_url') . '/request/backend/ajax/estimates/delete';
        $data['mark_estimate_action'] = $this->timber->config('request_url') . '/request/backend/ajax/estimates/mark';
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
     * Edit Expenses Data
     *
     * @since 1.0
     * @access public
     * @return array
     */
    public function editData($estimates_id)
    {
        $estimates_id = ( (boolean) filter_var($estimates_id, FILTER_VALIDATE_INT) ) ? filter_var($estimates_id, FILTER_SANITIZE_NUMBER_INT) : false;
        if( false === $estimates_id){
            $this->timber->redirect( $this->timber->config('request_url') . '/404' );
        }

        $data = array();

        # Bind Actions
        $data['add_estimate_action'] = $this->timber->config('request_url') . '/request/backend/ajax/estimates/add';
        $data['edit_estimate_action'] = $this->timber->config('request_url') . '/request/backend/ajax/estimates/edit';
        $data['delete_estimate_action'] = $this->timber->config('request_url') . '/request/backend/ajax/estimates/delete';
        $data['mark_estimate_action'] = $this->timber->config('request_url') . '/request/backend/ajax/estimates/mark';
        $data['site_currency'] = $this->timber->config('_site_currency_symbol');

        $user_id = $this->timber->security->getId();
        $estimate = $this->timber->invoice_model->getInvoiceByMultiple( array('type' => 2, 'in_id' => $estimates_id) );

        if( (false === $estimate) || !(is_object($estimate)) ){
            $this->timber->redirect( $this->timber->config('request_url') . '/404' );
        }

        $estimate = $estimate->as_array();

        $data['estimate_in_id'] = $estimate['in_id'];
        $data['estimate_reference'] = $estimate['reference'];
        $data['estimate_ref_id'] = "EST-" . str_pad($estimate['in_id'], 8, '0', STR_PAD_LEFT);

        if( !empty($data['estimate_ref_id']) ){
            $data['site_sub_page'] = $data['estimate_ref_id']  . " | ";
        }

        $data['estimate_owner_id'] = $estimate['owner_id'];
        $data['estimate_client_id'] = $estimate['client_id'];
        $data['estimate_status'] = $estimate['status'];

        $data['estimate_nice_status'] = str_replace(
            array('1','2','3','4','5','6'),
            array($this->timber->translator->trans('Opened'), $this->timber->translator->trans('Sent'), $this->timber->translator->trans('Accepted'), $this->timber->translator->trans('Rejected'), $this->timber->translator->trans('Invoiced'), $this->timber->translator->trans('Closed')), $estimate['status']
        );


        $data['estimate_type'] = $estimate['type'];
        $data['estimate_terms'] = unserialize($estimate['terms']);
        $data['estimate_tax'] = $estimate['tax'];
        $data['estimate_discount'] = $estimate['discount'];
        $data['estimate_total'] = $estimate['total'];
        $data['estimate_attach'] = $estimate['attach'];
        $data['estimate_rec_type'] = $estimate['rec_type'];
        $data['estimate_rec_id'] = $estimate['rec_id'];
        $data['estimate_due_date'] = $estimate['due_date'];
        $data['estimate_issue_date'] = $estimate['issue_date'];
        $data['estimate_created_at'] = $estimate['created_at'];
        $data['estimate_updated_at'] = $estimate['updated_at'];

        $data['estimate_attachments'] = array();
        $data['estimate_attachments_ids'] = array();
        $data['estimate_attachments_count'] = 0;

        # Attachments
        if( $estimate['attach'] == 'on' ){
            $attachments_ids = $this->timber->meta_model->getMetaByMultiple(array(
                'rec_id' => $estimate['in_id'],
                'rec_type' => 3,
                'me_key' => 'invoice_attachments_data'
            ));

            if( (false !== $attachments_ids) && (is_object($attachments_ids)) ){
                $attachments_ids = $attachments_ids->as_array();
                $data['estimate_attachments_ids'] = unserialize($attachments_ids['me_value']);

                foreach ($data['estimate_attachments_ids'] as $key => $value) {
                    $file = $this->timber->file_model->getFileById($value);
                    $data['estimate_attachments'][] = $file->as_array();
                }
                $data['estimate_attachments_count'] = count($data['estimate_attachments']);
            }
        }
        $data['estimate_attachments_ids'] = implode(',', $data['estimate_attachments_ids']);

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
     * View Expenses Data
     *
     * @since 1.0
     * @access public
     * @return array
     */
    public function viewData($estimates_id)
    {
        $estimates_id = ( (boolean) filter_var($estimates_id, FILTER_VALIDATE_INT) ) ? filter_var($estimates_id, FILTER_SANITIZE_NUMBER_INT) : false;
        if( false === $estimates_id){
            $this->timber->redirect( $this->timber->config('request_url') . '/404' );
        }

        $data = array();

        # Bind Actions
        $data['add_estimate_action'] = $this->timber->config('request_url') . '/request/backend/ajax/estimates/add';
        $data['edit_estimate_action'] = $this->timber->config('request_url') . '/request/backend/ajax/estimates/edit';
        $data['delete_estimate_action'] = $this->timber->config('request_url') . '/request/backend/ajax/estimates/delete';
        $data['mark_estimate_action'] = $this->timber->config('request_url') . '/request/backend/ajax/estimates/mark';
        $data['site_currency'] = $this->timber->config('_site_currency_symbol');

        $user_id = $this->timber->security->getId();
        $estimate = $this->timber->invoice_model->getInvoiceByMultiple( array('type' => 2, 'in_id' => $estimates_id) );


        if( (false === $estimate) || !(is_object($estimate)) ){
            $this->timber->redirect( $this->timber->config('request_url') . '/404' );
        }
        $estimate = $estimate->as_array();

        if( ($this->timber->access->getRule() == 'client') && ($user_id != $estimate['client_id']) ){
            $this->timber->redirect( $this->timber->config('request_url') . '/404' );
        }

        $client_data = $this->timber->user_model->getUserById( $estimate['client_id'] );
        if( (false === $client_data) || !(is_object($client_data)) ){
            $this->timber->redirect( $this->timber->config('request_url') . '/500' );
        }

        $client_data = $client_data->as_array();

        $data['estimate_client_email'] = $client_data['email'];
        $data['estimate_client_grav_id'] = $client_data['grav_id'];
        $data['estimate_client_user_name'] = $client_data['user_name'];
        $data['estimate_client_first_name'] = $client_data['first_name'];
        $data['estimate_client_last_name'] = $client_data['last_name'];
        $data['estimate_client_full_name'] = trim( $client_data['first_name'] . " " . $client_data['last_name'] );
        $data['estimate_client_company'] = $client_data['company'];
        $data['estimate_client_job'] = $client_data['job'];
        $data['estimate_client_website'] = $client_data['website'];
        $data['estimate_client_phone_num'] = $client_data['phone_num'];
        $data['estimate_client_zip_code'] = $client_data['zip_code'];
        $data['estimate_client_vat_nubmer'] = $client_data['vat_nubmer'];
        $data['estimate_client_country'] = $client_data['country'];
        $data['estimate_client_city'] = $client_data['city'];
        $data['estimate_client_address1'] = $client_data['address1'];
        $data['estimate_client_address2'] = $client_data['address2'];

        $data['estimate_in_id'] = $estimate['in_id'];
        $data['estimate_reference'] = $estimate['reference'];
        $data['estimate_ref_id'] = "EST-" . str_pad($estimate['in_id'], 8, '0', STR_PAD_LEFT);

        if( !empty($data['estimate_ref_id']) ){
            $data['site_sub_page'] = $data['estimate_ref_id']  . " | ";
        }

        $data['estimate_owner_id'] = $estimate['owner_id'];
        $data['estimate_client_id'] = $estimate['client_id'];
        $data['estimate_status'] = $estimate['status'];
        $data['estimate_nice_status'] = str_replace(
            array('1','2','3','4','5','6'),
            array($this->timber->translator->trans('Opened'), $this->timber->translator->trans('Sent'), $this->timber->translator->trans('Accepted'), $this->timber->translator->trans('Rejected'), $this->timber->translator->trans('Invoiced'), $this->timber->translator->trans('Closed')), $estimate['status']
        );
        $data['estimate_type'] = $estimate['type'];
        $data['estimate_terms'] = unserialize($estimate['terms']);
        $data['estimate_tax'] = $estimate['tax'];
        $data['estimate_discount'] = $estimate['discount'];

        $data['estimate_tax_currency'] = ($data['estimate_terms']['overall']['tax_type'] == 'percent') ? "%" : $this->timber->config('_site_currency_symbol');
        $data['estimate_discount_currency'] = ($data['estimate_terms']['overall']['discount_type'] == 'percent') ? "%" : $this->timber->config('_site_currency_symbol');

        $data['estimate_total'] = $estimate['total'];
        $data['estimate_attach'] = $estimate['attach'];
        $data['estimate_rec_type'] = $estimate['rec_type'];
        $data['estimate_rec_id'] = $estimate['rec_id'];
        $data['estimate_due_date'] = $estimate['due_date'];
        $data['estimate_issue_date'] = $estimate['issue_date'];
        $data['estimate_created_at'] = $estimate['created_at'];
        $data['estimate_updated_at'] = $estimate['updated_at'];

        $data['estimate_edit_link'] = $this->timber->config('request_url') . '/admin/estimates/edit/' . $estimate['in_id'];
        $data['estimate_view_link'] = $this->timber->config('request_url') . '/admin/estimates/view/' . $estimate['in_id'];

        $data['estimate_attachments'] = array();
        $data['estimate_attachments_ids'] = array();
        $data['estimate_attachments_count'] = 0;

        # Attachments
        if( $estimate['attach'] == 'on' ){
            $attachments_ids = $this->timber->meta_model->getMetaByMultiple(array(
                'rec_id' => $estimate['in_id'],
                'rec_type' => 3,
                'me_key' => 'invoice_attachments_data'
            ));

            if( (false !== $attachments_ids) && (is_object($attachments_ids)) ){
                $attachments_ids = $attachments_ids->as_array();
                $data['estimate_attachments_ids'] = unserialize($attachments_ids['me_value']);

                foreach ($data['estimate_attachments_ids'] as $key => $value) {
                    $file = $this->timber->file_model->getFileById($value);
                    $data['estimate_attachments'][] = $file->as_array();
                }
                $data['estimate_attachments_count'] = count($data['estimate_attachments']);
            }
        }

        $data['estimate_attachments_ids'] = implode(',', $data['estimate_attachments_ids']);

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
