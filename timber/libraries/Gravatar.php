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
 * Get gravatar for email or fall to the default one
 *
 * @since 1.0
 */
class Gravatar {

      /**
       * Gravatar base url
       *
       * @since 1.0
       * @access private
       * @var array $this->gravatar_url
       */
      private $gravatar_url = 'http://0.gravatar.com/avatar/';

      /**
       * Gravatar Platform
       *
       * @since 1.0
       * @access private
       * @var string $this->gravatar_platform
       */
      private $gravatar_platform;

      /**
       * Default app gravatar
       *
       * @since 1.0
       * @access private
       * @var array $this->default_gravatar
       */
      private $default_gravatar = '';

      /**
       * Gravatar URL properties:
       * - gravatar_id
       * - default identicon, monsterid, wavatar, <url>
       * - size
       * - rating G, PG, R, X
       * - file_extension jpg, png, gif ...
       * - border
       *
       * @link http://en.gravatar.com/site/implement/url More information
       *
       * @since 1.0
       * @access private
       * @var array $this->properties
       */
      private $properties = array(
            'size' => 60,
            'rating'  => 'G',
      );

      /**
       * User email
       *
       * @since 1.0
       * @access private
       * @var string $this->email
       */
      private $email = '';

      /**
       * File ID
       *
       * @since 1.0
       * @access private
       * @var string $this->file_id
       */
      private $file_id = 0;

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
            $this->default_gravatar = rtrim($this->timber->config('request_url'), '/index.php' ) . TIMBER_THEMES_DIR . '/default/assets/img/' . $this->timber->config('_default_gravatar') . '.png';
            $this->gravatar_platform =  $this->timber->config('_gravatar_platform');
      }

      /**
       * Set email
       *
       * @since 1.0
       * @access public
       * @param string $email
       * @return object an instance of this class
       */
      public function email($email)
      {
            $this->email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
            return $this;
      }

      /**
       * Set file id
       *
       * @since 1.0
       * @access public
       * @param integer $file_id
       * @return object an instance of this class
       */
      public function fileId($file_id)
      {
            $this->file_id = $file_id;
            return $this;
      }

      /**
       * Get gravatar url for specific email
       *
       * @since 1.0
       * @access public
       * @param integer $size
       * @return string
       */
      public function gUrl($size = 60)
      {
            $this->properties['size'] = $size;

            if( $this->gravatar_platform == 'gravatar' ){
                  # Get Gravatar
                  if( !filter_var($this->email, FILTER_VALIDATE_EMAIL) ){
                        return $this->default_gravatar;
                  }

                  return $this->gravatar_url . md5(strtolower(trim($this->email))) . '?s=' . $this->properties['size'] . '&d=' . urlencode($this->default_gravatar) . '&r='. $this->properties['rating'];
            }elseif( $this->gravatar_platform == 'native' ){
                  # Get File
                  if( ($this->file_id == 0) || !( $this->file_id > 0 ) ){
                        return $this->default_gravatar;
                  }
                  $file_data = $this->timber->file_model->getFileById($this->file_id);
                  if( (false === $file_data) || !(is_object($file_data)) ){
                        return $this->default_gravatar;
                  }
                  $file_data = $file_data->as_array();
                  $storage = ( $file_data['storage'] == '1' ) ? '/public/' : '/private/';
                  return rtrim( $this->timber->config('request_url'), '/index.php' ) . TIMBER_STORAGE_DIR . $storage . $file_data['hash'];
            }
      }

      /**
       * Get gravatar img html element for specific email
       *
       * @since 1.0
       * @access public
       * @param integer $size
       * @return string
       */
      public function gImg($size = 60)
      {
            $this->properties['size'] = $size;

      	return '<img src="' . $this->gUrl() . '" width="' . $this->properties['size'] . '" height="' . $this->properties['size'] . '">';
      }
}