<?php
/**
 * WP Hunt Api Tests.
 *
 * @since   0.0.0
 * @package WP_Hunt
 */
class WPH_Api_Test extends WP_UnitTestCase {

	/**
	 * Test if our class exists.
	 *
	 * @since  0.0.0
	 */
	function test_class_exists() {
		$this->assertTrue( class_exists( 'WPH_Api' ) );
	}

	/**
	 * Test that we can access our class through our helper function.
	 *
	 * @since  0.0.0
	 */
	function test_class_access() {
		$this->assertInstanceOf( 'WPH_Api', wp_hunt()->api );
	}

	/**
	 * Test saving Api keys to options.
	 *
	 * @since  0.0.0
	 */
	function test_save_api() {
		$clientid  = getenv( ' clientid' );
		$secretkey = getenv( 'secretkey' );
		wp_hunt()->api->save_api( $clientid, $secretkey );
		$this->assertEquals( $clientid, get_option( 'wp_hunt_clientid', '' ) );
		$this->assertEquals( $secretkey, get_option( 'wp_hunt_secretkey', '' ) );
	}

	/**
	 * Test get access token.
	 *
	 * @since  0.0.0
	 */
	function test_get_token() {
		$token = getenv( ' token' );
		$this->test_save_api();
		$result = wp_hunt()->api->get_token();
		if ( isset( $result['errors'] ) ) {
			var_dump( $result );
		}
		$this->assertEquals( $token, $result );
		$this->assertEquals( $token, get_option( 'wp_hunt_api_token', '' ) );
	}

	/**
	 * Test get collections.
	 *
	 * @since  0.0.0
	 */
	function test_get_collections() {
		$token = getenv( 'token' );
		update_option( 'wp_hunt_api_token', $token );
		$result = wp_hunt()->api->collections();
		if ( isset( $result['errors'] ) ) {
			var_dump( $result );
		}
		$this->assertArrayHasKey( 'data', $result );
		$this->assertNotNull( $result['data'] );
	}

	/**
	 * Test get goals.
	 *
	 * @since  0.0.0
	 */
	function test_get_goals() {
		$token = getenv( 'token' );
		update_option( 'wp_hunt_api_token', $token );
		$result = wp_hunt()->api->goals();
		if ( isset( $result['errors'] ) ) {
			var_dump( $result );
		}
		$this->assertArrayHasKey( 'data', $result );
		$this->assertNotNull( $result['data'] );
	}

	/**
	 * Test get makergroups.
	 *
	 * @since  0.0.0
	 */
	function test_get_makergroups() {
		$token = getenv( 'token' );
		update_option( 'wp_hunt_api_token', $token );
		$result = wp_hunt()->api->makergroups();
		if ( isset( $result['errors'] ) ) {
			var_dump( $result );
		}
		$this->assertArrayHasKey( 'data', $result );
		$this->assertNotNull( $result['data'] );
	}

	/**
	 * Test get posts.
	 *
	 * @since  0.0.0
	 */
	function test_get_posts() {
		$token = getenv( 'token' );
		update_option( 'wp_hunt_api_token', $token );
		$result = wp_hunt()->api->posts();
		if ( isset( $result['errors'] ) ) {
			var_dump( $result );
		}
		$this->assertArrayHasKey( 'data', $result );
		$this->assertNotNull( $result['data'] );
	}
}
