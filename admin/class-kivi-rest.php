<?php
/**
 * Helper class for Kivi REST calls.
 * Date: 16.9.2021
 */

add_action('kivi-admin-rest-form', array('KiviRest', 'testApiConnection'));

/*
add_action('kivi-admin-rest-form', function() {
	$instance = new Kivi_Admin('name', 'version1');
	$instance->kivi_sync();

});
*/

class KiviRest {

	private $api_url = 'https://api.test.kivi.etuovi.com/ext-api/v2/';

	public static function getItemsToDelete() {
		$instance    = new KiviRest();

		$res = $instance->kiviRemoteRequest( 'realties/homepage?IV_ACTIVE_FLAG=0', array(), $indexed_after = 60 );

		if ( ! is_wp_error( $res ) && ( $res['response']['code'] == 200 || $res['response']['code'] == 201 ) ) {
			return json_decode($res['body'], true);
		} else {
			error_log( "Error KiviRest :: getAllItems()" );
		}
	}

	public static function getAllItems( $indexed_after = true ) {
		$instance    = new KiviRest();

		$res = $instance->kiviRemoteRequest( 'realties/homepage', array(), $indexed_after );

		if ( ! is_wp_error( $res ) && ( $res['response']['code'] == 200 || $res['response']['code'] == 201 ) ) {
			return json_decode($res['body'], true);
		} else {
			error_log( "Error KiviRest :: getAllItems()" );
		}
	}

	public static function testApiConnection() {
		$instance    = new KiviRest();

		$args            = array();
		$args['timeout'] = 4; // short timeout as used when generating page

		$res = $instance->kiviRemoteRequest( 'realties/homepage', $args, 0 );

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

	public function getUiData( $realty_unique_no ){

		$realty_unique_no = intval($realty_unique_no);

		if( $realty_unique_no == 0){
			return false;
		}

		$instance    = new KiviRest();

		$res = $instance->kiviRemoteRequest( 'realties/homepage/'.$realty_unique_no.'/uiformat' );

		if ( ! is_wp_error( $res ) && ( $res['response']['code'] == 200 || $res['response']['code'] == 201 ) ) {
			return json_decode($res['body'], true);
		} else {
			error_log( "Error KiviRest :: getUiData( $realty_unique_no )" );
			return false;
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
	private function kiviRemoteRequest( $endpoint, $args = array(), $indexed_after = 16 ){
		$url = $this->api_url.$endpoint;
		$url = add_query_arg('IV_ACTIVE_FLAG', '1', $url);

		if($indexed_after){
			$date = date_create('now');
			date_sub($date, date_interval_create_from_date_string($indexed_after.' minutes'));
			$url = add_query_arg('INDEXED_AFTER', date_format($date, 'Y-m-d\TH:i:s.v\Z'), $url);
		}


		$auth_string = 'Basic '.get_option( 'kivi-rest-auth' );
		$args['headers'] = array( 'Authorization' => $auth_string );
		error_log('kiviRemoteRequest: '.$url );
		return wp_remote_request( $url, $args );
	}


}