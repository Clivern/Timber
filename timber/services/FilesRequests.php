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
 * Files Requests Services
 *
 * @since 1.0
 */
class FilesRequests extends \Timber\Services\Base {

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
	 * Upload Site Logo
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function uploadSiteLogo()
	{
		$result = $this->timber->storage->storeInPublic( 'c_site_logo', 'logo_', array('gif', 'jpg', 'png') );
		if( $result['status'] ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Logo uploaded successfully.');
			$this->response['info'] = $result['file_data'];
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			$this->response['info'] = $result['file_data'];
			return false;
		}
	}

	/**
	 * Upload Profile Avatar
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function uploadProfileAvatar()
	{
		$result = $this->timber->storage->storeInPublic( 'profile_avatar', 'avatar_', array('gif', 'jpg', 'png') );
		if( $result['status'] ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Avatar uploaded successfully.');
			$this->response['info'] = $result['file_data'];
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			$this->response['info'] = $result['file_data'];
			return false;
		}
	}

	/**
	 * Upload Record Attachments
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function uploadRecordAttachments()
	{
		$result = $this->timber->storage->storeInPrivate( 'record_attachment', 'attachment_' );
		if( $result['status'] ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Attachment uploaded successfully.');
			$this->response['info'] = $result['file_data'];
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			$this->response['info'] = $result['file_data'];
			return false;
		}
	}

	/**
	 * Dump File From Storage
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function dumpFile()
	{
		if( !(isset($_POST['file_name'])) || !(isset($_POST['file_storage'])) ){
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}

		if( $this->timber->storage->dumpStoredFile( $_POST['file_name'], $_POST['file_storage'] ) ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('File deleted successfully.');
			return true;
		}
	}
}