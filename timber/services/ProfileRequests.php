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
 * Profile Requests Services
 *
 * @since 1.0
 */
class ProfileRequests extends \Timber\Services\Base {

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
	 * Update Profile Request
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
    public function updateUserProfile()
    {

		$profile_data = $this->timber->validator->clear(array(
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
			'user_old_pwd' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vstrlenbetween:8,20&vpassword',
				'default' => '',
				'errors' => array(),
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

		if( true === $profile_data['error_status'] ){
			$this->response['data'] = $profile_data['error_text'];
			return false;
		}

		$user_id = $this->timber->security->getId();
		$user_data = $this->timber->user_model->getUserById($user_id);

		if( (false === $user_data) || !(is_object($user_data)) ){
			$this->response['data'] = $this->timber->translator->trans('Unauthorized access. Please login again.');
			$this->timber->security->endSession();
			return false;
		}
		$user_data = $user_data->as_array();

		$new_user_data = array();
		$new_user_data['us_id'] = $user_id;

		# Check if Username Used Before
		$username_exists = $this->timber->user_model->getUserByUsername($profile_data['user_name']['value']);
		if( (false === $username_exists) || !(is_object($username_exists)) ){
			$username_exists = false;
		}else{
			$username_exists = true;
		}
		if( ($profile_data['user_name']['value'] != $user_data['user_name']) && ($username_exists) ){
			$this->response['data'] = $this->timber->translator->trans('Provided username used before. Please use another.');
			return false;
		}

		# Check if Username Used Before
		$email_exists = $this->timber->user_model->getUserByEmail($profile_data['email']['value']);
		if( (false === $email_exists) || !(is_object($email_exists)) ){
			$email_exists = false;
		}else{
			$email_exists = true;
		}
		if( ($profile_data['email']['value'] != $user_data['email']) && ($email_exists) ){
			$this->response['data'] = $this->timber->translator->trans('Provided email used before. Please use another.');
			return false;
		}

		# Native Auth
		if( '1' == $user_data['auth_by'] ){
			# Check if Password Changed
			if( true === $profile_data['change_pwd']['status'] ){
				if( !($this->timber->hasher->CheckPassword($profile_data['user_old_pwd']['value'], $user_data['password'])) ){
					$this->response['data'] = $this->timber->translator->trans('You must insert old password to change password.');
					return false;
				}

				if( !($profile_data['user_new_pwd']['status']) ){
					$this->response['data'] = $this->timber->translator->trans('The new password must have a lenght of eight or more with at least two numbers.');
					return false;
				}

				$new_user_data['password'] = $this->timber->hasher->HashPassword($profile_data['user_new_pwd']['value']);
			}
		}

		if( ($profile_data['profile_avatar']['value'] != '') && ($this->timber->config('_gravatar_platform') == 'native') ){
			$file_data = explode('--||--', $profile_data['profile_avatar']['value']);
			$file_id = $this->timber->file_model->addFile(array(
				'title' => $file_data[1],
				'hash' => $file_data[0],
				'owner_id' => $this->timber->security->getId(),
				'description' => "Profile Avatar",
				'storage' => 1,
				'type' => pathinfo($file_data[1], PATHINFO_EXTENSION),
				'uploaded_at' => $this->timber->time->getCurrentDate(true),
			));

			$profile_data['profile_avatar']['value'] = $file_id;
			$new_user_data['grav_id'] = $profile_data['profile_avatar']['value'];
			# Delete Old One
			if($user_data['grav_id'] > 0){
				$this->timber->file_model->deleteFileById($user_data['grav_id']);
			}
		}

		# Update Changed Data
		$new_user_data['first_name'] = $profile_data['first_name']['value'];
		$new_user_data['last_name'] = $profile_data['last_name']['value'];
		$new_user_data['user_name'] = $profile_data['user_name']['value'];
		$new_user_data['email'] = $profile_data['email']['value'];
		$new_user_data['website'] = $profile_data['website']['value'];
		$new_user_data['language'] = $profile_data['language']['value'];
		$new_user_data['phone_num'] = $profile_data['phone_num']['value'];
		$new_user_data['country'] = $profile_data['country']['value'];
		$new_user_data['city'] = $profile_data['city']['value'];
		$new_user_data['job'] = $profile_data['job']['value'];
		$new_user_data['company'] = $profile_data['company']['value'];
		$new_user_data['address1'] = $profile_data['address1']['value'];
		$new_user_data['address2'] = $profile_data['address2']['value'];
		$new_user_data['zip_code'] = $profile_data['zip_code']['value'];
		$new_user_data['vat_nubmer'] = $profile_data['vat_nubmer']['value'];
		$new_user_data['updated_at'] = $this->timber->time->getCurrentDate(true);

		if( ($new_user_data['email'] != $user_data['email']) && !(empty($new_user_data['email'])) && ($user_data['access_rule'] != 1) ){
			//mark user as pending to verify email again
			$new_user_data['status'] = 2;
		}

		if( empty($new_user_data['email']) ){
			$new_user_data['status'] = 1;
		}

		$action_status = $this->timber->user_model->updateUserById($new_user_data);

		if( $action_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Profile updated successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Internal Server Error! Please try again later.');
			return false;
		}
    }
}