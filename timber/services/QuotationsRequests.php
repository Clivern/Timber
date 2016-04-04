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
 * Quotations Requests Services
 *
 * @since 1.0
 */
class QuotationsRequests extends \Timber\Services\Base {

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
     * Add New Quotation
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function addQuotation()
    {

        $quotation_data = $this->timber->validator->clear(array(
            'quotation_title' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:2,100',
                'default' => '',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('Quotation title is invalid.'),
                    'vstrlenbetween' => $this->timber->translator->trans('Quotation title is invalid.'),
                ),
            ),
            'quotation_terms' => array(
                'req' => 'post',
                'sanit' => 'squot',
                'valid' => 'vquot',
                'default' => '',
                'errors' => array(
                    'vsubs' =>  $this->timber->translator->trans('Invalid quotation elements detected.'),
                ),
            )
        ));

        if( true === $quotation_data['error_status'] ){
            $this->response['data'] = $quotation_data['error_text'];
            return false;
        }

        if( !($this->timber->access->checkPermission('add.quotations')) ){
            $this->response['data'] = $this->timber->translator->trans('Invalid Request.');
            return false;
        }

        $new_quotation_data = array();
        $new_quotation_data['title'] = $quotation_data['quotation_title']['value'];
        $new_quotation_data['reference'] = $this->timber->quotation_model->newReference("QUO");
        $new_quotation_data['owner_id'] = $this->timber->security->getId();
        $new_quotation_data['terms'] = serialize($quotation_data['quotation_terms']['value']);
        $new_quotation_data['created_at'] = $this->timber->time->getCurrentDate(true);
        $new_quotation_data['updated_at'] = $this->timber->time->getCurrentDate(true);

        $quotation_id = $this->timber->quotation_model->addQuotation($new_quotation_data);

        $meta_status = true;

        $meta_status &= (boolean) $this->timber->meta_model->addMeta(array(
            'rec_id' => $quotation_id,
            'rec_type' => 7,
            'me_key' => 'quotation_available_access',
            'me_value' => serialize(array()),
        ));

        if( $quotation_id && $meta_status ){
            $this->response['status'] = 'success';
            $this->response['data'] = $this->timber->translator->trans('Quotation created successfully.');
            $this->response['next_link'] = $this->timber->config('request_url') . '/admin/quotations/view/' . $quotation_id;
            return true;
        }else{
            $this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
            return false;
        }

    }

    /**
     * Submit Quotation
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function submitQuotation()
    {
        $quotation_id = ( (isset($_POST['quotation_id'])) && ((boolean) filter_var($_POST['quotation_id'], FILTER_VALIDATE_INT)) ) ? filter_var($_POST['quotation_id'], FILTER_SANITIZE_NUMBER_INT) : false;

        if( (false === $quotation_id) ){
            $this->response['data'] = $this->timber->translator->trans('Invalid Request.');
            return false;
        }

        $quotation = $this->timber->quotation_model->getQuotationById( $quotation_id );

        if( (false === $quotation) || !(is_object($quotation)) ){
            $this->response['data'] = $this->timber->translator->trans('Invalid Request.');
            return false;
        }

        $quotation = $quotation->as_array();

        $quotation_terms = unserialize($quotation['terms']);

        $submission = array(
            'submitter' => $this->timber->security->getId(),
            'created_at' => $this->timber->time->getCurrentDate(true),
            'values' => array(),
        );

        $rule = array();

        $i = 0;
        foreach ($quotation_terms as $key => $term) {

            if( 'text_elem' == $term['type'] ){

                if($term['required'] == 1){
                    $rule[$term['name']] = array(
                        'req' => 'post',
                        'sanit' => 'sstring',
                        'valid' => 'vnotempty&vstrlenbetween:0,150',
                        'default' => '',
                        'errors' => array(
                            'vstrlenbetween' => sprintf($this->timber->translator->trans('Provided %1$s is invalid.'), $term['label']),
                        ),
                    );
                }else{
                    $rule[$term['name']] = array(
                        'req' => 'post',
                        'sanit' => 'sstring',
                        'valid' => 'vnotempty&vstrlenbetween:1,150',
                        'default' => '',
                        'errors' => array(
                            'vnotempty' => sprintf($this->timber->translator->trans('Provided %1$s is invalid.'), $term['label']),
                            'vstrlenbetween' => sprintf($this->timber->translator->trans('Provided %1$s is invalid.'), $term['label']),
                        ),
                    );
                }

            }elseif( 'para_elem' == $term['type'] ){

                if($term['required'] == 1){
                    $rule[$term['name']] = array(
                        'req' => 'post',
                        'sanit' => 'sstring',
                        'valid' => 'vnotempty&vstrlenbetween:0,1000',
                        'default' => '',
                        'errors' => array(
                            'vstrlenbetween' => sprintf($this->timber->translator->trans('Provided %1$s is invalid.'), $term['label']),
                        ),
                    );
                }else{
                    $rule[$term['name']] = array(
                        'req' => 'post',
                        'sanit' => 'sstring',
                        'valid' => 'vnotempty&vstrlenbetween:1,1000',
                        'default' => '',
                        'errors' => array(
                            'vnotempty' => sprintf($this->timber->translator->trans('Provided %1$s is invalid.'), $term['label']),
                            'vstrlenbetween' => sprintf($this->timber->translator->trans('Provided %1$s is invalid.'), $term['label']),
                        ),
                    );
                }

            }elseif( 'date_elem' == $term['type'] ){

                if($term['required'] == 1){
                    $rule[$term['name']] = array(
                        'req' => 'post',
                        'sanit' => 'sstring',
                        'valid' => 'vnotempty&vdate',
                        'default' => '',
                        'errors' => array(),
                    );
                }else{
                    $rule[$term['name']] = array(
                        'req' => 'post',
                        'sanit' => 'sstring',
                        'valid' => 'vnotempty&vdate',
                        'default' => '',
                        'errors' => array(
                            'vnotempty' => sprintf($this->timber->translator->trans('Provided %1$s is invalid.'), $term['label']),
                            'vdate' => sprintf($this->timber->translator->trans('Provided %1$s is invalid.'), $term['label']),
                        ),
                    );
                }

            }elseif( 'mult_elem' == $term['type'] ){

                $rule[$term['name']] = array(
                    'req' => 'post',
                    'sanit' => 'sstring',
                    'valid' => 'vnotempty&vallinarray:' . implode(',', $term['data']['items']),
                    'default' => '',
                    'errors' => array(),
                );

            }elseif( 'drop_elem' == $term['type'] ){

                $rule[$term['name']] = array(
                    'req' => 'post',
                    'sanit' => 'sstring',
                    'valid' => 'vnotempty&vinarray:' . implode(',', $term['data']['items']),
                    'default' => '',
                    'errors' => array(),
                );


            }elseif( 'chek_elem' == $term['type'] ){

                $rule[$term['name']] = array(
                    'req' => 'post',
                    'sanit' => 'sstring',
                    'valid' => 'vnotempty&vinarray:Yes,No',
                    'default' => 'No',
                    'errors' => array(),
                );

            }

            $i += 1;
        }

        $submission_data = $user_data = $this->timber->validator->clear($rule);

        if( true === $submission_data['error_status'] ){
            $this->response['data'] = $submission_data['error_text'];
            return false;
        }

        foreach ($quotation_terms as $key => $term) {

            if( !(isset($submission_data[$term['name']])) && !(isset($submission_data[$term['name']]['value'])) ){
                continue;
            }

            $field_key = $term['label'];
            $field_value = $submission_data[$term['name']]['value'];

            $submission['values'][] = array(
                'key' => $field_key,
                'value' => $field_value
            );
        }

        if( ($this->timber->access->getRule() == 'client') ){

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

            if( !in_array($this->timber->security->getId(), $clients) ){
                $this->response['data'] = $this->timber->translator->trans('Invalid Request.');
                return false;
            }

            if(($key = array_search($this->timber->security->getId(), $clients)) !== false) {
                unset($clients[$key]);
            }

            $action_status = (boolean) $this->timber->meta_model->updateMetaByMultiple(array(
                'rec_id' => $quotation_id,
                'rec_type' => 7,
                'me_key' => 'quotation_available_access',
                'me_value' => serialize($clients),
            ));
        }

        $action_status = (boolean) $this->timber->meta_model->addMeta(array(
            'rec_id' => $quotation_id,
            'rec_type' => 7,
            'me_key' => 'quotation_submission',
            'me_value' => serialize($submission),
        ));

        if( $action_status ){
            $this->response['status'] = 'success';
            $this->response['data'] = $this->timber->translator->trans('Submission created successfully.');
            return true;
        }else{
            $this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
            return false;
        }
    }

    /**
     * Submit Quotation
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function publicSubmitQuotation()
    {
        $quotation_id = ( (isset($_POST['quotation_id'])) && ((boolean) filter_var($_POST['quotation_id'], FILTER_VALIDATE_INT)) ) ? filter_var($_POST['quotation_id'], FILTER_SANITIZE_NUMBER_INT) : false;
        $email = ( (isset($_POST['email'])) && (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) ) ? filter_var(trim($_POST['email']),FILTER_SANITIZE_EMAIL) : false;

        if( (false === $quotation_id) ){
            $this->response['data'] = $this->timber->translator->trans('Invalid Request.');
            return false;
        }

        $quotation = $this->timber->quotation_model->getQuotationById( $quotation_id );

        if( (false === $quotation) || !(is_object($quotation)) ){
            $this->response['data'] = $this->timber->translator->trans('Invalid Request.');
            return false;
        }

        $quotation = $quotation->as_array();

        $quotation_terms = unserialize($quotation['terms']);

        $submission = array(
            'submitter' => $email,
            'created_at' => $this->timber->time->getCurrentDate(true),
            'values' => array(),
        );

        $rule = array();

        $i = 0;
        foreach ($quotation_terms as $key => $term) {

            if( 'text_elem' == $term['type'] ){

                if($term['required'] == 1){
                    $rule[$term['name']] = array(
                        'req' => 'post',
                        'sanit' => 'sstring',
                        'valid' => 'vnotempty&vstrlenbetween:0,150',
                        'default' => '',
                        'errors' => array(
                            'vstrlenbetween' => sprintf($this->timber->translator->trans('Provided %1$s is invalid.'), $term['label']),
                        ),
                    );
                }else{
                    $rule[$term['name']] = array(
                        'req' => 'post',
                        'sanit' => 'sstring',
                        'valid' => 'vnotempty&vstrlenbetween:1,150',
                        'default' => '',
                        'errors' => array(
                            'vnotempty' => sprintf($this->timber->translator->trans('Provided %1$s is invalid.'), $term['label']),
                            'vstrlenbetween' => sprintf($this->timber->translator->trans('Provided %1$s is invalid.'), $term['label']),
                        ),
                    );
                }

            }elseif( 'para_elem' == $term['type'] ){

                if($term['required'] == 1){
                    $rule[$term['name']] = array(
                        'req' => 'post',
                        'sanit' => 'sstring',
                        'valid' => 'vnotempty&vstrlenbetween:0,1000',
                        'default' => '',
                        'errors' => array(
                            'vstrlenbetween' => sprintf($this->timber->translator->trans('Provided %1$s is invalid.'), $term['label']),
                        ),
                    );
                }else{
                    $rule[$term['name']] = array(
                        'req' => 'post',
                        'sanit' => 'sstring',
                        'valid' => 'vnotempty&vstrlenbetween:1,1000',
                        'default' => '',
                        'errors' => array(
                            'vnotempty' => sprintf($this->timber->translator->trans('Provided %1$s is invalid.'), $term['label']),
                            'vstrlenbetween' => sprintf($this->timber->translator->trans('Provided %1$s is invalid.'), $term['label']),
                        ),
                    );
                }

            }elseif( 'date_elem' == $term['type'] ){

                if($term['required'] == 1){
                    $rule[$term['name']] = array(
                        'req' => 'post',
                        'sanit' => 'sstring',
                        'valid' => 'vnotempty&vdate',
                        'default' => '',
                        'errors' => array(),
                    );
                }else{
                    $rule[$term['name']] = array(
                        'req' => 'post',
                        'sanit' => 'sstring',
                        'valid' => 'vnotempty&vdate',
                        'default' => '',
                        'errors' => array(
                            'vnotempty' => sprintf($this->timber->translator->trans('Provided %1$s is invalid.'), $term['label']),
                            'vdate' => sprintf($this->timber->translator->trans('Provided %1$s is invalid.'), $term['label']),
                        ),
                    );
                }

            }elseif( 'mult_elem' == $term['type'] ){

                $rule[$term['name']] = array(
                    'req' => 'post',
                    'sanit' => 'sstring',
                    'valid' => 'vnotempty&vallinarray:' . implode(',', $term['data']['items']),
                    'default' => '',
                    'errors' => array(),
                );

            }elseif( 'drop_elem' == $term['type'] ){

                $rule[$term['name']] = array(
                    'req' => 'post',
                    'sanit' => 'sstring',
                    'valid' => 'vnotempty&vinarray:' . implode(',', $term['data']['items']),
                    'default' => '',
                    'errors' => array(),
                );


            }elseif( 'chek_elem' == $term['type'] ){

                $rule[$term['name']] = array(
                    'req' => 'post',
                    'sanit' => 'sstring',
                    'valid' => 'vnotempty&vinarray:Yes,No',
                    'default' => 'No',
                    'errors' => array(),
                );

            }

            $i += 1;
        }

        $submission_data = $user_data = $this->timber->validator->clear($rule);

        if( true === $submission_data['error_status'] ){
            $this->response['data'] = $submission_data['error_text'];
            return false;
        }

        foreach ($quotation_terms as $key => $term) {

            if( !(isset($submission_data[$term['name']])) && !(isset($submission_data[$term['name']]['value'])) ){
                continue;
            }

            $field_key = $term['label'];
            $field_value = $submission_data[$term['name']]['value'];

            $submission['values'][] = array(
                'key' => $field_key,
                'value' => $field_value
            );
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
            $this->response['data'] = $this->timber->translator->trans('Invalid Request.');
            return false;
        }

        if(($key = array_search($email, $clients)) !== false) {
            unset($clients[$key]);
        }

        $action_status = (boolean) $this->timber->meta_model->updateMetaByMultiple(array(
            'rec_id' => $quotation_id,
            'rec_type' => 7,
            'me_key' => 'quotation_available_access',
            'me_value' => serialize($clients),
        ));

        $action_status = (boolean) $this->timber->meta_model->addMeta(array(
            'rec_id' => $quotation_id,
            'rec_type' => 7,
            'me_key' => 'quotation_submission',
            'me_value' => serialize($submission),
        ));

        if( $action_status ){
            $this->response['status'] = 'success';
            $this->response['data'] = $this->timber->translator->trans('Submission created successfully.');
            return true;
        }else{
            $this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
            return false;
        }

    }

    /**
     * Delete Quotation
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function deleteQuotation()
    {
        $quotation_id = ( (isset($_POST['quotation_id'])) && ((boolean) filter_var($_POST['quotation_id'], FILTER_VALIDATE_INT)) ) ? filter_var($_POST['quotation_id'], FILTER_SANITIZE_NUMBER_INT) : false;

        if( !($this->timber->access->checkPermission('delete.quotations')) ){
            $this->response['data'] = $this->timber->translator->trans('Invalid Request.');
            return false;
        }

        if( $quotation_id === false ){
            $this->response['data'] = $this->timber->translator->trans('Invalid Request.');
            return false;
        }

        # Delete Quotation and its meta data
        $action_status = (boolean) $this->timber->quotation_model->deleteQuotationById($quotation_id);
        $action_status &= (boolean) $this->timber->meta_model->dumpMetas(false, $quotation_id, 7);

        if( $action_status ){
            $this->response['status'] = 'success';
            $this->response['data'] = $this->timber->translator->trans('Quotation deleted successfully.');
            return true;
        }else{
            $this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
            return false;
        }
    }

    /**
     * Mark Quotation
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function markQuotation()
    {
        $quotation_id = ( (isset($_POST['quotation_id'])) && ((boolean) filter_var($_POST['quotation_id'], FILTER_VALIDATE_INT)) ) ? filter_var($_POST['quotation_id'], FILTER_SANITIZE_NUMBER_INT) : false;
        $submit_id = ( (isset($_POST['submit_id'])) && ((boolean) filter_var($_POST['submit_id'], FILTER_VALIDATE_INT)) ) ? filter_var($_POST['submit_id'], FILTER_SANITIZE_NUMBER_INT) : false;
        $action = ( (isset($_POST['action'])) && (in_array($_POST['action'], array('delete_submit', 'add_submitter'))) ) ? $_POST['action'] : false;
        $email = ( (isset($_POST['email'])) && (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) ) ? filter_var(trim($_POST['email']),FILTER_SANITIZE_EMAIL) : false;
        $client_id = ( (isset($_POST['client_id'])) && (filter_var($_POST['client_id'], FILTER_VALIDATE_INT)) ) ? filter_var(trim($_POST['client_id']),FILTER_SANITIZE_NUMBER_INT) : false;

        if( 'delete_submit' == $action ){

            if( !($this->timber->access->checkPermission('view.quotations')) ){
                $this->response['data'] = $this->timber->translator->trans('Invalid Request.');
                return false;
            }

            $action_status = (boolean) $this->timber->meta_model->deleteMetaByMultiple(array(
                'me_id' => $submit_id,
                'rec_id' => $quotation_id,
                'rec_type' => 7,
                'me_key' => 'quotation_submission',
            ));
            $this->response['data'] = $this->timber->translator->trans('Submission deleted successfully.');
        }

        if( 'add_submitter' == $action ){

            if( !($this->timber->access->checkPermission('view.quotations')) ){
                $this->response['data'] = $this->timber->translator->trans('Invalid Request.');
                return false;
            }

            if( ($email === false) && ($client_id === false) ){
                $this->response['data'] = $this->timber->translator->trans('Please select client or set email.');
                return false;
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

            $clients[] = ($client_id === false) ? $email : $client_id;

            # Quotation Notification for User of Client
            if($client_id !== false){
                $this->timber->notify->increment('quotations_notif', $client_id);
                $this->timber->notify->setMailerCron(array(
                    'method_name' => 'newQuotationEmailNotifier',
                    'qu_id' => $quotation_id,
                    'user_id' => $client_id,
                ));
            }elseif( ($client_id === false) && (!empty($email)) ){
                $this->timber->notify->setMailerCron(array(
                    'method_name' => 'newPublicQuotationEmailNotifier',
                    'qu_id' => $quotation_id,
                    'email' => $email,
                ));
            }

            $action_status = (boolean) $this->timber->meta_model->updateMetaByMultiple(array(
                'rec_id' => $quotation_id,
                'rec_type' => 7,
                'me_key' => 'quotation_available_access',
                'me_value' => serialize($clients),
            ));

            $this->response['data'] = $this->timber->translator->trans('Submitter added successfully.');
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