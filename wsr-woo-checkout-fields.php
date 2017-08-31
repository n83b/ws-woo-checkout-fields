<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*
Plugin Name: WSR Woo Checkout Fields
Plugin URI: http://websector.com.au
Description: Adds textbox, checkbox & select fields to woo commerce
Author: WSR
Version: 1
Author URI: http://websector.com.au
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
		add_filter( 'woocommerce_after_checkout_billing_form' , array($this, 'wsr_custom_checkout_fields' ));
		//process the checkout
		add_action('woocommerce_checkout_process', array($this, 'wsr_custom_checkout_field_process'));
		//update order meta
		add_action('woocommerce_checkout_update_order_meta', array($this, 'wsr_custom_checkout_field_update_order_meta'));
		//add fields to email
		add_filter('woocommerce_email_order_meta_keys', array($this, 'wsr_custom_order_meta_keys'));
	}

	function wsr_custom_checkout_fields( $fields ) {
		if ($this->textboxKey){
			woocommerce_form_field( 'wsr_order_textbox', array(
				'type'			=> 'text',
				'label'     	=> $this->textboxLabel,
		   		'required'  	=> true,
		    	'class'     	=> array('form-row-wide'),
		    	//'default'		=> get_user_meta( get_current_user_id(), 'preferred_contact_method', true ), 
			), $fields->get_value( 'wsr_order_textbox' ));
		}	

		if ($this->checkboxKey){
			woocommerce_form_field( 'wsr_order_checkbox', array(
				'type'			=> 'checkbox',
				'label'     	=> $this->checkboxLabel,
		   		'required'  	=> true,
		    	'class'     	=> array('input-checkbox'),
		    	//'default'		=> get_user_meta( get_current_user_id(), 'preferred_contact_method', true ), 
			), $fields->get_value( 'wsr_order_checkbox' ));
		}	

		if ($this->selectKey){
			woocommerce_form_field( 'wsr_order_select', array(
				'type' 			=> 'select',
			 	'label'      	=> $this->selectLabel,
			  	'required'   	=> true,
			  	'class'      	=> array('form-row-wide'),
			  	'options' 		=> $this->selectOptions,
			  	//'default'		=> get_user_meta( get_current_user_id(), 'preferred_contact_method', true ), 
			), $fields->get_value( 'wsr_order_select' ));
		}

	}

	function wsr_custom_checkout_field_process() {
	    
	    if ($this->textboxKey){
		    if (!$_POST['wsr_order_textbox'])
		        wc_add_notice( __( 'Please enter required fields' ), 'error' );
		}

	    if ($this->checkboxKey){
		    if (!$_POST['wsr_order_checkbox'])
		        wc_add_notice( __( 'Please check required fields' ), 'error' );
		}

		if ($this->selectKey){
		    if (!$_POST['wsr_order_select'])
		        wc_add_notice( __( 'Please select required fields' ), 'error' );
		}
	}

	function wsr_custom_checkout_field_update_order_meta( $order_id ) {
		if ($this->textboxKey){
    		if ($_POST['wsr_order_textbox']) update_post_meta( $order_id, $this->textboxKey, esc_attr($_POST['wsr_order_textbox']));
    	}

		if ($this->checkboxKey){
    		if ($_POST['wsr_order_checkbox']) update_post_meta( $order_id, $this->checkboxKey, esc_attr($_POST['wsr_order_checkbox']));
    	}

    	if ($this->selectKey){
    		if ($_POST['wsr_order_select']) update_post_meta( $order_id, $this->selectKey, esc_attr($_POST['wsr_order_select']));
    	}

    	//if you want to save to useer meta:
    	if ($this->textboxKey){
    		if ($_POST['wsr_order_textbox']){
	    		$theorder = new WC_Order( $order_id );
	    		$customer_id = $theorder->get_user_id();
	    		update_user_meta( $customer_id, 'custom_field', esc_attr($_POST['wsr_order_textbox']) );
	    	}
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