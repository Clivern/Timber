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
 * Load and parse .mo translation files
 *
 * This class is based on Zend Framework gettext adapter
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @since 1.0
 */
class Translator {

      /**
       * App locale
       *
       * @since 1.0
       * @access private
       * @var string $this->locale;
       */
      private $locale;

      /**
       * App translation files dir
       *
       * @since 1.0
       * @access private
       * @var string $this->locales_dir;
       */
      private $locales_dir = TIMBER_LANGS_DIR;

      /**
       * App locales
       *
       * @since 1.0
       * @access private
       * @var string $this->locales;
       */
      private $locales = array('en_US');

      /**
       * A list of locale messages
       *
       * @since 1.0
       * @access private
       * @var array|boolean $this->messages;
       */
      private $messages;

      /**
       * Zend adapter internal variable
       *
       * @since 1.0
       * @access private
       * @var boolean
       */
      private $_bigEndian = false;

      /**
       * Zend adapter internal variable
       *
       * @since 1.0
       * @access private
       * @var boolean
       */
      private $_file = false;

      /**
       * Zend adapter internal variable
       *
       * @since 1.0
       * @access private
       * @var array
       */
      private $_data = array();

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
            $this->locale = $this->timber->config('_site_lang');
            $this->syncLocales();
            $this->locale = (in_array($this->locale, $this->locales)) ? $this->locale : 'en_US';
            $this->load();
      }

      /**
       * Load and parse .mo file
       *
       * @since 1.0
       * @access public
       */
      public function load()
      {
            if( $this->locale == 'en_US' ){
                  $this->messages = false;
                  return true;
            }
            $locale_file = TIMBER_ROOT . "{$this->locales_dir}/{$this->locale}.mo";
            $this->messages = ( (is_file($locale_file)) && (file_exists($locale_file)) ) ? $this->loadTranslationData($locale_file, $this->locale) : false;
      }

      /**
       * Return translated text or original string
       *
       * @since 1.0
       * @access public
       * @return string
       */
      public function trans($string)
      {
            if(false == $this->messages){
                  return $string;
            }
            //return original string or translated
            return ( (isset($this->messages[$this->locale][$string])) ? $this->messages[$this->locale][$string] : $string);
      }

      /**
       * Get a list of locales
       *
       * @since 1.0
       * @access public
       * @return array
       */
      public function getLocales()
      {
            return $this->locales;
      }

      /**
       * Load translation data (MO file reader)
       *
       * @since 1.0
       * @access private
       * @param  string  $filename  MO file to add, full path must be given for access
       * @param  string  $locale    New Locale/Language to set, identical with locale identifier,
       *                            see Zend_Locale for more information
       * @param  array   $option    OPTIONAL Options to use
       * @return array
       */
      private function loadTranslationData($filename, $locale, array $options = array())
      {
            $this->_data      = array();
            $this->_bigEndian = false;
            $this->_file      = @fopen($filename, 'rb');
            if (!$this->_file) {
                  //file not exist
                  return false;
                  //require_once 'Zend/Translate/Exception.php';
                  //throw new Zend_Translate_Exception('Error opening translation file \'' . $filename . '\'.');
            }
            if (@filesize($filename) < 10) {
                  @fclose($this->_file);
                  //file not gettext file
                  return false;
                  //require_once 'Zend/Translate/Exception.php';
                  //throw new Zend_Translate_Exception('\'' . $filename . '\' is not a gettext file');
            }

            // get Endian
            $input = $this->readMOData(1);
            if (strtolower(substr(dechex($input[1]), -8)) == "950412de") {
                  $this->_bigEndian = false;
            } else if (strtolower(substr(dechex($input[1]), -8)) == "de120495") {
                  $this->_bigEndian = true;
            } else {
                  @fclose($this->_file);
                  return false;
                  //require_once 'Zend/Translate/Exception.php';
                  //throw new Zend_Translate_Exception('\'' . $filename . '\' is not a gettext file');
            }
            // read revision - not supported for now
            $input = $this->readMOData(1);

            // number of bytes
            $input = $this->readMOData(1);
            $total = $input[1];

            // number of original strings
            $input = $this->readMOData(1);
            $OOffset = $input[1];

            // number of translation strings
            $input = $this->readMOData(1);
            $TOffset = $input[1];

            // fill the original table
            fseek($this->_file, $OOffset);
            $origtemp = $this->readMOData(2 * $total);
            fseek($this->_file, $TOffset);
            $transtemp = $this->readMOData(2 * $total);

            for($count = 0; $count < $total; ++$count) {
                  if ($origtemp[$count * 2 + 1] != 0) {
                        fseek($this->_file, $origtemp[$count * 2 + 2]);
                        $original = @fread($this->_file, $origtemp[$count * 2 + 1]);
                        $original = explode("\0", $original);
                  } else {
                        $original[0] = '';
                  }

                  if ($transtemp[$count * 2 + 1] != 0) {
                        fseek($this->_file, $transtemp[$count * 2 + 2]);
                        $translate = fread($this->_file, $transtemp[$count * 2 + 1]);
                        $translate = explode("\0", $translate);
                        if ((count($original) > 1)) {
                              $this->_data[$locale][$original[0]] = $translate;
                              array_shift($original);
                              foreach ($original as $orig) {
                                    $this->_data[$locale][$orig] = '';
                              }
                        } else {
                              $this->_data[$locale][$original[0]] = $translate[0];
                        }
                  }
            }

            @fclose($this->_file);

            $this->_data[$locale][''] = trim($this->_data[$locale]['']);

            /*if (empty($this->_data[$locale][''])) {
                  $this->_adapterInfo[$filename] = 'No adapter information available';
            } else {
                  $this->_adapterInfo[$filename] = $this->_data[$locale][''];
            }*/

            unset($this->_data[$locale]['']);
            return $this->_data;
      }

      /**
       * Read values from the MO file
       *
       * @since 1.0
       * @access private
       * @param string  $bytes
       * @return string
       */
      private function readMOData($bytes)
      {
            if ($this->_bigEndian === false) {
                  return unpack('V' . $bytes, fread($this->_file, 4 * $bytes));
            } else {
                  return unpack('N' . $bytes, fread($this->_file, 4 * $bytes));
            }
      }

      /**
       * Sync Locales
       *
       * @since 1.0
       * @access public
       * @return array
       */
      private function syncLocales()
      {
            $locales = array();
            $path = TIMBER_ROOT . $this->locales_dir;

            if( !(is_dir($path)) ){
                  return true;
            }

            $path = rtrim( $path, '/' ) . '/';
            $dirs = @scandir( $path );
            foreach ( $dirs as $dir ) {
                  if ( $dir === '.' || $dir === '..'){ continue; }
                  if( (is_file( $path . $dir )) && (strpos($dir, '.mo') > 0) ){
                        $locale = rtrim( $dir, '.mo' );
                        if( !(in_array($locale, $locales)) && !(in_array($locale, $this->locales)) ){
                              $locales[] = $locale;
                        }
                  }
            }

            $this->locales = array_merge($locales, $this->locales);
            return true;
      }
}