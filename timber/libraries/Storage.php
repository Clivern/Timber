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

namespace Timber\Libraries;

/**
 * Storage Library
 *
 * @since 1.0
 */
class Storage {

	/**
	 * Storage Dir
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->storage_path
	 */
	private $storage_path;

	/**
	 * Public Storage Dir
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->public_path
	 */
	private $public_path = '/public';

	/**
	 * Private Storage Dir
	 *
	 * @since 1.0
	 * @access private
	 * @var string $this->private_path
	 */
	private $private_path = '/private';

	/**
	 * Instance of timber app
	 *
	 * @since 1.0
	 * @access private
	 * @var object $this->timber
	 */
	private $timber;

	/**
	 * Holds an instance of this class
	 *
	 * @since 1.0
	 * @access private
	 * @var object self::$instance
	 */
	private static $instance;

	/**
	 * Create instance of this class or return existing instance
	 *
	 * @since 1.0
	 * @access public
	 * @return object an instance of this class
	 */
	public static function instance()
	{
		if ( !isset(self::$instance) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Set class dependencies
	 *
	 * @since 1.0
	 * @access public
	 * @param object $timber
	 * @return object
	 */
	public function setDepen($timber)
	{
		$this->timber = $timber;
		return $this;
	}

	/**
	 * Configure class
	 *
	 * @since 1.0
	 * @access public
	 */
	public function config()
	{
		$this->storage_path = TIMBER_ROOT . TIMBER_STORAGE_DIR;
		$this->public_path = $this->storage_path . $this->public_path;
		$this->private_path = $this->storage_path . $this->private_path;
	}

	/**
	 * Store File in Public Storage
	 *
	 * @since 1.0
	 * @access public
	 * @param string $field_name
	 * @param string $unique_id
	 * @param array $extensions
	 * @param array $mime_types
	 * @param integer $max_size
	 * @return array
	 */
	public function storeInPublic( $field_name, $unique_id = '', $extensions = false , $mime_types = false, $max_size = false )
	{
		$storage = new \Upload\Storage\FileSystem( $this->public_path );
		$file = new \Upload\File( $field_name, $storage );

		$validation_rule = array();

		if( $max_size === false ){
			$validation_rule[] = new \Upload\Validation\Size( $this->timber->config('_max_upload_size') . 'M' );
		}else{
			$validation_rule[] = new \Upload\Validation\Size( $max_size . 'M' );
		}

		if( $mime_types !== false ){
			$validation_rule[] = new \Upload\Validation\Mimetype( $mime_types );
		}

		if( $extensions !== false ){
			$validation_rule[] = new \Upload\Validation\Extension( $extensions );
		}else{
			$extensions = unserialize($this->timber->config('_allowed_upload_extensions'));
			$extensions = array_map('strtolower', $extensions);
			$validation_rule[] = new \Upload\Validation\Extension( $extensions );
		}

		# MimeType List => http://www.webmaster-toolkit.com/mime-types.shtml
		$file->addValidations($validation_rule);

		# Access data about the file that has been uploaded
		$data = array(
			'name' => $file->getNameWithExtension(),
			'extension' => $file->getExtension(),
			'mime' => $file->getMimetype(),
			'size' => $file->getSize(),
			'md5' => $file->getMd5(),
			'dimensions' => $file->getDimensions()
		);

		$data['name'] = substr(rtrim($data['name'], '.' . $data['extension']), 0 , 50) . '.' . $data['extension'];
		# Stop Attck attempts
		$data['name'] = str_replace("--||--", "", $data['name']);

		# Optionally you can rename the file on upload
		$new_filename = $unique_id . 'atch-' . $this->timber->faker->randHash(rand (7 , 12)) . '-' . time();
		$new_file_path = $this->public_path . '/' . $new_filename . '.' . $data['extension'];
		while ( (is_file($new_file_path)) || (file_exists($new_file_path)) ) {
			# Rename file again
			$new_filename = $unique_id . 'atch-' . $this->timber->faker->randHash(rand (7 , 12)) . '-' . time();
			$new_file_path = $this->public_path . '/' . $new_filename . '.' . $data['extension'];
		}
		# Set new name
		$file->setName($new_filename);
		$data['new_name'] = $new_filename . '.' . $data['extension'];
		$data['path'] = rtrim( $this->timber->config('request_url'), '/index.php' ) . TIMBER_STORAGE_DIR . '/public/' . $data['new_name'];

		# Try to upload file
		try {
			# Success!
			$file->upload();
			return array(
				'status' => true,
				'error_text' => '',
				'file_data' => $data,
			);
		} catch (\Exception $e) {
			# Fail!
			return array(
				'status' => false,
				'error_text' => $file->getErrors(),
				'file_data' => $file->getErrors(),
			);
		}
	}

	/**
	 * Store File in Private Storage
	 *
	 * @since 1.0
	 * @access public
	 * @param string $field_name
	 * @param string $unique_id
	 * @param array $extensions
	 * @param array $mime_types
	 * @param integer $max_size
	 * @return array
	 */
	public function storeInPrivate( $field_name, $unique_id = '',  $extensions = false , $mime_types = false, $max_size = false )
	{
		$storage = new \Upload\Storage\FileSystem( $this->private_path );
		$file = new \Upload\File( $field_name, $storage );

		$validation_rule = array();

		if( $max_size === false ){
			$validation_rule[] = new \Upload\Validation\Size( $this->timber->config('_max_upload_size') . 'M' );
		}else{
			$validation_rule[] = new \Upload\Validation\Size( $max_size . 'M' );
		}

		if( $mime_types !== false ){
			$validation_rule[] = new \Upload\Validation\Mimetype( $mime_types );
		}

		if( $extensions !== false ){
			$validation_rule[] = new \Upload\Validation\Extension( $extensions );
		}else{
			$extensions = unserialize($this->timber->config('_allowed_upload_extensions'));
			$extensions = array_map('strtolower', $extensions);
			$validation_rule[] = new \Upload\Validation\Extension( $extensions );
		}

		# MimeType List => http://www.webmaster-toolkit.com/mime-types.shtml
		$file->addValidations($validation_rule);

		# Access data about the file that has been uploaded
		$data = array(
			'name' => $file->getNameWithExtension(),
			'extension' => $file->getExtension(),
			'mime' => $file->getMimetype(),
			'size' => $file->getSize(),
			'md5' => $file->getMd5(),
			'dimensions' => $file->getDimensions()
		);

		$data['name'] = substr(rtrim($data['name'], '.' . $data['extension']), 0 , 50) . '.' . $data['extension'];
		# Stop Attck attempts
		$data['name'] = str_replace("--||--", "", $data['name']);

		# Optionally you can rename the file on upload
		$new_filename = $unique_id . 'atch-' . $this->timber->faker->randHash(rand (7 , 12)) . '-' . time();
		$new_file_path = $this->private_path . '/' . $new_filename . '.' . $data['extension'];
		while ( (is_file($new_file_path)) || (file_exists($new_file_path)) ) {
			# Rename file again
			$new_filename = $unique_id . 'atch-' . $this->timber->faker->randHash(rand (7 , 12)) . '-' . time();
			$new_file_path = $this->private_path . '/' . $new_filename . '.' . $data['extension'];
		}
		# Set new name
		$file->setName($new_filename);
		$data['new_name'] = $new_filename . '.' . $data['extension'];
		$data['path'] = rtrim( $this->timber->config('request_url'), '/index.php' ) . TIMBER_STORAGE_DIR . '/private/' . $data['new_name'];

		# Try to upload file
		try {
			# Success!
			$file->upload();
			return array(
				'status' => true,
				'error_text' => '',
				'file_data' => $data,
			);
		} catch (\Exception $e) {
			# Fail!
			return array(
				'status' => false,
				'error_text' => $file->getErrors(),
				'file_data' => $data,
			);
		}
	}

	/**
	 * Remove files that not used in meta data
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function unStoreFiles()
	{
		$used_metas = array(
			'message_attachments_data',
			'invoice_attachments_data'
		);
		$used_ids = array();

		foreach ( $used_metas as $meta ) {
			$metas_list = $this->timber->meta_model->getMetasByKey($meta);

			foreach ( $metas_list as $meta_value ) {
				$meta_value['me_value'] = unserialize($meta_value['me_value']);
				$used_ids = array_merge($used_ids, $meta_value['me_value']);
			}
		}

		$all_files = $this->timber->file_model->getFiles();
		foreach ($all_files as $file_data ) {
			if( !in_array($file_data['fi_id'], $used_ids) ){ $this->timber->file_model->deleteFileById($file_data['fi_id']); }
		}

		return true;
	}

	/**
	 * Dump Stored File
	 *
	 * @since 1.0
	 * @access public
	 * @param string $file
	 * @param public|private $where
	 * @return boolean
	 */
	public function dumpStoredFile($file, $where = 'public')
	{
		$path = ( $where == 'public' ) ? $this->public_path : $this->private_path;

		if( (is_dir($path)) ){
			$path = rtrim( $path, '/' ) . '/';
			@chmod($path, 0755);
			if( (is_file( $path . $file )) && (file_exists( $path . $file )) ) {
				@chmod( $path . $file, 0755);
				@unlink( $path . $file );
			}
		}

		return true;
	}


	/**
	 * Dump Stored Files
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function dumpStoredFiles()
	{

		$files_table = '';
		$files_table .= '/* INSERT VALUES IN '. TIMBER_DB_PREFIX . TIMBER_DB_FILES_TABLE .' TABLE */'. PHP_EOL;
		$files_inserts = \ORM::for_table(TIMBER_DB_PREFIX . TIMBER_DB_FILES_TABLE)->find_array();
		if( (is_array($files_inserts)) && (count($files_inserts) > 0) ){
			foreach ($files_inserts as $key => $file_insert) {
				$insert_values = array_values($file_insert);
				$escaped_insert_values = array();
				foreach ($insert_values as $single_value) {
					$escaped_insert_values[] = addslashes($single_value);
				}
				unset($escaped_insert_values[1]);
				$files_table .= 'INSERT INTO ' . TIMBER_DB_PREFIX . TIMBER_DB_FILES_TABLE . ' VALUES (\'' . implode("', '", $escaped_insert_values) . '\');' . PHP_EOL;
			}

		}

		# Dump Public Files
		if( (is_dir($this->public_path)) ){
			$public_path = rtrim( $this->public_path, '/' ) . '/';
			@chmod($public_path, 0755);
			$files = @scandir( $public_path );
			if( !(is_array($files)) || !(count($files) > 0) ){
				return true;
			}
			foreach ( $files as $file ) {
				if ( ($file === '.') || ($file === '..') || ($file === '.gitkeep') || ($file === '.htaccess') ){ continue; }
				if( (is_file( $public_path . $file )) && !(strrpos($files_table, $file) > 0) ) {
					@chmod( $public_path . $file, 0755);
					@unlink( $public_path . $file );
				}
			}
		}

		# Dump Private Files
		if( (is_dir($this->private_path)) ){
			$private_path = rtrim( $this->private_path, '/' ) . '/';
			@chmod($private_path, 0755);
			$files = @scandir( $private_path );
			if( !(is_array($files)) || !(count($files) > 0) ){
				return true;
			}
			foreach ( $files as $file ) {
				if ( ($file === '.') || ($file === '..') || ($file === '.gitkeep') || ($file === '.htaccess') ){ continue; }
				if( (is_file( $private_path . $file )) && !(strrpos($files_table, $file) > 0) ) {
					@chmod( $private_path . $file, 0755);
					@unlink( $private_path . $file );
				}
			}
		}
	}

	/**
	 * Get File URL
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $file_id
	 * @return string
	 */
	public function getFileUrl($file_id, $default)
	{
		if( $file_id == 0 ){
			return $default;
		}
		$file_data = $this->timber->file_model->getFileById($file_id);
		if( (false === $file_data) || !(is_object($file_data)) ){
			return $default;
		}
		$file_data = $file_data->as_array();
		$storage = ( $file_data['storage'] == '1' ) ? '/public/' : '/private/';
		return rtrim( $this->timber->config('request_url'), '/index.php' ) . TIMBER_STORAGE_DIR . $storage . $file_data['hash'];
	}

	/**
	 * Get File Path
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $file_id
	 * @return string
	 */
	public function getRelFilePath($file_id, $default)
	{
		if( $file_id == 0 ){
			return $default;
		}
		$file_data = $this->timber->file_model->getFileById($file_id);
		if( (false === $file_data) || !(is_object($file_data)) ){
			return $default;
		}
		$file_data = $file_data->as_array();
		$storage = ( $file_data['storage'] == '1' ) ? '/public/' : '/private/';
		return '../../../../..' . TIMBER_STORAGE_DIR . $storage . $file_data['hash'];
	}

	/**
	 * Force file download
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $file_id
	 * @return void
	 */
	public function downloadFile($file_id, $hash)
	{
		$file_id = filter_var($file_id, FILTER_SANITIZE_NUMBER_INT);
		$hash = filter_var($hash, FILTER_SANITIZE_STRING);

		if( (empty($file_id)) || (empty($hash)) || ($file_id == 0) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		$file_data = $this->timber->file_model->getFileByMultiple(array(
			'fi_id' => $file_id,
			'hash' => $hash
		));

		if( (false === $file_data) || !(is_object($file_data)) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}
		$file_data = $file_data->as_array();
		$storage = ( $file_data['storage'] == '1' ) ? '/public/' : '/private/';
		$real_path = TIMBER_ROOT . TIMBER_STORAGE_DIR . $storage . $file_data['hash'];

		if( !(is_file($real_path)) || !(file_exists($real_path)) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		# Get Mime Type
		$finfo = @finfo_open(FILEINFO_MIME_TYPE);
		$mime_type = @finfo_file($finfo, $real_path);
		@finfo_close($finfo);

		# Download this shit
		header('Content-Type: ' . $mime_type);
		header('Content-Disposition: attachment; filename="' . $file_data['title'] . '"');
		readfile($real_path);
	}
}