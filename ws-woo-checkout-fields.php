<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * @package ws-woo-checkout-fields
 * @version 1.6
 */
/*
Plugin Name: Websector Woo Checkout Fields
Plugin URI: http://websector.com.au
Description: Adds cehckout fields to woo commerce
Author: Nathan Barnes
Version: 1
Author URI: http://websector.com.au
*/
class Ws_woo_checkout_fields{
	var $settings;
	function __construct() {
		//plugin settings
		$this->settings = array(
			'path'		=>  plugins_url() . '/ws-woo-checkout-fields'
		);

		//add checkbox field
		add_filter( 'woocommerce_after_checkout_billing_form' , array($this, 'ws_custom_checkout_fields' ));
		//process the checkout
		add_action('woocommerce_checkout_process', array($this, 'ws_custom_checkout_field_process'));
		//update order meta
		add_action('woocommerce_checkout_update_order_meta', array($this, 'ws_custom_checkout_field_update_order_meta'));
	}

	// Our hooked in function - $fields is passed via the filter!
	function ws_custom_checkout_fields( $fields ) {

		echo '<div id="ws-legal-field"><h3>'.__('Confirmation: ').'</h3>';
		woocommerce_form_field( 'ws_order_confirm', array(
			'type'			=> 'checkbox',
			'label'     	=> __('It is legal for me to receive and consume Mitra in the country I have specified for delivery.', 'woocommerce'),
	   		'required'  	=> true,
	    	'class'     	=> array('input-checkbox'),
		), $fields->get_value( 'ws_order_confirm' ));

		echo '</div>';
	}


	function ws_custom_checkout_field_process() {
	    // Check if set, if its not set add an error.
	    if (!$_POST['ws_order_confirm'])
	        wc_add_notice( __( 'Please agree to legal confirmation.' ), 'error' );
	}


	function ws_custom_checkout_field_update_order_meta( $order_id ) {
    	if ($_POST['ws_order_confirm']) update_post_meta( $order_id, 'Legal Confirmation', esc_attr($_POST['ws_order_confirm']));
	}

}

$ws_bootstrap = new Ws_woo_checkout_fields();