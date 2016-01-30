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
 * Profile Data Services
 *
 * @since 1.0
 */
class ProfileData extends \Timber\Services\Base {

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
	 * Get User Data
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function userData()
	{
		$user_id = $this->timber->security->getId();
		$user_data = $this->timber->user_model->getUserById($user_id);

		if( (false === $user_data) || !(is_object($user_data)) ){
			return false;
		}

		$user_data = $user_data->as_array();
		$data = array();

		$data['us_id'] = $user_data['us_id'];

		$data['nice_name'] = ( empty($user_data['first_name']) && empty($user_data['last_name']) ) ? $user_data['user_name'] : $user_data['first_name'] . " " . $user_data['last_name'];
		$data['first_name'] = $user_data['first_name'];
		$data['last_name'] = $user_data['last_name'];
		$data['user_name'] = $user_data['user_name'];
		$data['email'] = $user_data['email'];
		$data['website'] = $user_data['website'];
		$data['language'] = $user_data['language'];
		$data['phone_num'] = $user_data['phone_num'];
		$data['country'] = $user_data['country'];
		$data['city'] = $user_data['city'];
		$data['job'] = $user_data['job'];
		$data['company'] = $user_data['company'];
		$data['address1'] = $user_data['address1'];
		$data['address2'] = $user_data['address2'];
		$data['zip_code'] = $user_data['zip_code'];
		$data['vat_nubmer'] = $user_data['vat_nubmer'];
		$data['grav_id'] =	$user_data['grav_id'];
		$data['created_at'] = $user_data['created_at'];
		$data['updated_at'] = $user_data['updated_at'];
		$data['auth_by'] = $user_data['auth_by'];
		$data['access_rule'] = $user_data['access_rule'];
		$data['status'] = $user_data['status'];
		$data['social_login'] = ($user_data['auth_by'] == '1') ? 'off' : 'on';

		$data['form_action'] = $this->timber->config('request_url') . '/request/backend/ajax/profile/update_user_profile';

		if( $user_data['status'] == 2 ){
			$data['profile_alert'] = sprintf( $this->timber->translator->trans('Your email is pending approval. Please check you email or %1$ssend new message%2$s.'), '<a id="profile_verify_alert" href="' . $this->timber->config('request_url') . '/request/backend/ajax/verify/verifyemail' . '" class="alert-link">', '</a>' );
		}

		if( (empty($data['user_name'])) || (empty($data['first_name'])) || (empty($data['last_name'])) || (empty($data['email'])) ){
			$data['profile_alert'] = $this->timber->translator->trans('Please provide all required informations.');
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

		# $allowed_extensions = unserialize($this->timber->config('_allowed_upload_extensions'));
		# $allowed_extensions = implode(', ', $allowed_extensions);
		$allowed_extensions = ".png, .jpg";
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