<?php
/*
Plugin Name: IP Login
Description: Enables you to login from trusted IP without entering password	by specifying ?bypass_login=username parameter on site.
Version: 1.0.3
Author: Luka Petrovic
Author URI: http://nextweb.rs
License: GPL2
Text Domain: ipl
*/
if ( ! defined( 'ABSPATH' ) ) exit;
//---- Define some default values ----//
define('IPL_PLUGIN_URL', plugin_dir_url( __FILE__ ));
//----Add static files and output settings----//
add_action( 'wp_enqueue_scripts', 'ipl_insert_static');
add_action( 'admin_enqueue_scripts', 'ipl_insert_static' );

function ipl_insert_static() {
	if ( is_admin() ) {
		wp_enqueue_style( 'ipl_style', IPL_PLUGIN_URL . 'style.css' );
	}
}

//---- Add query argument ----//
add_filter( 'query_vars', 'ipl_add_q_vars' ); 
function ipl_add_q_vars($vars) {
  $vars[] = 'bypass_login';
  return $vars;
}
//---- Hook to init and do login ----//
add_action('init', 'ipl_do_login');
function ipl_do_login() {

if (isset($_GET['bypass_login'])) {
		if (ipl_get_ip()==false) {
		?><p style="text-align: center;line-height: 30px;padding: 5px;background: rgb(175, 0, 0);color: white;">Your IP can't be determined. You will not use this plugin, sorry...</p><?
		return;
		}	
	
		$username=sanitize_text_field( $_GET['bypass_login']);
		if (ipl_is_ip_authorized(ipl_get_ip())==false) {
		?><p style="text-align: center;line-height: 30px;padding: 5px;background: rgb(175, 0, 0);color: white;">Your IP: { <strong><? echo(ipl_get_ip());?></strong> } is not authorised for access without password.</p><?
		} else {
// do _login
			$user = get_user_by('login', $username );
			if ( !is_wp_error( $user ) ){
				wp_clear_auth_cookie();
				wp_set_current_user ( $user->ID );
				wp_set_auth_cookie  ( $user->ID );
				$redirect_to = user_admin_url();
				wp_safe_redirect( $redirect_to );
				exit();
			}
		}
	} 
}

//---- Get IP from headers ----//
function ipl_get_ip(){

		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		if (ipl_is_ip($ip)){
			return $ip;
		} else {
			return false;
		}
}
//---- Is IP authorised ----//
function ipl_is_ip_authorized($ip){
	$authorized=get_option('ipl_authorised_ips', array(''));
	if ($authorized !=''){
		if (in_array($ip,$authorized)==false) {
			return false;
		} else {
			return true;
		}
	} else {
		return false;
	}
}
//---- Is Valid IP----//
function ipl_is_ip($str) {
	$ret = filter_var($str, FILTER_VALIDATE_IP);

	return $ret;
}


//---- Admin Page ----//
add_action('admin_menu', 'ipl_menu_setup');
 
function ipl_menu_setup(){
		add_submenu_page( 'options-general.php', 'IP Login', 'IP Login','manage_options', 'ip_login', 'ipl_admin_menu' );
}
 
function ipl_admin_menu(){
if (!empty($_POST)){
		$nonce = $_POST['nonce'];
		if ( ! wp_verify_nonce( $nonce, 'ipl_nonce' ) ) {
			die( 'Security Fail;)' );			
		}
}
//add ip
if (isset($_POST['add_ip'])) {
		$ip=sanitize_text_field($_POST['add_ip']);
		if(ipl_is_ip($ip)) {
			$authorized=get_option('ipl_authorised_ips', array(''));
			if (ipl_is_ip_authorized($ip)) {
				//$already_authorised_flag=true;
			} else {
				$authorized[]=$ip;
				update_option('ipl_authorised_ips', $authorized);
			}
		} else {
			die('Not Valid IP');
		}

    } 
//delete IP
if (isset($_POST['delip'])) {
		$ips=sanitize_text_field($_POST['delip']);
		$ips=explode('|', $ips);
		
		$authorized=get_option('ipl_authorised_ips', array(''));
		$authorized = array_diff($authorized, $ips);
		update_option('ipl_authorised_ips', $authorized);


    } 
//html for page	
$authorized=get_option('ipl_authorised_ips', array());

include ('admin_page.php');

}