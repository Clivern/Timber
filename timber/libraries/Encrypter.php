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

namespace Timber\Libraries;

/**
 * Encrypt data using mcrypt or simple mechanism
 *
 * @since 1.0
 */
class Encrypter {

      /**
       * Encrypt level
       *
       * level 1 will use simple mecahnism
       * level 2 will use mcrypt extension
       *
       * @since 1.0
       * @access private
       * @var integer $this->encrypt_level
       */
      private $encrypt_level = ENCRYPT_LEVEL;

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
            //silence is golden
      }

      /**
       * Encrypt data
       *
       * @since 1.0
       * @access public
       * @param  array|string $data
       * @param  string $salt
       * @return string
       */
      public function encrypt($data, $salt)
      {
            $data = (is_array($data) || is_object( $data )) ? serialize($data) : $data;

            if($this->encrypt_level == 1){
                  $data = $this->simpleEncrypt($data, $salt);
            }
            if($this->encrypt_level == 2){
                  $data = $this->toughEncrypt($data, $salt);
            }
            return $data;
      }

      /**
       * Decrypt data
       *
       * @since 1.0
       * @access public
       * @param  string $data
       * @param  string $salt
       * @return array|string
       */
      public function decrypt($data, $salt)
      {
            if($this->encrypt_level == 1){
                  $data = $this->simpleDecrypt($data, $salt);
            }
            if($this->encrypt_level == 2){
                  $data = $this->toughDecrypt($data, $salt);
            }
            return $this->unserialize($data);
      }

      /**
       * Encrypt data
       *
       * @since 1.0
       * @access private
       * @param  string $data
       * @param  string $salt
       * @return string
       */
      private function simpleEncrypt($data, $salt)
      {
            //set default result
            $res = '';
            //use salt to harden hash
            for( $i = 0; $i < strlen($data); $i++){
                  $c = ord(substr($data, $i));
                  $c += ord(substr($salt, (($i + 1) % strlen($salt))));
                  $res .= chr($c & 0xFF);
            }
            $res = base64_encode($res);
            //return result
            return $res;
      }

      /**
       * Encrypt data
       *
       * @since 1.0
       * @access private
       * @param  string $data
       * @param  string $salt
       * @return string
       */
      private function toughEncrypt($data, $salt)
      {
            return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $data, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
      }

      /**
       * Decrypt data
       *
       * @since 1.0
       * @access private
       * @param  string $data
       * @param  string $salt
       * @return string
       */
      private function simpleDecrypt($data, $salt)
      {
            //set default result
            $res = '';
            $data = base64_decode($data);
            //use salt to harden hash
            for( $i = 0; $i < strlen($data); $i++){
                  $c = ord(substr($data, $i));
                  $c -= ord(substr($salt, (($i + 1) % strlen($salt))));
                  $res .= chr(abs($c) & 0xFF);
            }
            //return result
            return $res;
      }

      /**
       * Decrypt data
       *
       * @since 1.0
       * @access private
       * @param  string $data
       * @param  string $salt
       * @return string
       */
      private function toughDecrypt($data, $salt)
      {
            return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $salt, base64_decode($data), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
      }

      /**
       * Unserialize string of it is serialized or return original
       *
       * This method used to prevent issues that may encountered
       *
       * @since 1.0
       * @access private
       * @param string $original
       * @return string|array
       */
      private function unserialize( $original )
      {
            if ( $this->isSerialized( $original ) ){ // don't attempt to unserialize data that wasn't serialized going in
                  return @unserialize( $original );
            }
            return $original;
      }

      /**
       * Check value to find if it was serialized.
       *
       * @since 1.0
       * @access private
       * @param string $data
       * @param boolean $strict
       * @return boolean
       */
      private function isSerialized( $data, $strict = true )
      {
            // if it isn't a string, it isn't serialized.
            if ( ! is_string( $data ) ) {
                  return false;
            }
            $data = trim( $data );
            if ( 'N;' == $data ) {
                  return true;
            }
            if ( strlen( $data ) < 4 ) {
                  return false;
            }
            if ( ':' !== $data[1] ) {
                  return false;
            }
            if ( $strict ) {
                  $lastc = substr( $data, -1 );
                  if ( ';' !== $lastc && '}' !== $lastc ) {
                        return false;
                  }
            } else {
                  $semicolon = strpos( $data, ';' );
                  $brace     = strpos( $data, '}' );
                  // Either ; or } must exist.
                  if ( false === $semicolon && false === $brace )
                        return false;
                  // But neither must be in the first X characters.
                  if ( false !== $semicolon && $semicolon < 3 )
                        return false;
                  if ( false !== $brace && $brace < 4 )
                        return false;
            }
            $token = $data[0];
            switch ( $token ) {
                  case 's' :
                        if ( $strict ) {
                              if ( '"' !== substr( $data, -2, 1 ) ) {
                                    return false;
                              }
                        } elseif ( false === strpos( $data, '"' ) ) {
                              return false;
                        }
                        // or else fall through
                  case 'a' :
                  case 'O' :
                        return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
                  case 'b' :
                  case 'i' :
                  case 'd' :
                        $end = $strict ? '$' : '';
                        return (bool) preg_match( "/^{$token}:[0-9.E-]+;$end/", $data );
            }
            return false;
      }
}