<?php

class Tests_KiviFunctions_GetKiviOption extends WP_UnitTestCase {

	function test_should_setget_value(){
		set_kivi_option( 'test', 'testvalue' );
		$this->assertSame( 'testvalue', get_kivi_option('test') );
	}

	function test_should_setget_array_value(){
		$test_array = array('testvalue', 123, 'key' => 'VALUE');
		set_kivi_option( 'test_array', $test_array );
		$this->assertSame( $test_array, get_kivi_option('test_array') );
	}

	function test_map_post_meta(){
		$this->assertSame( 'Kunta', map_post_meta( '_municipality' ) );
	}

}

