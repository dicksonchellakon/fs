<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$plugin_dir_url =  plugin_dir_url( __FILE__ );
?>
<style>
.premium-box {width: 100%;}
.premium-box-head {background: #eae8e7; width: 100%; height:500px; text-align: center;}
.pho-upgrade-btn {display: block; text-align: center;}
.pho-upgrade-btn a{display: inline-block;  margin-top: 75px;}
.pho-upgrade-btn a:focus {outline: none; box-shadow: none; }
.main-heading  {text-align: center; background: #fff; margin-bottom: -70px;}
.main-heading img {margin-top: -200px;}

.premium-box-container {margin: 0 auto;}
.premium-box-container .description {text-align: center; display: block; padding: 35px 0;}
.premium-box-container .description:nth-child(odd) {background: #fff;}
.premium-box-container .description:nth-child(even) {background: #eae8e7;}

.premium-box-container .pho-desc-head {width: 768px; margin: 0 auto; position: relative;}
.premium-box-container .pho-desc-head:after {background:url(<?php echo $plugin_dir_url; ?>assets/img/head-arrow.png) no-repeat;
 position: absolute; right: -30px; top: -6px; width: 69px; height: 98px; content: "";} 

.premium-box-container .pho-desc-head h2 {color: #02c277; font-weight: bolder; font-size: 28px; text-transform: capitalize;
margin: 0;}
.pho-plugin-content {margin: 0 auto; width: 768px; overflow: hidden;}
.pho-plugin-content p {line-height: 32px; font-size: 18px; color: #212121; }
.pho-plugin-content img {width: auto; max-width: 100%;}
.description .pho-plugin-content ol { margin: 0; padding-left: 25px; text-align: left;}
.description .pho-plugin-content ol li {font-size: 16px; line-height: 28px; color: #212121; padding-left: 5px;}
.description .pho-plugin-content .pho-img-bg { width: 750px; margin: 0 auto; border-radius: 5px 5px 0 0; 
padding: 70px 0 40px; height: auto;}
.premium-box-container .description:nth-child(odd) .pho-img-bg {background: #f1f1f1 url(<?php echo $plugin_dir_url; ?>assets/img/image-frame-odd.png) no-repeat 100% top;}
.premium-box-container .description:nth-child(even) .pho-img-bg {background: #f1f1f1 url(<?php echo $plugin_dir_url; ?>assets/img/image-frame-even.png) no-repeat 100% top;}

</style>

<div class="premium-box">

    <div class="premium-box-head">
        <div class="pho-upgrade-btn">
        <a href="https://www.phoeniixx.com/product/woocommerce-check-pincodezipcode-for-shipping-and-cod/" target="_blank"><img src="<?php echo $plugin_dir_url; ?>assets/img/premium-btn.png" /></a>
        </div>
    </div>
    <div class="main-heading"><h1><img src="<?php echo $plugin_dir_url; ?>assets/img/premium-head.png" /></h1></div>

        <div class="premium-box-container">
        
            <div class="description">
                <div class="pho-desc-head"><h2>Add to Cart Activation based on Pincodes</h2></div>
                
                    <div class="pho-plugin-content">
                        <p>The Add to Cart Button won´t work unless the Pincode that the user has entered is available for delivery as per your list of available pin codes.</p>
                        <div class="pho-img-bg">
                        <img src="<?php echo $plugin_dir_url; ?>assets/img/change-img.jpg" />
                        </div>
                    </div>
            </div> <!-- description end -->
            
            <div class="description">
                <div class="pho-desc-head"><h2>Saturday/Sunday Off Option</h2></div>
                
                    <div class="pho-plugin-content">
                        <p>You have the option to activate or deactivate the Saturday/Sunday off option to ensure that no delivery dates are set for Saturdays or Sundays.</p>
                        <div class="pho-img-bg">
                        <img src="<?php echo $plugin_dir_url; ?>assets/img/saturday-sunday-off.jpg" />
                        </div>
                    </div>
            </div> <!-- description end -->
            
            <div class="description">
                <div class="pho-desc-head"><h2>Bulk CSV Uploads</h2></div>
                
                    <div class="pho-plugin-content">
                        <p>With bulk CSV uploads option you can enter as many pin codes as you want into the system in one go. There will be no need to manually add each Pin Code.</p>
                        <div class="pho-img-bg">
                        <img src="<?php echo $plugin_dir_url; ?>assets/img/bulk-csv-uploads.jpg" />
                        </div>
                    </div>
            </div> <!-- description end -->
            
            <div class="description">
                <div class="pho-desc-head"><h2>COD Verification</h2></div>
                
                    <div class="pho-plugin-content">
                        <p>You have the option to choose which pin codes will support the COD option and which pin codes won´t.</p>
                        <div class="pho-img-bg">
                        <img src="<?php echo $plugin_dir_url; ?>assets/img/cod-check.jpg" />
                        </div>
                    </div>
            </div> <!-- description end -->
            
            <div class="description">
                <div class="pho-desc-head"><h2>Delivery Dates Preview</h2></div>
                
                    <div class="pho-plugin-content">
                        <p>The delivery date for the pincodes chosen by the user will appear on the Product Page, Cart Page and even the Checkout Page.</p>
                        <div class="pho-img-bg">
                        <img src="<?php echo $plugin_dir_url; ?>assets/img/delivery-date.jpg" />
                        </div>
                    </div>
            </div> <!-- description end -->
            
            <div class="description">
                <div class="pho-desc-head"><h2>Advanced Styling Options</h2></div>
                
                    <div class="pho-plugin-content">
                        <p>You will get access to many advanced styling options to create a Check Pin Code format that will gel completely with your website.</p>
                        <div class="pho-img-bg">
                        <img src="<?php echo $plugin_dir_url; ?>assets/img/style-check.jpg" />
                        </div>
                    </div>
            </div> <!-- description end -->
            
            <div class="description">
                <div class="pho-desc-head"><h2>Checkout Page Pincode Check</h2></div>
                
                    <div class="pho-plugin-content">
                        <p>Usually customers enter a different pincode on Checkout page and process their order. But with this feature the Customer cannot enter a different Pincode in the checkout step of the order process as the Pincodes will be checked on Checkout Page also.</p>
                        <div class="pho-img-bg">
                        <img src="<?php echo $plugin_dir_url; ?>assets/img/Checkout-Page-Pincode-Check.jpg" />
                        </div>
                    </div>
            </div> <!-- description end -->
        </div> <!-- premium-box-container end -->
        
        <div class="pho-upgrade-btn">
        <a href="https://www.phoeniixx.com/product/woocommerce-check-pincodezipcode-for-shipping-and-cod/" target="_blank"><img src="<?php echo $plugin_dir_url; ?>assets/img/premium-btn.png" /></a>
        </div>

</div> <!-- premium-box end -->