<?php
/* Onemi Payment Gateway Class */
class Onemi_Payment_Gateway extends WC_Payment_Gateway {

    // Setup our Gateway's id, description and other values
    function __construct() {
	//ini_set('display_errors','on');
        global $woocommerce;
	// The global ID for this Payment method
	$this->id = "onemi_payment_gateway";

	// The Title shown on the top of the Payment Gateways Page next to all the other Payment Gateways
	$this->method_title = __( "OnEMI", 'onemi_payment_gateway' );

	// The description for this Payment Gateway, shown on the actual Payment options page on the backend
	$this->method_description = __( "OnEMI Payment Gateway Plug-in for WooCommerce", 'onemi_payment_gateway' );

	// The title to be used for the vertical tabs that can be ordered top to bottom
	$this->title = __( "OnEMI", 'onemi_payment_gateway' );

	// If you want to show an image next to the gateway's name on the frontend, enter a URL to an image.
	$this->icon = null;

	// Bool. Can be set to true if you want payment fields to show on the checkout 
	// if doing a direct integration, which we are doing in this case
	$this->has_fields = FALSE;
        //$this->credit_card_form();

	// Supports the default credit card form
	//$this->supports = array( 'default_credit_card_form' );

	// This basically defines your settings which are then loaded with init_settings()
	$this->init_form_fields();

	// After init_settings() is called, you can get the settings and load them into variables, e.g:
	// $this->title = $this->get_option( 'title' );
	$this->init_settings();
	
	// Turn these settings into variables we can use
	foreach ( $this->settings as $setting_key => $value ) {
		$this->$setting_key = $value;
	}
	
	// Lets check for SSL
	add_action( 'admin_notices', array( $this,	'do_ssl_check' ) );
        
        
	//add_action('woocommerce_receipt_'.$this->id, array($this, 'receipt_page'));
	// Save settings
	if ( is_admin() ) {
		// Versions over 2.0
		// Save our administration options. Since we are not going to be doing anything special
		// we have not defined 'process_admin_options' in this class so the method in the parent
		// class will be used instead
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
	}
        
       add_action('woocommerce_receipt_'.$this->id, array(&$this, 'receipt_page'));
       add_action( 'woocommerce_api_' . strtolower( get_class( $this ) ), array( $this, 'check_onemi_response' ) );
        
    } // End __construct()



