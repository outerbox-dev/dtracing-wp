<?php
/*
 * Template Name: Login as custom User
 */

function custom_login_as_user_with_token( $admin_token, $username ) {
	// Check if a user has this token
	$admins = get_users( array(
		'meta_key'   => '_custom_login_token',
		'meta_value' => $admin_token,
		'number'     => 1
	) );

	if ( ! empty( $admins ) && current_user_can( 'administrator' ) ) {
		$user = get_user_by( 'login', $username );
		if ( $user ) {
			// Store the current admin user ID in the session
			if ( ! session_id() ) {
				session_start();
			}
			$_SESSION['admin_user_id'] = get_current_user_id();

			// Log in as the specified user
			wp_set_current_user( $user->ID );
			wp_set_auth_cookie( $user->ID );
			do_action( 'wp_login', $user->user_login, $user );
			wp_redirect( admin_url() );
			exit;
		} else {
			wp_die( 'User not found.' );
		}
	} else {
		wp_die( 'Invalid token or permission denied.' );
	}
}

function custom_login_as_user_with_token_endpoint() {
	custom_login_as_user_with_token( $_GET['token'], $_GET['username'] );
}

add_action( 'init', 'custom_login_as_user_with_token_endpoint' );

if ( isset( $_GET['token'] ) && isset( $_GET['username'] ) ) {
	custom_login_as_user_with_token_endpoint();
}

