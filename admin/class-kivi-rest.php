<?php
/**
 * Helper class for Kivi REST calls.
 * Date: 16.9.2021
 */

class KiviRest {

	private $api_url = '';

	/**
	 * @return string
	 */
	public function getApiUrl() {
		return $this->api_url;
	}

	/**
	 * @param string $api_url
	 */
	public function setApiUrl( $api_url ) {
		$this->api_url = $api_url;
	}

	/**
	 * KiviRest constructor.
	 */
	public function __construct() {
		$this->setApiUrl( apply_filters( 'kivi-rest-api-url', 'https://api.prod.kivi.etuovi.com/v2/' ) );
	}

	public static function getItemsToDelete() {
		$instance = new KiviRest();

		$res = $instance->kiviRemoteRequest( 'realties/homepage?INCLUDE_FIELDS=REALTY_UNIQUE_NO', array(), 60*4, 0 );

		if ( ! is_wp_error( $res ) && ( $res['response']['code'] == 200 || $res['response']['code'] == 201 ) ) {
			return json_decode( $res['body'], true );
		} else {
			error_log( "Error KiviRest :: getItemsToDelete()" );
		}
	}

	public static function getAllItems( $indexed_after = 16 ) {
		$instance = new KiviRest();

		$res = $instance->kiviRemoteRequest( 'realties/homepage', array(), $indexed_after );

		if ( ! is_wp_error( $res ) && ( $res['response']['code'] == 200 || $res['response']['code'] == 201 ) ) {
			return json_decode( $res['body'], true );
		} else {
			error_log( "Error KiviRest :: getAllItems()" );
		}
	}

	public static function testApiConnection() {
		$instance = new KiviRest();

		$args            = array();
		$args['timeout'] = 4; // short timeout as used when generating page

		$res = $instance->kiviRemoteRequest( 'realties/homepage', $args, 0 );

		if ( ! is_wp_error( $res ) && ( $res['response']['code'] == 200 || $res['response']['code'] == 201 ) ) {
			$header_data = $res['headers']->getAll();
			if ( isset( $header_data['x-total-hit-count'] ) ) {
				echo $header_data['x-total-hit-count'] . " kohdetta löydetty, ";
				echo $header_data['x-rate-limit-remaining'] .' pyyntöä jäljellä (tunnissa)';
				if( $header_data['x-rate-limit-remaining'] > $header_data['x-total-hit-count'] ){
				echo " <button id='rest-update-all' type='button'>Nouda / päivitä kaikki</button>";
				}
			} else {
				echo $res['response']['code'] . ' OK';
			}

		} else {
			var_dump( $res['body'] );
		}
	}

	public function getUiData( $realty_unique_no ) {

		$realty_unique_no = intval( $realty_unique_no );

		if ( $realty_unique_no == 0 ) {
			return array();
		}

		$instance = new KiviRest();

		$res = $instance->kiviRemoteRequest( 'realties/homepage/' . $realty_unique_no . '/uiformat', array(), 0 );

		if ( ! is_wp_error( $res ) && ( $res['response']['code'] == 200 || $res['response']['code'] == 201 ) ) {
			return json_decode( $res['body'], true );
		} else {
			error_log( "Error KiviRest :: getUiData( $realty_unique_no )" );

			return array();
		}
	}

	/**
	 * @param $endpoint string ex. "realties"
	 * @param array $args for request
	 * @param int $indexed_after minutes
	 *
	 * Wrapper method for adding authorization to wp_remote_get.
	 *
	 * @return mixed
	 */
	private function kiviRemoteRequest( $endpoint, $args = array(), $indexed_after = 16, $iv_active_flag = 1 ) {
		$url = $this->api_url . $endpoint;
		$url = add_query_arg( 'IV_ACTIVE_FLAG', $iv_active_flag, $url );

		if ( $indexed_after ) {
			$date = date_create( 'now' );
			date_sub( $date, date_interval_create_from_date_string( $indexed_after . ' minutes' ) );
			$url = add_query_arg( 'INDEXED_AFTER', date_format( $date, 'Y-m-d\TH:i:s.v\Z' ), $url );
		}


		$auth_string     = 'Basic ' . get_option( 'kivi-rest-auth' );
		$args['headers'] = array( 'Authorization' => $auth_string );
		error_log('Request: '.$url);
		return wp_remote_request( $url, $args );
	}


}