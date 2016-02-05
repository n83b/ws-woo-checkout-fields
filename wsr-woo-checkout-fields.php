<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * @package ws-woo-checkout-fields
 * @version 1.6
 */
/*
Plugin Name: WSR Woo Checkout Fields
Plugin URI: http://websector.com.au
Description: Adds textbox & checkbox fields to woo commerce
Author: WSR
Version: 1
Author URI: http://websector.com.au
*/
class WSR_woo_checkout_fields{
	var $settings;

/************************************************************************
	Simply Edit these variables.  Empty label string removes field */	
	//textbox
	var $textboxLabel = 'Please add a message for the card';
	
	//Checkbox
	var $checkboxLabel = '';

	//Select
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
	}

	function wsr_custom_checkout_fields( $fields ) {
		if ($this->textboxLabel){
			woocommerce_form_field( 'wsr_order_textbox', array(
				'type'			=> 'text',
				'label'     	=> $this->textboxLabel,
		   		'required'  	=> true,
		    	'class'     	=> array('form-row-wide'),
			), $fields->get_value( 'wsr_order_textbox' ));
		}	

		if ($this->checkboxLabel){
			woocommerce_form_field( 'wsr_order_checkbox', array(
				'type'			=> 'checkbox',
				'label'     	=> $this->checkboxLabel,
		   		'required'  	=> true,
		    	'class'     	=> array('input-checkbox'),
			), $fields->get_value( 'wsr_order_checkbox' ));
		}	

		if ($this->selectLabel){
			woocommerce_form_field( 'wsr_order_select', array(
				'type' 			=> 'select',
			 	'label'      	=> $this->selectLabel,
			  	'required'   	=> true,
			  	'class'      	=> array('form-row-wide'),
			  	'options' 		=> $this->selectOptions,
			), $fields->get_value( 'wsr_order_select' ));
		}

	}

	function wsr_custom_checkout_field_process() {
	    
	    if ($this->textboxLabel){
		    if (!$_POST['wsr_order_textbox'])
		        wc_add_notice( __( 'Please enter required fields' ), 'error' );
		}

	    if ($this->checkboxLabel){
		    if (!$_POST['wsr_order_checkbox'])
		        wc_add_notice( __( 'Please check required fields' ), 'error' );
		}

		if ($this->selectLabel){
		    if (!$_POST['wsr_order_select'])
		        wc_add_notice( __( 'Please select required fields' ), 'error' );
		}
	}

	function wsr_custom_checkout_field_update_order_meta( $order_id ) {
		if ($this->textboxLabel){
    		if ($_POST['wsr_order_textbox']) update_post_meta( $order_id, $this->textboxLabel, esc_attr($_POST['wsr_order_textbox']));
    	}

		if ($this->checkboxLabel){
    		if ($_POST['wsr_order_checkbox']) update_post_meta( $order_id, $this->checkboxLabel, esc_attr($_POST['wsr_order_checkbox']));
    	}

    	if ($this->selectLabel){
    		if ($_POST['wsr_order_select']) update_post_meta( $order_id, $this->selectLabel, esc_attr($_POST['wsr_order_select']));
    	}
	}	

}

$wsr_bootstrap = new WSR_woo_checkout_fields();