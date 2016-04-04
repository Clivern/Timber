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
 * Manage date and time according to app timezone
 *
 * @since 1.0
 */
class Time {

      /**
       * Holds and instance of timezone class
       *
       * @since 1.0
       * @access private
       * @var object $this->time_zone
       */
      private $time_zone;

      /**
       * Holds and instance of datetime class in GMT
       *
       * @since 1.0
       * @access private
       * @var object $this->date_time
       */
      private $gmt_date_time;

      /**
       * Holds and instance of datetime class in Local
       *
       * @since 1.0
       * @access private
       * @var object $this->date_time
       */
      private $local_date_time;

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
       * Set up user timezone and both current gmt and local datetime
       *
       * @since 1.0
       * @access public
       * @link http://php.net/manual/en/timezones.php a list of timezone names
       */
      public function config()
      {
            $this->time_zone = new \DateTimeZone( $this->timber->config('_site_timezone') );
            $this->gmt_date_time = new \DateTime( 'now', new \DateTimeZone('GMT') );
            $this->local_date_time = new \DateTime( 'now', $this->time_zone );
      }

      /**
       * Get user time zone
       *
       * @since 1.0
       * @access public
       * @return string
       */
      public function getTimeZoneName()
      {
            return $this->time_zone->getName();
      }

      /**
       * Get current timezone time offset from GMT
       *
       * @since 1.0
       * @access public
       * @return integer
       */
      public function getTimeZoneOffset()
      {
            return $this->time_zone->getOffset($this->local_date_time);
      }

      /**
       * Get a list of available timezones names
       *
       * @since 1.0
       * @access public
       * @return array
       */
      public function listIdentifiers()
      {
            return $this->time_zone->listIdentifiers();
      }

      /**
       * Get array containing dst, offset and the timezone name
       *
       * @since 1.0
       * @access public
       * @return array
       */
      public function listAbbreviations()
      {
            return $this->time_zone->listAbbreviations();
      }

      /**
       * Get current timestamp
       *
       * @since 1.0
       * @access public
       * @return string
       */
      public function getCurrentTimestamp()
      {
            return $this->gmt_date_time->getTimestamp();
      }

      /**
       * Get current date whether local or gmt
       *
       * @since 1.0
       * @access public
       * @param boolean $gmt
       * @param string $format
       * @return string
       */
      public function getCurrentDate($gmt = false, $format = 'Y-m-d H:i:s')
      {
            if( $gmt ){
                  return $this->gmt_date_time->format($format);
            }else{
                  return $this->local_date_time->format($format);
            }
      }

      /**
       * Get old date
       *
       * A list of intervals
       * +1 second
       * +2 seconds
       * +1 minute
       * +2 minutes
       * +1 hour
       * +2 hours
       * +1 day
       * +2 days
       * +1 week
       * +2 weeks
       * +1 month
       * +2 months
       * +1 year
       * +1 years
       *
       * @since 1.0
       * @access public
       * @param string $interval
       * @param boolean $gmt
       * @param string $format
       * @return string
       */
      public function getDateAfter($interval = '+0 second', $date = 'now', $gmt = false, $format = 'Y-m-d H:i:s')
      {
            if( $gmt ){
                  $gmt_date_time = ($date == 'now') ? new \DateTime( $date, new \DateTimeZone('GMT') ) : new \DateTime( $date );
                  $gmt_date_time->modify($interval);
                  return $gmt_date_time->format($format);
            }else{
                  $local_date_time = ($date == 'now') ? new \DateTime( $date, $this->time_zone ) : new \DateTime( $date );
                  $local_date_time->modify($interval);
                  return $local_date_time->format($format);
            }
      }

      /**
       * Change date to timestamp
       *
       * datetime in form of Y-m-d H:i:s
       *
       * @since 1.0
       * @access public
       * @param  string  $datetime
       * @param  boolean $gmt
       * @return integer
       */
      public function dateToTimestamp($datetime, $gmt = false)
      {
            $datetime = explode(' ', $datetime);
            $date = (isset($datetime[0])) ? explode('-', $datetime[0]): false;
            $time = (isset($datetime[1])) ? explode(':', $datetime[1]): false;
            if( $gmt ){
                  $gmt_date_time_obj = new \DateTime( 'now', new \DateTimeZone('GMT') );
                  $gmt_date_time_obj->setDate( $date[0], $date[1], $date[2] );
                  $gmt_date_time_obj->setTime( $time[0], $time[1], $time[2] );
                  return $gmt_date_time_obj->getTimestamp();
            }else{
                  $local_date_time_obj = new \DateTime( 'now', $this->time_zone );
                  $local_date_time_obj->setDate( $date[0], $date[1], $date[2] );
                  $local_date_time_obj->setTime( $time[0], $time[1], $time[2] );
                  return $local_date_time_obj->getTimestamp();
            }
      }

