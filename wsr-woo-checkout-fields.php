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
	var $textboxKey = 'Message on card';
	var $textboxLabel = 'Please add a message for the card';
	
	//Checkbox
	var $checkboxKey = '';
	var $checkboxLabel = '';

	//Select
	var $selectKey = 'For Funeral?';
	var $selectLabel = 'Is this order for a funeral?';
	var $selectOptions = array(
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
		if ($this->textboxKey){
			$fields['billing']['wsr_billing_textbox'] = array(
	        	'label'     	=> $this->textboxLabel
			    'placeholder'   => $this->textboxLabel
			    'required'  	=> true,
			    'priority'		=> 120,
			    'class'     	=> array('form-row-wide'),
			    'default' 		=> $fields->get_value( 'wsr_billing_textbox' )
			);
		}	

		if ($this->checkboxKey){
			$fields['billing']['wsr_billing_checkbox'] = array(
				'type' 		=> 'checkbox',
	        	'label'     => $this->checkboxLabel,
			    'required'  => true,
			    'priority'	=> 130,
			    'class'     => array('input-checkbox'),
			    'default'	=> $fields->get_value( 'wsr_billing_checkbox' ),
			);
		}	

		if ($this->selectKey){
			$fields['billing']['wsr_billing_select'] = array(
				'type' 		=> 'select',
	        	'label'     => $this->selectLabel,
			    'required'  => true,
			    'priority'	=> 140,
			    'class'     => array('form-row-wide'),
			    'options' 	=> $this->selectOptions,
			    'default'	=> $fields->get_value( 'wsr_billing_select' ),
			);
		}

	}



	/*******************************************
	 * Change the order of the checkout fields with priority
	 * Make fields required or not. 
	 *
	 */
	function wsr_default_checkout_fields_order(){
		//$fields['billing']['billing_phone']['priority'] = 130;
		//$fields['billing']['billing_phone']['required'] = false;
	}



	/*******************************************
	 * Error checking on fields after submission
	 *
	 */
	function wsr_custom_checkout_field_process() {
	    
	    if ($this->textboxKey){
		    if (!$_POST['wsr_billing_textbox'])
		        wc_add_notice( __( 'Please enter required fields' ), 'error' );
		}

	    if ($this->checkboxKey){
		    if (!$_POST['wsr_billing_checkbox'])
		        wc_add_notice( __( 'Please check required fields' ), 'error' );
		}

		if ($this->selectKey){
		    if (!$_POST['wsr_billing_select'])
		        wc_add_notice( __( 'Please select required fields' ), 'error' );
		}
	}



	/*******************************************
	 * Update checkout field post meta on submit
	 *
	 */
	function wsr_custom_checkout_field_update_order_meta( $order_id ) {
		if ($this->textboxKey){
    		if ($_POST['wsr_billing_textbox']) update_post_meta( $order_id, $this->textboxKey, esc_attr($_POST['wsr_billing_textbox']));
    	}

		if ($this->checkboxKey){
    		if ($_POST['wsr_billing_checkbox']) update_post_meta( $order_id, $this->checkboxKey, esc_attr($_POST['wsr_billing_checkbox']));
    	}

    	if ($this->selectKey){
    		if ($_POST['wsr_billing_select']) update_post_meta( $order_id, $this->selectKey, esc_attr($_POST['wsr_billing_select']));
    	}
	}



	/*******************************************
	 * Saves the field to the user meta if required
	 *
	 */
	function wsr_custom_checkout_field_update_user_meta( $user_id) {
		if ($this->textboxKey){
			if ($user_id && $_POST['wsr_billing_textbox']) update_user_meta( $user_id, 'wsr_meta_textbox', esc_attr($_POST['wsr_billing_textbox']));
	    }

	    if ($this->checkboxKey){
			if ($user_id && $_POST['wsr_billing_checkbox']) update_user_meta( $user_id, 'wsr_meta_checkbox', esc_attr($_POST['wsr_billing_checkbox']));
	    }

	    if ($this->selectKey){
			if ($user_id && $_POST['wsr_billing_select']) update_user_meta( $user_id, 'wsr_meta_selectbox', esc_attr($_POST['wsr_billing_select']));
	    }
	}



	function wsr_custom_order_meta_keys( $keys ) {
	     if ($this->textboxKey){
	     	$keys[] = $this->textboxKey;
	     }

	     if ($this->checkboxKey){
	     	$keys[] = $this->checkboxKey;
	     }

	     if ($this->selectKey){
	     	$keys[] = $this->selectKey;
	     }

	     return $keys;
	}	

}

$wsr_bootstrap = new WSR_woo_checkout_fields();