    // Build the administration fields for this specific Gateway
    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title'     => __( 'Enable / Disable', 'onemi_payment_gateway' ),
                'label'     => __( 'Enable this payment gateway', 'onemi_payment_gateway' ),
                'type'	=> 'checkbox',
                'default'	=> 'no',
            ),
            'title' => array(
                'title'     => __( 'Title', 'onemi_payment_gateway' ),
                'type'	=> 'text',
                'desc_tip'	=> __( 'Payment title the customer will see during the checkout process.', 'onemi_payment_gateway' ),
                'default'	=> __( 'OnEMI', 'onemi_payment_gateway' ),
            ),
            'description' => array(
                'title'	=> __( 'Description', 'onemi_payment_gateway' ),
                'type'	=> 'textarea',
                'desc_tip'	=> __( 'Payment description the customer will see during the checkout process.', 'onemi_payment_gateway' ),
                'default'	=> __( 'Direct payment, EMI and Loans via OnEmi. OnEmi accepts VISA, MasterCard, Debit Cards and the Net Banking of all major banks.', 'onemi_payment_gateway' ),
                'css'	=> 'max-width:350px;'
            ),
            'MerchantId' => array(
                'title'	=> __( 'Merchant ID ', 'onemi_payment_gateway' ),
                'type'	=> 'text',
                'desc_tip'	=> __( 'Provided by OnEMI to uniquely identify the merchant.', 'onemi_payment_gateway' ),
            ),
            'MerchantPass' => array(
                'title'	=> __( 'Merchant Password', 'onemi_payment_gateway' ),
                'type'	=> 'password',
                'desc_tip'	=> __( 'Provided by OnEMI to validate the merchant.', 'onemi_payment_gateway' ),
            ),
            'AccessKey' => array(
                'title'	=> __( 'Access Key ', 'onemi_payment_gateway' ),
                'type'	=> 'text',
                'desc_tip'	=> __( 'Provided by OnEMI to create Signature.', 'onemi_payment_gateway' ),
            ),
            'SecretKey' => array(
                'title'	=> __( 'Secret Key', 'onemi_payment_gateway' ),
                'type'	=> 'text',
                'desc_tip'=> __( 'Provided by OnEMI to create Signature.', 'onemi_payment_gateway' ),
            ),
            'environment' => array(
                'title'	=> __( 'OnEMI Test Mode', 'onemi_payment_gateway' ),
                'label'	=> __( 'Enable Test Mode', 'onemi_payment_gateway' ),
                'type'	=> 'checkbox',
                'description' => __( 'Place the payment gateway in test mode.', 'onemi_payment_gateway' ),
                'default'	=> 'no',
            )
	);		
    }


    /**
     * Receipt Page
     **/
    public function receipt_page($order){
        //echo '<p>'.__('Thank you for your order, please click the button below to pay with OnEmi.', 'onemi_payment_gateway').'</p>';
        echo $this->generate_onemi_form($order);
    }
    
    public function generate_onemi_form($order_id){

        global $woocommerce;
        global $current_user;
        $customer_order = new WC_Order( $order_id );
      
        
        set_include_path('lib'.PATH_SEPARATOR.get_include_path());
        require_once ABSPATH.'/wp-content/plugins/woocommerce-onemi-gateway/lib/Zend/Crypt/Hmac.php';
	
	// Are we testing right now or is it a real transaction
	$environment = ( $this->environment == "yes" ) ? 'TRUE' : 'FALSE';

	// Decide which URL to post to
	$environment_url = ( "FALSE" == $environment ) ? 'https://secure.authorize.net/gateway/transact.dll' : 'http://test.onemi.in/pg/PaySub.aspx';
        
        $customer_order_id = $customer_order->id;
        
        $arr = str_split(strval(time()), 5);
	$MerTranId = $arr[0].mt_rand(10000, 99999).$arr[1];
        
	$data = "loginacceskey=".$this->AccessKey."&transactionid=".$MerTranId."&amount=".$customer_order->order_total;
        
        $customer_name =  $current_user->user_login;
        $responseurl = site_url().'/wc-api/onemi_payment_gateway';
        $securitySignature = Zend_Crypt_Hmac::compute($this->SecretKey, 'sha1', $data);
		
	//$concat = $this->MerchantId."|".$this->MerchantPass."|".$customer_order->order_total."|".$customer_order_id."|".$customer_name."|".$responseurl;
	//$checksum = md5($concat);
        
        $mobile = (($customer_order->billing_phone)?$customer_order->billing_phone:get_the_author_meta('mobile'));
        
        // This is where the fun stuff begins
	$payload = array(
            'MerchantId'    => $this->MerchantId,
            'MerchantPass'  => $this->MerchantPass,
            'TranCur'       => 'INR',
            'Amt'           => $customer_order->order_total,
            'MerOrdRefNo'   => $customer_order_id,
            "ResponseUrl"   => $responseurl,
            "Cname"         => $customer_name,
            "EmailId"       => $current_user->user_email,
            "Mobile"        => (($mobile)?$mobile:'8939493943'),
            "MerTranId"     => $MerTranId,
            "Signature"     => $securitySignature
        );
        $this->addPaymentTracking($payload);
        
        $payu_args_array = array();
        foreach($payload as $key => $value){
          $payu_args_array[] = "<input type='hidden' name='$key' value='$value'/>";
        }
        $output ='<form action="'.$environment_url.'" method="post" id="onemi_payment_form">' . implode('', $payu_args_array)
                .'<input type="submit" class="button-alt" id="submit_onemi_payment_form" value="'.__('Pay via OnEmi', 'onemi_payment_gateway').'" /> '
                .'<a class="button cancel" href="">'.__('Cancel order &amp; restore cart', 'onemi_payment_gateway').'</a>'
                . '</form>';
        return $output;
    }
    
    function isExistPaymentTracking($MerOrdRefNo) {
        global $wpdb;
        
        return $wpdb->get_var( $wpdb->prepare("SELECT orderNo FROM custTbl_payments WHERE orderNo = %s", $MerOrdRefNo) );
    }
    
    function addPaymentTracking($payload) {
        global $wpdb;
        
        $paymentMethod = 'OnEMI';
        
        $time = time();
        if(!$this->isExistPaymentTracking($payload['MerOrdRefNo'])) {
            $wpdb->query(
                $wpdb->prepare("INSERT INTO custTbl_payments 
		( orderNo, paymentMethod, transactionAmt, paymentProviderSignature, created, modified)
		VALUES ( %d, %s, %d, %s, %d, %d)", 
                array($payload['MerOrdRefNo'], $paymentMethod, $payload['Amt'], $payload['Signature'], $time, $time)
                )
            );
        }
        
    }
    
    function updatePaymentTracking($payload) {
        global $wpdb;
        
        $bankname = array('00' => 'Other', '01' => 'AMEX', '02' => 'Axis Bank', '03' => 'Citibank', '04' => 'HDFC Bank', '05' => 'HSBC',
                          '06' => 'ICICI Bank','07' => 'IndusInd', '08' => 'Kotak Mahindra', '09' => 'RBL', '10' => 'SBI',
                          '11' => 'Standard Chartered');
        $tranchannelName = array('CC' => 'Credit Card', 'DC' => 'Debit Card', 'NB' => 'Net banking', 'LN' => 'Loan');
        $tenureMonth = array('00' => 0, '01' => 3, '02' => 6, '03' => 9, '04' => 12, '05' => 15, '06' => 18,'07' => 24);
        $errorName = array('00' => 'No Errors', '0' => 'No Errors', '01' => 'Missing Mandatory Parameters', '02' => 'Invalid Merchant', '03' => 'Invalid Channel', 
                           '04' => 'Invalid Bank Code', '05' => 'Invalid Tenure', '06' => 'Invalid Promo code', 
                           '07' => 'Signature Mismatch', '08' => 'Invalid Amount', '09' => 'Invalid Payment Option');
        $responseName = array(101 => 'Invalid Credentials', 102 => 'Success', 103 => 'Failed', 104 => 'Cancelled', 105 => 'Loan Applied', 106 => 'Loan Applied');
        
        
        $wpdb->query(
            $wpdb->prepare("UPDATE custTbl_payments SET 
                paymentProviderTranid = %d,  bankTransactionId = %d, bankCode = %s, bankName = %s,
                pgRefno = %d,  pgRefid = %s, paidAmt = %d, requestPostedDate = %s, transactionChannelCode = %s,
                transactionChannelName = %s, tenureCode = %s, tenureMonths = %d, pgAuthCode = %s,
                errorCode = %d, errorName = %s, responseCode = %d, responseName = %s, status = 1, modified = %d
                WHERE orderNo = %d AND status = 2", 
                array(
                    $payload['onemitranid'],$payload['transactionid'], $payload['bankcode'], $bankname[$payload['bankcode']],
                    $payload['pgrefno'], $payload['refid'], $payload['paidamt'], $payload['postdate'], $payload['tranchannel'],
                    $tranchannelName[$payload['tranchannel']], $payload['tenure'], $tenureMonth[$payload['tenure']], $payload['authcode'],
                    $payload['errorcode'], $errorName[$payload['errorcode']], $payload['responsecode'], $responseName[$payload['responsecode']], time(),
                    $payload['merordrefno']
                )
            )
        );
        
    }
    
    /**
     * Process the payment and return the result
     **/
    function process_payment($order_id){
        
        global $woocommerce;
        
        $order = new WC_Order($order_id);
        
        return array(
            'result' 	=> 'success',
            'redirect'	=> $order->get_checkout_payment_url( true )
        );
    }
    
    function check_onemi_response() {
        
        //Generated and passed by Merchant
        $merordrefno = filter_input(INPUT_POST, 'merordrefno');//Same order reference number passed by merchant.
        $tranchannel = filter_input(INPUT_POST, 'tranchannel'); //This is passed to merchant to identify the payment method used to process the payment
        
        //Generated by OnEMI
        $onemitranid = filter_input(INPUT_POST, 'onemitranid'); //Unique transaction ID generated by OnEMI
        $responsecode = filter_input(INPUT_POST, 'responsecode'); //Code generated by OnEMI to denote transaction status
        $signature = filter_input(INPUT_POST, 'signature'); //Generated by OnEMI to help the merchant validate the transaction
        $errorcode = filter_input(INPUT_POST, 'errorcode');//Code generated by OnEMI to identify any error in the transaction
        
        //Generated by Bank
        $transactionid = filter_input(INPUT_POST, 'transactionid'); //Bank generated transaction ID unique for every transaction
        $bankcode = filter_input(INPUT_POST, 'bankcode'); //This is passed to merchant to identify the bank selected by customer
        $tenure = filter_input(INPUT_POST, 'tenure'); //This is passed to merchant to identify the tenure selected by customer
        
        //Generated by Payment Gateway
        $pgrefno = filter_input(INPUT_POST, 'pgrefno'); //PG generated reference number
        $authcode = filter_input(INPUT_POST, 'authcode'); //PG generated authentication code
        $refid = filter_input(INPUT_POST, 'refid'); //PG generated reference ID
        
        
        
        $tranamt = filter_input(INPUT_POST, 'tranamt');//Total amount processed for payment
        $paidamt = filter_input(INPUT_POST, 'paidamt');//Total amount paid by customer
        $postdate = filter_input(INPUT_POST, 'postdate'); //Request Posted Date
        
        
        if($merordrefno) {
            
            
            global $woocommerce;
        
            $order = new WC_Order($merordrefno);
            
            if($order -> status !== 'completed'){
                
                if($responsecode === 102 || $responsecode === 105) {
                    // Payment has been successful
                    $order->add_order_note( __( 'Thank you for shopping with us. Your transaction is successful through OnEMI. We will be shipping your order to you soon.', 'onemi_payment_gateway' ) );

                    // Mark order as Paid
                    $order->payment_complete();
                    $woocommerce->cart->empty_cart();
                }
                else if($responsecode === 101 || $responsecode === 103 || $responsecode === 104 || $responsecode === 106) {
                     $order -> update_status('failed');
                     $order->add_order_note( __( 'Thank you for shopping with us. However, the transaction has been declined.', 'onemi_payment_gateway' ) );
                }   
            }
        
            $payload = array(
                'merordrefno'   => $merordrefno,
                'tranchannel'   => $tranchannel,
                'onemitranid'   => $onemitranid,
                'responsecode'  => $responsecode,
                'signature'     => $signature,
                'errorcode'     => $errorcode,
                "transactionid" => $transactionid,
                "bankcode"      => $bankcode,
                "tenure"        => $tenure,
                "pgrefno"       => $pgrefno,
                "authcode"      => $authcode,
                "refid"         => $refid,
                'paidamt'       => $paidamt,
                'postdate'      => $postdate
            );
            $this->updatePaymentTracking($payload);
            wp_redirect($this->get_return_url( $order ));
            exit();
        }
    }
}