<?php
/*
 * Template Name: Generate Login Token
 */

function generate_and_store_login_token( $user_id ) {
	$token = bin2hex( random_bytes( 32 ) ); // Generate a secure random token
	update_user_meta( $user_id, '_custom_login_token', $token );

	return $token;
}

if ( current_user_can( 'administrator' ) ) {
	$current_user_id = get_current_user_id();
	$token           = get_user_meta( $current_user_id, '_custom_login_token', true );
	if ( ! $token ) {
		$token = generate_and_store_login_token( $current_user_id );
	}
	echo 'Your login token is: ' . $token; // Display the token (for testing purposes)
}
