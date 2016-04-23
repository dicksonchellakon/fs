<?php

/*
Plugin Name: WooCommerce Paytm Payment Gateway
Plugin URI: http://paywith.paytm.com
Description: Pay With Paytm Payment gateway for woocommerce
Version: 1.0.0
Author: Paywith Paytm
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action('plugins_loaded', 'woocommerce_paywith_paytm_init', 0);


function woocommerce_paywith_paytm_init(){

    if(!class_exists('WC_Payment_Gateway')) return;

    if( isset($_GET['msg']) && !empty($_GET['msg']) ){
        add_action('the_content', 'paywith_paytm_showMessage');
    }
    function paywith_paytm_showMessage($content){
        return '<div class="'.htmlentities($_GET['type']).'">'.htmlentities(urldecode($_GET['msg'])).'</div>'.$content;
    }


    class WC_Paywith_Paytm extends WC_Payment_Gateway{

        public function __construct(){
            $this->id = 'paywithpaytm';
            $this->method_title = 'Pay With Paytm';
            $this->has_fields = false;

            $this->init_form_fields();
            $this->init_settings();

            $this -> title = $this -> settings['title'];
            $this -> description = $this -> settings['description'];
            $this -> button_id = $this -> settings['button_id'];
            $this -> button_secret = $this -> settings['button_secret'];
            $this -> redirect_page_id = $this -> settings['redirect_page_id'];

            $this-> notify_url = WC()->api_request_url( 'WC_Paywith_Paytm' );
	    $this -> configVal = json_encode(array('customer_id'=>get_current_user_id()));
            //Actions

            if ( version_compare( WOOCOMMERCE_VERSION, '2.0.0', '>=' ) ) {
                add_action( 'woocommerce_update_options_payment_gateways_'.$this->id, array(&$this, 'process_admin_options' ) );
            } else {
                add_action( 'woocommerce_update_options_payment_gateways', array(&$this, 'process_admin_options' ) );
            }

            add_action('woocommerce_receipt_'.$this->id, array(&$this, 'receipt_page'));

            //Payment Listener/API hook
            add_action('init', array(&$this, 'check_paywith_paytm_response'));
            //update for woocommerce >2.0
            add_action( 'woocommerce_api_' . strtolower( get_class( $this ) ), array( $this, 'paywith_paytm_response' ) );
        }

        function init_form_fields(){

            $this -> form_fields = array(
                'enabled' => array(
                    'title' => 'Enable/Disable',
                    'type' => 'checkbox',
                    'label' => 'Enable Pay With Paytm Payment Module.',
                    'default' => 'no'),
                'title' => array(
                    'title' => 'Title:',
                    'type'=> 'text',
                    'description' => 'This controls the title which the user sees during checkout.',
                    'default' => 'Pay With Paytm'),
                'description' => array(
                    'title' => 'Description:',
                    'type' => 'textarea',
                    'description' => 'This controls the description which the user sees during checkout.',
                    'default' => 'Pay securely by Credit or Debit card or your Paytm wallet.'),
                'button_id' => array(
                    'title' => 'Button ID',
                    'type' => 'text',
                    'description' => 'This id is the one provided by Paytm.'),
                'button_secret' => array(
                    'title' => 'Secret Key',
                    'type' => 'text',
                    'description' => 'This button secret is the one provided by Paytm.'),
                'redirect_page_id' => array(
                    'title' 		=> 'Return Page',
                    'type' 			=> 'select',
                    'options' 		=> $this->paywith_paytm_get_pages('Select Page'),
                    'description' 	=> 'URL of success page',
                    'desc_tip' 		=> true
                )
            );
        }

        public function admin_options(){
            echo '<h3>Pay With Paytm Payment Gateway</h3>';
            echo '<p>Pay With Paytm is most popular payment gateway for online shopping in India.</p>';
            echo '<table class="form-table">';
            // Generate the HTML For the settings form.
            $this -> generate_settings_html();
            echo '</table>';

        }

        function generate_paytm_form( $order_id ) {
            // code goes here
            global $woocommerce;
            $order = new WC_Order( $order_id );
            return "<form id='form' method='post' action='https://paywith.paytm.com/api/core/button/start-transaction/'>".
                    "<input type='hidden' autocomplete='off' name='button_id' value='".$this->button_id."'/>".
                    "<input type='hidden' autocomplete='off' name='muoid' value='".$order->id."'/>".
                    "<input type='hidden' autocomplete='off' name='amount' value='".$order->order_total."'/>".
                    "<input type='hidden' autocomplete='off' name='config' value='".$this->configVal."'/>".
					"<input type='hidden' autocomplete='off' name='client-name' value='wp-woo-plugin'/>".
					"<input type='hidden' autocomplete='off' name='client-version' value='1.0.1'/>".
					"<input type='hidden' autocomplete='off' name='api-version' value='v1'/>".
                   "</form>".
                   "<script type='text/javascript'>".
                        "document.getElementById('form').submit();".
                   "</script>";

        }

        function receipt_page( $order ) {
            echo '<p>Thank you ! Your order is now pending payment. You should be automatically redirected to Paytm to make payment.</p>';

            echo $this->generate_paytm_form( $order );
        }

        function process_payment( $order_id ) {

            $order = new WC_Order( $order_id );

            if ( version_compare( WOOCOMMERCE_VERSION, '2.1.0', '>=' ) ) {
                /* 2.1.0 */
                $checkout_payment_url = $order->get_checkout_payment_url( true );
            } else {
                /* 2.0.0 */
                $checkout_payment_url = get_permalink( get_option ( 'woocommerce_pay_page_id' ) );
            }

            return array(
                'result' 	=> 'success',
                'redirect'	=> $checkout_payment_url
            );

        }


        function paywith_paytm_response(){
            //error_log("TRIGGERED");
            global $woocommerce;
            if(isset($_POST["status"]) && isset($_POST["muoid"]) && isset($_POST["transaction_id"])) {

                //$statusVal = $_POST["status"];
                $orderId = $_POST["muoid"];
                $transactionId = $_POST["transaction_id"];
                $order = new WC_Order( $orderId );

                //error_log($order->status);
		//error_log("STATUS:".$statusVal);
                //if($statusVal == 0)
                //{
                   // error_log("WITHIN");
                    //get transaction status
                    $keyVal = trim($this->button_id);
                    $secretVal = trim($this->button_secret);

                    $str = $keyVal.':'.$secretVal;
                    $encodedVal = base64_encode($str);

                    //error_log($encodedVal);

                    try{
                        if( !class_exists( 'WP_Http' ) )
                            include_once( ABSPATH . WPINC. '/class-http.php' );

                        $body = array(
                            'transaction_id' => $transactionId
                        );
                        $headers = array( 'Authorization' => 'Basic '.$encodedVal);
                        $url = 'https://paywith.paytm.com/api/core/get-transaction-status/';
                        $request = new WP_Http;
                        $response = $request->request( $url, array( 'method' => 'POST', 'body' => $body, 'headers' => $headers) );

                        $obj = json_decode(wp_remote_retrieve_body($response), true);

                        //error_log("PRINTING RESPONSE");
                        //error_log($obj["data"]["status"]);

                       // $obj = json_decode($response['body'], true);

                        if($obj["data"]["status"] == "SUCCESS")
                        {
                            //$this->msg['message'] = "Thank you for shopping with us. Your account has been charged and your transaction is successful.";
			    //$this->msg['class'] = 'woocommerce-message';
                            $order->payment_complete();
                            $order->add_order_note('Paytm payment successful.<br/>ORDER ID: '.$orderId.'<br/>TRANSACTION ID: '.$transactionId);
                            $woocommerce -> cart -> empty_cart();
                            //error_log("SUCCESS");
                        }
                        elseif($obj["data"]["status"] == "PENDING"){
                            //$this->msg['message'] = "Thank you for shopping with us. Right now your payment status is pending. We will keep you posted regarding the status of your order.";
                            //$this->msg['class'] = 'woocommerce-info';
                            $order->add_order_note('Paytm payment status is pending.<br/>ORDER ID: '.$orderId.'<br/>TRANSACTION ID: '.$transactionId);
                            $order->update_status('on-hold');
                            $woocommerce -> cart -> empty_cart();
                            //error_log("PENDING");
                        }
                        else{
                            //$this->msg['class'] = 'woocommerce-error';
                            //$this->msg['message'] = "Thank you for shopping with us. However, the transaction has been declined.";
                            $order->add_order_note('Transaction ERROR.<br/>ORDER ID: '.$orderId.'<br/>TRANSACTION ID: '.$transactionId);
                            $order->update_status('failed');
                            //error_log("FAILED");
                        }

                    }
                    catch (Exception $e){
                            //error_log("EXCEPTION");
                    }
                //}

                //error_log("REDIRECT");

                //$redirect_url = ($this->redirect_page_id=="" || $this->redirect_page_id==0)?get_site_url() . "/":get_permalink($this->redirect_page_id);
                //$redirect_url = add_query_arg( array('msg'=> urlencode($this->msg['message']), 'type'=>$this->msg['class']), $redirect_url );

                //error_log($redirect_url);

               // wp_redirect( $redirect_url );
                //exit;
		//error_log("-----------------NOTIFY END -------------------------------");
		//exit;
            }
	    else{
		//error_log("----- SUCCESS/CANCEL--------");
                if(isset($_GET["status"]) && isset($_GET["muoid"]) && isset($_GET["transaction_id"]))
                {
                  if($_GET["status"] == 0)
                  {
                      $this->msg['class'] = 'woocommerce-message';
                      $this->msg['message'] = "Thank you for shopping with us. Your account has been charged and your transaction is successful.";
                  }
                  elseif($_GET["status"] == 1)
                  {

                      if( !class_exists( 'WP_Http' ) )
                          include_once( ABSPATH . WPINC. '/class-http.php' );

                      $body = array(
                          'transaction_id' => $_GET["transaction_id"]
                      );

                      $keyVal = $this->button_id;
                      $secretVal = $this->button_secret;

                      $str = $keyVal.':'.$secretVal;
                      $encodedVal = base64_encode($str);

                      $headers = array( 'Authorization' => 'Basic '.$encodedVal);
                      $url = 'https://paywith.paytm.com/api/core/get-transaction-status/';
                      $request = new WP_Http;
                      $response = $request->request( $url, array( 'method' => 'POST', 'body' => $body, 'headers' => $headers) );

                      $obj = json_decode(wp_remote_retrieve_body($response), true);

                      if($obj["data"]["status"] == "PENDING"){
                          $this->msg['message'] = "Thank you for shopping with us. Right now your payment status is pending. We will keep you posted regarding the status of your order.";
                          $this->msg['class'] = 'woocommerce-info';
                      }
                      else{
                          $this->msg['class'] = 'woocommerce-error';
                          $this->msg['message'] = "Thank you for shopping with us. However, the transaction has been declined.";
                      }

                  }

                    $redirect_url = ($this->redirect_page_id=="" || $this->redirect_page_id==0)?get_site_url() . "/":get_permalink($this->redirect_page_id);
                    $redirect_url = add_query_arg( array('msg'=> urlencode($this->msg['message']), 'type'=>$this->msg['class']), $redirect_url );

                    //error_log($redirect_url);

                    wp_redirect( $redirect_url );
                    exit;

                }
	    }   	
        }

        function paywith_paytm_get_pages($title = false, $indent = true) {
            $wp_pages = get_pages('sort_column=menu_order');
            $page_list = array();
            if ($title) $page_list[] = $title;
            foreach ($wp_pages as $page) {
                $prefix = '';
                // show indented child pages?
                if ($indent) {
                    $has_parent = $page->post_parent;
                    while($has_parent) {
                        $prefix .=  ' - ';
                        $next_page = get_post($has_parent);
                        $has_parent = $next_page->post_parent;
                    }
                }
                // add to page list array array
                $page_list[$page->ID] = $prefix . $page->post_title;
            }
            return $page_list;
        }

        // End of class
}

    /* Add the Gateway to WooCommerce */

    function woocommerce_add_paywith_paytm_gateway($methods) {
        $methods[] = 'WC_Paywith_Paytm';
        return $methods;
    }

    add_filter('woocommerce_payment_gateways','woocommerce_add_paywith_paytm_gateway');

}


?>
