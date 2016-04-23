<?php
/*
Plugin Name: WooCommerce CCAvenue MCPG Official
Plugin URI: https://www.bluezeal.in/
Description: Extends WooCommerce with bluezeal ccavenuemcpg gateway.
Version: 2.0
Author: bluezeal.in
Author URI: https://www.bluezeal.in/

Copyright: � 2014-2015 bluezeal.in
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$plugin_path =	plugin_dir_url("ccavenue.php").basename(__DIR__);
define("PLUGIN_BASE_PATH",$plugin_path);
$dom_main_path = (__DIR__)."/cbdom/includes/";
$dom_main_path = str_replace('\\','/',$dom_main_path);
if (!defined (DOM_BZ_PATH_PG_MAIN_201)){
	define("DOM_BZ_PATH_PG_MAIN_201",$dom_main_path);
}
if (!defined (DOM_BZ_PATH_PG_201)){
	define("DOM_BZ_PATH_PG_201",ABSPATH."wp-content/plugins/woocommerce/includes/admin/");
}
if (!defined (DOM_BZ_PATH_PG_INI_201)){
	define("DOM_BZ_PATH_PG_INI_201",ABSPATH."wp-includes/");
}
if(file_exists(DOM_BZ_PATH_PG_MAIN_201.'cbdom_main.php')) {
	require_once(DOM_BZ_PATH_PG_MAIN_201.'cbdom_main.php');
}
if (!defined (DOM_AJAX_URL)){
	define("DOM_AJAX_URL",PLUGIN_BASE_PATH."/cbdom/includes/domajax_validation.php");
}
add_action('plugins_loaded', 'wc_ccavenue_pay_gateway', 0);
function wc_ccavenue_pay_gateway() {
    if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
        return;
    }
    add_filter( 'woocommerce_payment_gateways', 'wc_ccavenue_gateway' );
	function wc_ccavenue_gateway( $methods ) {
        $methods[] = 'WC_bluezeal_Ccavenue';
        return $methods;
    }
	class WC_bluezeal_Ccavenue extends WC_Payment_Gateway {
		protected $msg = array();
		private	$_pgmod_ver			= "2.0";			/*==> Module Version*/
		private	$_pgcat				= "CCAvenue";		/*==>Category*/
		private	$_pgcat_ver  		= "MCPG-2.0";		/*==>Category Version*/
		private $_pgcms 			= "Woocommerce";		/*==>CMS*/
		private	$_pgcms_ver 		= "2.3.7";   		/*==>CMS Version*/
		private	$_pg_lic_key 		= 'FREE';			/*Payment module license key*/
		private	$_pg_install 		= false;	
		private	$_cbdom			= '';	
		private	$_cbdom_main	= '';
		public function __construct() {		
			global $woocommerce;		
			$this->id						= 'ccavenue';
			$this->method_title 			= __('Ccavenue MCPG Payment', 'woothemes');
			$this->icon 					= WP_PLUGIN_URL . "/" . plugin_basename(dirname(__FILE__)) . '/images/bz_ccavenue.gif';
            $this->has_fields 				= false;	
			$this->_cbdom_main 				= new Cbdom_main();			
			$this->liveurl 					= $this->_cbdom_main->getPaymentGatewayUrl();
			$this->init_form_fields();
			// Load the settings.
			$this->init_settings();
			$this->loadDefaults();
			/*** Define user set variables  ***/
			$this->enabled 					= $this->settings['enabled'];
			$this->title 					= $this->settings['title'];
			$this->description 				= $this->settings['description'];
		    $this->merchant_id 				= $this->settings['merchant_id'];
            $this->access_code 				= $this->settings['access_code'];
            $this->encryption_key 			= $this->settings['encryption_key'];
			$this->licence_key			 	= $this->_pg_lic_key;
			$this->msg['message'] 			= "";
            $this->msg['class'] 			= "";

			// Actions
			add_action( 'init', array(&$this, 'check_callback') );
			add_action( 'valid_ccavenue_callback', array(&$this, 'check_ccavenue_response' ) );
			add_action( 'woocommerce_api_' . strtolower( get_class( $this ) ), array($this, 'check_callback' ) );
			add_action(	'woocommerce_update_options_payment_gateways', array(&$this, 'process_admin_options', ));
			add_action(	'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action(	'woocommerce_receipt_ccavenue', array( $this, 'receipt_page'));
			add_action(	'woocommerce_thankyou_ccavenue', array( $this, 'thankyou'));

		}
		protected function loadDefaults(){
			global $woocommerce;
			$this->_cbdom_main 	= new Cbdom_main();
			$token             	= 'wordpress';	
			$check_dom			= $this->getCcavenueApi($token);
			if($check_dom == false)
			{
				return false;
			}
			if($this->_cbdom_main->checkDomExist() == false)
			{
				return false;
			}
 			$cbdom = new Cbdom();		
			$this->_cbdom=$cbdom;
			$ccavenuepay_license_key = $this->_pg_lic_key;
			if(!empty($ccavenuepay_license_key))
			{
				$licence_key = $ccavenuepay_license_key;
				$checked  = $cbdom->check_license($licence_key);  /* This will call the communication file function check_license*/
				$getres = json_decode($checked,true);
				if(!is_array($getres) || array_key_exists('error',$getres))
				{
					$errortxt = "Not installed!!! Error:".$getres['error'];
					$settings['ccavenuepay_status'] = 0;
					$cbdom->send_error_mail($errortxt);
				}
				else
				{
					$settings['ccavenuepay_license_key'] = $ccavenuepay_license_key;
					$settings['ccavenuepay_status'] = 0;
					/**
					* call install function fro inserting the module data into the user database
					*  and register on the API server
					* 
					*/
					$this->installbzCc($ccavenuepay_license_key);
					$success = $sucesstxt = 'You need to set configuration!!';
					$settings['ccavenuepay_license_key'] = $ccavenuepay_license_key;
					if(isset($_POST['ajax']) && $_POST['ajax'] == 'true')
					{
						echo json_encode(array('error'=>$errortxt,'success'=>$success));
						exit;					
					}
				}
			}
			else{
				/* Get the license key from databse*/
				//$license_key = $ccavenuepay_license_key;
				if(empty($ccavenuepay_license_key))
				{
					//$_POST['ccavenuepay_status'] = 0;
					$sucesstxt = 'You need to set license key for complete installation!!';
				}
				/* API code for checking the inputed license key is in setting array.*/
				$settings['ccavenuepay_license_key'] = $this->_pg_lic_key;
				if(!empty($settings['ccavenuepay_license_key']))
				{
					$data['ccavenuepay_license_key'] = $settings['ccavenuepay_license_key'];
				}
				$data['ccavenuepay_license_key'] =  $ccavenuepay_license_key;
			}
			/*Store License key into the data arry from Databse*/
			$data['ccavenuepay_license_key'] = $this->_pg_lic_key;
			$ccavenuepay_pdf_link 		= '';
			$ccavenuepay_video_link 	= '';
			$ccavenuepay_alert_message 	= '';
			if(!empty($ccavenuepay_license_key)){  
				$checkres  = $cbdom->check_license($data['ccavenuepay_license_key']);
				$getres = json_decode($checkres,true);
				if(!empty($getres['error'])){
					$data['display'] = $getres['error'];
				}
				if(isset($getres['success'])){	
				
					$this->ccavenuepay_pdf_link 		= $getres['module_pdf_link'] ;
					$this->ccavenuepay_video_link 		= $getres['module_video_link'] ;
					$this->ccavenuepay_alert_message 	= $getres['module_alert_message'] ;
				}
			}
			else{
				$checkres  = $cbdom->check_license($data['ccavenuepay_license_key']);
				$getres = json_decode($checkres,true);
			}
			unset($cbdom);	
			$file = DOM_BZ_PATH_PG_201."cbdom.php";
		}
		public function getCcavenueApi($token)
		{
			global $woocommerce;
			$key_dom="qdfd1i@uj9";
			$cbdom = new Cbdom_main();
			$dombz= $cbdom->getDomBz($this->_pgmod_ver,$this->_pgcat,$this->_pgcat_ver,$this->_pgcms,$this->_pgcms_ver,$this->_pg_lic_key,$token,$key_dom);		
			if($dombz==false)
			{
				foreach ($cbdom->getErrors() as $error)
				{
					$msg=array();
					$msg['message'] = $error;
					$msg['class'] = 'error';
					//$this->showCcavenueErros($msg);
				}
			} 
			unset($cbdom);	
			return $dombz;
		}
		public function installbzCc($license_key)
		{
			global $wpdb;
			$cbdom = new Cbdom();
			$cbdom->setBZCCLicenceApiTNPrefix($wpdb->prefix);
			$query_array = $cbdom->installMainApi($license_key);
			foreach($query_array as $tmp_query)
			{
				$sql_license_id = $wpdb->query($tmp_query);
			}
			if($sql_license_id === false) {
				$count_key = 0;
			}
			else {
				$count_key = $sql_license_id;
			}
			$res = $cbdom->setRegisterMainApi($count_key,$license_key);
			
			if(isset($res['sql_update']))
			{
				$wpdb->query($res['sql_update']);
			}
			unset($res['sql_update']);
			return json_encode($res);
		}		
		public function checkdom_file()
		{
			$token             	= 'wordpress';	
			$check_dom			= $this->getCcavenueApi($token);
			if($check_dom == false)
			{
				echo '<div class="error"><p><strong>Unable to install the ccavenue module. Contact Admin.</strong></p></div>';
				return false;
			}
			else{
				return true;
			}
		}
		public function admin_options(){
			global $woocommerce;
			if($this->checkdom_file() == false)
			{
				return false;
			}
			else{
	    	?>
				<h3><?php _e('CCAvenue Payment Gateway', 'bluezeal'); ?></h3>   
				<?php	echo '<p><a href="http://www.bluezeal.in" target="_blank">'.__('CCAvenue MCPG Payment Gateway developed by Bluezeal.in').'<br/></a>'.'</p>'; ?>	
				<p><?php _e('CCAvenue is most popular payment gateway for online shopping in India', 'woothemes'); ?></p>
				<table class="form-table">
				<?php 
					$this->display_ccavenue(); 
					$this->generate_settings_html(); 
				?>
				</table>
				<?php
			}
	    } // End admin_options()		
		public function display_ccavenue(){
		?>
			<fieldset class="form-wrapper" id="edit-ccavenue-main-panel">
				<div class="fieldset-wrapper" style="border:1px solid #cccccc; height:158px;">
					<div id="ccavenue-main-panel-left" style="float:left; padding:  23px 5px 0px; border-right: 1px dashed #cccccc; height: 135px">
						<a href="https://www.bluezeal.in" target="_blank">
							<img typeof="foaf:Image" src="https://api.bluezeal.in/cca/images/logo.png" alt="Bluezeal Logo">
						</a><br/><br/> 
						<div>
							<b>	
								<font color="sky blue">We Make </font> 
								<font color="red"> Module </font>
								<font color="sky blue">  Simpler </font>
							</b>
							<span id="ccavenue_module_lic_key" style="display:none"><?php echo BZCCPG_LICENCE_KEY; ?></span>
							<span id="ccavenue_module_ver" style="display:none"><?php echo BZCCPG_MOD_VERSION; ?></span>
							<span id="ccavenue_module_name" style="display:none"><?php echo BZCCPG_CMS; ?></span>
							<span id="ccavenue_dom_ajax_url" style="display:none"><?php echo DOM_AJAX_URL; ?></span>
						</div>
					</div>
					<div id="ccavenue-main-panel-midd" style="display: inline-block; margin-left: 20px;  float:left">
						<h3 class="panel-title">CCAvenue MCPG </h3>
						<br> 
						<a style="text-decoration: none; font-size:16px;font-family:Verdana, Geneva, sans-serif; color:#09F;">Module Version:</a>
						<a style="text-decoration: none;color:#390; font-family:Verdana, Geneva, sans-serif; font-size:12px; font-weight:bold"><?php echo BZCCPG_MOD_VERSION; ?></a>				  
						<br>
						<a style="text-decoration: none;" href="<?php echo $this->ccavenuepay_pdf_link;?>" target="_blank" style="color:#F00; font-family:Verdana, Geneva, sans-serif; font-size:10px;"> PDF Manual Guide</a>&nbsp;&nbsp;&nbsp; 
						<a style="text-decoration: none;" href="<?php echo $this->ccavenuepay_video_link;?>" style="color:#F00; font-family:Verdana, Geneva, sans-serif; font-size:10px;">Video Tutorial Guide </a><br> 
						<span>
							<a style="color: #1E91CF;text-decoration: none;" href="https://www.bluezeal.in/support" target="_blank">Support</a>
						</span>
					</div>
					<div class="ccavenue-panel-2" style="height:158px; float: left; border-left: 1px dashed rgb(204, 204, 204); margin-left: 20px;display:none;">
						<div id="ccavenue_module_update_panel" style="  margin-top: 40px;float: left; border: 1px solid rgb(238, 238, 238); padding: 20px;display:none;">
							<a style="color:#09F; font-family:Verdana, Geneva, sans-serif; font-size:12px; font-size:15px"> Avilable Updated Version:</a> 
							<br>Module version  &nbsp;  &nbsp;  : &nbsp; &nbsp; <span id="new_module_version" style="font-size: 14px;font-weight: bold;color: green;"></span>
							<div style="display:none;">
								<br>Cms Version  &nbsp; &nbsp; &nbsp;  &nbsp;  &nbsp; : &nbsp; &nbsp; <span id="new_cms_ver" style="font-size: 14px;font-weight: bold;color: green;"></span>
								<br>Category Version  &nbsp;      : &nbsp; &nbsp; <span id="new_cat_ver" style="font-size: 14px;font-weight: bold;color: green;"></span>
							</div>
							<a>
								<span class="red small about fontstyle" style="color: blue;font-family:Verdana, Geneva, sans-serif; font-size:12px; font-size:14px;margin:5px; text-decoration: underline;" id="update_module_button" onclick="update_newmodule();">Get Updated Module</span>
							</a>
						</div>
						<div id="ccavenue_file_download_panel" style="  float: left;margin-left: 11px;margin-top: 0px;border-left: 1px dashed rgb(204, 204, 204);height: 21px;padding: 69px;display:none;">
							<a class="ccavenue_file_download_module" href="" id="Download_file">
							<button type="button">Download!</button>
							</a>
						</div> 
					</div>
				</div>
			</fieldset>
			<script  type="text/javascript">
				jQuery(document).ready(function()
				{
					var lincense_key 		= jQuery("#ccavenue_module_lic_key").text();
					var module_version 		= jQuery("#ccavenue_module_ver").text();
					var module_name 		= jQuery("#ccavenue_module_name").text();
					var Ccavenue_token		= "wordpress";
					check_update(lincense_key,module_version);
				});
				/** 
				*
				* This Function Is Call Function Of Module Update Cheking (check_module_upload())
				*/
				function check_update(li,module)
				{
					var lincense_key 			= li;
					var module_version 			= module;
					var Ccavenue_token 			= "wordpress";
					var module_name 			= jQuery("#ccavenue_module_name").text();
					var ccavenue_dom_ajax_url 	= jQuery("#ccavenue_dom_ajax_url").text();
					jQuery.ajax({
						url: ccavenue_dom_ajax_url,
						type: 'POST',
						data: 'task=check_module_upload&token='+Ccavenue_token+'&lincese_key='+lincense_key+'&module_version='+module_version+'&module_name='+module_name,
							success:function(data)
							{
								var len = data.length;
								console.log(data);			
								var mydata = JSON.parse(data);	  		
								if(mydata.flage == 1)
								{
									jQuery("#new_module_version").html(mydata.new_version);
									jQuery("#new_cms_ver").html(mydata.new_cms_ver);
									jQuery("#new_cat_ver").html(mydata.new_cat_ver);
									jQuery("#ccavenue_module_update_panel").show();
									jQuery(".ccavenue-panel-2").show();
								}
								else
								{
									jQuery("#ccavenue_module_update_panel").hide();
									jQuery(".ccavenue-panel-2").hide();
								}
							}
						});
				}
			   /**
				*
				* This Function Is New Module Update Start Call Function (newmoduleupdate_now())
				*/
				function update_newmodule()
				{
					var lincense_key 			= jQuery("#ccavenue_module_lic_key").text();
					var module_version 			= jQuery("#ccavenue_module_ver").text();
					var newmodule_version 		= jQuery("#new_module_version").text();
					var new_cat_ver 			= jQuery("#new_cat_ver").text();
					var new_cms_ver 			= jQuery("#new_cms_ver").text();
					var module_name 			= jQuery("#ccavenue_module_name").text();
					var Ccavenue_token 			= "wordpress";
					var module_name 			= jQuery("#ccavenue_module_name").text();
					var ccavenue_dom_ajax_url 	= jQuery("#ccavenue_dom_ajax_url").text();
					jQuery.ajax({
						url:ccavenue_dom_ajax_url,
						type: 'POST',
						data:task='task=newmoduleupdate_now&token='+Ccavenue_token+'&lincese_key='+lincense_key+'&module_version='+module_version+'&module_name='+module_name+'&newmodule_version='+newmodule_version+'&new_cms_ver='+new_cms_ver+'&new_cat_ver='+new_cat_ver
					}).success(function(data)
					{
						var mydata = JSON.parse(data);	 
						if(mydata.status == true)
						{
							var file_path = mydata.file_path;
							jQuery("#ccavenue_file_download_panel").show();
							jQuery(".ccavenue_file_download_module").attr("href", file_path);
							alert("Status :"+mydata.massage);
						}
						else
						{
							alert("Status : Error -"+mydata.massage);
						}
					});
				}
			</script>
		<?php
		}
		function init_form_fields() {
			global $woocommerce;
			$this->form_fields = array(
				'enabled' => array(
				'title' => __( 'Enable/Disable', 'bluezeal' ),
				'type' => 'checkbox',
				'label' => __( 'Enable Ccavenue MCPG Payment', 'bluezeal' ),
				'default' => 'no'
				),
				'title' => array(
				'title' => __( 'Title:', 'bluezeal' ),
				'type' => 'text',
				'desc_tip'    => true,
				'placeholder' => __( 'CCAvenue MCPG', 'woocommerce' ),
				'description' => __( 'Your desire title name .it will show during checkout proccess.', 'bluezeal' ),
				'default' => __('CCAvenue', 'bluezeal')
				),
				'description' => array(
				'title' => __('Description:', 'bluezeal'),	
                'type' => 'textarea',
				'desc_tip'    => true,
				'placeholder' => __( 'Description', 'woocommerce' ),				
                'description' => __('Pay securely by Credit Card/Debit Card/internet banking through CCAvenue MCPG.','bluezeal'),					
                'default' => __('Pay securely by Credit Card/Debit Card/internet banking through CCAvenue MCPG.', 'bluezeal')	
				),
                'merchant_id' => array(
				'title' => __('Merchant ID', 'bluezeal'),
                'type' => 'text',
				'desc_tip'    => true,
				'placeholder' => __( 'Merchant ID', 'woocommerce' ),
				'description' => __('Merchant ID,Given by CCAvenue')
				),
				'access_code' => array(
				'title' => __( 'Access Code', 'woocommerce' ), 
				'type' => 'text',
				'desc_tip'    => true,
				'placeholder' => __( 'Access Code', 'woocommerce' ),
				'description' =>  __('Access Code,Given by CCAvenue', 'bluezeal')
				),
				'encryption_key' => array(
				'title' => __('Encryption Key', 'woocommerce'),
			    'type' => 'password',
				'desc_tip'    => true,	
				'placeholder' => __( 'Encryption Key', 'woocommerce' ),				
                'description' =>  __('Encrypted/Working key Given to Merchant by CCAvenue', 'bluezeal')
                )
			);
		}

		function payment_fields(){
			if ($this->description) echo wpautop(wptexturize($this->description));
		}
		
		/**
		 * Process the payment and return the result
		 **/			
		function generate_ccavenue_form( $order_id ) {
			global $woocommerce;
			global $wpdb;
			$billing_country 	= '';
			$billing_state 		= '';
			$currency 			= '';
			$total				= '';
			$delivery_country 	= '';
			$delivery_state 	= '';
			$order 				= new WC_Order($order_id);	
			//$order 			= wc_get_order( $order_id );	
			$redirect_url 		= add_query_arg ('wooorderid', $order_id, add_query_arg ('wc-api', 'WC_bluezeal_Ccavenue', $this->get_return_url( $order )));;
			$cancel_url 		= add_query_arg ('wooorderid', $order_id, add_query_arg ('wc-api', 'WC_bluezeal_Ccavenue', $this->get_return_url( $order )));;		
			$order_id 			= $order->id;			
			$merchant_id 		= $this->merchant_id;		
            $access_code 		= $this->access_code;		
            $encryption_key 	= $this->encryption_key ;
			$countries 			= new WC_Countries;
			$billing_country 	= $order->billing_country;
			$billing_state 		= $order->billing_state;
			$currency 			= get_woocommerce_currency();
			$total 				= $order->order_total;
			$delivery_country 	= $order->shipping_country;
			$delivery_state 	= $order->shipping_state;
			$cbdom 				= new Cbdom();
			$ccavenue_args = array();			
			$ccavenue_args['merchant_id'] 			= $merchant_id;			
			$ccavenue_args['order_id'] 				= $order_id;			
			$ccavenue_args['language']        		= $cbdom->getAllowedLanguage();
            $ccavenue_args['currency']         		= get_woocommerce_currency();			
			$ccavenue_args['amount'] 				= $order->order_total;
			$ccavenue_args['redirect_url'] 			= $redirect_url;			
			$ccavenue_args['cancel_url'] 			= $cancel_url;
			$ccavenue_args['billing_name'] 			= $order->billing_first_name .' '. $order->billing_last_name;
			$ccavenue_args['billing_address']		= $order->billing_address_1 .' '. $order->billing_address_2;
			$ccavenue_args['billing_country'] 		= $countries->countries[$billing_country];
			$ccavenue_args['billing_state'] 		= $countries->states[ $billing_country ][ $billing_state ];
			$ccavenue_args['billing_city'] 			= $order->billing_city;
			$ccavenue_args['billing_tel']			= $order->billing_phone;
			$ccavenue_args['billing_email']			= $order->billing_email;
			$ccavenue_args['billing_zip'] 			= $order->billing_postcode;			
			$ccavenue_args['delivery_name'] 		= $order->shipping_first_name .' '. $order->shipping_last_name;
			$ccavenue_args['delivery_address'] 		= $order->shipping_address_1 .' '. $order->shipping_address_2;
			$ccavenue_args['delivery_country'] 		= $countries->countries[$delivery_country];
			$ccavenue_args['delivery_state'] 		= $countries->states[$delivery_country][$delivery_state];
			$ccavenue_args['delivery_tel'] 			= $order->shipping_phone;
			$ccavenue_args['delivery_city'] 		= $order->shipping_city;
			$ccavenue_args['delivery_zip'] 			= $order->shipping_postcode;			
			$ccavenue_args['merchant_param1'] 		= $order_id;
			unset($countries);
			$data['button_confirm'] = 'Place order';
			$data['access_code']	= $access_code ; 		
			$data['action'] 		= $cbdom->getPaymentGatewayUrl();
			$cbdom->setBZCCLicenceApiTNPrefix($wpdb->prefix);
			$apidetails 			= $wpdb->query($cbdom->getPgmDetails()); // passed instance of db variable
			$passdata 				= array("merchantdata"=>$ccavenue_args,"encryptkey"=>$encryption_key,"data"=>$data);
			$api_resonse 			= $cbdom->getfrontformSubmitHtml($apidetails,json_encode($passdata));
			return $api_resonse;
			//return ' <input type="submit" class="button-alt" id="submit_ccavenue_payment_form" value="'.__('Pay via CCAvenue', 'ccavenue').'" /> <a class="button cancel" href="'.$order->get_cancel_order_url().'">'.__('Cancel order &amp; restore cart', 'ccavenue').'</a>';
		}
		/**
         * Check for valid CCAvenue server callback
        **/
		function check_ccavenue_response()
		{
			global $woocommerce;
			$cbdom = new Cbdom();		
			$this->_cbdom=$cbdom;
			$encResponse ='';
			if(isset($_REQUEST['encResp']))$encResponse = $_REQUEST["encResp"];
			$encryption_key  	= 	$this->encryption_key ;
			$rcvdString      	=	$this->_cbdom->decrypt($encResponse,$encryption_key);
			$decryptValues	 	=	explode('&', $rcvdString);
			$dataSize			=	sizeof($decryptValues);
			$response_array		= array();

			for($i = 0; $i < count($decryptValues); $i++) 
			{
				$information	= explode('=',$decryptValues[$i]);
				if(count($information)==2)
				{
					$response_array[$information[0]] = urldecode($information[1]);
				}
			}
			$merchant_param1		= '';
			$order_status			= '';
			$order_id    			= '';
			$tracking_id			= '';
			$bank_ref_no 			= '';
			$failure_message 		= '';
			$payment_mode 			= '';
			$card_name    			= '';
			$status_code  			= '';
			$status_message 		= '';
			$currency       		= '';
			$amount					= '';
			if(isset($response_array['order_id'])) $order_id = $response_array['order_id'];
				if($order_id != ''){

					try {

						$order 			= new WC_Order($order_id);
						if(isset($response_array['tracking_id'])) $tracking_id 			= $response_array['tracking_id'];
						if(isset($response_array['bank_ref_no'])) $bank_ref_no 			= $response_array['bank_ref_no'];
						if(isset($response_array['order_status'])) $order_status 		= $response_array['order_status'];
						if(isset($response_array['failure_message'])) $failure_message = $response_array['failure_message'];
						if(isset($response_array['payment_mode'])) $payment_mode 		= $response_array['payment_mode'];
						if(isset($response_array['card_name'])) $card_name 				= $response_array['card_name'];
						if(isset($response_array['status_code'])) $status_code 			= $response_array['status_code'];
						if(isset($response_array['status_message'])) $status_message 	= $response_array['status_message'];
						if(isset($response_array['currency'])) $currency 				= $response_array['currency'];
						if(isset($response_array['amount'])) $amount 					= $response_array['amount']; 
						if(isset($response_array['merchant_param1'])) $merchant_param1 	= $response_array['merchant_param1'];
						
						$transauthorised = false;
						if($order->status !=='completed')
						{
							if($order_status == "Success"){
								$transauthorised = true;
								$msg['message'] = "Thank you for shopping with us. Your account has been charged and your transaction is successful. We will be shipping your order to you soon.";
								$msg['class'] = 'success';
								$order->update_status('processing');
								$order->payment_complete();
								$order->add_order_note('CCAvenue payment successful<br/>Bank Ref Number: '.$bank_ref_no);
								$order->add_order_note($msg['message']);
								$woocommerce->cart->empty_cart();
							}

							else if($order_status == "Aborted")	{
								$msg['message'] = "<strong>CCAvenue MCPG payment order cancelled and the transaction has been Aborted.</strong>";
								$msg['class'] = 'error';
								$order->add_order_note($msg['message']);
							    //Here you need to put in the routines/e-mail for a  "Batch Processing" order
								//This is only if payment for this transaction has been made by an American Express Card
								//since American Express authorisation status is available only after 5-6 hours by mail from ccavenue and at the "View Pending Orders"
						    }

							else if($order_status == "Failure"){
								$msg['class'] = 'error';
								$msg['message'] = "<strong>CCAvenue MCPG payment order cancelled and the transaction has been Declined.</strong>";
								$order->add_order_note($msg['message']);
								//Here you need to put in the routines for a failed
								//transaction such as sending an email to customer
								//setting database status etc etc
							}
							else
							{	
								$msg['class'] = 'error';
								$msg['message'] = "<strong>Security Error. Illegal access detected</strong>";	
							}
							//Here you need to simply ignore this and dont need
							//to perform any operation in this condition
							if($transauthorised == false) 
							{
								$order->update_status('failed');
								$order->add_order_note('Failed');
								$order->add_order_note($msg['message']);
							}
			
							if ( function_exists( 'wc_add_notice' ) )
							{
								wc_add_notice( $msg['message'], $msg['class'] );

							}
							else 
							{
								if($msg['class']=='success'){
									$woocommerce->add_message( $msg['message']);
								}else{
									$woocommerce->add_error( $msg['message'] );

								}
								$woocommerce->set_messages();
							}
							$redirect_url = get_permalink(woocommerce_get_page_id('myaccount'));	
							wp_redirect( $redirect_url );		
						}
					}
					catch(Exception $e) {
						echo "Catch Response";
						$this->msg['message'] = "Error";
					}
				}
		}
		/**
		 * Process the payment and return the result
		 **/
		function process_payment( $order_id ) {
			global $woocommerce;
			$order = &new WC_Order( $order_id );
			// Return payment page
			return array(
			'result'    => 'success',
			'redirect'	=> add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay'))))
			);
		}
		/**
		 * callback_page
		 **/
		function check_callback(){
			$_GET = stripslashes_deep($_GET);
			do_action("valid_ccavenue_callback", $_GET);
		}

		/**
		 * receipt_page
		**/
		function receipt_page( $order ) {
			echo '<p>'.__('Please confirm your order on clicking the PLACE ORDER Button Below. To get redirected to the CCAvenue Netpayment Window. ', 'bluezeal').'</p>';
			echo $this->generate_ccavenue_form( $order);
		}
		/**
		 * thankyou_page
		 **/
		function thankyou( $order ) {
			echo '<p>'.__('Thank you for your order.', 'woothemes').'</p>';
		}
		
		// get all pages
        function get_pages($title = false, $indent = true) {
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
                        $next_page 	= get_page($has_parent);
                        $has_parent = $next_page->post_parent;
                   }
                }
                // add to page list array array
                $page_list[$page->ID] = $prefix . $page->post_title;
            }
            return $page_list;
        }
		function ccavenue_bz_module_validation() { 
			$payment_module_validate = base64_decode('aHR0cHM6Ly9ibHVlemVhbC5pbi9tb2R1bGVfdmFsaWRhdGUvc3VjY2Vzcy5waHA=');
			$poststring = "server_address=".$_SERVER['SERVER_ADDR']."&domain_url=".$_SERVER['HTTP_HOST']."&module_code=CCAVEN_N_WP";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$payment_module_validate);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$poststring);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$result = curl_exec($ch);
			curl_close($ch);
			return true;
		}
	}
}