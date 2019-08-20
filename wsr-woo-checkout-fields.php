<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*
Plugin Name: WSR Woo Checkout Fields
Plugin URI: http://websector.com.au
Description: Adds textbox, checkbox & select fields to woo commerce
Author: WSR
Version: 1
Author URI: http://websector.com.au

Woo checkout field documentation 
https://docs.woocommerce.com/document/tutorial-customising-checkout-fields-using-actions-and-filters/
*/
class WSR_woo_checkout_fields{
	var $settings;

/************************************************************************
	Simply Edit these variables.  Empty key string removes field */	

	//textbox
	var $wsr_textbox = 'Text box key';
	var $wsr_textbox_label = 'Enter text here';
	
	//Checkbox
	var $wsr_checkbox = '';
	var $wsr_checkbox_label = '';

	//Select
	var $wsr_select = 'Select box key';
	var $wsr_select_label = 'Make selection';
	var $wsr_select_options = array(
			'no' => 'No',
			'yes'  => 'Yes'
		);

//**********************************************************************

	function __construct() {

		//plugin settings
		$this->settings = array(
			'path'		=>  plugins_url() . '/ws-woo-checkout-fields'
		);

		//add checkbox field
		add_filter( 'woocommerce_checkout_fields' , array($this, 'wsr_custom_checkout_fields'), 20 );
		//Change order or make required default checkout fields
		add_filter( 'woocommerce_checkout_fields' , array($this, 'wsr_default_checkout_fields_order'), 20 );
		//process the checkout
		add_action('woocommerce_checkout_process', array($this, 'wsr_custom_checkout_field_process'));
		//update order meta
		add_action('woocommerce_checkout_update_order_meta', array($this, 'wsr_custom_checkout_field_update_order_meta'));
		//update user meta
		add_action('woocommerce_checkout_update_user_meta', array($this, 'wsr_custom_checkout_field_update_user_meta'));
		//add fields to email
		add_filter('woocommerce_email_order_meta_keys', array($this, 'wsr_custom_order_meta_keys'));
	}


	/*******************************************
	 * Add new checkout fields
	 *
	 */
	function wsr_custom_checkout_fields( $fields ) {
		if ($this->wsr_textbox){
			$fields['billing']['wsr_textbox'] = array(
	        	    'label'    	        => $this->wsr_textbox_label,
			    'placeholder'       => $this->wsr_textbox_label,
			    'required'  	=> true,
			    'priority'		=> 120,
			    'class'     	=> array('form-row-wide'),
			    'default' 		=> $fields->get_value( 'wsr_textbox' )
			);
		}	

		if ($this->wsr_checkbox){
			$fields['billing']['wsr_checkbox'] = array(
				'type' 		=> 'checkbox',
	        	'label'     => $this->wsr_checkbox_label,
			    'required'  => true,
			    'priority'	=> 130,
			    'class'     => array('input-checkbox'),
			    'default'	=> $fields->get_value( 'wsr_checkbox' ),
			);
		}	

		if ($this->wsr_select){
			$fields['billing']['wsr_select'] = array(
				'type' 		=> 'select',
	        	'label'     => $this->wsr_select_label,
			    'required'  => true,
			    'priority'	=> 140,
			    'class'     => array('form-row-wide'),
			    'options' 	=> $this->wsr_select_options,
			    'default'	=> $fields->get_value( 'wsr_select' ),
			);
		}	

		return $fields;
	}



	/*******************************************
	 * Change the order of the checkout fields with priority
	 * Make fields required or not. 
	 *
	 */
	function wsr_default_checkout_fields_order($fields){
		//$fields['billing']['billing_phone']['priority'] = 130;
		//$fields['billing']['billing_phone']['required'] = false;
		//return $fields
	}



	/*******************************************
	 * Error checking on fields after submission
	 *
	 */
	function wsr_custom_checkout_field_process() {
	    
	    if ($this->wsr_textbox){
		    if (!$_POST['wsr_textbox'])
		        wc_add_notice( __( 'Please enter required fields' ), 'error' );
		}

	    if ($this->wsr_checkbox){
		    if (!$_POST['wsr_checkbox'])
		        wc_add_notice( __( 'Please check required fields' ), 'error' );
		}

		if ($this->wsr_select){
		    if (!$_POST['wsr_select'])
		        wc_add_notice( __( 'Please select required fields' ), 'error' );
		}
	}



	/*******************************************
	 * Update checkout field post meta on submit
	 *
	 */
	function wsr_custom_checkout_field_update_order_meta( $order_id ) {
		if ($this->wsr_textbox){
    		if ($_POST['wsr_textbox']) update_post_meta( $order_id, $this->wsr_textbox, esc_attr($_POST['wsr_textbox']));
    	}

		if ($this->wsr_checkbox){
    		if ($_POST['wsr_checkbox']) update_post_meta( $order_id, $this->wsr_checkbox, esc_attr($_POST['wsr_checkbox']));
    	}

    	if ($this->wsr_select){
    		if ($_POST['wsr_select']) update_post_meta( $order_id, $this->wsr_select, esc_attr($_POST['wsr_select']));
    	}

    	//if you want to save to useer meta:
    	if ($this->wsr_textbox){
    		if ($_POST['wsr_order_textbox']){
	    		$theorder = new WC_Order( $order_id );
	    		$customer_id = $theorder->get_user_id();
	    		update_user_meta( $customer_id, 'custom_field', esc_attr($_POST['wsr_order_textbox']) );
	    	}
    	}
	}



	/*******************************************
	 * Saves the field to the user meta if required
	 *
	 */
	function wsr_custom_checkout_field_update_user_meta( $user_id) {
		if ($this->wsr_textbox){
			if ($user_id && $_POST['wsr_textbox']) update_user_meta( $user_id, 'wsr_textbox_meta', esc_attr($_POST['wsr_textbox']));
	    }

	    if ($this->wsr_checkbox){
			if ($user_id && $_POST['wsr_checkbox']) update_user_meta( $user_id, 'wsr_checkbox_meta', esc_attr($_POST['wsr_checkbox']));
	    }

	    if ($this->wsr_select){
			if ($user_id && $_POST['wsr_select']) update_user_meta( $user_id, 'wsr_select_meta', esc_attr($_POST['wsr_select']));
	    }
	}



	function wsr_custom_order_meta_keys( $keys ) {
	     if ($this->wsr_textbox){
	     	$keys[] = $this->wsr_textbox;
	     }

	     if ($this->wsr_checkbox){
	     	$keys[] = $this->wsr_checkbox;
	     }

	     if ($this->wsr_select){
	     	$keys[] = $this->wsr_select;
	     }

	     return $keys;
	}	

}

$wsr_bootstrap = new WSR_woo_checkout_fields();
