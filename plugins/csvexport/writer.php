<?php
/**
 * CSV Export - Export Records to CSV
 *
 * @author      Clivern <support@clivern.com>
 * @copyright   2016 Clivern
 * @link        http://clivern.com
 * @license     http://codecanyon.com/license
 * @version     1.0
 * @package     CSV Export
 */

/**
 * CSV Writer Class
 *
 * @since 1.0
 */
class Writer {

    /**
     * Defines delimiter char
     *
     * @since 1.0
     * @access  private
     * @var string $this->delimiter
     */
    private $delimiter = ',';

    /**
     * Defines file name
     *
     * @since 1.0
     * @access  private
     * @var string $this->file_name
     */
    private $file_name = 'data';

    /**
     * Holds CSV records
     *
     * @since 1.0
     * @access  private
     * @var array $this->data
     */
    private $data = array();

	/**
	 * CSV writer instance
	 *
	 * @since 1.0
	 * @access private
	 * @var object $this->csv_writer
	 */
	private $csv_writer;

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
	 * Configure Writer
	 *
	 * @since 1.0
	 * @access public
	 * @param  array  $data
	 * @param  string $delimiter
	 * @return object
	 */
	public function config($data = array(), $file_name = 'data', $delimiter = ',')
	{
		$this->data = $data;
		$this->file_name = $file_name;
		$this->delimiter = $delimiter;

		return $this;
	}

	/**
	 * Build CSV
	 *
	 * @since 1.0
	 * @access public
	 */
	public function buildCSV()
	{
        $fp = fopen('php://temp', 'r+');
        foreach ($this->data as $row) {
            fputcsv($fp, $row, $this->delimiter);
        }
        rewind($fp);
        $content = stream_get_contents($fp);
        fclose($fp);

      	return $content;
	}

	/**
	 * Download CSV
	 *
	 * @since 1.0
	 * @access public
	 */
	public function downloadCSV()
	{
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=' . $this->file_name . '.csv');

		// create a file pointer connected to the output stream
		$output = fopen('php://output', 'w');

        foreach ($this->data as $row) {
            fputcsv($output, $row, $this->delimiter);
        }

        exit();
	}
}