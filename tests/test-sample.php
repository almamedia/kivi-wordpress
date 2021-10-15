<?php

class KiviFunctionsTest extends WP_UnitTestCase {

	function test_kivi_option_setget(){
		set_kivi_option( 'test', 'testvalue' );
		$this->assertSame( 'testvalue', get_kivi_option('test') );
	}

}

