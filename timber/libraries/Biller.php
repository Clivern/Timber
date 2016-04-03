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
 * Biller Library
 *
 * @since 1.0
 * @link http://www.tcpdf.org
 */
class Biller {

	/**
	 * Biller configs
	 *
	 * @since 1.0
	 * @access private
	 * @var array $this->configs
	 */
	private $configs = array(
		'page_orientation' => 'P',
		'unit' => 'mm',
		'page_format' => 'A4',
		'header_logo_width' => 30,
		'font_name_main' => 'helvetica',
		'font_size_main' => 10,
		'font_name_data' => 'helvetica',
		'font_size_data' => 8,
		'font_monospaced' => 'courier',
		'margin_left' => 15,
		'margin_top' => 27,
		'margin_right' => 15,
		'margin_header' => 5,
		'margin_footer' => 10,
		'margin_bottom' => 25,
		'image_scale_ratio' => 1.25,
	);

	/**
	 * PDF Object
	 *
	 * @since 1.0
	 * @access private
	 * @var object $this->pdf
	 */
	private $pdf;

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
		//silence is golden
	}

	/**
	 * Create invoice or Estimate
	 *
	 * <code>
	 * $data = array(
	 * 		'title' INV00000001
	 * 		'file' INV00000001
	 * 		'ref_id' INV-00001
	 * 		'subject' Invoice
	 * 		'logo' logo.png
	 * 		'name' Timber
	 * 		'description' Web Development Service
	 *
	 *   	'company' name - address1 - address2 - city - country - vat - phone
	 *   	'client' issue_date - due_date - name - company - address1 - address2 - city - country - vat - phone
	 *   	'notes'
	 *   	'items'
	 *   	'overall'
	 * )
	 * </code>
	 *
	 * @since 1.0
	 * @access public
	 * @param array $data
	 */
	public function create( $data )
	{
		# create new PDF document
		$this->pdf = new \TCPDF( $this->configs['page_orientation'], $this->configs['unit'], $this->configs['page_format'], true, 'UTF-8', false );

		# set document information
		$this->pdf->SetCreator( $this->timber->config('app_name') . '-' . TIMBER_CURRENT_VERSION );
		$this->pdf->SetAuthor( $this->timber->config('app_author') );

		$this->pdf->SetTitle( $data['title'] );
		$this->pdf->SetSubject( $data['subject'] );
		$this->pdf->SetKeywords( $this->timber->config('app_name') . ', ' .  $this->timber->config('app_author') );

		# set default header data
		# $this->pdf->SetHeaderData('logo.png', header_logo_width, "Timber", "Web Development Service");

		# Set Logo
		$this->pdf->SetHeaderData( $data['logo'], $this->configs['header_logo_width'], $data['name'], $data['description'] );

		# set header and footer fonts
		$this->pdf->setHeaderFont( array($this->configs['font_name_main'], '', $this->configs['font_size_main']) );
		$this->pdf->setFooterFont( array($this->configs['font_name_data'], '', $this->configs['font_size_data']) );

		# set default monospaced font
		$this->pdf->SetDefaultMonospacedFont( $this->configs['font_monospaced'] );

		# set margins
		$this->pdf->SetMargins( $this->configs['margin_left'], $this->configs['margin_top'], $this->configs['margin_right'] );
		$this->pdf->SetHeaderMargin( $this->configs['margin_header'] );
		$this->pdf->SetFooterMargin( $this->configs['margin_footer'] );

		# set auto page breaks
		$this->pdf->SetAutoPageBreak( TRUE, $this->configs['margin_bottom'] );

		# set image scale factor
		$this->pdf->setImageScale( $this->configs['image_scale_ratio'] );

		# Set some language-dependent strings (optional)
		$this->pdf->setLanguageArray(array(
			'a_meta_charset' => 'UTF-8',
			'a_meta_dir' => 'ltr',
			'a_meta_language' => 'en',
			'w_page' => $this->timber->translator->trans('Page'),
		));

		# Set Font
		$this->pdf->SetFont('helvetica', 'B', 11);

		# Add a page
		$this->pdf->AddPage();

		# Set Font
		$this->pdf->SetFont('helvetica', '', 8);

		if( strpos($data['ref_id'], "ST") > 0 ){
			$tbl = '<table cellspacing="0" cellpadding="1" border="0">
		  	<tr>
		    	<td><b>' . $this->timber->translator->trans('From:') . '</b></td>
		    	<td></td>
		    	<td><b>' . $this->timber->translator->trans('Estimate No: ') . $data['ref_id'] . '</b></td>
		  	</tr>';
		}else{
			$tbl = '<table cellspacing="0" cellpadding="1" border="0">
		  	<tr>
		    	<td><b>' . $this->timber->translator->trans('From:') . '</b></td>
		    	<td></td>
		    	<td><b>' . $this->timber->translator->trans('Invoice No: ') . $data['ref_id'] . '</b></td>
		  	</tr>';
		}

		$tbl .= '<tr>
					<td>' . $data['company']['name'] . '</td>
					<td></td>
					<td><b>' . $this->timber->translator->trans('To:') . '</b></td>
				</tr>';

		$tbl .= '<tr>
					<td>' . $data['company']['address1'] . '</td>
					<td></td>
					<td>' . $data['client']['name'] . '</td>
				</tr>';

		$tbl .= '<tr>
					<td>' . $data['company']['address2'] . '</td>
					<td></td>
					<td>' . $data['client']['company'] . '</td>
				</tr>';

		$tbl .= '<tr>
					<td>' . $data['company']['city'] . '</td>
					<td></td>
					<td>' . $data['client']['address1'] . '</td>
				</tr>';

		$tbl .= '<tr>
					<td>' . $data['company']['country'] . '</td>
					<td></td>
					<td>' . $data['client']['address2'] . '</td>
				</tr>';

		$tbl .= ($data['company']['vat'] != '') ? '<tr><td>' . $this->timber->translator->trans('VAT: ') . $data['company']['vat'] . '</td><td></td><td>' . $data['client']['city'] . '</td></tr>' : '<tr><td><br/></td><td></td><td>' . $data['client']['city'] . '</td></tr>';

		$tbl .= ($data['company']['phone']) ? '<tr><td>' . $this->timber->translator->trans('Phone: ') . $data['company']['phone'] . '</td><td></td><td>' . $data['client']['country'] . '</td></tr>' : '<tr><td><br/></td><td></td><td></td></tr>';

		$tbl .= ($data['client']['vat'] != '') ? '<tr><td></td><td></td><td>' . $this->timber->translator->trans('VAT: ') . $data['client']['vat'] . '</td></tr>' : '<tr><td></td><td></td><td><br/></td></tr>';

		$tbl .= ($data['client']['phone'] != '') ? '<tr><td></td><td></td><td>' . $this->timber->translator->trans('Phone: ') . $data['client']['phone'] . '</td></tr>' : '<tr><td></td><td></td><td><br/></td></tr>';

		# Client and Company
		$this->pdf->writeHTML($tbl, true, false, false, false, '');

		$tbl = '<table cellspacing="0" cellpadding="1" border="0">
		    <tr>
		        <td></td>
		        <td></td>
		        <td></td>
		    </tr>
		    <tr>
		        <td></td>
		        <td></td>
		        <td><b>' . $this->timber->translator->trans('Issue Date: ') . '</b> ' . $data['client']['issue_date'] . '</td>
		    </tr>
		    <tr>
		        <td></td>
		        <td></td>
		        <td><b>' . $this->timber->translator->trans('Due Date: ') . '</b> ' . $data['client']['due_date'] . '</td>
		    </tr>
		</table><br/><br/><br/><br/>';

		# Invoice Dates
		$this->pdf->writeHTML($tbl, true, false, false, false, '');

		# Letter To Client
		/*
		$this->pdf->Write(0, 'Dear Ms. Jane Doe,', '', 0, 'L', true, 0, false, false, 0);
		$this->pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
		$this->pdf->Write(0, 'Please find below a cost-breakdown for the recent work completed. Please make payment at your earliest convenience, and do not hesitate to contact me with any questions..', '', 0, 'L', true, 0, false, false, 0);
		$this->pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
		$this->pdf->Write(0, 'Many thanks,', '', 0, 'L', true, 0, false, false, 0);
		$this->pdf->Write(0, 'Your Name', '', 0, 'L', true, 0, false, false, 0);
		*/

		$this->pdf->writeHTML( $data['notes'], true, false, false, false, '' );

		# Cost Breakdown
		$tbl = '<br/><br/><br/><br/><table cellspacing="0" cellpadding="1" border="0">
		  	<tr>
		    	<td style="background-color: #eee" colspan="2"><br><br><b>' . $this->timber->translator->trans('Item') . '</b><br></td>
		    	<td style="background-color: #eee;text-align:center"><br><br><b>' . $this->timber->translator->trans('Quantity') .'</b><br></td>
		    	<td style="background-color: #eee;text-align:center"><br><br><b>' . $this->timber->translator->trans('Unit Price') . ' (' . $this->timber->config('_site_currency') .')</b><br></td>
		    	<td style="background-color: #eee;text-align:right"><br><br><b>'. $this->timber->translator->trans('Total') .' (' . $this->timber->config('_site_currency') . ')</b><br></td>
		  	</tr>';


		foreach ($data['items'] as $item) {
			$tbl .='<tr>
			    		<td style="background-color: #fff" colspan="2"><br><br><b>' . $item['item_title'] . '</b><br/>' . $item['item_description'] . '<br></td>
			    		<td style="text-align:center;background-color: #fff"><br><br>' . $item['item_quantity'] . '<br></td>
			    		<td style="text-align:center;background-color: #fff"><br><br>' . $item['item_unit_price'] . '<br></td>
			    		<td style="text-align:right;background-color: #fff"><br><br>' . $item['item_sub_total'] . '<br></td>
			  		</tr>';
		}

		$invoice_tax_currency = ($data['overall']['tax_type'] == 'percent') ? "%" : $this->timber->config('_site_currency');
		$invoice_discount_currency = ($data['overall']['discount_type'] == 'percent') ? "%" : $this->timber->config('_site_currency');

		$tbl .='<tr>
		    		<td colspan="2"></td>
		    		<td></td>
		    		<td style="background-color: #fff"><br><b>'. $this->timber->translator->trans('Subtotal') .' (' . $this->timber->config('_site_currency') . ') :</b></td>
		    		<td style="background-color: #fff;text-align:right"><br>' . $data['overall']['sub_total'] . '</td>
		  		</tr>
		  		<tr>
		    		<td colspan="2"></td>
		    		<td></td>
		    		<td style="background-color: #fff"><br><b>'. $this->timber->translator->trans('Discount') .' (' . $invoice_discount_currency . ') :</b></td>
		    		<td style="background-color: #fff;text-align:right"><br>' . $data['overall']['discount_value'] . '</td>
		  		</tr>
		  		<tr>
		    		<td colspan="2"></td>
		    		<td></td>
		    		<td style="background-color: #fff"><br><b>'. $this->timber->translator->trans('Taxes') .' (' . $invoice_tax_currency . ') :</b></td>
		    		<td style="background-color: #fff;text-align:right"><br>' . $data['overall']['tax_value'] . '</td>
		  		</tr>
		  		<tr>
		    		<td colspan="2"></td>
		    		<td></td>
		    		<td style="background-color: #fff"><br><b>'. $this->timber->translator->trans('Total') .' (' . $this->timber->config('_site_currency') . ') :</b></td>
		    		<td style="background-color: #fff;text-align:right"><br>' . $data['overall']['total_value'] . '</td>
		  		</tr>
		    	<tr>
		    		<td colspan="2"></td>
		    		<td></td>
		    		<td style="background-color: #fff"><br><b>'. $this->timber->translator->trans('Paid') .' (' . $this->timber->config('_site_currency') . ') :</b></td>
		    		<td style="background-color: #fff;text-align:right"><br>' . $data['overall']['paid_value'] . '</td>
		  		</tr>
			</table><br/><br/><br/><br/>';

		# Write HTML
		$this->pdf->writeHTML($tbl, true, false, false, false, '');

		# Write terms
		$this->pdf->Write(0, $this->timber->translator->trans('Many thanks for your custom! We look forward to doing business with you again in due course.'), '', 0, 'L', true, 0, false, false, 0);

		# Close and Output
		$this->pdf->Output( $data['file'] . '.pdf', 'I' );
	}

	/**
	 * Download Invoice or Estimate
	 *
	 * @since 1.0
	 * @access public
	 * @param  integer $file_id
	 * @param  string $hash
	 * @return string
	 */
	public function downloadFile($file_id, $hash)
	{
		$file_id = filter_var($file_id, FILTER_SANITIZE_NUMBER_INT);
		$hash = filter_var($hash, FILTER_SANITIZE_STRING);

		$invoice = $this->timber->invoice_model->getInvoiceByMultiple(array(
			'in_id' => $file_id,
			'reference' => $hash
		));

		if( (false === $invoice) || !(is_object($invoice)) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		$invoice = $invoice->as_array();

		$client = $this->timber->user_model->getUserById( $invoice['client_id'] );

		if( (false === $client) || !(is_object($client)) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		$client = $client->as_array();

		$data['title'] = ($invoice['type'] == 1) ? "INV-" . str_pad($invoice['in_id'], 8, '0', STR_PAD_LEFT) : "EST-" . str_pad($invoice['in_id'], 8, '0', STR_PAD_LEFT);
		$data['file'] = ($invoice['type'] == 1) ? "INV-" . str_pad($invoice['in_id'], 8, '0', STR_PAD_LEFT) : "EST-" . str_pad($invoice['in_id'], 8, '0', STR_PAD_LEFT);
		$data['ref_id'] = ($invoice['type'] == 1) ? "INV-" . str_pad($invoice['in_id'], 8, '0', STR_PAD_LEFT) : "EST-" . str_pad($invoice['in_id'], 8, '0', STR_PAD_LEFT);
		$data['subject'] = ($invoice['type'] == 1) ? "Invoice" : "Estimate";

		var_dump($this->timber->config('_site_logo'));
		var_dump('../../../../..' . TIMBER_THEMES_DIR . $this->timber->twig->getDefaultTheme() . '/assets/img/logo.png');
		die();
		$data['logo'] = $this->timber->storage->getRelFilePath( $this->timber->config('_site_logo'), '../../../../..' . TIMBER_THEMES_DIR . $this->timber->twig->getDefaultTheme() . '/assets/img/logo.png' );
		var_dump($data['logo']);
		$data['name'] = "";
		$data['description'] = "";

		$data['client'] = array();
		$data['client']['issue_date'] = $invoice['issue_date'];
		$data['client']['due_date'] = $invoice['due_date'];
		$data['client']['name'] = trim( $client['first_name'] . " " . $client['last_name'] );
		$data['client']['company'] = $client['company'];
		$data['client']['address1'] = $client['address1'];
		$data['client']['address2'] = $client['address2'];
		$data['client']['city'] = $client['city'];
		$data['client']['country'] = $client['country'];
		$data['client']['vat'] = $client['vat_nubmer'];
		$data['client']['phone'] = $client['phone_num'];

		$data['company'] = array();
		$data['company']['name'] = $this->timber->config('_site_title');
		$data['company']['address1'] = $this->timber->config('_site_address_line1');
		$data['company']['address2'] = $this->timber->config('_site_address_line2');
		$data['company']['city'] = $this->timber->config('_site_city');
		$data['company']['country'] = $this->timber->config('_site_country');
		$data['company']['vat'] = $this->timber->config('_site_vat_number');
		$data['company']['phone'] = $this->timber->config('_site_phone');

		$data['terms'] = unserialize($invoice['terms']);

		$data['items'] = $data['terms']['items'];
		$data['notes'] = $data['terms']['notes'];
		$data['overall'] = $data['terms']['overall'];

		$this->create($data);
		die();
	}
}