      /**
       * Change timestamp to local or gmt date time
       *
       * @since 1.0
       * @access public
       * @param  integer $timestamp
       * @param  boolean $gmt
       * @param  string  $format
       * @return string
       */
      public function timestampToDate($timestamp, $gmt = false, $format = 'Y-m-d H:i:s')
      {
            if( $gmt ){
                  $gmt_date_time_obj = new \DateTime( 'now', new \DateTimeZone('GMT') );
                  $gmt_date_time_obj->setTimestamp($timestamp);
                  return $gmt_date_time_obj->format($format);
            }else{
                  $local_date_time_obj = new \DateTime( 'now', $this->time_zone );
                  $local_date_time_obj->setTimestamp($timestamp);
                  return $local_date_time_obj->format($format);
            }
      }

      /**
       * Get diff from current timestamp
       *
       * @since 1.0
       * @access public
       * @param  integer $timestamp
       * @return array
       */
      public function getTimestampDiff($timestamp, $gmt = false)
      {
            if( $gmt ){
                  $timestamp_obj = new \DateTime( 'now', new \DateTimeZone('GMT') );
                  $timestamp_obj->setTimestamp($timestamp);
                  return $timestamp_obj->diff($this->gmt_date_time);
            }else{
                  $timestamp_obj = new \DateTime( 'now', $this->time_zone );
                  $timestamp_obj->setTimestamp($timestamp);
                  return $timestamp_obj->diff($this->local_date_time);
            }
      }

      /**
       * Get diff from current datetime
       *
       * @since 1.0
       * @access public
       * @param  string $datetime
       * @param  boolean $gmt
       * @return array
       */
      public function getDatetimeDiff($datetime, $gmt = false)
      {
            $datetime = explode(' ', $datetime);
            $date = (isset($datetime[0])) ? explode('-', $datetime[0]): false;
            $time = (isset($datetime[1])) ? explode(':', $datetime[1]): false;
            if( $gmt ){
                  $date_time_obj = new \DateTime( 'now', new \DateTimeZone('GMT') );
                  $date_time_obj->setDate( $date[0], $date[1], $date[2] );
                  $date_time_obj->setTime( $time[0], $time[1], $time[2] );
                  return $date_time_obj->diff($this->gmt_date_time);
            }else{
                  $date_time_obj = new \DateTime( 'now', $this->time_zone );
                  $date_time_obj->setDate( $date[0], $date[1], $date[2] );
                  $date_time_obj->setTime( $time[0], $time[1], $time[2] );
                  return $date_time_obj->diff($this->local_date_time);
            }
      }

      /**
       * Get diff in human readable format
       *
       * @since 1.0
       * @access public
       * @param object $diff
       * @return string
       */
      public function humanDiff($diff)
      {
            $format = '';
            if( (empty($format)) && ($diff->y > 0) ){
                  if($diff->y == 1){
                        $format = '%y '. $this->timber->translator->trans('year');
                  }else{
                        $format = '%y '. $this->timber->translator->trans('years');
                  }
            }
            if( (empty($format)) && ($diff->m > 0) ){
                  if($diff->m == 1){
                        $format = '%m '. $this->timber->translator->trans('month');
                  }else{
                        $format = '%m '. $this->timber->translator->trans('months');
                  }
            }
            if( (empty($format)) && ($diff->d > 0) ){
                  if($diff->d == 1){
                        $format = '%d '. $this->timber->translator->trans('day');
                  }else{
                        $format = '%d '. $this->timber->translator->trans('days');
                  }
            }
            if( (empty($format)) && ($diff->h > 0) ){
                  if($diff->h == 1){
                        $format = '%h '. $this->timber->translator->trans('hour');
                  }else{
                        $format = '%h '. $this->timber->translator->trans('hours');
                  }
            }
            if( (empty($format)) && ($diff->i > 0) ){
                  if($diff->i == 1){
                        $format = '%i '. $this->timber->translator->trans('minute');
                  }else{
                        $format = '%i '. $this->timber->translator->trans('minutes');
                  }
            }
            if( (empty($format)) && ($diff->s > 0)){
                  if($diff->s == 1){
                        $format = '%s '. $this->timber->translator->trans('second');
                  }else{
                        $format = '%s '. $this->timber->translator->trans('seconds');
                  }
            }
            return $diff->format($format . ' ' . $this->timber->translator->trans('ago'));
      }

      /**
       * Add or Subtract days from date
       *
       * @since 1.0
       * @access public
       * @param string $date
       * @param string $days
       * @param string $format
       * @return string
       */
      public function dateAddition($date, $days, $format = 'Y-m-d')
      {
            $date = date($date);
            $newdate = strtotime ( $days . ' days' , strtotime ( $date ) ) ;
            $newdate = date ( $format , $newdate );
            return $newdate;
      }

}