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
 * Benchmark Timber
 *
 * @since 1.0
 */
class Bench {

    /**
     * Start Time
     *
     * @since 1.0
     * @access private
     * @var array $this->start_time
     */
    private $start_time;

    /**
     * End Time
     *
     * @since 1.0
     * @access private
     * @var array $this->end_time
     */
    private $end_time;

    /**
     * Memory Usage
     *
     * @since 1.0
     * @access private
     * @var array $this->memory_usage
     */
    private $memory_usage;

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
     * Config class properties
     *
     * @since 1.0
     * @access public
     */
    public function config()
    {
        # Silence is Golden
    }

    /**
     * Sets start microtime
     *
     * @since 1.0
     * @access public
     */
    public function start()
    {
        $this->start_time = microtime(true);
    }

    /**
     * Sets end microtime
     *
     * @since 1.0
     * @access public
     */
    public function end()
    {
        $this->end_time = microtime(true);
        $this->memory_usage = memory_get_usage(true);
    }

    /**
     * Log Benchmark Data
     *
     * @since 1.0
     * @access public
     */
    public function log()
    {
        # 156ms or 1.123s
        $this->timber->log->info('Elapsed Time = ' . $this->timber->bench->getTime());
        # elapsed microtime in float
        $this->timber->log->info('Elapsed Time = ' . $this->timber->bench->getTime(true));
        # 156ms or 1s
        $this->timber->log->info('Elapsed Time = ' . $this->timber->bench->getTime(false, '%d%s'));

        # 152B or 90.00Kb or 15.23Mb
        $this->timber->log->info('Memory Peak = ' . $this->timber->bench->getMemoryPeak());
        # memory peak in bytes
        $this->timber->log->info('Memory Peak = ' . $this->timber->bench->getMemoryPeak(true));
        # 152B or 90.152Kb or 15.234Mb
        $this->timber->log->info('Memory Peak = ' . $this->timber->bench->getMemoryPeak(false, '%.3f%s'));

        # 152B or 90.00Kb or 15.23Mb
        $this->timber->log->info('Memory Usage = ' . $this->timber->bench->getMemoryUsage());
    }

    /**
     * Returns the elapsed time, readable or not
     *
     * @since 1.0
     * @access public
     * @param  boolean $readable
     * @param  string  $format
     * @return string|float
     */
    public function getTime($raw = false, $format = null)
    {
        $elapsed = $this->end_time - $this->start_time;
        return $raw ? $elapsed : $this->readableElapsedTime($elapsed, $format);
    }

    /**
     * Returns the memory usage at the end checkpoint
     *
     * @since 1.0
     * @access public
     * @param  boolean $readable
     * @param  string  $format
     * @return string|float
     */
    public function getMemoryUsage($raw = false, $format = null)
    {
        return $raw ? $this->memory_usage : $this->readableSize($this->memory_usage, $format);
    }

    /**
     * Returns the memory peak, readable or not
     *
     * @since 1.0
     * @access public
     * @param  boolean $readable
     * @param  string  $format
     * @return string|float
     */
    public function getMemoryPeak($raw = false, $format = null)
    {
        $memory = memory_get_peak_usage(true);
        return $raw ? $memory : $this->readableSize($memory, $format);
    }

    /**
     * Returns a human readable memory size
     *
     * @since 1.0
     * @access private
     * @param   int    $size
     * @param   string $format
     * @param   int    $round
     * @return  string
     */
    private function readableSize($size, $format = null, $round = 3)
    {
        $mod = 1024;
        if (is_null($format)) {
            $format = '%.2f%s';
        }
        $units = explode(' ','B Kb Mb Gb Tb');
        for ($i = 0; $size > $mod; $i++) {
            $size /= $mod;
        }
        if (0 === $i) {
            $format = preg_replace('/(%.[\d]+f)/', '%d', $format);
        }
        return sprintf($format, round($size, $round), $units[$i]);
    }

    /**
     * Returns a human readable elapsed time
     *
     * @since 1.0
     * @access private
     * @param  float $microtime
     * @param  string  $format
     * @return string
     */
    private static function readableElapsedTime($microtime, $format = null, $round = 3)
    {
        if (is_null($format)) {
            $format = '%.3f%s';
        }
        if ($microtime >= 1) {
            $unit = 's';
            $time = round($microtime, $round);
        } else {
            $unit = 'ms';
            $time = round($microtime*1000);
            $format = preg_replace('/(%.[\d]+f)/', '%d', $format);
        }
        return sprintf($format, $time, $unit);
    }
}