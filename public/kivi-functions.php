<?php
/**
 *
 * @link       https://kivi.etuovi.com/
 * @since      1.0.0
 *
 * @package    Kivi
 * @subpackage Kivi/public
 * @author     ktalo <antti.keskitalo@almamedia.fi>
 */

/**
 * Template tag for displaying the filters form
 * @return html object
 */
function map_post_meta( $meta_field ) {
	return Kivi_Public::map_post_meta( $meta_field );
}

function set_kivi_option( $name, $value ) {
	$old = get_option( 'kivi-options' );
	if ( ! is_array( $old ) ) {
		$old = [];
	}
	$kivi_options = array_merge( $old, array( $name => $value ) );
	update_option( 'kivi-options', $kivi_options );
}

function get_kivi_option( $name ) {
	$kivi_options = get_option( 'kivi-options' );
	if ( is_array( $kivi_options ) && array_key_exists( $name, $kivi_options ) ) {
		return $kivi_options[ $name ];
	} else {
		return "";
	}
}

/*
* Used by the index template to populate search criteria for the filtering
* of the posts based on the (custom) metadata.
*/
function populate_searchcriteria( &$criteria, &$request, $field, $key, $operator, $intval = false ) {
	if ( isset( $request[ $field ] ) && $request[ $field ] != '' ) {
		$value = sanitize_text_field( $request[ $field ] );
		$type  = 'CHAR';
		if ( $intval ) {
			$value = intval( $request[ $field ] );
			$type  = 'NUMERIC';
		}
		$criteria = array(
			'key'     => $key,
			'value'   => $value,
			'compare' => $operator,
			'type'    => $type,
		);
	}
}

/*
* Helper to get a posted value from the POST/GET request.
*/
function get_posted_value( &$request, $value ) {
	$ret = "";
	if ( isset( $request["submit"] ) && isset( $request[ $value ] ) ) {
		$ret = $request[ $value ];
	}

	return $ret;
}
