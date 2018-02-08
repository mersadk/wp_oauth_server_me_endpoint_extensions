<?php
/**
* Plugin Name: WP OAuth Server oauth/me endpoint extension
* Plugin URI: 
* Description: Extends oauth/me endpoint to return user roles 
* Version: 1.0
* Author: Mersad Katana
*/

/**
 * Initial code for this plugin is copied from 
 * https://wp-oauth.com/documentation/how-tos/extending-endpoints/
 */
 
 
add_filter('wo_endpoints','wo_extend_resource_api', 2);
function wo_extend_resource_api ($methods){
 $methods['me'] = array('func'=>'_wo_mk_me_extend');
 return $methods;
}

/**
* Replaces the default me enpoint
* @param [type] $token [description]
* @return [type] [description]
*/
function _wo_mk_me_extend ( $token=null ){
	$user_id = &$token['user_id'];

	global $wpdb;
	$me_query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}users WHERE ID=%d", $user_id);
	$me_data = $wpdb->get_row($me_query, ARRAY_A);

	/** prevent sensitive data - makes me happy ;) */
	unset( $me_data['user_pass'] );
	unset( $me_data['user_activation_key'] );
	unset( $me_data['user_url'] );
	unset( $me_data['session_tokens'] );

	// Get user WP object
	$user = get_user_by("id", $user_id);
	$me_data['roles'] = $user->roles;

	$response = new OAuth2\Response($me_data);
	$response->send();
	exit;
}