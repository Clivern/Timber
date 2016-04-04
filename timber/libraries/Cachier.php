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
 * Cachier Library
 *
 * Support PaypalExpress and Stripe
 *
 * For Test Credentials :
 *  > Visit (https://developer.paypal.com) and (https://stripe.com)
 *
 * @since 1.0
 * @link http://omnipay.thephpleague.com/
 */
class Cachier {

	/**
	 * Cachier configs
	 *
	 * @since 1.0
	 * @access private
	 * @var array $this->configs
	 */
	private $configs;

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
		$this->configs['paypal_express'] = unserialize($this->timber->config('_paypal_details'));
		$this->configs['stripe'] = unserialize($this->timber->config('_stripe_details'));
	}

	/**
	 * Pay with Paypal Express
	 *
	 * @since 1.0
	 * @access public
	 */
	public function payPaypalExpress()
	{
		if( $this->configs['paypal_express']['status'] == 'off' ){
			$this->timber->redirect( $this->timber->config('request_url') . '/admin/invoices/checkout?return_code=2e' );
		}

		$invoices = $this->timber->cookie->get('_checkout_invoices', '');
		$invoices = $this->timber->encrypter->decrypt($invoices, RANDOM_HASH);
		$invoices = explode(',', $invoices);
		$new_invoices = array();

		foreach ($invoices as $invoice ) {
			$new_invoices[] = ( (boolean) filter_var($invoice, FILTER_VALIDATE_INT) ) ? filter_var($invoice, FILTER_SANITIZE_NUMBER_INT) : false;
		}

		if( count($new_invoices) <= 0 ){
			$this->timber->redirect( $this->timber->config('request_url') . '/admin/invoices/checkout?return_code=1e' );
		}

		$description = ': ';
		$price = 0;
		$invoices_ids = array();

		foreach ($new_invoices as $invoice_id ) {

			$invoice = $this->timber->invoice_model->getInvoiceByMultiple( array('type' => 1, 'in_id' => $invoice_id) );

			if( (false === $invoice) || !(is_object($invoice)) ){
				continue;
			}
			if( $invoice['status'] == '1' ){
				continue;
			}

			$invoice = $invoice->as_array();
			$terms = unserialize($invoice['terms']);
			$invoices_ids[] = $invoice['in_id'];
			$price += ($invoice['total'] - $terms['overall']['paid_value']);
			$description .= 'INV-' . str_pad($invoice['in_id'], 8, '0', STR_PAD_LEFT) . ', ';
		}

		if( $price == 0 ){
			$this->timber->redirect( $this->timber->config('request_url') . '/admin/invoices/checkout?return_code=1e' );
		}

		$params = array(
			'cancelUrl' => $this->timber->config('request_url') . '/request/backend/direct/cancel_pay/paypal',
			'returnUrl' => $this->timber->config('request_url') . '/request/backend/direct/success_pay/paypal',
			'name'	=> 'Checkout',
			'description' => 'Checkout ' . $description,
			'amount' => round($price,2),
			'ids' => implode('-', $invoices_ids),
			'currency' => $this->timber->config('_site_currency'),
		);

		$this->timber->cookie->set('_cachier_param', serialize($params));

		$gateway = \Omnipay\Omnipay::create('PayPal_Express');
		$gateway->setUsername($this->configs['paypal_express']['username']);
		$gateway->setPassword($this->configs['paypal_express']['password']);
		$gateway->setSignature($this->configs['paypal_express']['signature']);
		$gateway->setTestMode($this->configs['paypal_express']['test_mode']);

		$response = $gateway->purchase($params)->send();

		if( $response->isSuccessful() ) {

			# payment was successful: update database
			# print_r($response);
			$invoices = $this->timber->cookie->get('_checkout_invoices', '');
			$invoices = $this->timber->encrypter->decrypt($invoices, RANDOM_HASH);
			$invoices = explode(',', $invoices);
			$new_invoices = array();

			foreach ($invoices as $invoice ) {
				$new_invoices[] = ( (boolean) filter_var($invoice, FILTER_VALIDATE_INT) ) ? filter_var($invoice, FILTER_SANITIZE_NUMBER_INT) : false;
			}

			if( count($new_invoices) <= 0 ){
				# Somthing goes wrong after success payments
				$this->timber->cookie->delete('_checkout_invoices');
				$this->timber->cookie->delete('_cachier_param');
				$this->timber->redirect( $this->timber->config('request_url') . '/admin/invoices/checkout?return_code=6e' );
			}

			$action_status = true;

			foreach ($new_invoices as $invoice_id ) {

				$invoice = $this->timber->invoice_model->getInvoiceByMultiple( array('type' => 1, 'in_id' => $invoice_id) );

				if( (false === $invoice) || !(is_object($invoice)) ){
					continue;
				}

				$invoice = $invoice->as_array();
				$terms = unserialize($invoice['terms']);
				$terms['overall']['paid_value'] = $terms['overall']['total_value'];

				$action_status &= (boolean) $this->timber->invoice_model->updateInvoiceById(array(
					'in_id' => $invoice_id,
					'status' => '1',
					'terms' => serialize($terms),
				));
			}

			if($action_status == true){
				$this->timber->cookie->delete('_checkout_invoices');
				$this->timber->cookie->delete('_cachier_param');
				$this->timber->redirect( $this->timber->config('request_url') . '/admin/invoices?return_code=1s' );
			}else{
				$this->timber->cookie->delete('_checkout_invoices');
				$this->timber->cookie->delete('_cachier_param');
				$this->timber->redirect( $this->timber->config('request_url') . '/admin/invoices/checkout?return_code=6e' );
			}

		}elseif( $response->isRedirect() ) {

			# redirect to offsite payment gateway
			$response->redirect();

		}else{

			# payment failed: display message to customer
			$this->timber->redirect( $this->timber->config('request_url') . '/admin/invoices/checkout?return_code=4e' );

		}
	}

	/**
	 * Paypal Express Success
	 *
	 * @since 1.o
	 * @access public
	 */
	public function successPaypalExpress()
	{

		if( $this->configs['paypal_express']['status'] == 'off' ){
			$this->timber->redirect( $this->timber->config('request_url') . '/admin/invoices/checkout?return_code=2e' );
		}

   		$gateway = \Omnipay\Omnipay::create('PayPal_Express');
		$gateway->setUsername($this->configs['paypal_express']['username']);
		$gateway->setPassword($this->configs['paypal_express']['password']);
		$gateway->setSignature($this->configs['paypal_express']['signature']);
		$gateway->setTestMode($this->configs['paypal_express']['test_mode']);

		$params = ($this->timber->cookie->exist('_cachier_param')) ? $this->timber->cookie->get('_cachier_param', serialize(array())) : serialize(array());
		$params = unserialize($params);
  		$response = $gateway->completePurchase($params)->send();
  		$paypalResponse = $response->getData();

  		if( (isset($paypalResponse['PAYMENTINFO_0_ACK'])) && ($paypalResponse['PAYMENTINFO_0_ACK'] === 'Success') ) {

      		/**
      		 * On Success Response Return The Following Data In Form Of ARRAY
			 *   [TOKEN] => EC-9UG818206K371990L
			 *   [SUCCESSPAGEREDIRECTREQUESTED] => false
			 *   [TIMESTAMP] => 2015-10-03T14:05:03Z
			 *   [CORRELATIONID] => 5da0e73d65e97
			 *   [ACK] => Success
			 *   [VERSION] => 85.0
			 *   [BUILD] => 000000
			 *   [INSURANCEOPTIONSELECTED] => false
			 *   [SHIPPINGOPTIONISDEFAULT] => false
			 *   [PAYMENTINFO_0_TRANSACTIONID] => 7327236385976450U
			 *   [PAYMENTINFO_0_TRANSACTIONTYPE] => expresscheckout
			 *   [PAYMENTINFO_0_PAYMENTTYPE] => instant
			 *   [PAYMENTINFO_0_ORDERTIME] => 2015-10-03T14:05:03Z
			 *   [PAYMENTINFO_0_AMT] => 39.03
			 *   [PAYMENTINFO_0_FEEAMT] => 1.43
			 *   [PAYMENTINFO_0_TAXAMT] => 0.00
			 *   [PAYMENTINFO_0_CURRENCYCODE] => USD
			 *   [PAYMENTINFO_0_PAYMENTSTATUS] => Completed
			 *   [PAYMENTINFO_0_PENDINGREASON] => None
			 *   [PAYMENTINFO_0_REASONCODE] => None
			 *   [PAYMENTINFO_0_PROTECTIONELIGIBILITY] => Eligible
			 *   [PAYMENTINFO_0_PROTECTIONELIGIBILITYTYPE] => ItemNotReceivedEligible,UnauthorizedPaymentEligible
			 *   [PAYMENTINFO_0_SECUREMERCHANTACCOUNTID] => J4QW9G83K4PZN
			 *   [PAYMENTINFO_0_ERRORCODE] => 0
			 *   [PAYMENTINFO_0_ACK] => Success
      		 */

			$invoices = $this->timber->cookie->get('_checkout_invoices', '');
			$invoices = $this->timber->encrypter->decrypt($invoices, RANDOM_HASH);
			$invoices = explode(',', $invoices);
			$new_invoices = array();

			foreach ($invoices as $invoice ) {
				$new_invoices[] = ( (boolean) filter_var($invoice, FILTER_VALIDATE_INT) ) ? filter_var($invoice, FILTER_SANITIZE_NUMBER_INT) : false;
			}

			if( count($new_invoices) <= 0 ){
				# Somthing goes wrong after success payments
				$this->timber->cookie->delete('_checkout_invoices');
				$this->timber->cookie->delete('_cachier_param');
				$this->timber->redirect( $this->timber->config('request_url') . '/admin/invoices/checkout?return_code=6e' );
			}

			$action_status = true;

			foreach ($new_invoices as $invoice_id ) {

				$invoice = $this->timber->invoice_model->getInvoiceByMultiple( array('type' => 1, 'in_id' => $invoice_id) );

				if( (false === $invoice) || !(is_object($invoice)) ){
					continue;
				}

				$invoice = $invoice->as_array();
				$terms = unserialize($invoice['terms']);
				$terms['overall']['paid_value'] = $terms['overall']['total_value'];

				$action_status &= (boolean) $this->timber->invoice_model->updateInvoiceById(array(
					'in_id' => $invoice_id,
					'status' => '1',
					'terms' => serialize($terms),
				));
			}

			if($action_status == true){
				$this->timber->cookie->delete('_checkout_invoices');
				$this->timber->cookie->delete('_cachier_param');
				$this->timber->redirect( $this->timber->config('request_url') . '/admin/invoices?return_code=1s' );
			}else{
				$this->timber->cookie->delete('_checkout_invoices');
				$this->timber->cookie->delete('_cachier_param');
				$this->timber->redirect( $this->timber->config('request_url') . '/admin/invoices/checkout?return_code=6e' );
			}

  		}else{
      		# Failed transaction
      		$this->timber->redirect( $this->timber->config('request_url') . '/admin/invoices/checkout?return_code=4e' );
      	}
	}

	/**
	 * Paypal Express Error
	 *
	 * @since 1.0
	 * @access public
	 */
	public function errorPaypalExpress()
	{
		if( $this->configs['paypal_express']['status'] == 'off' ){
			$this->timber->redirect( $this->timber->config('request_url') . '/admin/invoices/checkout?return_code=2e' );
		}
		# Error Result
		$this->timber->redirect( $this->timber->config('request_url') . '/admin/invoices/checkout?return_code=3e' );
	}

	/**
	 * Pay with Stripe
	 *
	 * @since 1.0
	 * @return boolean
	 */
	public function payStripe()
	{

		if( $this->configs['stripe']['status'] == 'off' ){
			$this->timber->redirect( $this->timber->config('request_url') . '/admin/invoices/checkout?return_code=2e' );
		}

		$invoices = $this->timber->cookie->get('_checkout_invoices', '');
		$invoices = $this->timber->encrypter->decrypt($invoices, RANDOM_HASH);

		$credit_card_name = (isset($_POST['credit_card_name'])) ? filter_var(trim($_POST['credit_card_name']), FILTER_SANITIZE_STRING) : false;
		$credit_card_number = (isset($_POST['credit_card_number'])) ? filter_var(trim($_POST['credit_card_number']), FILTER_SANITIZE_STRING) : false;
		$credit_card_expiry_month = (isset($_POST['credit_card_expiry_month'])) ? filter_var(trim($_POST['credit_card_expiry_month']), FILTER_SANITIZE_STRING) : false;
		$credit_card_expiry_year = (isset($_POST['credit_card_expiry_year'])) ? filter_var(trim($_POST['credit_card_expiry_year']), FILTER_SANITIZE_STRING) : false;
		$credit_card_cvv = (isset($_POST['credit_card_cvv'])) ? filter_var(trim($_POST['credit_card_cvv']), FILTER_SANITIZE_STRING) : false;
		//Remove spaces
		$credit_card_number = str_replace(' ', '', $credit_card_number);
		if( !($credit_card_name) || !($credit_card_number) || !($credit_card_expiry_month) || !($credit_card_expiry_year) || !($credit_card_cvv) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/admin/invoices/checkout?return_code=5e' );
		}

		$invoices = explode(',', $invoices);
		$new_invoices = array();

		foreach ($invoices as $invoice ) {
			$new_invoices[] = ( (boolean) filter_var($invoice, FILTER_VALIDATE_INT) ) ? filter_var($invoice, FILTER_SANITIZE_NUMBER_INT) : false;
		}

		if( count($new_invoices) <= 0 ){
			$this->timber->redirect( $this->timber->config('request_url') . '/admin/invoices/checkout?return_code=1e' );
		}

		$description = ': ';
		$price = 0;
		$invoices_ids = array();

		foreach ($new_invoices as $invoice_id ) {

			$invoice = $this->timber->invoice_model->getInvoiceByMultiple( array('type' => 1, 'in_id' => $invoice_id) );

			if( (false === $invoice) || !(is_object($invoice)) ){
				continue;
			}
			if( $invoice['status'] == '1' ){
				continue;
			}

			$invoice = $invoice->as_array();
			$terms = unserialize($invoice['terms']);
			$invoices_ids[] = $invoice['in_id'];
			$price += ($invoice['total'] - $terms['overall']['paid_value']);
			$description .= 'INV-' . str_pad($invoice['in_id'], 8, '0', STR_PAD_LEFT) . ', ';
		}

		if( $price == 0 ){
			$this->timber->redirect( $this->timber->config('request_url') . '/admin/invoices/checkout?return_code=1e' );
		}

		$params = array(
			'name'	=> 'Checkout',
			'description' => 'Checkout ' . $description,
			'amount' => round($price,2),
			'ids' => implode('-', $invoices_ids),
			'currency' => $this->timber->config('_site_currency'),
		);

		$this->timber->cookie->set('_cachier_param', serialize($params));

		$gateway = \Omnipay\Omnipay::create('Stripe');
		$gateway->setApiKey($this->configs['stripe']['client_api']);

		$formData = array(
			'number' => $credit_card_number,
			'expiryMonth' => $credit_card_expiry_month,
			'expiryYear' => $credit_card_expiry_year,
			'cvv' => $credit_card_cvv
		);

		$response = $gateway->purchase(array('amount' => round($price,2), 'currency' => $this->timber->config('_site_currency'), 'card' => $formData))->send();

		if( $response->isSuccessful() ) {

			# payment was successful: update database
			$invoices = $this->timber->cookie->get('_checkout_invoices', '');
			$invoices = $this->timber->encrypter->decrypt($invoices, RANDOM_HASH);
			$invoices = explode(',', $invoices);
			$new_invoices = array();

			foreach ($invoices as $invoice ) {
				$new_invoices[] = ( (boolean) filter_var($invoice, FILTER_VALIDATE_INT) ) ? filter_var($invoice, FILTER_SANITIZE_NUMBER_INT) : false;
			}

			if( count($new_invoices) <= 0 ){
				# Somthing goes wrong after success payments
				$this->timber->cookie->delete('_checkout_invoices');
				$this->timber->cookie->delete('_cachier_param');
				$this->timber->redirect( $this->timber->config('request_url') . '/admin/invoices/checkout?return_code=6e' );
			}

			$action_status = true;

			foreach ($new_invoices as $invoice_id ) {

				$invoice = $this->timber->invoice_model->getInvoiceByMultiple( array('type' => 1, 'in_id' => $invoice_id) );

				if( (false === $invoice) || !(is_object($invoice)) ){
					continue;
				}

				$invoice = $invoice->as_array();
				$terms = unserialize($invoice['terms']);
				$terms['overall']['paid_value'] = $terms['overall']['total_value'];

				$action_status &= (boolean) $this->timber->invoice_model->updateInvoiceById(array(
					'in_id' => $invoice_id,
					'status' => '1',
					'terms' => serialize($terms),
				));

			}

			if($action_status == true){
				$this->timber->cookie->delete('_checkout_invoices');
				$this->timber->cookie->delete('_cachier_param');
				$this->timber->redirect( $this->timber->config('request_url') . '/admin/invoices?return_code=1s' );
			}else{
				$this->timber->cookie->delete('_checkout_invoices');
				$this->timber->cookie->delete('_cachier_param');
				$this->timber->redirect( $this->timber->config('request_url') . '/admin/invoices/checkout?return_code=6e' );
			}

		}elseif( $response->isRedirect() ) {

			# redirect to offsite payment gateway
			$response->redirect();

		}else{

			# payment failed: display message to customer
			$this->timber->redirect( $this->timber->config('request_url') . '/admin/invoices/checkout?return_code=4e' );

		}
	}

	/**
	 * Get Error Phrase
	 *
	 * @since 1.0
	 * @access public
	 * @return string|boolean
	 */
	public function errorPhrase()
	{
		$return_code = (isset($_GET['return_code'])) ? $_GET['return_code'] : '';
		$return_message = (isset($_GET['return_message'])) ? $_GET['return_message'] : '';

		switch ($return_code) {

			case '1e':
				return $this->timber->translator->trans('Invalid Request.');

			case '2e':
				return $this->timber->translator->trans('Payment method requested inactive.');

			case '3e':
				return $this->timber->translator->trans('Oops! It Looks Like Your Transaction Was Cancelled.');

			case '4e':
				return $this->timber->translator->trans('Oops! It Looks Like Your Transaction Was Failed.');

			case '5e':
				return $this->timber->translator->trans('Please enter a valid credit card data.');

			case '6e':
				return $this->timber->translator->trans('Something goes wrong and failed the transaction.');

			case '8e':
				return $this->timber->translator->trans('Sorry! this action is disabled in demo.');

			case '1s':
				return $this->timber->translator->trans('Thank you! Your Payment was processed successfully.');

			default:
				return $return_message;

		}
	}
}