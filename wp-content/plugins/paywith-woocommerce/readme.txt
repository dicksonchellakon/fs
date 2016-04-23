=== Woocommerce Pay with Paytm Plugin===
Pay with Paytm is the simplest of the available ways to do payment integration to your website.

Contributors: paywithpaytm
Tags:buy now, currency, payment, paytm, payment for wordpress, paytm integration, Paytm payment, paytm plugin for wordpress, wordpress Paytm, woo commerce, woocommerce payment,payments , easy payments, easy payment plugin wordpress
Requires at least: 3.0.1
Tested up to: 3.9
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
Pay with Paytm is the simplest of the available ways to do payment integration to your website. It helps you create a no frills Payment button on your checkout page where amount can be fixed preconfigured on our servers or can be dynamic. The person who clicks the Pay with Paytm button will be taken to Pay with Paytm website where user will complete the transaction via Paytm and Pay with Paytm returns back the browser handle to the website after payment confirmation. Website will also receive a notification, which will be a server-to-server call as soon as the payment is done.

== Installation ==

* Download paywith paytm woocommerce plugin 
* Upload plugin to your wordpress blog or website
* Install and activate plugin

# Integration 
* In wordpress, Go to Woocommerce -> Settings -> Checkout
* Under Payment Gateways, Pay With Paytm should appear.
* Go to its settings.
* Enable the plugin.
* Set Title to "Pay with Paytm"

* For Button Id and Button secret:-
    * Signup/Sign In on paywithpaytm.com and fill in the profile details and bank details.
    * Create a pay button in the Catalog section. 
    * Copy button id and button secret.
    * Go to settings->edit
    * Set Success Url :- (yourbasewebsiteurl)?wc-api=WC_Paywith_Paytm
    * Set Cancel Url :- (yourbasewebsiteurl)?wc-api=WC_Paywith_Paytm
    * Set Notify Url :- (yourbasewebsiteurl)?wc-api=WC_Paywith_Paytm
    * Set button id and secret in the admin field of Woocommerce Pay with Paytm
   
* Set the redirect url where you want to redirect after payment.

== Frequently Asked Questions ==

= Whom to contact in case of any queries or any technical support ? =

Please email to care@paywithpaytm.com and we’ll try to answer your queries in shortest time possible.

 