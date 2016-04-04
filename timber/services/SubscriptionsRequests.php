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
 * Subscriptions Requests Services
 *
 * @since 1.0
 */
class SubscriptionsRequests extends \Timber\Services\Base {

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
     * Add New Subscription
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function addSubscription()
    {

        $subscription_data = $this->timber->validator->clear(array(
            'sub_client_id' => array(
                'req' => 'post',
                'sanit' => 'snumberint',
                'valid' => 'vnotempty&vint',
                'default' => '',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('Please select client.'),
                    'vint' => $this->timber->translator->trans('Please select client.')
                ),
            ),
            # 1- 2- 3-
            'sub_status' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vinarray:1,2,3',
                'default' => '1',
                'errors' => array(),
            ),
            'sub_begin_at' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vdate',
                'default' => '',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('Begin date is invalid.'),
                    'vdate' => $this->timber->translator->trans('Begin date is invalid.'),
                ),
            ),
            'sub_end_at' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vdate',
                'default' => '',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('End date is invalid.'),
                    'vdate' => $this->timber->translator->trans('End date is invalid.'),
                ),
            ),
            'sub_terms' => array(
                'req' => 'post',
                'sanit' => 'ssubs',
                'valid' => 'vsubs',
                'default' => '',
                'errors' => array(
                    'vsubs' =>  $this->timber->translator->trans('Invalid subscription items detected or subscription overall data is invalid.'),
                ),
            ),
            'sub_frequency_value' => array(
                'req' => 'post',
                'sanit' => 'snumberint',
                'valid' => 'vnotempty&vint&vintbetween:1,15000',
                'default' => '1',
                'errors' => array(
                    'vintbetween' =>  $this->timber->translator->trans('Provided frequency not a valid number.'),
                ),
            ),
            'sub_frequency_type' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vinarray:d,w,m,y',
                'default' => 'm',
            ),
            'sub_attachments' => array(
                'req' => 'post',
                'sanit' => 'sfiles',
                'valid' => 'vnotempty&vfiles:' . implode(',' , unserialize($this->timber->config('_allowed_upload_extensions'))),
                'default' => '',
                'errors' => array(),
            ),
        ));

        if( true === $subscription_data['error_status'] ){
            $this->response['data'] = $subscription_data['error_text'];
            return false;
        }

        if( !($this->timber->access->checkPermission('add.subscriptions')) ){
            $this->response['data'] = $this->timber->translator->trans('Invalid Request.');
            return false;
        }

        if( !($subscription_data['sub_end_at']['value'] >= $subscription_data['sub_begin_at']['value']) ){
            $this->response['data'] = $this->timber->translator->trans('End date must not be less than Begin date.');
            return false;
        }

        $new_subscription_data = array();
        $new_subscription_data['reference'] = $this->timber->subscription_model->newReference("SUB");
        $new_subscription_data['owner_id'] = $this->timber->security->getId();
        $new_subscription_data['client_id'] = $subscription_data['sub_client_id']['value'];

        # 1- 2- 3-
        $new_subscription_data['status'] = $subscription_data['sub_status']['value'];

        $new_subscription_data['frequency'] = $subscription_data['sub_frequency_value']['value'] . "-" . $subscription_data['sub_frequency_type']['value'];


        $new_subscription_data['terms'] = serialize(array(
            'notes' => $subscription_data['sub_terms']['value']['notes'],
            'items' => $subscription_data['sub_terms']['value']['items'],
            'overall' =>  $subscription_data['sub_terms']['value']['overall']
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
            )
        )*/

        $new_subscription_data['tax'] = $subscription_data['sub_terms']['value']['overall']['tax_value'];
        $new_subscription_data['discount'] = $subscription_data['sub_terms']['value']['overall']['discount_value'];
        $new_subscription_data['total'] = $subscription_data['sub_terms']['value']['overall']['total_value'];

        $new_subscription_data['begin_at'] = $subscription_data['sub_begin_at']['value'];
        $new_subscription_data['end_at'] = $subscription_data['sub_end_at']['value'];

        $new_subscription_data['attach'] = 'off';
        $new_subscription_data['created_at'] = $this->timber->time->getCurrentDate(true);
        $new_subscription_data['updated_at'] = $this->timber->time->getCurrentDate(true);

        # Add Attachments
        $sub_attachments = $subscription_data['sub_attachments']['value'];

        $files_ids = array();
        if( (is_array($sub_attachments)) && (count($sub_attachments) > 0) ){
            foreach( $sub_attachments as $sub_attachment ) {
                $sub_attachment = explode('--||--', $sub_attachment);
                $files_ids[] = $this->timber->file_model->addFile(array(
                    'title' => $sub_attachment[1],
                    'hash' => $sub_attachment[0],
                    'owner_id' => $this->timber->security->getId(),
                    'description' => "Subscription Attachments",
                    'storage' => 2,
                    'type' => pathinfo($sub_attachment[1], PATHINFO_EXTENSION),
                    'uploaded_at' => $this->timber->time->getCurrentDate(true),
                ));
            }
            $new_subscription_data['attach'] = 'on';
        }

        $subscription_id = $this->timber->subscription_model->addSubscription($new_subscription_data);

        # Add Metas
        $meta_status = true;

        $meta_status &= (boolean) $this->timber->meta_model->addMeta(array(
            'rec_id' => $subscription_id,
            'rec_type' => 8,
            'me_key' => 'subscription_attachments_data',
            'me_value' => serialize($files_ids),
        ));

        $meta_status &= (boolean) $this->timber->meta_model->addMeta(array(
            'rec_id' => $subscription_id,
            'rec_type' => 8,
            'me_key' => 'subscription_invoices_data',
            'me_value' => 1,
        ));

        # Subscription Notification
        $this->timber->notify->increment('subscriptions_notif', $new_subscription_data['client_id']);
        $this->timber->notify->setMailerCron(array(
            'method_name' => 'newSubscriptionEmailNotifier',
            'sub_id' => $subscription_id,
        ));

        if( $subscription_id && $meta_status ){
            $this->response['status'] = 'success';
            $this->response['data'] = $this->timber->translator->trans('Subscription created successfully.');
            $this->response['next_link'] = $this->timber->config('request_url') . '/admin/subscriptions/view/' . $subscription_id;
            return true;
        }else{
            $this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
            return false;
        }

    }

    /**
     * Edit Subscription
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function editSubscription()
    {
        $subscription_data = $this->timber->validator->clear(array(
            'sub_id' => array(
                'req' => 'post',
                'sanit' => 'snumberint',
                'valid' => 'vnotempty&vint',
                'default' => '',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('Invalid Request.'),
                    'vint' => $this->timber->translator->trans('Invalid Request.')
                ),
            ),
            'sub_client_id' => array(
                'req' => 'post',
                'sanit' => 'snumberint',
                'valid' => 'vnotempty&vint',
                'default' => '',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('Please select client.'),
                    'vint' => $this->timber->translator->trans('Please select client.')
                ),
            ),
            # 1- 2- 3-
            'sub_status' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vinarray:1,2,3',
                'default' => '1',
                'errors' => array(),
            ),
            'sub_begin_at' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vdate',
                'default' => '',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('Begin date is invalid.'),
                    'vdate' => $this->timber->translator->trans('Begin date is invalid.'),
                ),
            ),
            'sub_end_at' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vdate',
                'default' => '',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('End date is invalid.'),
                    'vdate' => $this->timber->translator->trans('End date is invalid.'),
                ),
            ),
            'sub_terms' => array(
                'req' => 'post',
                'sanit' => 'ssubs',
                'valid' => 'vsubs',
                'default' => '',
                'errors' => array(
                    'vsubs' =>  $this->timber->translator->trans('Invalid subscription items detected or subscription overall data is invalid.'),
                ),
            ),
            'sub_frequency_value' => array(
                'req' => 'post',
                'sanit' => 'snumberint',
                'valid' => 'vnotempty&vint&vintbetween:1,15000',
                'default' => '1',
                'errors' => array(
                    'vintbetween' =>  $this->timber->translator->trans('Provided frequency not a valid number.'),
                ),
            ),
            'sub_frequency_type' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vinarray:d,w,m,y',
                'default' => 'm',
                'errors' => array(),
            ),
            'sub_attachments' => array(
                'req' => 'post',
                'sanit' => 'sfiles',
                'valid' => 'vnotempty&vfiles:' . implode(',' , unserialize($this->timber->config('_allowed_upload_extensions'))),
                'default' => '',
                'errors' => array(),
            ),
            'sub_old_attachments' => array(
                'req' => 'post',
                'sanit' => 'sfilesids',
                'valid' => 'vfilesids',
                'default' => array(),
                'errors' => array(),
            ),
        ));

        if( true === $subscription_data['error_status'] ){
            $this->response['data'] = $subscription_data['error_text'];
            return false;
        }

        if( !($this->timber->access->checkPermission('edit.subscriptions')) ){
            $this->response['data'] = $this->timber->translator->trans('Invalid Request.');
            return false;
        }

        $subscription = $this->timber->subscription_model->getSubscriptionById($subscription_data['sub_id']['value']);

        if( (false === $subscription) || !(is_object($subscription)) ){
            $this->response['data'] = $this->timber->translator->trans('Invalid Request.');
            return false;
        }

        $subscription = $subscription->as_array();

        if( !($subscription_data['sub_end_at']['value'] >= $subscription_data['sub_begin_at']['value']) ){
            $this->response['data'] = $this->timber->translator->trans('End date must not be less than Begin date.');
            return false;
        }

        $new_subscription_data = array();
        $new_subscription_data['su_id'] = $subscription_data['sub_id']['value'];
        $new_subscription_data['client_id'] = $subscription_data['sub_client_id']['value'];

        # 1- 2- 3-
        $new_subscription_data['status'] = $subscription_data['sub_status']['value'];

        $new_subscription_data['frequency'] = $subscription_data['sub_frequency_value']['value'] . "-" . $subscription_data['sub_frequency_type']['value'];

        $new_subscription_data['terms'] = serialize(array(
            'notes' => $subscription_data['sub_terms']['value']['notes'],
            'items' => $subscription_data['sub_terms']['value']['items'],
            'overall' =>  $subscription_data['sub_terms']['value']['overall']
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
            )
        )*/

        $new_subscription_data['tax'] = $subscription_data['sub_terms']['value']['overall']['tax_value'];
        $new_subscription_data['discount'] = $subscription_data['sub_terms']['value']['overall']['discount_value'];
        $new_subscription_data['total'] = $subscription_data['sub_terms']['value']['overall']['total_value'];

        $new_subscription_data['begin_at'] = $subscription_data['sub_begin_at']['value'];
        $new_subscription_data['end_at'] = $subscription_data['sub_end_at']['value'];

        $new_subscription_data['attach'] = 'off';
        $new_subscription_data['updated_at'] = $this->timber->time->getCurrentDate(true);

        # Add Attachments
        $sub_attachments = $subscription_data['sub_attachments']['value'];

        $files_ids = array();
        if( $subscription['attach'] == 'on' ){
            $attachments_ids = $this->timber->meta_model->getMetaByMultiple(array(
                'rec_id' => $subscription_data['sub_id']['value'],
                'rec_type' => 8,
                'me_key' => 'subscription_attachments_data'
            ));
            if( (false !== $attachments_ids) && (is_object($attachments_ids)) ){
                $attachments_ids = $attachments_ids->as_array();
                $files_ids = unserialize($attachments_ids['me_value']);

                foreach ($files_ids as $key => $value) {
                    if( !in_array( $value, $subscription_data['sub_old_attachments']['value'] ) ){
                        unset($files_ids[$key]);
                    }
                }
            }
        }

        if( (is_array($sub_attachments)) && (count($sub_attachments) > 0) ){
            foreach( $sub_attachments as $sub_attachment ) {
                $sub_attachment = explode('--||--', $sub_attachment);
                $files_ids[] = $this->timber->file_model->addFile(array(
                    'title' => $sub_attachment[1],
                    'hash' => $sub_attachment[0],
                    'owner_id' => $this->timber->security->getId(),
                    'description' => "Subscription Attachments",
                    'storage' => 2,
                    'type' => pathinfo($sub_attachment[1], PATHINFO_EXTENSION),
                    'uploaded_at' => $this->timber->time->getCurrentDate(true),
                ));
            }
        }

        if( count($files_ids) > 0 ){
            $new_subscription_data['attach'] = 'on';
        }

        $action_status = (boolean) $this->timber->subscription_model->updateSubscriptionById($new_subscription_data);

        $action_status &= (boolean) $this->timber->meta_model->updateMetaByMultiple(array(
            'rec_id' => $subscription_data['sub_id']['value'],
            'rec_type' => 8,
            'me_key' => 'subscription_attachments_data',
            'me_value' => serialize($files_ids),
        ));

        if( $action_status ){
            $this->response['status'] = 'success';
            $this->response['data'] = $this->timber->translator->trans('Subscription updated successfully.');
            return true;
        }else{
            $this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
            return false;
        }
    }

    /**
     * Invoice Subscriptions
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function invoiceSubscription()
    {
        $subscription_data = $this->timber->validator->clear(array(
            'sub_id' => array(
                'req' => 'post',
                'sanit' => 'snumberint',
                'valid' => 'vnotempty&vint',
                'default' => '',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('Invalid Request.'),
                    'vint' => $this->timber->translator->trans('Invalid Request.')
                )
            )
        ));


        if( true === $subscription_data['error_status'] ){
            $this->response['data'] = $subscription_data['error_text'];
            return false;
        }

        if( !($this->timber->access->checkPermission('view.subscriptions')) ){
            $this->response['data'] = $this->timber->translator->trans('Invalid Request.');
            return false;
        }

        $subscription = $this->timber->subscription_model->getSubscriptionById($subscription_data['sub_id']['value']);

        if( (false === $subscription) || !(is_object($subscription)) ){
            $this->response['data'] = $this->timber->translator->trans('Invalid Request.');
            return false;
        }

        $subscription = $subscription->as_array();

        $new_invoice_data = array();

        $new_invoice_data['reference'] = $this->timber->invoice_model->newReference("INV");
        $new_invoice_data['owner_id'] = $this->timber->security->getId();
        $new_invoice_data['client_id'] = $subscription['client_id'];
        $new_invoice_data['status'] = 3;
        $new_invoice_data['type'] = 1;
        $new_invoice_data['terms'] = $subscription['terms'];

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


        $new_invoice_data['tax'] = $subscription['tax'];
        $new_invoice_data['discount'] = $subscription['discount'];
        $new_invoice_data['total'] = $subscription['total'];

        # (8) subscription (11) project (12) none
        $new_invoice_data['rec_type'] = 8;
        $new_invoice_data['rec_id'] = $subscription_data['sub_id']['value'];

        $frequency = explode('-', $subscription['frequency']);
        $frequency = $frequency[0] * intval(str_replace(array('d','w','m','y'), array(1, 7, 30, 365), $frequency[1]));

        $invoices_count = $this->timber->meta_model->getMetaByMultiple(array(
                'rec_id' => $subscription['su_id'],
                'rec_type' => 8,
                'me_key' => 'subscription_invoices_data'
        ));

        if( (false === $invoices_count) || !(is_object($invoices_count)) ){
            $this->response['data'] = $this->timber->translator->trans('Please delete this subscription and create again.');
            return false;
        }

        $invoices_count = $invoices_count->as_array();

        $new_invoice_data['due_date'] = $this->timber->time->dateAddition( $subscription['begin_at'], '+' . ($frequency * ($invoices_count['me_value'] - 1)) );
        $new_invoice_data['issue_date'] = $this->timber->time->getCurrentDate(true);

        if( $new_invoice_data['due_date'] >= $subscription['end_at'] ){
            $this->response['data'] = $this->timber->translator->trans('Subscription end date reached. Please update subscription.');
            return false;
        }

        $new_invoice_data['attach'] = 'off';
        $new_invoice_data['created_at'] = $this->timber->time->getCurrentDate(true);
        $new_invoice_data['updated_at'] = $this->timber->time->getCurrentDate(true);

        $invoice_id = $this->timber->invoice_model->addInvoice($new_invoice_data);

        # Add Metas
        $meta_status = true;

        $meta_status &= (boolean) $this->timber->meta_model->addMeta(array(
            'rec_id' => $invoice_id,
            'rec_type' => 3,
            'me_key' => 'invoice_attachments_data',
            'me_value' => serialize(array()),
        ));

        $meta_status &= (boolean) $this->timber->meta_model->updateMetaByMultiple(array(
            'rec_id' => $subscription['su_id'],
            'rec_type' => 8,
            'me_key' => 'subscription_invoices_data',
            'me_value' => ($invoices_count['me_value'] + 1),
        ));

        if( $invoice_id && $meta_status ){
            $this->response['status'] = 'success';
            $this->response['data'] = $this->timber->translator->trans('Subscription invoiced successfully.');
            return true;
        }else{
            $this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
            return false;
        }
    }

    /**
     * Delete Subscription
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function deleteSubscription()
    {
        $subscription_id = ( (isset($_POST['subscription_id'])) && ((boolean) filter_var($_POST['subscription_id'], FILTER_VALIDATE_INT)) ) ? filter_var($_POST['subscription_id'], FILTER_SANITIZE_NUMBER_INT) : false;

        if( !($this->timber->access->checkPermission('delete.subscriptions')) ){
            $this->response['data'] = $this->timber->translator->trans('Invalid Request.');
            return false;
        }

        if( $subscription_id === false ){
            $this->response['data'] = $this->timber->translator->trans('Invalid Request.');
            return false;
        }

        # Delete Subscription and its meta data
        $action_status = (boolean) $this->timber->subscription_model->deleteSubscriptionById($subscription_id);
        $action_status &= (boolean) $this->timber->meta_model->dumpMetas(false, $subscription_id, 8);

        if( $action_status ){
            $this->response['status'] = 'success';
            $this->response['data'] = $this->timber->translator->trans('Subscription deleted successfully.');
            return true;
        }else{
            $this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
            return false;
        }
    }

    /**
     * Mark Subscription
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function markSubscription()
    {
        $subscription_id = ( (isset($_POST['subscription_id'])) && ((boolean) filter_var($_POST['subscription_id'], FILTER_VALIDATE_INT)) ) ? filter_var($_POST['subscription_id'], FILTER_SANITIZE_NUMBER_INT) : false;
        $action = ( (isset($_POST['action'])) && (in_array($_POST['action'], array('checkout', 'un_checkout', 'un_paid', 'paid'))) ) ? $_POST['action'] : false;

        # Mark as un paid subscription
        if( 'un_paid' == $action ){
            $action_status = (boolean) $this->timber->subscription_model->updateSubscriptionById(array(
                'in_id' => $subscription_id,
                'status' => '1'
            ));

            $this->response['data'] = $this->timber->translator->trans('Subscription updated successfully.');
        }

        # Mark as paid subscription
        if( 'paid' == $action ){
            $action_status = (boolean) $this->timber->subscription_model->updateSubscriptionById(array(
                'in_id' => $subscription_id,
                'status' => '2'
            ));

            $this->response['data'] = $this->timber->translator->trans('Subscription updated successfully.');
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