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
 * Members Requests Services
 *
 * @since 1.0
 */
class MembersRequests extends \Timber\Services\Base {

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
     * Add New Member
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function addMember()
    {
        $user_data = $this->timber->validator->clear(array(
            'first_name' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:2,60',
                'default' => '',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('Provided First Name is invalid.'),
                    'vstrlenbetween' => $this->timber->translator->trans('Provided First Name is invalid.'),
                ),
            ),
            'last_name' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:2,60',
                'default' => '',
                'errors' => array(),
                'optional' => $this->timber->translator->trans('Provided Last Name is invalid.'),
            ),
            'profile_avatar' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vfile:png,jpg,gif',
                'default' => '',
                'errors' => array()
            ),
            'user_name' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:5,20&vusername',
                'default' => '',
                'errors' => array(),
                'optional' => $this->timber->translator->trans('Username must have a lenght of five or more and contain only letters and numbers.'),
            ),
            'job' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:1,250',
                'default' => '',
                'errors' => array(),
                'optional' => $this->timber->translator->trans('Provided Job Title is invalid.'),
            ),
            'company' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:1,60',
                'default' => '',
                'errors' => array(),
                'optional' => $this->timber->translator->trans('Provided Company is invalid.'),
            ),
            'email' => array(
                'req' => 'post',
                'sanit' => 'semail',
                'valid' => 'vnotempty&vemail&vstrlenbetween:2,100',
                'default' => '',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('The provided email must not be empty.'),
                    'vemail' => $this->timber->translator->trans('The provided email is invalid.'),
                    'vstrlenbetween' => $this->timber->translator->trans('The provided email is invalid.'),
                ),
            ),
            'website' => array(
                'req' => 'post',
                'sanit' => 'surl',
                'valid' => 'vnotempty&vurl&vstrlenbetween:1,150',
                'default' => '',
                'errors' => array(),
                'optional' => $this->timber->translator->trans('Provided URL is invalid.'),
            ),
            'language' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vlocale',
                'default' => 'en_US',
                'errors' => array(),
            ),
            'phone_num' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:1,60',
                'default' => '',
                'errors' => array(),
                'optional' => $this->timber->translator->trans('Provided Phone Number is invalid.'),
            ),
            'country' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vcountry',
                'default' => 'US',
                'errors' => array(),
            ),
            'city' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:1,60',
                'default' => '',
                'errors' => array(),
                'optional' => $this->timber->translator->trans('Provided City is invalid.'),
            ),
            'address1' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:1,100',
                'default' => '',
                'errors' => array(),
                'optional' => $this->timber->translator->trans('Provided Address Line 1 is invalid.'),
            ),
            'address2' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:1,100',
                'default' => '',
                'errors' => array(),
                'optional' => $this->timber->translator->trans('Provided Address Line 2 is invalid.'),
            ),
            'zip_code' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:1,60',
                'default' => '',
                'errors' => array(),
                'optional' => $this->timber->translator->trans('Provided ZIP code is invalid.'),
            ),
            'vat_nubmer' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:1,60',
                'default' => '',
                'errors' => array(),
                'optional' => $this->timber->translator->trans('Provided VAT Number is invalid.'),
            ),
            'access_rule' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vinarray:1,2,3',
                'default' => '3',
                'errors' => array(),
            ),
            'user_pwd' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:8,20&vpassword',
                'default' => '',
                'errors' => array(),
                'optional' => $this->timber->translator->trans('The password must have a lenght of eight or more with at least two numbers.'),
            ),
            'process_type' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vinarray:1,2,3',
                'default' => '1',
                'errors' => array(),
            ),
        ));

        if( true === $user_data['error_status'] ){
            $this->response['data'] = $user_data['error_text'];
            return false;
        }

        # Check if Username Used Before
        $username_exists = $this->timber->user_model->getUserByUsername($user_data['user_name']['value']);
        if( (false !== $username_exists) && (is_object($username_exists)) ){
            $this->response['data'] = $this->timber->translator->trans('Provided username used before. Please use another.');
            return false;
        }

        # Check if Username Used Before
        $email_exists = $this->timber->user_model->getUserByEmail($user_data['email']['value']);
        if( (false !== $email_exists) && (is_object($email_exists)) ){
            $this->response['data'] = $this->timber->translator->trans('Provided email used before. Please use another.');
            return false;
        }


        if( (false === $user_data['user_pwd']['status']) && ($user_data['process_type']['value'] != '1') ){
            $this->response['data'] = $this->timber->translator->trans('The password must have a lenght of eight or more with at least two numbers.');
            return false;
        }

        if( ($this->timber->access->getRule() == 'staff') && ($user_data['access_rule']['value'] != '3') ){
            $this->response['data'] = $this->timber->translator->trans('Invalid Request.');
            return false;
        }

        if( ($this->timber->access->getRule() == 'staff') &&  !($this->timber->access->checkPermission('add.clients')) && ($user_data['access_rule']['value'] == '3') ){
            $this->response['data'] = $this->timber->translator->trans('Invalid Request.');
            return false;
        }

        $new_member_data = array();
        $new_member_data['user_name'] = $user_data['user_name']['value'];
        $new_member_data['first_name'] = $user_data['first_name']['value'];
        $new_member_data['last_name'] = $user_data['last_name']['value'];
        $new_member_data['company'] = $user_data['company']['value'];
        $new_member_data['email'] = $user_data['email']['value'];
        $new_member_data['website'] = $user_data['website']['value'];
        $new_member_data['phone_num'] = $user_data['phone_num']['value'];
        $new_member_data['zip_code'] = $user_data['zip_code']['value'];
        $new_member_data['vat_nubmer'] = $user_data['vat_nubmer']['value'];
        $new_member_data['language'] = $user_data['language']['value'];
        $new_member_data['job'] = $user_data['job']['value'];
        $new_member_data['country'] = $user_data['country']['value'];
        $new_member_data['city'] = $user_data['city']['value'];
        $new_member_data['address1'] = $user_data['address1']['value'];
        $new_member_data['address2'] = $user_data['address2']['value'];
        $new_member_data['password'] = $this->timber->hasher->HashPassword($user_data['user_pwd']['value']);
        $new_member_data['sec_hash'] = $this->timber->faker->randHash(20);
        $new_member_data['identifier'] = '';
        $new_member_data['auth_by'] = '1';
        $new_member_data['access_rule'] = $user_data['access_rule']['value'];
        $new_member_data['status'] = '1';
        $new_member_data['grav_id'] = '0';

        $new_member_data['created_at'] = $this->timber->time->getCurrentDate(true);
        $new_member_data['updated_at'] = $this->timber->time->getCurrentDate(true);

        if( ($user_data['profile_avatar']['value'] != '') && ($this->timber->config('_gravatar_platform') == 'native') ){
            $file_data = explode('--||--', $user_data['profile_avatar']['value']);
            $file_id = $this->timber->file_model->addFile(array(
                'title' => $file_data[1],
                'hash' => $file_data[0],
                'owner_id' => $this->timber->security->getId(),
                'description' => "Profile Avatar",
                'storage' => 1,
                'type' => pathinfo($file_data[1], PATHINFO_EXTENSION),
                'uploaded_at' => $this->timber->time->getCurrentDate(true),
            ));

            $user_data['profile_avatar']['value'] = $file_id;
            $new_member_data['grav_id'] = $user_data['profile_avatar']['value'];
        }

        $user_id = $this->timber->user_model->addUser($new_member_data);


        $action_status = (boolean) $this->timber->user_meta_model->addMeta(array(
            'us_id' => $user_id,
            'me_key' => '_user_access_nonce',
            'me_value' => serialize(array(
                'n' => $this->timber->faker->randHash(10),
                't' => $this->timber->time->getCurrentDate(true),
            ))
        ));


        # Invite User
        if( $user_data['process_type']['value'] == '1' ){
            $hash = $this->timber->faker->randHash(20) . time();
            $action_status &= (boolean) $this->timber->user_meta_model->addMeta(array(
                'us_id' => $user_id,
                'me_key' => '_user_register_hash',
                'me_value' => $hash,
            ));

            $action_status &= (boolean) $this->timber->notify->execMailerCron(array(
                'method_name' => 'registerInviteEmailNotifier',
                'us_id' => $user_id,
                'hash' => $hash,
                'first_name' => $new_member_data['first_name'],
                'last_name' => $new_member_data['last_name'],
                'email' => $new_member_data['email'],
                'user_name' => $new_member_data['user_name'],
            ));
        }

        # Alert User
        if( $user_data['process_type']['value'] == '3' ){
            # Send Login Data Alert Email
            $action_status &= (boolean) $this->timber->notify->execMailerCron(array(
                'method_name' => 'loginInfoEmailNotifier',
                'us_id' => $user_id,
                'first_name' => $new_member_data['first_name'],
                'last_name' => $new_member_data['last_name'],
                'email' => $new_member_data['email'],
                'password' => $user_data['user_pwd']['value'],
                'user_name' => $new_member_data['user_name'],
            ));
        }

        if( $user_id && $action_status ){
            $this->response['status'] = 'success';
            $this->response['next_link'] = $this->timber->config('request_url') . '/admin/members/edit/' . $user_id;
            $this->response['data'] = $this->timber->translator->trans('Member account created successfully.');
            return true;
        }else{
            $this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
            return false;
        }
    }

    /**
     * Update Member Profile
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function updateMemberProfile()
    {

        $user_data = $this->timber->validator->clear(array(
            'user_id' => array(
                'req' => 'post',
                'sanit' => 'snumberint',
                'valid' => 'vnotempty&vint',
                'default' => '',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('Invalid Request.'),
                    'vint' => $this->timber->translator->trans('Invalid Request.')
                ),
            ),
            'first_name' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:2,60',
                'default' => '',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('Provided First Name is invalid.'),
                    'vstrlenbetween' => $this->timber->translator->trans('Provided First Name is invalid.'),
                ),
            ),
            'last_name' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:2,60',
                'default' => '',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('Provided Last Name is invalid.'),
                    'vstrlenbetween' => $this->timber->translator->trans('Provided Last Name is invalid.'),
                ),
            ),
            'profile_avatar' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vfile:png,jpg,gif',
                'default' => '',
                'errors' => array()
            ),
            'user_name' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:5,20&vusername',
                'default' => '',
                'errors' => array(
                    'vnotempty' => $this->timber->translator->trans('Username must have a lenght of five or more and contain only letters and numbers.'),
                    'vstrlenbetween' => $this->timber->translator->trans('Username must have a lenght of five or more and contain only letters and numbers.'),
                    'vusername' => $this->timber->translator->trans('Username must have a lenght of five or more and contain only letters and numbers.'),
                ),
            ),
            'job' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:1,250',
                'default' => '',
                'errors' => array(),
                'optional' => $this->timber->translator->trans('Provided Job Title is invalid.'),
            ),
            'company' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:1,60',
                'default' => '',
                'errors' => array(),
                'optional' => $this->timber->translator->trans('Provided Company is invalid.'),
            ),
            'email' => array(
                'req' => 'post',
                'sanit' => 'semail',
                'valid' => 'vnotempty&vemail&vstrlenbetween:2,100',
                'default' => '',
                'errors' => array(),
            ),
            'website' => array(
                'req' => 'post',
                'sanit' => 'surl',
                'valid' => 'vnotempty&vurl&vstrlenbetween:1,150',
                'default' => '',
                'errors' => array(),
                'optional' => $this->timber->translator->trans('Provided URL is invalid.'),
            ),
            'language' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vlocale',
                'default' => 'en_US',
                'errors' => array(),
            ),
            'phone_num' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:1,60',
                'default' => '',
                'errors' => array(),
                'optional' => $this->timber->translator->trans('Provided Phone Number is invalid.'),
            ),
            'country' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vcountry',
                'default' => 'US',
                'errors' => array(),
            ),
            'city' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:1,60',
                'default' => '',
                'errors' => array(),
                'optional' => $this->timber->translator->trans('Provided City is invalid.'),
            ),
            'address1' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:1,100',
                'default' => '',
                'errors' => array(),
                'optional' => $this->timber->translator->trans('Provided Address Line 1 is invalid.'),
            ),
            'address2' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:1,100',
                'default' => '',
                'errors' => array(),
                'optional' => $this->timber->translator->trans('Provided Address Line 2 is invalid.'),
            ),
            'zip_code' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:1,60',
                'default' => '',
                'errors' => array(),
                'optional' => $this->timber->translator->trans('Provided ZIP code is invalid.'),
            ),
            'vat_nubmer' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:1,60',
                'default' => '',
                'errors' => array(),
                'optional' => $this->timber->translator->trans('Provided VAT Number is invalid.'),
            ),
            'user_new_pwd' => array(
                'req' => 'post',
                'sanit' => 'sstring',
                'valid' => 'vnotempty&vstrlenbetween:8,20&vpassword',
                'default' => '',
                'errors' => array(),
            ),
            'change_pwd' => array(
                'req'=> 'post',
                'valid'=> 'vcheckbox',
                'default'=> '',
            ),
        ));

        if( true === $user_data['error_status'] ){
            $this->response['data'] = $user_data['error_text'];
            return false;
        }

        $user_old_data = $this->timber->user_model->getUserById($user_data['user_id']['value']);

        if( (false === $user_old_data) || !(is_object($user_old_data)) ){
            $this->response['data'] = $this->timber->translator->trans('Invalid Request.');
            return false;
        }
        $user_old_data = $user_old_data->as_array();


        if( ($this->timber->access->getRule() == 'staff') && ($user_old_data['access_rule'] != '3') ){
            $this->response['data'] = $this->timber->translator->trans('Invalid Request.');
            return false;
        }

        if( ($this->timber->access->getRule() == 'staff') &&  !($this->timber->access->checkPermission('edit.clients')) && ($user_old_data['access_rule'] == '3') ){
            $this->response['data'] = $this->timber->translator->trans('Invalid Request.');
            return false;
        }


        # Check if Username Used Before
        $username_exists = $this->timber->user_model->getUserByUsername($user_data['user_name']['value']);
        if( (false === $username_exists) || !(is_object($username_exists)) ){
            $username_exists = false;
        }else{
            $username_exists = true;
        }
        if( ($user_data['user_name']['value'] != $user_old_data['user_name']) && ($username_exists) ){
            $this->response['data'] = $this->timber->translator->trans('Provided username used before. Please use another.');
            return false;
        }

        # Check if Username Used Before
        $email_exists = $this->timber->user_model->getUserByEmail($user_data['email']['value']);
        if( (false === $email_exists) || !(is_object($email_exists)) ){
            $email_exists = false;
        }else{
            $email_exists = true;
        }
        if( ($user_data['email']['value'] != $user_old_data['email']) && ($email_exists) ){
            $this->response['data'] = $this->timber->translator->trans('Provided email used before. Please use another.');
            return false;
        }

        $new_member_data = array();

        $new_member_data['us_id'] = $user_old_data['us_id'];
        $new_member_data['user_name'] = $user_data['user_name']['value'];
        $new_member_data['first_name'] = $user_data['first_name']['value'];
        $new_member_data['last_name'] = $user_data['last_name']['value'];
        $new_member_data['company'] = $user_data['company']['value'];
        $new_member_data['email'] = $user_data['email']['value'];
        $new_member_data['website'] = $user_data['website']['value'];
        $new_member_data['phone_num'] = $user_data['phone_num']['value'];
        $new_member_data['zip_code'] = $user_data['zip_code']['value'];
        $new_member_data['vat_nubmer'] = $user_data['vat_nubmer']['value'];
        $new_member_data['language'] = $user_data['language']['value'];
        $new_member_data['job'] = $user_data['job']['value'];
        $new_member_data['country'] = $user_data['country']['value'];
        $new_member_data['city'] = $user_data['city']['value'];
        $new_member_data['address1'] = $user_data['address1']['value'];
        $new_member_data['address2'] = $user_data['address2']['value'];
        $new_member_data['updated_at'] = $this->timber->time->getCurrentDate(true);


        # Native Auth
        if( '1' == $user_old_data['auth_by'] ){
            # Check if Password Changed
            if( true === $user_data['change_pwd']['status'] ){

                if( !($user_data['user_new_pwd']['status']) ){
                    $this->response['data'] = $this->timber->translator->trans('The new password must have a lenght of eight or more with at least two numbers.');
                    return false;
                }

                $new_member_data['password'] = $this->timber->hasher->HashPassword($user_data['user_new_pwd']['value']);
            }
        }

        if( ($user_data['profile_avatar']['value'] != '') && ($this->timber->config('_gravatar_platform') == 'native') ){
            $file_data = explode('--||--', $user_data['profile_avatar']['value']);
            $file_id = $this->timber->file_model->addFile(array(
                'title' => $file_data[1],
                'hash' => $file_data[0],
                'owner_id' => $this->timber->security->getId(),
                'description' => "Profile Avatar",
                'storage' => 1,
                'type' => pathinfo($file_data[1], PATHINFO_EXTENSION),
                'uploaded_at' => $this->timber->time->getCurrentDate(true),
            ));

            $user_data['profile_avatar']['value'] = $file_id;
            $new_member_data['grav_id'] = $user_data['profile_avatar']['value'];
            # Delete Old One
            if($user_old_data['grav_id'] > 0){
                $this->timber->file_model->deleteFileById($user_old_data['grav_id']);
            }
        }

        $action_status = $this->timber->user_model->updateUserById($new_member_data);

        if( $action_status ){
            $this->response['status'] = 'success';
            $this->response['data'] = $this->timber->translator->trans('Member account updated successfully.');
            return true;
        }else{
            $this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
            return false;
        }
    }

    /**
     * Delete Member
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function deleteMember()
    {
        $member_id = ( (isset($_POST['member_id'])) && ((boolean) filter_var($_POST['member_id'], FILTER_VALIDATE_INT)) ) ? filter_var($_POST['member_id'], FILTER_SANITIZE_NUMBER_INT) : false;

        if($member_id == 1){
            $this->response['data'] = $this->timber->translator->trans('OOps! Super admin can\'t be deleted.');
            return false;
        }

        if( $member_id === false ){
            $this->response['data'] = $this->timber->translator->trans('Invalid Request.');
            return false;
        }

        if( ($this->timber->access->getRule() == 'staff') &&  !($this->timber->access->checkPermission('delete.clients')) ){
            $this->response['data'] = $this->timber->translator->trans('Invalid Request.');
            return false;
        }
        if( ($this->timber->access->getRule() == 'staff') ){
            $action_status  = (boolean) $this->timber->user_model->deleteUserByMultiple(array(
                'us_id' => $member_id,
                'access_rule' => '3'
            ));
            if( $action_status ){
                $action_status &= (boolean) $this->timber->user_meta_model->dumpUserMetas($member_id);
            }
        }else{
            $action_status  = (boolean) $this->timber->user_model->deleteUserById($member_id);
            $action_status &= (boolean) $this->timber->user_meta_model->dumpUserMetas($member_id);
        }

        if( $action_status ){
            $this->response['status'] = 'success';
            $this->response['data'] = $this->timber->translator->trans('Member account deleted successfully.');
            return true;
        }else{
            $this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
            return false;
        }
    }
}