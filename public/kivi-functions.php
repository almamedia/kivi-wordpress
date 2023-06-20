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

/*
 * echo image html to AGENT -section on frontend.
 */
add_action('kivi_single_ui_section_AGENT', function(){
	$img_src = get_post_meta( get_the_ID(), '_ui_AGENT_IMAGE', true );
	if( ! empty($img_src) ){
		echo "<div class='kivi-agent-image'>
		<img src='{$img_src}' />
		</div>";
	}
});


add_shortcode('kivi-ostotoimeksiannot', function(){

	if ( false === ( $results = get_transient( 'kivi-rest-purchase-announcements' ) ) ) {
		$results = KiviRest::getPurchaseAnnouncements();
		set_transient( 'kivi-rest-purchase-announcements', $results, HOUR_IN_SECONDS );
	}

	ob_start();

	foreach( $results as $ota ) { // "osto toimeksi anto"

        if ( $overridden_template = locate_template( 'kivi-purchase-announcement.php' ) ) {
	        include( $overridden_template );
        } else {
        	include( dirname( __FILE__ ) . '/../includes/partials/kivi-purchase-announcement.php');
        }

	}

	return ob_get_clean();
});

/**
 * Use kivi-brand-color (set in the Kivi admin screen) as color or background color for some elements.
 * If you want to disable this, empty the settings value.
 * If you want to use more brand color, use (child) theme css or Customizer custom css.
 */
add_filter( 'wp_get_custom_css', function( $css ){
	$brand_color = esc_html( get_option("kivi-brand-color") );

	if( ! empty($brand_color) ) {
		$color_selector = 'a.kivi-item-link:hover';
		$bgcolor_selector = 'h3.kivi-single-item-body-header, input[name=kivi-contact-submit]:hover, input#kivi-index-search';
		$css .= "\n {$color_selector} { color: {$brand_color}; } ";
		$css .= "\n {$bgcolor_selector} { background-color: {$brand_color}; } \n";
	}


	return $css;
}, 10, 1 );

add_action('kivi_single_presentation_text_after', function(){
	$links = get_post_meta( get_the_ID(), '_ui_LINKS', true );
	if( ! is_array($links)) {
		return;
	}
	foreach( $links as $link ){
		$link['TYPE']   =    esc_attr( $link['TYPE'] );
		$link['URL']    =    esc_attr( $link['URL'] );
		$link['DESCRIPTION'] = esc_html( $link['DESCRIPTION'] );
		echo "<p class='kivi-single-item-link link-type-{$link['TYPE']}'>
			<a href='{$link['URL']}' target='_blank'>{$link['DESCRIPTION']}</a>
		</p>";
	}
});
