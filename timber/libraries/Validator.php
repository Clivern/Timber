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
 * Move All The Shit Away
 *
 * @since 1.0
 */
class Validator {

      /**
       * Current validated input
       *
       * @since 1.0
       * @access private
       * @var array $this->input
       */
      private $input;

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
            $this->gpcFix();
      }

      /**
       * Check if Magic Quotes Active and Fix
       *
       * @since 1.0
       * @access private
       */
      private function gpcFix(){

            if( !(function_exists('get_magic_quotes_gpc')) ){
                  return false;
            }

            if( (@get_magic_quotes_gpc()) && (isset($_POST)) && (is_array($_POST)) && (count($_POST > 0)) ){
                  foreach($_POST as $key => $value){
                        $_POST[$key] = !(is_array($value)) ? stripslashes($value) : $value;
                  }
                  return true;
            }
            return true;
      }

      /**
       * Clear $_GET and $_POST vars
       *
       *
       * Prevents internet pirates from exploiting timber
       * If there is any issue here, i should remove my IDE and simply sell toys :)
       * So IF YOU FOUND BUG HERE SEND EMAIL TO HELLO@CLIVERN.COM TO INFORM.
       *
       * <code>
       * $inputs = array(
       *       'key' => array(
       *             'req'=> 'get|post',
       *             'sanit'=> '',
       *             'valid'=> '',
       *             'default'=>'',
       *             'errors'=>array(
       *                         'vrule' => error,
       *                         'vrule' => error,
       *                   ),
       *             ),
       *        ......,
       *        ......,
       * );
       * </code>
       *
       * @since 1.0
       * @access public
       * @param array $inputs
       * @return array
       */
      public function clear($inputs)
      {
            $cleared_data = array(
                  'error_status' => false,
                  'error_text' => '',
            );
            foreach ( $inputs as $key => $rules ) {
                  $request = ($rules[ 'req' ] == 'get') ? $_GET : $_POST;
                  $sanit_rules = (isset($rules[ 'sanit' ])) ? $rules[ 'sanit' ] : null;
                  $valid_rules = (isset($rules[ 'valid' ])) ? $rules[ 'valid' ] : null;
                  $this->input = array(
                        'old_value' => '',
                        'value' => '',
                        'status' => false,
                        'error' => false,
                  );
                  $this->input[ 'value' ] = (isset($request[ $key ])) ? $request[ $key ] : '';
                  $this->input['old_value'] = $this->input[ 'value' ];
                  //we need to validate user inputs
                  if ( $this->input[ 'value' ] !== null ) {
                        $this->input[ 'status' ] = ( boolean ) $this->valid($this->input[ 'value' ], $valid_rules);
                  }
                  //then sanitize
                  if ( $this->input[ 'value' ] !== null ) {
                        $this->input[ 'value' ] = $this->sanit($this->input[ 'value' ], $sanit_rules);
                  }
                  //override default
                  if ( (isset($rules[ 'default' ])) && ($rules[ 'default' ] !== null) && ($this->input[ 'status' ] == false) ) {
                        $this->input[ 'value' ] = $rules[ 'default' ];
                  }

                  if( (isset($rules['optional'])) && ($this->input[ 'value' ] != $this->input['old_value']) ){
                        $cleared_data['error_status'] = true;
                        $cleared_data['error_text'] = $rules['optional'];
                  }

                  //check if error found in
                  if( (false === $cleared_data['error_status']) && (false !== $this->input['error']) && (isset($rules['errors'])) && (isset($rules['errors'][$this->input['error']])) ){
                        $cleared_data['error_status'] = true;
                        $cleared_data['error_text'] = $rules['errors'][$this->input['error']];
                  }
                  $cleared_data[ $key ] = $this->input;

            }
            return $cleared_data;
      }

      /**
       * Clear array of values
       *
       *
       * Prevents internet pirates from exploiting timber
       * If there is any issue here, i should remove my IDE and simply sell toys :)
       * So IF YOU FOUND BUG HERE SEND EMAIL TO HELLO@CLIVERN.COM TO INFORM.
       *
       * <code>
       * $values = array(
       *       'key' => array(
       *             'value'=> '',
       *             'sanit'=> '',
       *             'valid'=> '',
       *             'default'=>'',
       *             'errors' => array()
       *             ),
       *        ......,
       *        ......,
       * );
       * </code>
       *
       * @since 1.0
       * @access public
       * @param array $values
       * @return array
       */
      public function clear_values($values)
      {
            $cleared_values = array(
                  'error_status' => false,
                  'error_text' => '',
            );
            foreach ( $values as $key => $rules ) {
                  $sanit_rules = (isset($rules[ 'sanit' ])) ? $rules[ 'sanit' ] : null;
                  $valid_rules = (isset($rules[ 'valid' ])) ? $rules[ 'valid' ] : null;
                  $this->input = array(
                        'old_value' => '',
                        'value' => '',
                        'status' => false,
                        'error' => false
                  );
                  $this->input[ 'value' ] = (isset($rules[ 'value' ])) ? $rules[ 'value' ] : '';
                  $this->input['old_value'] = $this->input[ 'value' ];
                  if ( $this->input[ 'value' ] !== null ) {
                        $this->input[ 'status' ] = ( boolean ) $this->valid($this->input[ 'value' ], $valid_rules);
                  }
                  if ( $this->input[ 'value' ] !== null ) {
                        $this->input[ 'value' ] = $this->sanit($this->input[ 'value' ], $sanit_rules);
                  }
                  if ( (isset($rules[ 'default' ])) && ($rules[ 'default' ] !== null) && ($this->input[ 'status' ] == false) ) {
                        $this->input[ 'value' ] = $rules[ 'default' ];
                  }

                  if( (isset($rules['optional'])) && ($this->input[ 'value' ] != $this->input['old_value']) ){
                        $cleared_data['error_status'] = true;
                        $cleared_data['error_text'] = $rules['optional'];
                  }

                  //check if error found in
                  if( (false === $cleared_values['error_status']) && (false !== $this->input['error']) && (isset($rules['errors'])) && (isset($rules['errors'][$this->input['error']])) ){
                        $cleared_values['error_status'] = true;
                        $cleared_values['error_text'] = $rules['errors'][$this->input['error']];
                  }
                  $cleared_values[ $key ] = $this->input;
            }
            return $cleared_values;
      }

      /**
       * Execute different sanitization methods on given input value and return value again
       *
       * @since 1.0
       * @access private
       * @param string|array $value
       * @param string $rules
       * @return string|array
       */
      private function sanit($value, $rules)
      {
            if ( $rules == null ) {
                  return $value;
            }
            if ( strpos($rules, '&') ) {
                  $rules = explode('&', $rules);
            }
            else {
                  $rules = array( $rules );
            }
            $san_value = $value;
            $san_value = (is_array($san_value)) ? $san_value : trim($san_value);
            foreach ( $rules as $rule ) {
                  if ( strpos($rule, ':') ) {
                        $rule = explode(':', $rule);
                        $method = $rule[ 0 ];
                        $args = $rule[ 1 ];
                        $san_value = $this->$method($san_value, $args);
                  }
                  else {
                        $method = $rule;
                        $san_value = $this->$method($san_value);
                  }
            }
            return $san_value;
      }

      /**
       * Execute different validation methods on given input value and return status
       *
       * @since 1.0
       * @access private
       * @param string|array $value
       * @param string $rules
       * @return boolean
       */
      private function valid($value, $rules)
      {
            if ( $rules == null ) {
                  return true;
            }
            if ( strpos($rules, '&') ) {
                  $rules = explode('&', $rules);
            }
            else {
                  $rules = array( $rules );
            }
            $passed = true;
            $value = (is_array($value)) ? $value : trim($value);
            foreach ( $rules as $rule ) {
                  if ( strpos($rule, ':') ) {
                        $rule = explode(':', $rule);
                        $method = $rule[ 0 ];
                        $args = $rule[ 1 ];
                        $passed &= $this->$method($value, $args);
                        //detect first broken rule
                        $this->input['error'] = ( !($passed) && ($this->input['error'] == false) ) ? $method : $this->input['error'];
                  }
                  else {
                        $method = $rule;
                        $passed &= $this->$method($value);
                        //detect first broken rule
                        $this->input['error'] = ( !($passed) && ($this->input['error'] == false) ) ? $method : $this->input['error'];
                  }
            }
            return $passed;
      }

      /**
       * Sanitize email value
       *
       * Usage : add ..&semail to sanit rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return string sanitized value
       */
      private function semail($value)
      {
            return filter_var($value, FILTER_SANITIZE_EMAIL);
      }

      /**
       * Sanitize encoded value
       * + to used add ..&sencoded to sanit rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return string sanitized value
       */
      private function sencoded($value)
      {
            return filter_var($value, FILTER_SANITIZE_ENCODED);
      }

      /**
       * Sanitize full special chars value
       *
       * Usage : add ..&sfullspecialchars to sanit rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return string sanitized value
       */
      private function sfullspecialchars($value)
      {
            return filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
      }

      /**
       * Sanitize magic quotes value
       *
       * Usage : add ..&smagicquotes to sanit rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return string sanitized value
       */
      private function smagicquotes($value)
      {
            return filter_var($value, FILTER_SANITIZE_MAGIC_QUOTES);
      }

      /**
       * Sanitize number float value
       *
       * Usage : add ..&snumberfloat to sanit rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return string sanitized value
       */
      private function snumberfloat($value)
      {
            return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT);
      }

      /**
       * Sanitize number int value
       *
       * Usage : add ..&snumberint to sanit rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return string sanitized value
       */
      private function snumberint($value)
      {
            return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
      }

      /**
       * Sanitize special chars value
       *
       * Usage : add ..&sspecialchars to sanit rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return string sanitized value
       */
      private function sspecialchars($value)
      {
            return filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
      }

      /**
       * Sanitize string value
       *
       * Usage : add ..&sstring to sanit rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return string sanitized value
       */
      private function sstring($value)
      {
            return filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
      }

      /**
       * Sanitize url value
       *
       * Usage : add ..&surl to sanit rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return string sanitized value
       */
      private function surl($value)
      {
            return filter_var($value, FILTER_SANITIZE_URL);
      }

      /**
       * Sanitize comment
       *
       * Usage : add ..&scomment
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return string sanitized value
       */
      private function scomment($value)
      {
            $value = \Timber\Libraries\HtmLawed::hl( $value, array(
                  'safe' => 1,
                  'comment' => 1,
                  'css_expression' => 0,
                  'deny_attribute' => 'style',
                  'anti_link_spam' => array('/./', ''),
                  'elements' => 'a,abbr,acronym,b,blockquote,cite,code,del,em,i,q,strong'
            ), 'a=-*, -id, rel, href, title;abbr=-*, -id, title;acronym=-*, -id, title;b=-*, -id;blockquote=-*, -id, cite;cite=-*, -id;code=-*, -id;del=-*, -id, datetime;em=-*, -id;i=-*, -id;q=-*, -id, cite;strong=-*, -id;');
            return $value;
      }

      /**
       * Sanitize taxes
       *
       * Usage : add ..&stax to sanit rules
       *
       * @since 1.0
       * @access private
       * @param array $value
       * @return array sanitized value
       */
      private function stax($value)
      {

            if( !(is_array($value)) || !(isset($value['title'])) || !(isset($value['value'])) ){
                  return array();
            }

            if( (count($value['title']) < 1) || (count($value['value']) < 1) || (count($value['title']) != count($value['value'])) ){
                  return array();
            }

            $new_value = array();
            $tracked_values = array();
            foreach ($value['title'] as $key => $title) {
                  if( empty($title) ){ continue; }
                  if( in_array(trim($title), $tracked_values) ){ continue; }
                  if( !(isset($value['value'][$key])) || (empty($value['value'][$key])) || !(is_numeric($value['value'][$key])) ){ continue; }
                  $tracked_values[] = trim($title);
                  $new_value[] = array('name' => trim($title), 'value' => trim($value['value'][$key]) );
            }

            return $new_value;
      }

      /**
       * Sanitize Files IDs
       *
       * Usage : add ..&sfilesids to sanit rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return array
       */
      private function sfilesids($value)
      {
            $value = trim($value);
            if( empty($value) ){
                  return array();
            }
            $values = explode(',', $value);
            $svalues = array();

            foreach ($values as $key => $value) {
                  if( (boolean) filter_var($value, FILTER_VALIDATE_INT) ){
                        $svalues[] = $value;
                  }
            }

            return (count($svalues) > 0) ? $svalues : array();
      }

      /**
       * Sanitize Users IDs
       *
       * Usage : add ..&susersids to sanit rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return array
       */
      private function susersids($value)
      {
            $value = trim($value);
            if( empty($value) ){
                  return array();
            }
            $values = explode(',', $value);
            $svalues = array();

            foreach ($values as $key => $value) {
                  if( (boolean) filter_var($value, FILTER_VALIDATE_INT) ){
                        $svalues[] = $value;
                  }
            }

            return (count($svalues) > 0) ? $svalues : array();
      }

      /**
       * Sanitize Files
       *
       * Usage : add ..&sfiles to sanit rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return array
       */
      private function sfiles($value)
      {
            $files = explode('----||||----', $value);
            $svalue = array();
            foreach ($files as $file) {
                  if( (empty($file)) || !(strpos($file, '--||--') > 0) ){ continue; }
                  $svalue[] = $file;
            }
            return $svalue;
      }

      /**
       * Sanitize invoices
       *
       * Usage : add ..&sinvs to sanit rules
       *
       * @since 1.0
       * @access private
       * @param array $value
       * @return array
       */
      private function sinvs($value)
      {
            $terms = array();

            # Notes status
            $notes_status = true;
            if( !isset($value['notes']) ){
                  # Notes optional
                  $terms['notes'] = '';
            }else{
                  $terms['notes'] = $value['notes'];
            }

            # Items status
            $items_status = true;
            if( !(isset($value['items'])) || !(is_array($value['items'])) || !(count($value['items']) == 6) ){
                  $items_status &= false;
            }
            if( !($items_status) || !(isset($value['items']['item_select'])) || !(is_array($value['items']['item_select'])) || !(count($value['items']['item_select']) > 0) || !(isset($value['items']['item_title'])) || !(is_array($value['items']['item_title'])) || !(count($value['items']['item_title']) > 0) || !(count($value['items']['item_title']) == count($value['items']['item_select'])) || !(isset($value['items']['item_description'])) || !(is_array($value['items']['item_description'])) || !(count($value['items']['item_description']) > 0) || !(count($value['items']['item_description']) == count($value['items']['item_select'])) || !(isset($value['items']['item_quantity'])) || !(is_array($value['items']['item_quantity'])) || !(count($value['items']['item_quantity']) > 0) || !(count($value['items']['item_quantity']) == count($value['items']['item_select'])) || !(isset($value['items']['item_unit_price'])) || !(is_array($value['items']['item_unit_price'])) || !(count($value['items']['item_unit_price']) > 0) || !(count($value['items']['item_unit_price']) == count($value['items']['item_select'])) || !(isset($value['items']['item_sub_total'])) || !(is_array($value['items']['item_sub_total'])) || !(count($value['items']['item_sub_total']) > 0) || !(count($value['items']['item_sub_total']) == count($value['items']['item_select'])) ){
                  $items_status &= false;
            }

            $terms['items'] = array();
            $sub_total = 0;
            if( ($items_status) ){
                  $i = 0;

                  $value['items']['item_select'] = array_values($value['items']['item_select']);
                  $value['items']['item_title'] = array_values($value['items']['item_title']);
                  $value['items']['item_description'] = array_values($value['items']['item_description']);
                  $value['items']['item_quantity'] = array_values($value['items']['item_quantity']);
                  $value['items']['item_unit_price'] = array_values($value['items']['item_unit_price']);
                  $value['items']['item_sub_total'] = array_values($value['items']['item_sub_total']);

                  foreach ($value['items']['item_select'] as $key => $item) {

                        if( (empty($value['items']['item_title'][$key])) || !($this->vfloat($value['items']['item_quantity'][$key],'0')) || !($this->vfloat($value['items']['item_unit_price'][$key],',,,0')) ){
                              continue;
                        }
                        $terms['items'][$i]['item_select'] = $value['items']['item_select'][$key];
                        $terms['items'][$i]['item_title'] = $value['items']['item_title'][$key];
                        $terms['items'][$i]['item_description'] = $value['items']['item_description'][$key];
                        $terms['items'][$i]['item_quantity'] = $value['items']['item_quantity'][$key];
                        $terms['items'][$i]['item_unit_price'] = $value['items']['item_unit_price'][$key];
                        #$terms['items'][$i]['item_sub_total'] = $value['items']['item_sub_total'][$key];
                        $terms['items'][$i]['item_sub_total'] = $value['items']['item_quantity'][$key] * $value['items']['item_unit_price'][$key];

                        $sub_total += $terms['items'][$i]['item_sub_total'];

                        $i += 1;
                  }
            }

            # Overall status
            $overall_status = true;
            if( ($items_status) && !(count($terms['items']) > 0) ){
                  $items_status &= false;
                  $overall_status &= false;
            }
            if( !($items_status) || !(isset($value['overall'])) || !(is_array($value['overall'])) || !(count($value['overall']) == 8) ){
                  $overall_status &= false;
            }
            # If Overall still valid
            if( !($items_status) || !($overall_status) || !(isset($value['overall']['sub_total'])) || !(isset($value['overall']['discount_type'])) || !(isset($value['overall']['discount_value'])) || !(isset($value['overall']['tax_type'])) || !(isset($value['overall']['tax_select'])) || !(isset($value['overall']['tax_value'])) || !(isset($value['overall']['total_value'])) || !(isset($value['overall']['paid_value'])) ){
                  $overall_status &= false;
            }

            $terms['overall'] = array();
            if( ($items_status) && ($overall_status) ){
                  $terms['overall']['sub_total'] = $sub_total;

                  $terms['overall']['discount_type'] = (in_array($value['overall']['discount_type'], array( 'off', 'percent', 'flat'))) ? $value['overall']['discount_type'] : 'off';
                  $terms['overall']['discount_value'] = ($this->vfloat($value['overall']['discount_value'],',,,0')) ? $value['overall']['discount_value'] : 0;
                  $terms['overall']['tax_type'] = (in_array($value['overall']['tax_type'], array( 'off', 'percent', 'flat'))) ? $value['overall']['tax_type'] : 'off';
                  $terms['overall']['tax_select'] = filter_var($value['overall']['tax_select'],FILTER_SANITIZE_STRING);
                  $terms['overall']['tax_value'] = ($this->vfloat($value['overall']['tax_value'],',,,0')) ? $value['overall']['tax_value'] : 0;

                  if( 'percent' == $terms['overall']['discount_type'] ){
                        $sub_total -= ($sub_total * $terms['overall']['discount_value']) / 100;
                  }elseif( 'flat' == $terms['overall']['discount_type'] ){
                        $sub_total -= $terms['overall']['discount_value'];
                  }

                  if( 'percent' == $terms['overall']['tax_type'] ){
                        $sub_total += ($sub_total * $terms['overall']['tax_value']) / 100;
                  }elseif( 'flat' == $terms['overall']['tax_type'] ){
                        $sub_total += $terms['overall']['tax_value'];
                  }

                  $terms['overall']['total_value'] = $sub_total;
                  $terms['overall']['paid_value'] = $value['overall']['paid_value'];

                  if( $terms['overall']['total_value'] < 0 ){
                        $terms['overall']['total_value'] = 0;
                  }

                  if( $terms['overall']['paid_value'] < 0 ){
                        $terms['overall']['paid_value'] = 0;
                  }

                  if( $terms['overall']['paid_value'] >  $terms['overall']['total_value']){
                        $terms['overall']['paid_value'] = $terms['overall']['total_value'];
                  }
            }

            return $terms;
      }

      /**
       * Sanitize expenses
       *
       * Usage : add ..&sexpen to sanit rules
       *
       * @since 1.0
       * @access private
       * @param array $value
       * @return array
       */
      private function sexpen($value)
      {
            $terms = array();

            # Notes status
            $desc_status = true;
            if( !isset($value['description']) ){
                  # Notes optional
                  $terms['description'] = '';
            }else{
                  $terms['description'] = filter_var($value['description'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
            }

            $title_status = true;
            $value['title'] = (isset($value['title'])) ? trim($value['title']) : '';
            $value['title'] = filter_var($value['title'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
            if( (empty($value['title'])) || (strlen($value['title']) <= 2) ){
                  # Notes optional
                  $terms['title'] = '';
                  $title_status &= false;
            }else{
                  $terms['title'] = $value['title'];
            }

            # Overall status
            $overall_status = true;
            # If Overall still valid
            if( !($desc_status) || !($title_status) || !(isset($value['sub_total'])) || !(isset($value['discount_type'])) || !(isset($value['discount_value'])) || !(isset($value['tax_type'])) || !(isset($value['tax_select'])) || !(isset($value['tax_value'])) || !(isset($value['total_value'])) ){
                  $overall_status &= false;
            }

            if( ($title_status) && ($desc_status) && ($overall_status) ){

                  $terms['sub_total'] = ($this->vfloat($value['sub_total'],'0')) ? $value['sub_total'] : 0;

                  $sub_total = $terms['sub_total'];

                  $terms['discount_type'] = (in_array($value['discount_type'], array( 'off', 'percent', 'flat'))) ? $value['discount_type'] : 'off';
                  $terms['discount_value'] = ($this->vfloat($value['discount_value'],',,,0')) ? $value['discount_value'] : 0;
                  $terms['tax_type'] = (in_array($value['tax_type'], array( 'off', 'percent', 'flat'))) ? $value['tax_type'] : 'off';
                  $terms['tax_select'] = filter_var($value['tax_select'],FILTER_SANITIZE_STRING);
                  $terms['tax_value'] = ($this->vfloat($value['tax_value'],',,,0')) ? $value['tax_value'] : 0;

                  if( 'percent' == $terms['discount_type'] ){
                        $sub_total -= ($sub_total * $terms['discount_value']) / 100;
                  }elseif( 'flat' == $terms['discount_type'] ){
                        $sub_total -= $terms['discount_value'];
                  }

                  if( 'percent' == $terms['tax_type'] ){
                        $sub_total += ($sub_total * $terms['tax_value']) / 100;
                  }elseif( 'flat' == $terms['tax_type'] ){
                        $sub_total += $terms['tax_value'];
                  }

                  $terms['total_value'] = $sub_total;

                  if( ($terms['total_value'] < 0) ){
                        $overall_status &= false;
                        $terms['total_value'] = 0;
                  }
            }

            return $terms;
      }

      /**
       * Sanitize estimates
       *
       * Usage : add ..&sestim to sanit rules
       *
       * @since 1.0
       * @access private
       * @param array $value
       * @return array
       */
      private function sestim($value)
      {
            $terms = array();

            # Notes status
            $notes_status = true;
            if( !isset($value['notes']) ){
                  # Notes optional
                  $terms['notes'] = '';
            }else{
                  $terms['notes'] = $value['notes'];
            }

            # Items status
            $items_status = true;
            if( !(isset($value['items'])) || !(is_array($value['items'])) || !(count($value['items']) == 6) ){
                  $items_status &= false;
            }
            if( !($items_status) || !(isset($value['items']['item_select'])) || !(is_array($value['items']['item_select'])) || !(count($value['items']['item_select']) > 0) || !(isset($value['items']['item_title'])) || !(is_array($value['items']['item_title'])) || !(count($value['items']['item_title']) > 0) || !(count($value['items']['item_title']) == count($value['items']['item_select'])) || !(isset($value['items']['item_description'])) || !(is_array($value['items']['item_description'])) || !(count($value['items']['item_description']) > 0) || !(count($value['items']['item_description']) == count($value['items']['item_select'])) || !(isset($value['items']['item_quantity'])) || !(is_array($value['items']['item_quantity'])) || !(count($value['items']['item_quantity']) > 0) || !(count($value['items']['item_quantity']) == count($value['items']['item_select'])) || !(isset($value['items']['item_unit_price'])) || !(is_array($value['items']['item_unit_price'])) || !(count($value['items']['item_unit_price']) > 0) || !(count($value['items']['item_unit_price']) == count($value['items']['item_select'])) || !(isset($value['items']['item_sub_total'])) || !(is_array($value['items']['item_sub_total'])) || !(count($value['items']['item_sub_total']) > 0) || !(count($value['items']['item_sub_total']) == count($value['items']['item_select'])) ){
                  $items_status &= false;
            }

            $terms['items'] = array();
            $sub_total = 0;
            if( ($items_status) ){
                  $i = 0;

                  $value['items']['item_select'] = array_values($value['items']['item_select']);
                  $value['items']['item_title'] = array_values($value['items']['item_title']);
                  $value['items']['item_description'] = array_values($value['items']['item_description']);
                  $value['items']['item_quantity'] = array_values($value['items']['item_quantity']);
                  $value['items']['item_unit_price'] = array_values($value['items']['item_unit_price']);
                  $value['items']['item_sub_total'] = array_values($value['items']['item_sub_total']);

                  foreach ($value['items']['item_select'] as $key => $item) {

                        if( (empty($value['items']['item_title'][$key])) || !($this->vfloat($value['items']['item_quantity'][$key],'0')) || !($this->vfloat($value['items']['item_unit_price'][$key],',,,0')) ){
                              continue;
                        }
                        $terms['items'][$i]['item_select'] = $value['items']['item_select'][$key];
                        $terms['items'][$i]['item_title'] = $value['items']['item_title'][$key];
                        $terms['items'][$i]['item_description'] = $value['items']['item_description'][$key];
                        $terms['items'][$i]['item_quantity'] = $value['items']['item_quantity'][$key];
                        $terms['items'][$i]['item_unit_price'] = $value['items']['item_unit_price'][$key];
                        #$terms['items'][$i]['item_sub_total'] = $value['items']['item_sub_total'][$key];
                        $terms['items'][$i]['item_sub_total'] = $value['items']['item_quantity'][$key] * $value['items']['item_unit_price'][$key];

                        $sub_total += $terms['items'][$i]['item_sub_total'];

                        $i += 1;
                  }
            }

            # Overall status
            $overall_status = true;
            if( ($items_status) && !(count($terms['items']) > 0) ){
                  $items_status &= false;
                  $overall_status &= false;
            }
            if( !($items_status) || !(isset($value['overall'])) || !(is_array($value['overall'])) || !(count($value['overall']) == 8) ){
                  $overall_status &= false;
            }
            # If Overall still valid
            if( !($items_status) || !($overall_status) || !(isset($value['overall']['sub_total'])) || !(isset($value['overall']['discount_type'])) || !(isset($value['overall']['discount_value'])) || !(isset($value['overall']['tax_type'])) || !(isset($value['overall']['tax_select'])) || !(isset($value['overall']['tax_value'])) || !(isset($value['overall']['total_value'])) || !(isset($value['overall']['paid_value'])) ){
                  $overall_status &= false;
            }

            $terms['overall'] = array();
            if( ($items_status) && ($overall_status) ){
                  $terms['overall']['sub_total'] = $sub_total;

                  $terms['overall']['discount_type'] = (in_array($value['overall']['discount_type'], array( 'off', 'percent', 'flat'))) ? $value['overall']['discount_type'] : 'off';
                  $terms['overall']['discount_value'] = ($this->vfloat($value['overall']['discount_value'],',,,0')) ? $value['overall']['discount_value'] : 0;
                  $terms['overall']['tax_type'] = (in_array($value['overall']['tax_type'], array( 'off', 'percent', 'flat'))) ? $value['overall']['tax_type'] : 'off';
                  $terms['overall']['tax_select'] = filter_var($value['overall']['tax_select'],FILTER_SANITIZE_STRING);
                  $terms['overall']['tax_value'] = ($this->vfloat($value['overall']['tax_value'],',,,0')) ? $value['overall']['tax_value'] : 0;

                  if( 'percent' == $terms['overall']['discount_type'] ){
                        $sub_total -= ($sub_total * $terms['overall']['discount_value']) / 100;
                  }elseif( 'flat' == $terms['overall']['discount_type'] ){
                        $sub_total -= $terms['overall']['discount_value'];
                  }

                  if( 'percent' == $terms['overall']['tax_type'] ){
                        $sub_total += ($sub_total * $terms['overall']['tax_value']) / 100;
                  }elseif( 'flat' == $terms['overall']['tax_type'] ){
                        $sub_total += $terms['overall']['tax_value'];
                  }

                  $terms['overall']['total_value'] = $sub_total;
                  $terms['overall']['paid_value'] = $value['overall']['paid_value'];

                  if( $terms['overall']['total_value'] < 0 ){
                        $terms['overall']['total_value'] = 0;
                  }

                  if( $terms['overall']['paid_value'] < 0 ){
                        $terms['overall']['paid_value'] = 0;
                  }

                  if( $terms['overall']['paid_value'] >  $terms['overall']['total_value']){
                        $terms['overall']['paid_value'] = $terms['overall']['total_value'];
                  }
            }

            return $terms;
      }

      /**
       * Sanitize subscriptions
       *
       * Usage : add ..&ssubs to sanit rules
       *
       * @since 1.0
       * @access private
       * @param array $value
       * @return array
       */
      private function ssubs($value)
      {
            $terms = array();

            # Notes status
            $notes_status = true;
            if( !isset($value['notes']) ){
                  # Notes optional
                  $terms['notes'] = '';
            }else{
                  $terms['notes'] = $value['notes'];
            }

            # Items status
            $items_status = true;
            if( !(isset($value['items'])) || !(is_array($value['items'])) || !(count($value['items']) == 6) ){
                  $items_status &= false;
            }
            if( !($items_status) || !(isset($value['items']['item_select'])) || !(is_array($value['items']['item_select'])) || !(count($value['items']['item_select']) > 0) || !(isset($value['items']['item_title'])) || !(is_array($value['items']['item_title'])) || !(count($value['items']['item_title']) > 0) || !(count($value['items']['item_title']) == count($value['items']['item_select'])) || !(isset($value['items']['item_description'])) || !(is_array($value['items']['item_description'])) || !(count($value['items']['item_description']) > 0) || !(count($value['items']['item_description']) == count($value['items']['item_select'])) || !(isset($value['items']['item_quantity'])) || !(is_array($value['items']['item_quantity'])) || !(count($value['items']['item_quantity']) > 0) || !(count($value['items']['item_quantity']) == count($value['items']['item_select'])) || !(isset($value['items']['item_unit_price'])) || !(is_array($value['items']['item_unit_price'])) || !(count($value['items']['item_unit_price']) > 0) || !(count($value['items']['item_unit_price']) == count($value['items']['item_select'])) || !(isset($value['items']['item_sub_total'])) || !(is_array($value['items']['item_sub_total'])) || !(count($value['items']['item_sub_total']) > 0) || !(count($value['items']['item_sub_total']) == count($value['items']['item_select'])) ){
                  $items_status &= false;
            }

            $terms['items'] = array();
            $sub_total = 0;
            if( ($items_status) ){
                  $i = 0;

                  $value['items']['item_select'] = array_values($value['items']['item_select']);
                  $value['items']['item_title'] = array_values($value['items']['item_title']);
                  $value['items']['item_description'] = array_values($value['items']['item_description']);
                  $value['items']['item_quantity'] = array_values($value['items']['item_quantity']);
                  $value['items']['item_unit_price'] = array_values($value['items']['item_unit_price']);
                  $value['items']['item_sub_total'] = array_values($value['items']['item_sub_total']);

                  foreach ($value['items']['item_select'] as $key => $item) {

                        if( (empty($value['items']['item_title'][$key])) || !($this->vfloat($value['items']['item_quantity'][$key],'0')) || !($this->vfloat($value['items']['item_unit_price'][$key],',,,0')) ){
                              continue;
                        }
                        $terms['items'][$i]['item_select'] = $value['items']['item_select'][$key];
                        $terms['items'][$i]['item_title'] = $value['items']['item_title'][$key];
                        $terms['items'][$i]['item_description'] = $value['items']['item_description'][$key];
                        $terms['items'][$i]['item_quantity'] = $value['items']['item_quantity'][$key];
                        $terms['items'][$i]['item_unit_price'] = $value['items']['item_unit_price'][$key];
                        #$terms['items'][$i]['item_sub_total'] = $value['items']['item_sub_total'][$key];
                        $terms['items'][$i]['item_sub_total'] = $value['items']['item_quantity'][$key] * $value['items']['item_unit_price'][$key];

                        $sub_total += $terms['items'][$i]['item_sub_total'];

                        $i += 1;
                  }
            }

            # Overall status
            $overall_status = true;
            if( ($items_status) && !(count($terms['items']) > 0) ){
                  $items_status &= false;
                  $overall_status &= false;
            }
            if( !($items_status) || !(isset($value['overall'])) || !(is_array($value['overall'])) || !(count($value['overall']) == 7) ){
                  $overall_status &= false;
            }
            # If Overall still valid
            if( !($items_status) || !($overall_status) || !(isset($value['overall']['sub_total'])) || !(isset($value['overall']['discount_type'])) || !(isset($value['overall']['discount_value'])) || !(isset($value['overall']['tax_type'])) || !(isset($value['overall']['tax_select'])) || !(isset($value['overall']['tax_value'])) || !(isset($value['overall']['total_value'])) ){
                  $overall_status &= false;
            }

            $terms['overall'] = array();
            if( ($items_status) && ($overall_status) ){
                  $terms['overall']['sub_total'] = $sub_total;

                  $terms['overall']['discount_type'] = (in_array($value['overall']['discount_type'], array( 'off', 'percent', 'flat'))) ? $value['overall']['discount_type'] : 'off';
                  $terms['overall']['discount_value'] = ($this->vfloat($value['overall']['discount_value'],',,,0')) ? $value['overall']['discount_value'] : 0;
                  $terms['overall']['tax_type'] = (in_array($value['overall']['tax_type'], array( 'off', 'percent', 'flat'))) ? $value['overall']['tax_type'] : 'off';
                  $terms['overall']['tax_select'] = filter_var($value['overall']['tax_select'],FILTER_SANITIZE_STRING);
                  $terms['overall']['tax_value'] = ($this->vfloat($value['overall']['tax_value'],',,,0')) ? $value['overall']['tax_value'] : 0;

                  if( 'percent' == $terms['overall']['discount_type'] ){
                        $sub_total -= ($sub_total * $terms['overall']['discount_value']) / 100;
                  }elseif( 'flat' == $terms['overall']['discount_type'] ){
                        $sub_total -= $terms['overall']['discount_value'];
                  }

                  if( 'percent' == $terms['overall']['tax_type'] ){
                        $sub_total += ($sub_total * $terms['overall']['tax_value']) / 100;
                  }elseif( 'flat' == $terms['overall']['tax_type'] ){
                        $sub_total += $terms['overall']['tax_value'];
                  }

                  $terms['overall']['total_value'] = $sub_total;

                  if( $terms['overall']['total_value'] < 0 ){
                        $terms['overall']['total_value'] = 0;
                  }
                  $terms['overall']['paid_value'] = 0;
            }

            return $terms;
      }

      /**
       * Sanitize quotations
       *
       * Usage : add ..&squot to sanit rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return string
       */
      private function squot($value)
      {
            $value = json_decode($value);
            $status = true;

            $new_value = array();
            $i = 1;
            foreach ($value as $key => $element) {
                  $status &= ( (isset($element->type)) && (in_array($element->type, array('sect_elem', 'text_elem', 'para_elem', 'chek_elem', 'mult_elem', 'drop_elem', 'date_elem'))) ) ? true : false;
                  $status &= ( (isset($element->required)) ) ? true : false;
                  $status &= ( (isset($element->label)) ) ? true : false;
                  $status &= ( (isset($element->name)) ) ? true : false;
                  $status &= ( (isset($element->data)) ) ? true : false;
                  $status &= ( (isset($element->data->placeholder)) ) ? true : false;
                  $status &= ( (isset($element->data->items)) ) ? true : false;

                  if( ('sect_elem' ==  $element->type) && ($status) ){

                        $new_value[$i] = array();
                        $new_value[$i]['type'] = 'sect_elem';
                        $new_value[$i]['required'] = '';
                        $new_value[$i]['label'] = '';
                        $new_value[$i]['name'] = '';
                        $new_value[$i]['data'] = array();
                        $new_value[$i]['data']['placeholder'] = '';
                        $new_value[$i]['data']['items'] = '';

                  }elseif( ('text_elem' ==  $element->type) && ($status) ){

                        $new_value[$i] = array();
                        $new_value[$i]['type'] = 'text_elem';
                        $new_value[$i]['required'] = (in_array($element->required, array('1','2'))) ? $element->required : 1;
                        $new_value[$i]['label'] = filter_var(trim($element->label), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                        $status &= ( (empty($new_value[$i]['label'])) || (strlen($new_value[$i]['label']) <= 2) ) ? false : true;
                        $new_value[$i]['name'] = preg_replace('/[^A-Za-z0-9-]+/', '-', $new_value[$i]['label']) . $i . $i;
                        $new_value[$i]['name'] = strtolower($new_value[$i]['name']);
                        $new_value[$i]['data'] = array();
                        $new_value[$i]['data']['placeholder'] = filter_var($element->data->placeholder, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                        $new_value[$i]['data']['items'] = '';

                  }elseif( ('para_elem' ==  $element->type) && ($status) ){

                        $new_value[$i] = array();
                        $new_value[$i]['type'] = 'para_elem';
                        $new_value[$i]['required'] = (in_array($element->required, array('1','2'))) ? $element->required : 1;
                        $new_value[$i]['label'] = filter_var(trim($element->label), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                        $status &= ( (empty($new_value[$i]['label'])) || (strlen($new_value[$i]['label']) <= 2) ) ? false : true;
                        $new_value[$i]['name'] = preg_replace('/[^A-Za-z0-9-]+/', '-', $new_value[$i]['label']) . $i . $i;
                        $new_value[$i]['name'] = strtolower($new_value[$i]['name']);
                        $new_value[$i]['data'] = array();
                        $new_value[$i]['data']['placeholder'] = filter_var($element->data->placeholder, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                        $new_value[$i]['data']['items'] = '';

                  }elseif( ('chek_elem' ==  $element->type) && ($status) ){

                        $new_value[$i] = array();
                        $new_value[$i]['type'] = 'chek_elem';
                        $new_value[$i]['required'] = (in_array($element->required, array('1','2'))) ? $element->required : 1;
                        $new_value[$i]['label'] = filter_var(trim($element->label), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                        $status &= ( (empty($new_value[$i]['label'])) || (strlen($new_value[$i]['label']) <= 2) ) ? false : true;
                        $new_value[$i]['name'] = preg_replace('/[^A-Za-z0-9-]+/', '-', $new_value[$i]['label']) . $i . $i;
                        $new_value[$i]['name'] = strtolower($new_value[$i]['name']);
                        $new_value[$i]['data'] = array();
                        $new_value[$i]['data']['placeholder'] = '';
                        $new_value[$i]['data']['items'] = '';

                  }elseif( ('mult_elem' ==  $element->type) && ($status) ){

                        $new_value[$i] = array();
                        $new_value[$i]['type'] = 'mult_elem';
                        $new_value[$i]['required'] = (in_array($element->required, array('1','2'))) ? $element->required : 1;
                        $new_value[$i]['label'] = filter_var(trim($element->label), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                        $status &= ( (empty($new_value[$i]['label'])) || (strlen($new_value[$i]['label']) <= 2) ) ? false : true;
                        $new_value[$i]['name'] = preg_replace('/[^A-Za-z0-9-]+/', '-', $new_value[$i]['label']) . $i . $i;
                        $new_value[$i]['name'] = strtolower($new_value[$i]['name']);
                        $new_value[$i]['data'] = array();
                        $new_value[$i]['data']['placeholder'] = '';
                        $items = (is_array($element->data->items)) ? implode(',', $element->data->items) : '';
                        $items = filter_var(trim($items), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                        $new_value[$i]['data']['items'] = explode(',', $items);

                  }elseif( ('drop_elem' ==  $element->type) && ($status) ){

                        $new_value[$i] = array();
                        $new_value[$i]['type'] = 'drop_elem';
                        $new_value[$i]['required'] = (in_array($element->required, array('1','2'))) ? $element->required : 1;
                        $new_value[$i]['label'] = filter_var(trim($element->label), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                        $status &= ( (empty($new_value[$i]['label'])) || (strlen($new_value[$i]['label']) <= 2) ) ? false : true;
                        $new_value[$i]['name'] = preg_replace('/[^A-Za-z0-9-]+/', '-', $new_value[$i]['label']) . $i . $i;
                        $new_value[$i]['name'] = strtolower($new_value[$i]['name']);
                        $new_value[$i]['data'] = array();
                        $new_value[$i]['data']['placeholder'] = '';
                        $items = (is_array($element->data->items)) ? implode(',', $element->data->items) : '';
                        $items = filter_var(trim($items), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                        $new_value[$i]['data']['items'] = explode(',', $items);

                  }elseif( ('date_elem' ==  $element->type) && ($status) ){

                        $new_value[$i] = array();
                        $new_value[$i]['type'] = 'date_elem';
                        $new_value[$i]['required'] = (in_array($element->required, array('1','2'))) ? $element->required : 1;
                        $new_value[$i]['label'] = filter_var(trim($element->label), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                        $status &= ( (empty($new_value[$i]['label'])) || (strlen($new_value[$i]['label']) <= 2) ) ? false : true;
                        $new_value[$i]['name'] = preg_replace('/[^A-Za-z0-9-]+/', '-', $new_value[$i]['label']) . $i . $i;
                        $new_value[$i]['name'] = strtolower($new_value[$i]['name']);
                        $new_value[$i]['data'] = array();
                        $new_value[$i]['data']['placeholder'] = filter_var($element->data->placeholder, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                        $new_value[$i]['data']['items'] = '';

                  }

                  $i += 1;
            }

            return $new_value;
      }

      /**
       * Validate quotations
       *
       * Usage : add ..&vquot to valid rules
       *
       * @since 1.0
       * @access private
       * @param array $value
       * @return boolean
       */
      private function vquot($value)
      {
            $value = json_decode($value);
            $status = true;

            $new_value = array();
            $i = 1;
            foreach ($value as $key => $element) {
                  $status &= ( (isset($element->type)) && (in_array($element->type, array('sect_elem', 'text_elem', 'para_elem', 'chek_elem', 'mult_elem', 'drop_elem', 'date_elem'))) ) ? true : false;
                  $status &= ( (isset($element->required)) ) ? true : false;
                  $status &= ( (isset($element->label)) ) ? true : false;
                  $status &= ( (isset($element->name)) ) ? true : false;
                  $status &= ( (isset($element->data)) ) ? true : false;
                  $status &= ( (isset($element->data->placeholder)) ) ? true : false;
                  $status &= ( (isset($element->data->items)) ) ? true : false;

                  if( ('sect_elem' ==  $element->type) && ($status) ){

                        $new_value[$i] = array();
                        $new_value[$i]['type'] = 'sect_elem';
                        $new_value[$i]['required'] = '';
                        $new_value[$i]['label'] = '';
                        $new_value[$i]['name'] = '';
                        $new_value[$i]['data'] = array();
                        $new_value[$i]['data']['placeholder'] = '';
                        $new_value[$i]['data']['items'] = '';

                  }elseif( ('text_elem' ==  $element->type) && ($status) ){

                        $new_value[$i] = array();
                        $new_value[$i]['type'] = 'text_elem';
                        $new_value[$i]['required'] = (in_array($element->required, array('1','2'))) ? $element->required : 1;
                        $new_value[$i]['label'] = filter_var(trim($element->label), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                        $status &= ( (empty($new_value[$i]['label'])) || (strlen($new_value[$i]['label']) <= 2) ) ? false : true;
                        $new_value[$i]['name'] = preg_replace('/[^A-Za-z0-9-]+/', '-', $new_value[$i]['label']) . $i . $i;
                        $new_value[$i]['name'] = strtolower($new_value[$i]['name']);
                        $new_value[$i]['data'] = array();
                        $new_value[$i]['data']['placeholder'] = filter_var($element->data->placeholder, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                        $new_value[$i]['data']['items'] = '';

                  }elseif( ('para_elem' ==  $element->type) && ($status) ){

                        $new_value[$i] = array();
                        $new_value[$i]['type'] = 'para_elem';
                        $new_value[$i]['required'] = (in_array($element->required, array('1','2'))) ? $element->required : 1;
                        $new_value[$i]['label'] = filter_var(trim($element->label), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                        $status &= ( (empty($new_value[$i]['label'])) || (strlen($new_value[$i]['label']) <= 2) ) ? false : true;
                        $new_value[$i]['name'] = preg_replace('/[^A-Za-z0-9-]+/', '-', $new_value[$i]['label']) . $i . $i;
                        $new_value[$i]['name'] = strtolower($new_value[$i]['name']);
                        $new_value[$i]['data'] = array();
                        $new_value[$i]['data']['placeholder'] = filter_var($element->data->placeholder, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                        $new_value[$i]['data']['items'] = '';

                  }elseif( ('chek_elem' ==  $element->type) && ($status) ){

                        $new_value[$i] = array();
                        $new_value[$i]['type'] = 'chek_elem';
                        $new_value[$i]['required'] = (in_array($element->required, array('1','2'))) ? $element->required : 1;
                        $new_value[$i]['label'] = filter_var(trim($element->label), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                        $status &= ( (empty($new_value[$i]['label'])) || (strlen($new_value[$i]['label']) <= 2) ) ? false : true;
                        $new_value[$i]['name'] = preg_replace('/[^A-Za-z0-9-]+/', '-', $new_value[$i]['label']) . $i . $i;
                        $new_value[$i]['name'] = strtolower($new_value[$i]['name']);
                        $new_value[$i]['data'] = array();
                        $new_value[$i]['data']['placeholder'] = '';
                        $new_value[$i]['data']['items'] = '';

                  }elseif( ('mult_elem' ==  $element->type) && ($status) ){

                        $new_value[$i] = array();
                        $new_value[$i]['type'] = 'mult_elem';
                        $new_value[$i]['required'] = (in_array($element->required, array('1','2'))) ? $element->required : 1;
                        $new_value[$i]['label'] = filter_var(trim($element->label), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                        $status &= ( (empty($new_value[$i]['label'])) || (strlen($new_value[$i]['label']) <= 2) ) ? false : true;
                        $new_value[$i]['name'] = preg_replace('/[^A-Za-z0-9-]+/', '-', $new_value[$i]['label']) . $i . $i;
                        $new_value[$i]['name'] = strtolower($new_value[$i]['name']);
                        $new_value[$i]['data'] = array();
                        $new_value[$i]['data']['placeholder'] = '';
                        $items = (is_array($element->data->items)) ? implode(',', $element->data->items) : '';
                        $items = filter_var(trim($items), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                        $new_value[$i]['data']['items'] = explode(',', $items);

                  }elseif( ('drop_elem' ==  $element->type) && ($status) ){

                        $new_value[$i] = array();
                        $new_value[$i]['type'] = 'drop_elem';
                        $new_value[$i]['required'] = (in_array($element->required, array('1','2'))) ? $element->required : 1;
                        $new_value[$i]['label'] = filter_var(trim($element->label), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                        $status &= ( (empty($new_value[$i]['label'])) || (strlen($new_value[$i]['label']) <= 2) ) ? false : true;
                        $new_value[$i]['name'] = preg_replace('/[^A-Za-z0-9-]+/', '-', $new_value[$i]['label']) . $i . $i;
                        $new_value[$i]['name'] = strtolower($new_value[$i]['name']);
                        $new_value[$i]['data'] = array();
                        $new_value[$i]['data']['placeholder'] = '';
                        $items = (is_array($element->data->items)) ? implode(',', $element->data->items) : '';
                        $items = filter_var(trim($items), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                        $new_value[$i]['data']['items'] = explode(',', $items);

                  }elseif( ('date_elem' ==  $element->type) && ($status) ){

                        $new_value[$i] = array();
                        $new_value[$i]['type'] = 'date_elem';
                        $new_value[$i]['required'] = (in_array($element->required, array('1','2'))) ? $element->required : 1;
                        $new_value[$i]['label'] = filter_var(trim($element->label), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                        $status &= ( (empty($new_value[$i]['label'])) || (strlen($new_value[$i]['label']) <= 2) ) ? false : true;
                        $new_value[$i]['name'] = preg_replace('/[^A-Za-z0-9-]+/', '-', $new_value[$i]['label']) . $i . $i;
                        $new_value[$i]['name'] = strtolower($new_value[$i]['name']);
                        $new_value[$i]['data'] = array();
                        $new_value[$i]['data']['placeholder'] = filter_var($element->data->placeholder, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
                        $new_value[$i]['data']['items'] = '';

                  }

                  $i += 1;
            }

            return (boolean) $status;
      }


      /**
       * Validate subscriptions
       *
       * Usage : add ..&vsubs to valid rules
       *
       * @since 1.0
       * @access private
       * @param array $value
       * @return boolean
       */
      private function vsubs($value)
      {
            $terms = array();

            # Notes status
            $notes_status = true;
            if( !isset($value['notes']) ){
                  # Notes optional
                  $terms['notes'] = '';
            }else{
                  $terms['notes'] = $value['notes'];
            }

            # Items status
            $items_status = true;
            if( !(isset($value['items'])) || !(is_array($value['items'])) || !(count($value['items']) == 6) ){
                  $items_status &= false;
            }
            if( !($items_status) || !(isset($value['items']['item_select'])) || !(is_array($value['items']['item_select'])) || !(count($value['items']['item_select']) > 0) || !(isset($value['items']['item_title'])) || !(is_array($value['items']['item_title'])) || !(count($value['items']['item_title']) > 0) || !(count($value['items']['item_title']) == count($value['items']['item_select'])) || !(isset($value['items']['item_description'])) || !(is_array($value['items']['item_description'])) || !(count($value['items']['item_description']) > 0) || !(count($value['items']['item_description']) == count($value['items']['item_select'])) || !(isset($value['items']['item_quantity'])) || !(is_array($value['items']['item_quantity'])) || !(count($value['items']['item_quantity']) > 0) || !(count($value['items']['item_quantity']) == count($value['items']['item_select'])) || !(isset($value['items']['item_unit_price'])) || !(is_array($value['items']['item_unit_price'])) || !(count($value['items']['item_unit_price']) > 0) || !(count($value['items']['item_unit_price']) == count($value['items']['item_select'])) || !(isset($value['items']['item_sub_total'])) || !(is_array($value['items']['item_sub_total'])) || !(count($value['items']['item_sub_total']) > 0) || !(count($value['items']['item_sub_total']) == count($value['items']['item_select'])) ){
                  $items_status &= false;
            }

            $terms['items'] = array();
            $sub_total = 0;
            if( ($items_status) ){
                  $i = 0;

                  $value['items']['item_select'] = array_values($value['items']['item_select']);
                  $value['items']['item_title'] = array_values($value['items']['item_title']);
                  $value['items']['item_description'] = array_values($value['items']['item_description']);
                  $value['items']['item_quantity'] = array_values($value['items']['item_quantity']);
                  $value['items']['item_unit_price'] = array_values($value['items']['item_unit_price']);
                  $value['items']['item_sub_total'] = array_values($value['items']['item_sub_total']);

                  foreach ($value['items']['item_select'] as $key => $item) {

                        if( (empty($value['items']['item_title'][$key])) || !($this->vfloat($value['items']['item_quantity'][$key],'0')) || !($this->vfloat($value['items']['item_unit_price'][$key],',,,0')) ){
                              continue;
                        }

                        $terms['items'][$i]['item_select'] = $value['items']['item_select'][$key];
                        $terms['items'][$i]['item_title'] = $value['items']['item_title'][$key];
                        $terms['items'][$i]['item_description'] = $value['items']['item_description'][$key];
                        $terms['items'][$i]['item_quantity'] = $value['items']['item_quantity'][$key];
                        $terms['items'][$i]['item_unit_price'] = $value['items']['item_unit_price'][$key];
                        #$terms['items'][$i]['item_sub_total'] = $value['items']['item_sub_total'][$key];
                        $terms['items'][$i]['item_sub_total'] = $value['items']['item_quantity'][$key] * $value['items']['item_unit_price'][$key];

                        $sub_total += $terms['items'][$i]['item_sub_total'];


                        $i += 1;
                  }
            }

            # Overall status
            $overall_status = true;
            if( ($items_status) && !(count($terms['items']) > 0) ){
                  $items_status &= false;
                  $overall_status &= false;
            }

            if( !($items_status) || !(isset($value['overall'])) || !(is_array($value['overall'])) || !(count($value['overall']) == 7) ){
                  $overall_status &= false;
            }
            # If Overall still valid
            if( !($items_status) || !($overall_status) || !(isset($value['overall']['sub_total'])) || !(isset($value['overall']['discount_type'])) || !(isset($value['overall']['discount_value'])) || !(isset($value['overall']['tax_type'])) || !(isset($value['overall']['tax_select'])) || !(isset($value['overall']['tax_value'])) || !(isset($value['overall']['total_value'])) ){
                  $overall_status &= false;
            }

            $terms['overall'] = array();
            if( ($items_status) && ($overall_status) ){
                  $terms['overall']['sub_total'] = $sub_total;

                  $terms['overall']['discount_type'] = (in_array($value['overall']['discount_type'], array( 'off', 'percent', 'flat'))) ? $value['overall']['discount_type'] : 'off';
                  $terms['overall']['discount_value'] = ($this->vfloat($value['overall']['discount_value'],',,,0')) ? $value['overall']['discount_value'] : 0;
                  $terms['overall']['tax_type'] = (in_array($value['overall']['tax_type'], array( 'off', 'percent', 'flat'))) ? $value['overall']['tax_type'] : 'off';
                  $terms['overall']['tax_select'] = filter_var($value['overall']['tax_select'],FILTER_SANITIZE_STRING);
                  $terms['overall']['tax_value'] = ($this->vfloat($value['overall']['tax_value'],',,,0')) ? $value['overall']['tax_value'] : 0;

                  if( 'percent' == $terms['overall']['discount_type'] ){
                        $sub_total -= ($sub_total * $terms['overall']['discount_value']) / 100;
                  }elseif( 'flat' == $terms['overall']['discount_type'] ){
                        $sub_total -= $terms['overall']['discount_value'];
                  }

                  if( 'percent' == $terms['overall']['tax_type'] ){
                        $sub_total += ($sub_total * $terms['overall']['tax_value']) / 100;
                  }elseif( 'flat' == $terms['overall']['tax_type'] ){
                        $sub_total += $terms['overall']['tax_value'];
                  }

                  $terms['overall']['total_value'] = $sub_total;

                  if( ($terms['overall']['total_value'] < 0) ){
                        $overall_status &= false;
                  }
            }

            return (boolean) ($notes_status && $items_status && $overall_status);
      }

      /**
       * Validate invoices
       *
       * Usage : add ..&vinvs to valid rules
       *
       * @since 1.0
       * @access private
       * @param array $value
       * @return boolean
       */
      private function vinvs($value)
      {
            $terms = array();

            # Notes status
            $notes_status = true;
            if( !isset($value['notes']) ){
                  # Notes optional
                  $terms['notes'] = '';
            }else{
                  $terms['notes'] = $value['notes'];
            }

            # Items status
            $items_status = true;
            if( !(isset($value['items'])) || !(is_array($value['items'])) || !(count($value['items']) == 6) ){
                  $items_status &= false;
            }
            if( !($items_status) || !(isset($value['items']['item_select'])) || !(is_array($value['items']['item_select'])) || !(count($value['items']['item_select']) > 0) || !(isset($value['items']['item_title'])) || !(is_array($value['items']['item_title'])) || !(count($value['items']['item_title']) > 0) || !(count($value['items']['item_title']) == count($value['items']['item_select'])) || !(isset($value['items']['item_description'])) || !(is_array($value['items']['item_description'])) || !(count($value['items']['item_description']) > 0) || !(count($value['items']['item_description']) == count($value['items']['item_select'])) || !(isset($value['items']['item_quantity'])) || !(is_array($value['items']['item_quantity'])) || !(count($value['items']['item_quantity']) > 0) || !(count($value['items']['item_quantity']) == count($value['items']['item_select'])) || !(isset($value['items']['item_unit_price'])) || !(is_array($value['items']['item_unit_price'])) || !(count($value['items']['item_unit_price']) > 0) || !(count($value['items']['item_unit_price']) == count($value['items']['item_select'])) || !(isset($value['items']['item_sub_total'])) || !(is_array($value['items']['item_sub_total'])) || !(count($value['items']['item_sub_total']) > 0) || !(count($value['items']['item_sub_total']) == count($value['items']['item_select'])) ){
                  $items_status &= false;
            }

            $terms['items'] = array();
            $sub_total = 0;
            if( ($items_status) ){
                  $i = 0;

                  $value['items']['item_select'] = array_values($value['items']['item_select']);
                  $value['items']['item_title'] = array_values($value['items']['item_title']);
                  $value['items']['item_description'] = array_values($value['items']['item_description']);
                  $value['items']['item_quantity'] = array_values($value['items']['item_quantity']);
                  $value['items']['item_unit_price'] = array_values($value['items']['item_unit_price']);
                  $value['items']['item_sub_total'] = array_values($value['items']['item_sub_total']);

                  foreach ($value['items']['item_select'] as $key => $item) {

                        if( (empty($value['items']['item_title'][$key])) || !($this->vfloat($value['items']['item_quantity'][$key],'0')) || !($this->vfloat($value['items']['item_unit_price'][$key],',,,0')) ){
                              continue;
                        }

                        $terms['items'][$i]['item_select'] = $value['items']['item_select'][$key];
                        $terms['items'][$i]['item_title'] = $value['items']['item_title'][$key];
                        $terms['items'][$i]['item_description'] = $value['items']['item_description'][$key];
                        $terms['items'][$i]['item_quantity'] = $value['items']['item_quantity'][$key];
                        $terms['items'][$i]['item_unit_price'] = $value['items']['item_unit_price'][$key];
                        #$terms['items'][$i]['item_sub_total'] = $value['items']['item_sub_total'][$key];
                        $terms['items'][$i]['item_sub_total'] = $value['items']['item_quantity'][$key] * $value['items']['item_unit_price'][$key];

                        $sub_total += $terms['items'][$i]['item_sub_total'];


                        $i += 1;
                  }
            }

            # Overall status
            $overall_status = true;
            if( ($items_status) && !(count($terms['items']) > 0) ){
                  $items_status &= false;
                  $overall_status &= false;
            }

            if( !($items_status) || !(isset($value['overall'])) || !(is_array($value['overall'])) || !(count($value['overall']) == 8) ){
                  $overall_status &= false;
            }
            # If Overall still valid
            if( !($items_status) || !($overall_status) || !(isset($value['overall']['sub_total'])) || !(isset($value['overall']['discount_type'])) || !(isset($value['overall']['discount_value'])) || !(isset($value['overall']['tax_type'])) || !(isset($value['overall']['tax_select'])) || !(isset($value['overall']['tax_value'])) || !(isset($value['overall']['total_value'])) || !(isset($value['overall']['paid_value'])) ){
                  $overall_status &= false;
            }

            $terms['overall'] = array();
            if( ($items_status) && ($overall_status) ){
                  $terms['overall']['sub_total'] = $sub_total;

                  $terms['overall']['discount_type'] = (in_array($value['overall']['discount_type'], array( 'off', 'percent', 'flat'))) ? $value['overall']['discount_type'] : 'off';
                  $terms['overall']['discount_value'] = ($this->vfloat($value['overall']['discount_value'],',,,0')) ? $value['overall']['discount_value'] : 0;
                  $terms['overall']['tax_type'] = (in_array($value['overall']['tax_type'], array( 'off', 'percent', 'flat'))) ? $value['overall']['tax_type'] : 'off';
                  $terms['overall']['tax_select'] = filter_var($value['overall']['tax_select'],FILTER_SANITIZE_STRING);
                  $terms['overall']['tax_value'] = ($this->vfloat($value['overall']['tax_value'],',,,0')) ? $value['overall']['tax_value'] : 0;

                  if( 'percent' == $terms['overall']['discount_type'] ){
                        $sub_total -= ($sub_total * $terms['overall']['discount_value']) / 100;
                  }elseif( 'flat' == $terms['overall']['discount_type'] ){
                        $sub_total -= $terms['overall']['discount_value'];
                  }

                  if( 'percent' == $terms['overall']['tax_type'] ){
                        $sub_total += ($sub_total * $terms['overall']['tax_value']) / 100;
                  }elseif( 'flat' == $terms['overall']['tax_type'] ){
                        $sub_total += $terms['overall']['tax_value'];
                  }

                  $terms['overall']['total_value'] = $sub_total;
                  $terms['overall']['paid_value'] = $value['overall']['paid_value'];

                  if( ($terms['overall']['total_value'] < 0) ){
                        $overall_status &= false;
                  }
            }

            return (boolean) ($notes_status && $items_status && $overall_status);
      }

      /**
       * Validate expenses
       *
       * Usage : add ..&vexpen to valid rules
       *
       * @since 1.0
       * @access private
       * @param array $value
       * @return boolean
       */
      private function vexpen($value)
      {
            $terms = array();

            # Notes status
            $desc_status = true;
            if( !isset($value['description']) ){
                  # Notes optional
                  $terms['description'] = '';
            }else{
                  $terms['description'] = filter_var($value['description'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
            }

            $title_status = true;
            $value['title'] = (isset($value['title'])) ? trim($value['title']) : '';
            $value['title'] = filter_var($value['title'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
            if( (empty($value['title'])) || (strlen($value['title']) <= 2) ){
                  # Notes optional
                  $terms['title'] = '';
                  $title_status &= false;
            }else{
                  $terms['title'] = $value['title'];
            }

            # Overall status
            $overall_status = true;
            # If Overall still valid
            if( !($desc_status) || !($title_status) || !(isset($value['sub_total'])) || !(isset($value['discount_type'])) || !(isset($value['discount_value'])) || !(isset($value['tax_type'])) || !(isset($value['tax_select'])) || !(isset($value['tax_value'])) || !(isset($value['total_value'])) ){
                  $overall_status &= false;
            }

            if( ($title_status) && ($desc_status) && ($overall_status) ){

                  $terms['sub_total'] = ($this->vfloat($value['sub_total'],'0')) ? $value['sub_total'] : 0;

                  $sub_total = $terms['sub_total'];

                  $terms['discount_type'] = (in_array($value['discount_type'], array( 'off', 'percent', 'flat'))) ? $value['discount_type'] : 'off';
                  $terms['discount_value'] = ($this->vfloat($value['discount_value'],',,,0')) ? $value['discount_value'] : 0;
                  $terms['tax_type'] = (in_array($value['tax_type'], array( 'off', 'percent', 'flat'))) ? $value['tax_type'] : 'off';
                  $terms['tax_select'] = filter_var($value['tax_select'],FILTER_SANITIZE_STRING);
                  $terms['tax_value'] = ($this->vfloat($value['tax_value'],',,,0')) ? $value['tax_value'] : 0;

                  if( 'percent' == $terms['discount_type'] ){
                        $sub_total -= ($sub_total * $terms['discount_value']) / 100;
                  }elseif( 'flat' == $terms['discount_type'] ){
                        $sub_total -= $terms['discount_value'];
                  }

                  if( 'percent' == $terms['tax_type'] ){
                        $sub_total += ($sub_total * $terms['tax_value']) / 100;
                  }elseif( 'flat' == $terms['tax_type'] ){
                        $sub_total += $terms['tax_value'];
                  }

                  $terms['total_value'] = $sub_total;

                  if( ($terms['total_value'] < 0) ){
                        $overall_status &= false;
                  }
            }

            return (boolean) ($desc_status && $title_status && $overall_status);

            /*array(
                  'title' => "",
                  'description' => "",

                  'sub_total' => '',
                  'tax_type' => '',
                  'tax_select' => '',
                  'tax_value' => '',
                  'discount_type' => '',
                  'discount_value' => '',
                  'total_value' => '',
            )*/

      }


      /**
       * Validate estimates
       *
       * Usage : add ..&vestim to valid rules
       *
       * @since 1.0
       * @access private
       * @param array $value
       * @return boolean
       */
      private function vestim($value)
      {
            $terms = array();

            # Notes status
            $notes_status = true;
            if( !isset($value['notes']) ){
                  # Notes optional
                  $terms['notes'] = '';
            }else{
                  $terms['notes'] = $value['notes'];
            }

            # Items status
            $items_status = true;
            if( !(isset($value['items'])) || !(is_array($value['items'])) || !(count($value['items']) == 6) ){
                  $items_status &= false;
            }
            if( !($items_status) || !(isset($value['items']['item_select'])) || !(is_array($value['items']['item_select'])) || !(count($value['items']['item_select']) > 0) || !(isset($value['items']['item_title'])) || !(is_array($value['items']['item_title'])) || !(count($value['items']['item_title']) > 0) || !(count($value['items']['item_title']) == count($value['items']['item_select'])) || !(isset($value['items']['item_description'])) || !(is_array($value['items']['item_description'])) || !(count($value['items']['item_description']) > 0) || !(count($value['items']['item_description']) == count($value['items']['item_select'])) || !(isset($value['items']['item_quantity'])) || !(is_array($value['items']['item_quantity'])) || !(count($value['items']['item_quantity']) > 0) || !(count($value['items']['item_quantity']) == count($value['items']['item_select'])) || !(isset($value['items']['item_unit_price'])) || !(is_array($value['items']['item_unit_price'])) || !(count($value['items']['item_unit_price']) > 0) || !(count($value['items']['item_unit_price']) == count($value['items']['item_select'])) || !(isset($value['items']['item_sub_total'])) || !(is_array($value['items']['item_sub_total'])) || !(count($value['items']['item_sub_total']) > 0) || !(count($value['items']['item_sub_total']) == count($value['items']['item_select'])) ){
                  $items_status &= false;
            }

            $terms['items'] = array();
            $sub_total = 0;
            if( ($items_status) ){
                  $i = 0;

                  $value['items']['item_select'] = array_values($value['items']['item_select']);
                  $value['items']['item_title'] = array_values($value['items']['item_title']);
                  $value['items']['item_description'] = array_values($value['items']['item_description']);
                  $value['items']['item_quantity'] = array_values($value['items']['item_quantity']);
                  $value['items']['item_unit_price'] = array_values($value['items']['item_unit_price']);
                  $value['items']['item_sub_total'] = array_values($value['items']['item_sub_total']);

                  foreach ($value['items']['item_select'] as $key => $item) {

                        if( (empty($value['items']['item_title'][$key])) || !($this->vfloat($value['items']['item_quantity'][$key],'0')) || !($this->vfloat($value['items']['item_unit_price'][$key],',,,0')) ){
                              continue;
                        }

                        $terms['items'][$i]['item_select'] = $value['items']['item_select'][$key];
                        $terms['items'][$i]['item_title'] = $value['items']['item_title'][$key];
                        $terms['items'][$i]['item_description'] = $value['items']['item_description'][$key];
                        $terms['items'][$i]['item_quantity'] = $value['items']['item_quantity'][$key];
                        $terms['items'][$i]['item_unit_price'] = $value['items']['item_unit_price'][$key];
                        #$terms['items'][$i]['item_sub_total'] = $value['items']['item_sub_total'][$key];
                        $terms['items'][$i]['item_sub_total'] = $value['items']['item_quantity'][$key] * $value['items']['item_unit_price'][$key];

                        $sub_total += $terms['items'][$i]['item_sub_total'];


                        $i += 1;
                  }
            }

            # Overall status
            $overall_status = true;
            if( ($items_status) && !(count($terms['items']) > 0) ){
                  $items_status &= false;
                  $overall_status &= false;
            }

            if( !($items_status) || !(isset($value['overall'])) || !(is_array($value['overall'])) || !(count($value['overall']) == 8) ){
                  $overall_status &= false;
            }
            # If Overall still valid
            if( !($items_status) || !($overall_status) || !(isset($value['overall']['sub_total'])) || !(isset($value['overall']['discount_type'])) || !(isset($value['overall']['discount_value'])) || !(isset($value['overall']['tax_type'])) || !(isset($value['overall']['tax_select'])) || !(isset($value['overall']['tax_value'])) || !(isset($value['overall']['total_value'])) || !(isset($value['overall']['paid_value'])) ){
                  $overall_status &= false;
            }

            $terms['overall'] = array();
            if( ($items_status) && ($overall_status) ){
                  $terms['overall']['sub_total'] = $sub_total;

                  $terms['overall']['discount_type'] = (in_array($value['overall']['discount_type'], array( 'off', 'percent', 'flat'))) ? $value['overall']['discount_type'] : 'off';
                  $terms['overall']['discount_value'] = ($this->vfloat($value['overall']['discount_value'],',,,0')) ? $value['overall']['discount_value'] : 0;
                  $terms['overall']['tax_type'] = (in_array($value['overall']['tax_type'], array( 'off', 'percent', 'flat'))) ? $value['overall']['tax_type'] : 'off';
                  $terms['overall']['tax_select'] = filter_var($value['overall']['tax_select'],FILTER_SANITIZE_STRING);
                  $terms['overall']['tax_value'] = ($this->vfloat($value['overall']['tax_value'],',,,0')) ? $value['overall']['tax_value'] : 0;

                  if( 'percent' == $terms['overall']['discount_type'] ){
                        $sub_total -= ($sub_total * $terms['overall']['discount_value']) / 100;
                  }elseif( 'flat' == $terms['overall']['discount_type'] ){
                        $sub_total -= $terms['overall']['discount_value'];
                  }

                  if( 'percent' == $terms['overall']['tax_type'] ){
                        $sub_total += ($sub_total * $terms['overall']['tax_value']) / 100;
                  }elseif( 'flat' == $terms['overall']['tax_type'] ){
                        $sub_total += $terms['overall']['tax_value'];
                  }

                  $terms['overall']['total_value'] = $sub_total;
                  $terms['overall']['paid_value'] = $value['overall']['paid_value'];

                  if( ($terms['overall']['total_value'] < 0) ){
                        $overall_status &= false;
                  }
            }

            return (boolean) ($notes_status && $items_status && $overall_status);
      }

     /**
       * Validate Files IDs
       *
       * Usage : add ..&vfilesids to validate rules
       *
       * @since 1.0
       * @access private
       * @param array $value
       * @return boolean
       */
      private function vfilesids($value)
      {
            $value = trim($value);
            if( empty($value) ){
                  return false;
            }
            $values = explode(',', $value);
            $status = true;

            foreach ($values as $key => $value) {
                  $status &= (boolean) filter_var($value, FILTER_VALIDATE_INT);
            }

            return (boolean) $status;
      }

     /**
       * Validate Users IDs
       *
       * Usage : add ..&vfilesids to validate rules
       *
       * @since 1.0
       * @access private
       * @param array $value
       * @return boolean
       */
      private function vusersids($value)
      {
            $value = trim($value);
            if( empty($value) ){
                  return false;
            }
            $values = explode(',', $value);
            $svalues = array();

            $status = true;

            foreach ($values as $key => $value) {
                  $status &= (boolean) filter_var($value, FILTER_VALIDATE_INT);
                  if( (boolean) filter_var($value, FILTER_VALIDATE_INT) ){
                        $svalues[] = $value;
                  }
            }

            return (count($svalues) > 0) ? $status : false;
      }

      /**
       * Validate taxes
       *
       * Usage : add ..&vtax to validate rules
       *
       * @since 1.0
       * @access private
       * @param array $value
       * @return boolean
       */
      private function vtax($value)
      {
            return (boolean)( (is_array($value)) && (count($value) >= 1) );
      }

      /**
       * Validate boolean value
       *
       * Usage : add ..&vboolean to validate rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return boolean
       */
      private function vboolean($value)
      {
            return ( boolean ) (filter_var($value, FILTER_VALIDATE_BOOLEAN));
      }

      /**
       * Validate email value
       *
       * Usage : add ..&vemail to validate rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return boolean
       */
      private function vemail($value)
      {
            return ( boolean ) (filter_var($value, FILTER_VALIDATE_EMAIL));
      }

      /**
       * Validate float value
       *
       * Usage : add ..&vfloat to validate rules
       *
       * more_than,equal,less_than,more_than_eq,less_than_eq
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @param string $args
       * @return boolean
       */
      private function vfloat($value, $args)
      {
            $args = explode(',', $args);
            $value = trim($value);

            if( $value === '' ){
                  return false;
            }

            if( ($value == '0') && ( (isset($args[1])) || (isset($args[3])) || (isset($args[4])) ) ){
                  return true;
            }

            $status = true;
            $status &= ((boolean) filter_var($value, FILTER_VALIDATE_FLOAT) || (boolean) filter_var($value, FILTER_VALIDATE_INT));

            $status &= (isset($args[0]) && ($args[0] !== '') && ($args[0] !== 0)) ? ( (boolean) $value > $args[0]) : true;
            $status &= (isset($args[1]) && ($args[1] !== '') && ($args[1] !== 0)) ? ( (boolean) $value == $args[1]) : true;
            $status &= (isset($args[2]) && ($args[2] !== '') && ($args[2] !== 0)) ? ( (boolean) $value < $args[2]) : true;

            $status &= (isset($args[3]) && ($args[3] !== '') && ($args[3] !== 0)) ? ( (boolean) $value >= $args[3]) : true;
            $status &= (isset($args[4]) && ($args[4] !== '') && ($args[4] !== 0)) ? ( (boolean) $value <= $args[4]) : true;

            return (boolean) $status;
      }

      /**
       * Validate int value
       *
       * Usage : add ..&vint to validate rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return boolean
       */
      private function vint($value)
      {
            return ( boolean ) (filter_var($value, FILTER_VALIDATE_INT));
      }

      /**
       * Validate ip value
       *
       * Usage : add ..&vip to validate rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return boolean
       */
      private function vip($value)
      {
            return ( boolean ) (filter_var($value, FILTER_VALIDATE_IP));
      }

      /**
       * Validate regexp value
       *
       * Usage : add ..&vregexp to validate rules
       *
       * THIS METHOD TRY TO VALIDATE REGEXPs SO USING OF @ TO PREVENT NOTICES
       * THAT COULD RISE IF CLIENT SUBMIT INVALID REGEXPs
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return boolean
       */
      private function vregexp($value)
      {
            $value = trim($value);
            return ( (boolean) (empty($value)) ) ? true : (@preg_match($value, null) !== false);
            return true;
      }

      /**
       * Validate url value
       *
       * Usage : add ..&vurl to validate rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return boolean
       */
      private function vurl($value)
      {
            return ( boolean ) (filter_var($value, FILTER_VALIDATE_URL));
      }

      /**
       * Validate color value
       *
       * Usage : add ..&vcolor to validate rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return boolean
       */
      private function vcolor($value)
      {
            return ( boolean ) (preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $value));
      }

      /**
       * Validate future date value
       *
       * Usage : add ..&vfdate to validate rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return boolean
       */
      private function vfdate($value)
      {
            return ( boolean ) ((preg_match("/^(([1-2]?\\d{3})-((?:0\\d|1[0-2]))-(?:[0-2]\\d|3[0-1]))$/", $value)) && ($value > current_time('Y-m-d')));
      }

      /**
       * Validate date value
       *
       * Usage : add ..&vdate to validate rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return boolean
       */
      private function vdate($value)
      {
            return ( boolean ) ( preg_match("/^(([1-2]?\\d{3})-((?:0\\d|1[0-2]))-(?:[0-2]\\d|3[0-1]))$/", $value) );
      }

      /**
       * Validate datetime value
       *
       * Usage : add ..&vdatetime to validate rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return boolean
       */
      private function vdatetime($value)
      {
            return ( boolean ) ((preg_match("/^(([1-2]?\\d{3})-((?:0\\d|1[0-2]))-(?:[0-2]\\d|3[0-1]))\\s*(?:((?:[0-1]\\d|2[0-3])):([0-5]\\d):([0-5]\\d))?$/", $value)) && ($value > current_time('Y-m-d H:i:s')));
      }

      /**
       * Validate notempty value
       *
       * Usage : add ..&vnotempty to validate rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return boolean
       */
      private function vnotempty($value)
      {
            return ( boolean ) (($value != '') && (!empty($value)) && ($value != null));
      }

      /**
       * Validate inarray value
       *
       * Usage : add ..&vinarray:left,right,center to validate rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return boolean
       */
      private function vinarray($value, $args)
      {
            $args = explode(',', $args);
            return ( boolean ) (in_array($value, $args));
      }

      /**
       * Validate inarray value
       *
       * Usage : add ..&vallinarray:left,right,center to validate rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return boolean
       */
      private function vallinarray($value, $args)
      {
            $args = explode(',', $args);
            $value = explode(',', $value);
            $status = true;
            foreach ($value as $item) {
                  $status &= ( (empty($item)) || (in_array($item, $args)) );
            }
            return ( boolean ) $status;
      }

      /**
       * Validate intbetween value
       *
       * Usage : add ..&vintbetween:1,1000 to validate rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return boolean
       */
      private function vintbetween($value, $args)
      {
            $args = explode(',', $args);
            $min = ( int ) $args[ 0 ];
            $max = ( int ) $args[ 1 ];
            return ( boolean ) (($value >= $min) && ($value <= $max));
      }

      /**
       * Validate equals value
       *
       * Usage : add ..&vequals:helo to validate rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return boolean
       */
      private function vequals($value, $args)
      {
            return ( boolean ) ($value == $args);
      }

      /**
       * Validate checkbox value
       *
       * Usage : add ..&vcheckbox to validate rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return boolean
       */
      private function vcheckbox($value)
      {
            return ( boolean ) ($value == '1');
      }

      /**
       * Validate if str lenght between two values
       *
       * Usage : add ..&vstrlenbetween:1,250 to validate rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @param string $args
       * @return boolean
       */
      private function vstrlenbetween($value, $args)
      {
            $args = explode(',', $args);
            $value_lenght = strlen($value);
            $min = ( int ) $args[ 0 ];
            $max = ( int ) $args[ 1 ];
            return ( boolean ) (($value_lenght >= $min) && ($value_lenght <= $max));
      }

      /**
       * Validate if username is valid and meet minimum requirements
       *
       * For username to pass it must have lenght of 5 or more
       * and contain words and numbers are optional
       *
       * Username Must be alphanumeric containing at least 3 letters
       *
       * @since 1.0
       * @access private
       * @param string $username
       * @return boolean
       */
      private function vusername($username)
      {
            return (boolean)(preg_match('/^(?=\w*[a-z]{3})(\w+)$/i', $username));
      }

      /**
       * Validate if password is valid and meet minimum requirements To be strong
       *
       * For password to pass it must have lenght of 8 or more
       * and contain at least two numbers and the rest may be numbers, letters or special char (!@#$%^&*(){}[]|?:;,.+-_)
       *
       * @since 1.0
       * @access private
       * @param string $password
       * @return boolean
       */
      private function vpassword($password){
            $pwd_length = strlen($password);
            preg_match_all('/[0-9]/', $password, $match);
            $digits_lenght = count($match[0]);
            preg_match_all('/[a-z]/i', $password, $match);
            $letters_lenght = count($match[0]);
            preg_match_all('/[!@#$%^&*(){}\[\]|?:;,.+\-_]/', $password, $match);
            $special_chars_lenght = count($match[0]);
            return (boolean) ( ($digits_lenght >= 2) && ($pwd_length == ($digits_lenght + $letters_lenght + $special_chars_lenght)) );
      }

      /**
       * Validate timezone
       *
       * Usage : add ..&vtimezone to validate rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return boolean
       */
      private function vtimezone($value)
      {
            $time_zones = $this->timber->time->listIdentifiers();
            return (boolean)(in_array($value, $time_zones));
      }

      /**
       * Validate gravatar
       *
       * Usage : add ..&vgrav to validate rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return boolean
       */
      private function vgrav($value)
      {
            $langs = array('grav1','grav2','grav3','grav4','grav5','grav6','grav7');
            return (boolean) (in_array($value, $langs));
      }

      /**
       * Validate font
       *
       * Usage : add ..&vfont to validate rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return boolean
       */
      private function vfont($value)
      {
            $fonts = '[{"family":"ABeeZee"},{"family":"Abel"},{"family":"Abril Fatface"},{"family":"Aclonica"},{"family":"Acme"},{"family":"Actor"},{"family":"Adamina"},{"family":"Advent Pro"},{"family":"Aguafina Script"},{"family":"Akronim"},{"family":"Aladin"},{"family":"Aldrich"},{"family":"Alef"},{"family":"Alegreya"},{"family":"Alegreya SC"},{"family":"Alex Brush"},{"family":"Alfa Slab One"},{"family":"Alice"},{"family":"Alike"},{"family":"Alike Angular"},{"family":"Allan"},{"family":"Allerta"},{"family":"Allerta Stencil"},{"family":"Allura"},{"family":"Almendra"},{"family":"Almendra Display"},{"family":"Almendra SC"},{"family":"Amarante"},{"family":"Amaranth"},{"family":"Amatic SC"},{"family":"Amethysta"},{"family":"Anaheim"},{"family":"Andada"},{"family":"Andika"},{"family":"Angkor"},{"family":"Annie Use Your Telescope"},{"family":"Anonymous Pro"},{"family":"Antic"},{"family":"Antic Didone"},{"family":"Antic Slab"},{"family":"Anton"},{"family":"Arapey"},{"family":"Arbutus"},{"family":"Arbutus Slab"},{"family":"Architects Daughter"},{"family":"Archivo Black"},{"family":"Archivo Narrow"},{"family":"Arimo"},{"family":"Arizonia"},{"family":"Armata"},{"family":"Artifika"},{"family":"Arvo"},{"family":"Asap"},{"family":"Asset"},{"family":"Astloch"},{"family":"Asul"},{"family":"Atomic Age"},{"family":"Aubrey"},{"family":"Audiowide"},{"family":"Autour One"},{"family":"Average"},{"family":"Average Sans"},{"family":"Averia Gruesa Libre"},{"family":"Averia Libre"},{"family":"Averia Sans Libre"},{"family":"Averia Serif Libre"},{"family":"Bad Script"},{"family":"Balthazar"},{"family":"Bangers"},{"family":"Basic"},{"family":"Battambang"},{"family":"Baumans"},{"family":"Bayon"},{"family":"Belgrano"},{"family":"Belleza"},{"family":"BenchNine"},{"family":"Bentham"},{"family":"Berkshire Swash"},{"family":"Bevan"},{"family":"Bigelow Rules"},{"family":"Bigshot One"},{"family":"Bilbo"},{"family":"Bilbo Swash Caps"},{"family":"Bitter"},{"family":"Black Ops One"},{"family":"Bokor"},{"family":"Bonbon"},{"family":"Boogaloo"},{"family":"Timberlby One"},{"family":"Timberlby One SC"},{"family":"Brawler"},{"family":"Bree Serif"},{"family":"Bubblegum Sans"},{"family":"Bubbler One"},{"family":"Buda"},{"family":"Buenard"},{"family":"Butcherman"},{"family":"Butterfly Kids"},{"family":"Cabin"},{"family":"Cabin Condensed"},{"family":"Cabin Sketch"},{"family":"Caesar Dressing"},{"family":"Cagliostro"},{"family":"Calligraffitti"},{"family":"Cambo"},{"family":"Candal"},{"family":"Cantarell"},{"family":"Cantata One"},{"family":"Cantora One"},{"family":"Capriola"},{"family":"Cardo"},{"family":"Carme"},{"family":"Carrois Gothic"},{"family":"Carrois Gothic SC"},{"family":"Carter One"},{"family":"Caudex"},{"family":"Cedarville Cursive"},{"family":"Ceviche One"},{"family":"Changa One"},{"family":"Chango"},{"family":"Chau Philomene One"},{"family":"Chela One"},{"family":"Chelsea Market"},{"family":"Chenla"},{"family":"Cherry Cream Soda"},{"family":"Cherry Swash"},{"family":"Chewy"},{"family":"Chicle"},{"family":"Chivo"},{"family":"Cinzel"},{"family":"Cinzel Decorative"},{"family":"Clicker Script"},{"family":"Coda"},{"family":"Coda Caption"},{"family":"Codystar"},{"family":"Combo"},{"family":"Comfortaa"},{"family":"Coming Soon"},{"family":"Concert One"},{"family":"Condiment"},{"family":"Content"},{"family":"Contrail One"},{"family":"Convergence"},{"family":"Cookie"},{"family":"Copse"},{"family":"Corben"},{"family":"Courgette"},{"family":"Cousine"},{"family":"Coustard"},{"family":"Covered By Your Grace"},{"family":"Crafty Girls"},{"family":"Creepster"},{"family":"Crete Round"},{"family":"Crimson Text"},{"family":"Croissant One"},{"family":"Crushed"},{"family":"Cuprum"},{"family":"Cutive"},{"family":"Cutive Mono"},{"family":"Damion"},{"family":"Timbercing Script"},{"family":"Timbergrek"},{"family":"Dawning of a New Day"},{"family":"Days One"},{"family":"Delius"},{"family":"Delius Swash Caps"},{"family":"Delius Unicase"},{"family":"Della Respira"},{"family":"Denk One"},{"family":"Devonshire"},{"family":"Didact Gothic"},{"family":"Diplomata"},{"family":"Diplomata SC"},{"family":"Domine"},{"family":"Donegal One"},{"family":"Doppio One"},{"family":"Dorsa"},{"family":"Dosis"},{"family":"Dr Sugiyama"},{"family":"Droid Sans"},{"family":"Droid Sans Mono"},{"family":"Droid Serif"},{"family":"Duru Sans"},{"family":"Dynalight"},{"family":"EB Garamond"},{"family":"Eagle Lake"},{"family":"Eater"},{"family":"Economica"},{"family":"Electrolize"},{"family":"Elsie"},{"family":"Elsie Swash Caps"},{"family":"Emblema One"},{"family":"Emilys Candy"},{"family":"Engagement"},{"family":"Englebert"},{"family":"Enriqueta"},{"family":"Erica One"},{"family":"Esteban"},{"family":"Euphoria Script"},{"family":"Ewert"},{"family":"Exo"},{"family":"Expletus Sans"},{"family":"Fanwood Text"},{"family":"Fascinate"},{"family":"Fascinate Inline"},{"family":"Faster One"},{"family":"Fasthand"},{"family":"Fauna One"},{"family":"Federant"},{"family":"Federo"},{"family":"Felipa"},{"family":"Fenix"},{"family":"Finger Paint"},{"family":"Fjalla One"},{"family":"Fjord One"},{"family":"Flamenco"},{"family":"Flavors"},{"family":"Fondamento"},{"family":"Fontdiner Swanky"},{"family":"Forum"},{"family":"Francois One"},{"family":"Freckle Face"},{"family":"Fredericka the Great"},{"family":"Fredoka One"},{"family":"Freehand"},{"family":"Fresca"},{"family":"Frijole"},{"family":"Fruktur"},{"family":"Fugaz One"},{"family":"GFS Didot"},{"family":"GFS Neohellenic"},{"family":"Gabriela"},{"family":"Gafata"},{"family":"Galdeano"},{"family":"Galindo"},{"family":"Gentium Basic"},{"family":"Gentium Book Basic"},{"family":"Geo"},{"family":"Geostar"},{"family":"Geostar Fill"},{"family":"Germania One"},{"family":"Gilda Display"},{"family":"Give You Glory"},{"family":"Glass Antiqua"},{"family":"Glegoo"},{"family":"Gloria Hallelujah"},{"family":"Goblin One"},{"family":"Gochi Hand"},{"family":"Gorditas"},{"family":"Goudy Bookletter 1911"},{"family":"Graduate"},{"family":"Grand Hotel"},{"family":"Gravitas One"},{"family":"Great Vibes"},{"family":"Griffy"},{"family":"Gruppo"},{"family":"Gudea"},{"family":"Habibi"},{"family":"Hammersmith One"},{"family":"Hanalei"},{"family":"Hanalei Fill"},{"family":"Handlee"},{"family":"Hanuman"},{"family":"Happy Monkey"},{"family":"Headland One"},{"family":"Henny Penny"},{"family":"Herr Von Muellerhoff"},{"family":"Holtwood One SC"},{"family":"Homemade Apple"},{"family":"Homenaje"},{"family":"IM Fell DW Pica"},{"family":"IM Fell DW Pica SC"},{"family":"IM Fell Double Pica"},{"family":"IM Fell Double Pica SC"},{"family":"IM Fell English"},{"family":"IM Fell English SC"},{"family":"IM Fell French Canon"},{"family":"IM Fell French Canon SC"},{"family":"IM Fell Great Primer"},{"family":"IM Fell Great Primer SC"},{"family":"Iceberg"},{"family":"Iceland"},{"family":"Imprima"},{"family":"Inconsolata"},{"family":"Inder"},{"family":"Indie Flower"},{"family":"Inika"},{"family":"Irish Grover"},{"family":"Istok Web"},{"family":"Italiana"},{"family":"Italianno"},{"family":"Jacques Francois"},{"family":"Jacques Francois Shadow"},{"family":"Jim Nightshade"},{"family":"Jockey One"},{"family":"Jolly Lodger"},{"family":"Josefin Sans"},{"family":"Josefin Slab"},{"family":"Joti One"},{"family":"Judson"},{"family":"Julee"},{"family":"Julius Sans One"},{"family":"Junge"},{"family":"Jura"},{"family":"Just Another Hand"},{"family":"Just Me Again Down Here"},{"family":"Kameron"},{"family":"Karla"},{"family":"Kaushan Script"},{"family":"Kavoon"},{"family":"Keania One"},{"family":"Kelly Slab"},{"family":"Kenia"},{"family":"Khmer"},{"family":"Kite One"},{"family":"Knewave"},{"family":"Kotta One"},{"family":"Koulen"},{"family":"Kranky"},{"family":"Kreon"},{"family":"Kristi"},{"family":"Krona One"},{"family":"La Belle Aurore"},{"family":"Lancelot"},{"family":"Lato"},{"family":"League Script"},{"family":"Leckerli One"},{"family":"Ledger"},{"family":"Lekton"},{"family":"Lemon"},{"family":"Libre Baskerville"},{"family":"Life Savers"},{"family":"Lilita One"},{"family":"Lily Script One"},{"family":"Limelight"},{"family":"Linden Hill"},{"family":"Lobster"},{"family":"Lobster Two"},{"family":"Londrina Outline"},{"family":"Londrina Shadow"},{"family":"Londrina Sketch"},{"family":"Londrina Solid"},{"family":"Lora"},{"family":"Love Ya Like A Sister"},{"family":"Loved by the King"},{"family":"Lovers Quarrel"},{"family":"Luckiest Guy"},{"family":"Lusitana"},{"family":"Lustria"},{"family":"Macondo"},{"family":"Macondo Swash Caps"},{"family":"Magra"},{"family":"Maiden Orange"},{"family":"Mako"},{"family":"Marcellus"},{"family":"Marcellus SC"},{"family":"Marck Script"},{"family":"Margarine"},{"family":"Marko One"},{"family":"Marmelad"},{"family":"Marvel"},{"family":"Mate"},{"family":"Mate SC"},{"family":"Maven Pro"},{"family":"McLaren"},{"family":"Meddon"},{"family":"MedievalSharp"},{"family":"Medula One"},{"family":"Megrim"},{"family":"Meie Script"},{"family":"Merienda"},{"family":"Merienda One"},{"family":"Merriweather"},{"family":"Merriweather Sans"},{"family":"Metal"},{"family":"Metal Mania"},{"family":"Metamorphous"},{"family":"Metrophobic"},{"family":"Michroma"},{"family":"Milonga"},{"family":"Miltonian"},{"family":"Miltonian Tattoo"},{"family":"Miniver"},{"family":"Miss Fajardose"},{"family":"Modern Antiqua"},{"family":"Molengo"},{"family":"Molle"},{"family":"Monda"},{"family":"Monofett"},{"family":"Monoton"},{"family":"Monsieur La Doulaise"},{"family":"Montaga"},{"family":"Montez"},{"family":"Montserrat"},{"family":"Montserrat Alternates"},{"family":"Montserrat Subrayada"},{"family":"Moul"},{"family":"Moulpali"},{"family":"Mountains of Christmas"},{"family":"Mouse Memoirs"},{"family":"Mr Bedfort"},{"family":"Mr Dafoe"},{"family":"Mr De Haviland"},{"family":"Mrs Saint Delafield"},{"family":"Mrs Sheppards"},{"family":"Muli"},{"family":"Mystery Quest"},{"family":"Neucha"},{"family":"Neuton"},{"family":"New Rocker"},{"family":"News Cycle"},{"family":"Niconne"},{"family":"Nixie One"},{"family":"Nobile"},{"family":"Nokora"},{"family":"Norican"},{"family":"Nosifer"},{"family":"Nothing You Could Do"},{"family":"Noticia Text"},{"family":"Noto Sans"},{"family":"Noto Serif"},{"family":"Nova Cut"},{"family":"Nova Flat"},{"family":"Nova Mono"},{"family":"Nova Oval"},{"family":"Nova Round"},{"family":"Nova Script"},{"family":"Nova Slim"},{"family":"Nova Square"},{"family":"Numans"},{"family":"Nunito"},{"family":"Odor Mean Chey"},{"family":"Offside"},{"family":"Old Standard TT"},{"family":"Oldenburg"},{"family":"Oleo Script"},{"family":"Oleo Script Swash Caps"},{"family":"Open Sans"},{"family":"Open Sans Condensed"},{"family":"Oranienbaum"},{"family":"Orbitron"},{"family":"Oregano"},{"family":"Orienta"},{"family":"Original Surfer"},{"family":"Oswald"},{"family":"Over the Raintimber"},{"family":"Overlock"},{"family":"Overlock SC"},{"family":"Ovo"},{"family":"Oxygen"},{"family":"Oxygen Mono"},{"family":"PT Mono"},{"family":"PT Sans"},{"family":"PT Sans Caption"},{"family":"PT Sans Narrow"},{"family":"PT Serif"},{"family":"PT Serif Caption"},{"family":"Pacifico"},{"family":"Paprika"},{"family":"Parisienne"},{"family":"Passero One"},{"family":"Passion One"},{"family":"Pathway Gothic One"},{"family":"Patrick Hand"},{"family":"Patrick Hand SC"},{"family":"Patua One"},{"family":"Paytone One"},{"family":"Peralta"},{"family":"Permanent Marker"},{"family":"Petit Formal Script"},{"family":"Petrona"},{"family":"Philosopher"},{"family":"Piedra"},{"family":"Pinyon Script"},{"family":"Pirata One"},{"family":"Plaster"},{"family":"Play"},{"family":"Playball"},{"family":"Playfair Display"},{"family":"Playfair Display SC"},{"family":"Podkova"},{"family":"Poiret One"},{"family":"Poller One"},{"family":"Poly"},{"family":"Pompiere"},{"family":"Pontano Sans"},{"family":"Port Lligat Sans"},{"family":"Port Lligat Slab"},{"family":"Prata"},{"family":"Preahvihear"},{"family":"Press Start 2P"},{"family":"Princess Sofia"},{"family":"Prociono"},{"family":"Prosto One"},{"family":"Puritan"},{"family":"Purple Purse"},{"family":"Quando"},{"family":"Quantico"},{"family":"Quattrocento"},{"family":"Quattrocento Sans"},{"family":"Questrial"},{"family":"Quicksand"},{"family":"Quintessential"},{"family":"Qwigley"},{"family":"Racing Sans One"},{"family":"Radley"},{"family":"Raleway"},{"family":"Raleway Dots"},{"family":"Rambla"},{"family":"Rammetto One"},{"family":"Ranchers"},{"family":"Rancho"},{"family":"Rationale"},{"family":"Redressed"},{"family":"Reenie Beanie"},{"family":"Revalia"},{"family":"Ribeye"},{"family":"Ribeye Marrow"},{"family":"Righteous"},{"family":"Risque"},{"family":"Roboto"},{"family":"Roboto Condensed"},{"family":"Roboto Slab"},{"family":"Rochester"},{"family":"Rock Salt"},{"family":"Rokkitt"},{"family":"Romanesco"},{"family":"Ropa Sans"},{"family":"Rosario"},{"family":"Rosarivo"},{"family":"Rouge Script"},{"family":"Ruda"},{"family":"Rufina"},{"family":"Ruge Boogie"},{"family":"Ruluko"},{"family":"Rum Raisin"},{"family":"Ruslan Display"},{"family":"Russo One"},{"family":"Ruthie"},{"family":"Rye"},{"family":"Sacramento"},{"family":"Sail"},{"family":"Salsa"},{"family":"Sanchez"},{"family":"Sancreek"},{"family":"Sansita One"},{"family":"Sarina"},{"family":"Satisfy"},{"family":"Scada"},{"family":"Schoolbell"},{"family":"Seaweed Script"},{"family":"Sevillana"},{"family":"Seymour One"},{"family":"Shadows Into Light"},{"family":"Shadows Into Light Two"},{"family":"Shanti"},{"family":"Share"},{"family":"Share Tech"},{"family":"Share Tech Mono"},{"family":"Shojumaru"},{"family":"Short Stack"},{"family":"Siemreap"},{"family":"Sigmar One"},{"family":"Signika"},{"family":"Signika Negative"},{"family":"Simonetta"},{"family":"Sintony"},{"family":"Sirin Stencil"},{"family":"Six Caps"},{"family":"Skranji"},{"family":"Slackey"},{"family":"Smokum"},{"family":"Smythe"},{"family":"Sniglet"},{"family":"Snippet"},{"family":"Snowburst One"},{"family":"Sofadi One"},{"family":"Sofia"},{"family":"Sonsie One"},{"family":"Sorts Mill Goudy"},{"family":"Source Code Pro"},{"family":"Source Sans Pro"},{"family":"Special Elite"},{"family":"Spicy Rice"},{"family":"Spinnaker"},{"family":"Spirax"},{"family":"Squada One"},{"family":"Stalemate"},{"family":"Stalinist One"},{"family":"Stardos Stencil"},{"family":"Stint Ultra Condensed"},{"family":"Stint Ultra Expanded"},{"family":"Stoke"},{"family":"Strait"},{"family":"Sue Ellen Francisco"},{"family":"Sunshiney"},{"family":"Supermercado One"},{"family":"Suwannaphum"},{"family":"Swanky and Moo Moo"},{"family":"Syncopate"},{"family":"Tangerine"},{"family":"Taprom"},{"family":"Tauri"},{"family":"Telex"},{"family":"Tenor Sans"},{"family":"Text Me One"},{"family":"The Girl Next Door"},{"family":"Tienne"},{"family":"Tinos"},{"family":"Titan One"},{"family":"Titillium Web"},{"family":"Trade Winds"},{"family":"Trocchi"},{"family":"Trochut"},{"family":"Trykker"},{"family":"Tulpen One"},{"family":"Ubuntu"},{"family":"Ubuntu Condensed"},{"family":"Ubuntu Mono"},{"family":"Ultra"},{"family":"Uncial Antiqua"},{"family":"Underdog"},{"family":"Unica One"},{"family":"UnifrakturCook"},{"family":"UnifrakturMaguntia"},{"family":"Unkempt"},{"family":"Unlock"},{"family":"Unna"},{"family":"VT323"},{"family":"Vampiro One"},{"family":"Varela"},{"family":"Varela Round"},{"family":"Vast Shadow"},{"family":"Vibur"},{"family":"Vidaloka"},{"family":"Viga"},{"family":"Voces"},{"family":"Volkhov"},{"family":"Vollkorn"},{"family":"Voltaire"},{"family":"Waiting for the Sunrise"},{"family":"Wallpoet"},{"family":"Walter Turncoat"},{"family":"Warnes"},{"family":"Wellfleet"},{"family":"Wendy One"},{"family":"Wire One"},{"family":"Yanone Kaffeesatz"},{"family":"Yellowtail"},{"family":"Yeseva One"},{"family":"Yesteryear"},{"family":"Zeyada"}]';
            $fonts = json_decode($fonts, true);
            $fonts_list = array();
            foreach ($fonts as $key => $font) {
                  $fonts_list[] = $font['family'];
            }
            return (boolean) (in_array($value, $fonts_list));
      }

      /**
       * Validate Locales
       *
       * Usage : add ..&vlocale to validate rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return boolean
       */
      private function vlocale($value)
      {
            $locales = $this->timber->translator->getLocales();
            return (boolean)( in_array($value, $locales) );
      }

      /**
       * Validate Country
       *
       * Usage : add ..&vcountry to validate rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return boolean
       */
      private function vcountry($value)
      {
            return (boolean) (preg_match('/^[A-Z]{2}$/', $value));
      }

      /**
       * Validate Currency
       *
       * Usage : add ..&vcurrency to validate rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @return boolean
       */
      private function vcurrency($value)
      {
            return (boolean) (preg_match('/^[A-Z]{3}$/', $value));
      }

      /**
       * Validate File
       *
       * Usage : add ..&vfile:ext1,ext2... to validate rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @param string $args
       * @return boolean
       */
      private function vfile($value, $args)
      {
            if( (empty($value)) || !(strpos($value, '--||--') > 0) ){
                  return false;
            }
            $args = strtolower($args);
            $args = explode(',', $args);
            $value = explode('--||--', $value);
            $status = true;
            foreach ($value as $item) {
                  $status &= in_array(pathinfo($item, PATHINFO_EXTENSION), $args);
            }

            return (boolean) $status;
      }

      /**
       * Validate Files
       *
       * Usage : add ..&vfiles:ext1,ext2... to validate rules
       *
       * @since 1.0
       * @access private
       * @param string $value
       * @param string $args
       * @return boolean
       */
      private function vfiles($value, $args)
      {
            $files = explode('----||||----', $value);
            $args = strtolower($args);
            $args = explode(',', $args);
            $status = true;

            foreach ($files as $file) {
                  if( (empty($file)) || !(strpos($file, '--||--') > 0) ){ continue; }
                  $file = explode('--||--', $file);
                  foreach ($file as $item) {
                        $status &= in_array(pathinfo($item, PATHINFO_EXTENSION), $args);
                  }
            }
            return (boolean) $status;
      }
}