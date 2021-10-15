<?php

class Tests_KiviFunctions_MapPostMeta extends WP_UnitTestCase {

	function test_should_map(){
		$this->assertSame( 'Kunta', map_post_meta( '_municipality' ) );
		$this->assertSame( 'Kohdetyyppi', map_post_meta( '_itemgroup' ) );
	}

}
