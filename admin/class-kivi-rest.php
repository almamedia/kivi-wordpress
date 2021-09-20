<?php
/**
 * Helper class for Kivi REST calls.
 * Date: 16.9.2021
 */

add_action('kivi-admin-rest-form', array('KiviRest', 'testApiConnection'));

class KiviRest {

	private $api_url = 'https://api.test.kivi.etuovi.com/ext-api/v2/';

	public static function testApiConnection() {
		$instance    = new KiviRest();

		$args            = array();
		$args['timeout'] = 4; // short timeout as used when generating page

		$res = $instance->kiviRemoteRequest( 'realties', $args );

		if ( ! is_wp_error( $res ) && ( $res['response']['code'] == 200 || $res['response']['code'] == 201 ) ) {
			$header_data = $res['headers']->getAll();
			if ( isset( $header_data['x-total-hit-count'] ) ) {
				echo $header_data['x-total-hit-count'] . " kohdetta löydetty";
			}
			else{
				echo $res['response']['code'].' OK';
			}

		} else {
			var_dump( $res['body'] );
		}
	}

	/**
	 * @param $endpoint string ex. "realties"
	 * @param array $args
	 *
	 * Wrapper method for adding authorization to wp_remote_get.
	 *
	 * @return mixed
	 */
	private function kiviRemoteRequest( $endpoint, $args = array() ){
		$url = $this->api_url.$endpoint;
		$auth_string = 'Basic '.get_option( 'kivi-rest-auth' );
		$args['headers'] = array( 'Authorization' => $auth_string );
		return wp_remote_request( $url, $args );
	}


}