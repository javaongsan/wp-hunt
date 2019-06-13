<?php
/**
 * WP Hunt Api.
 *
 * @since   0.0.0
 * @package WP_Hunt
 */

/**
 * WP Hunt Api.
 *
 * @since 0.0.0
 */
class WPH_Api {
	/**
	 * Parent plugin class.
	 *
	 * @since 0.0.0
	 *
	 * @var   WP_Hunt
	 */
	protected $plugin = null;

	protected $api_server_url = 'https://api.producthunt.com/';

	/**
	 * Constructor.
	 *
	 * @since  0.0.0
	 *
	 * @param  WP_Hunt $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  0.0.0
	 */
	public function hooks() {
		add_action( 'wp_ajax_save_api', array( $this, 'save_api' ) );
	}

	public function authenticate( $data ) {
		$this->save_api( $data['clientid'], $data['secretkey'] );
		return $this->get_access_token( $data['clientid'], $data['secretkey'] );
	}

	public function save_api( $clientid = '', $secretkey = '' ) {
		update_option( 'wp_hunt_clientid', $clientid );
		update_option( 'wp_hunt_secretkey', $secretkey );
	}

	/**
	 * Get API Token.
	 *
	 * @since  0.0.0
	 */
	public function get_token() {
		if ( ! empty( get_option( 'wp_hunt_api_token' ) ) ) {
			return get_option( 'wp_hunt_api_token' );
		}
		return $this->get_access_token( get_option( 'wp_hunt_clientid' ), get_option( 'wp_hunt_secretkey' ) );
	}

	/**
	 * Get API Token.
	 *
	 * @since  0.0.0
	 */
	public function get_access_token( $clientid = '', $secretkey = '' ) {
		$response_data = json_decode( $this->oauth_client_authentication( $clientid, $secretkey ) );
		if ( isset( $response_data->access_token ) ) {
			update_option( 'wp_hunt_api_token', $response_data->access_token );
			return $response_data->access_token;
		}

		return null;
	}

	/**
	 * OAuth Client Only Authentication
	 *
	 * @since  0.0.0
	 *
	 * @return access_token.
	 */
	public function oauth_client_authentication( $clientid, $secretkey ) {
		$endpoint = 'v2/oauth/token';
		$body     = array(
			'client_id'     => $clientid,
			'client_secret' => $secretkey,
			'grant_type'    => 'client_credentials',
		);
		$header   = array(
			'Content-Type:application/json',
		);
		return $this->wp_curl( $endpoint, $header, $body );
	}

	/**
	 * Get Collections
	 *
	 * @since  0.0.0
	 *
	 * @return collections.
	 */
	public function collections( $num = 1 ) {
		$query = 'query { collections(first: ' . $num . ') {edges { node { id, name } } } }';
		return $this->graphql( $query );
	}

	/**
	 * Get Goals
	 *
	 * @since  0.0.0
	 *
	 * @return goals.
	 */
	public function goals( $num = 1 ) {
		$query = 'query { goals(first: ' . $num . ') {edges { node { id, title } } } }';
		return $this->graphql( $query );
	}

	/**
	 * Get makerGroups
	 *
	 * @since  0.0.0
	 *
	 * @return makergroups.
	 */
	public function makergroups( $num = 1 ) {
		$query = 'query { makerGroups(first: ' . $num . ') {edges { node { id, name } } } }';
		return $this->graphql( $query );
	}

	/**
	 * Get Posts
	 *
	 * @since  0.0.0
	 *
	 * @return posts.
	 */
	public function posts( $num = 1 ) {
		$query = 'query { 
			posts(first: ' . $num . ') { 
				edges {
					node {
						name
						makers {
							username
							headline
							twitterUsername
							url
							profileImage
						}
						description
						tagline
						createdAt
						reviewsRating
						votesCount
						url
						thumbnail {
							videoUrl
							url
							type
						}
					}
				} 
			} 
		}';
		return $this->graphql( $query );
	}

	/**
	 * Get topics
	 *
	 * @since  0.0.0
	 *
	 * @return topics.
	 */
	public function topics( $num = 1 ) {
		$query = 'query { topics(first: ' . $num . ') {edges { node { id, name } } } }';
		return $this->graphql( $query );
	}

	/**
	 * GEt graphql query
	 *
	 * @since  0.0.0
	 *
	 * @return topics.
	 */
	public function graphql( $query ) {
		$request  = 'POST';
		$endpoint = 'v2/api/graphql';
		$body     = '{"query": "' . $query . '"}';
		$header   = array(
			'Content-Type:application/json',
			'Authorization: Bearer ' . get_option( 'wp_hunt_api_token' ),
		);
		return $this->curl( $endpoint, $request, $header, $body );
	}

	/**
	 * WP CURl.
	 *
	 * @since  0.0.0
	 */
	public function wp_curl( $endpoint, $header, $body, $request = 'POST' ) {
		$url  = $this->api_server_url . $endpoint;
		$args = array(
			'method'      => 'POST',
			'timeout'     => 60,
			'redirection' => 5,
			'httpversion' => '1.1',
			'blocking'    => true,
			'headers'     => $header,
			'body'        => $body,
			'cookies'     => array(),
		);

		switch ( $request ) {
			case 'POST':
				$response = wp_remote_post( $url, $args );
				break;
			case 'GET':
				$response = wp_remote_get( $url, $args );
				break;
		}

		if ( WP_DEBUG ) {
			error_log( print_r( $response, true ) );
		}

		if ( is_wp_error( $response ) ) {
			return false;
		}
		return wp_remote_retrieve_body( $response );
	}

	/**
	 * PHP Curl
	 *
	 * @since  0.0.0
	 *
	 * @return data if successful, false otherwise.
	 */
	private function curl( $endpoint, $request, $header, $body ) {
		try {
			$curl = curl_init();
			curl_setopt( $curl, CURLOPT_URL, $this->api_server_url . $endpoint );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $curl, CURLOPT_HTTPHEADER, $header );
			curl_setopt( $curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );

			switch ( $request ) {
				case 'POST':
					curl_setopt( $curl, CURLOPT_POST, true );
					break;
				case 'PUT':
					curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'PUT' );
					break;
			}

			curl_setopt( $curl, CURLOPT_POSTFIELDS, $body );

			if ( WP_DEBUG ) {
				curl_setopt( $curl, CURLOPT_VERBOSE, true );
				$verbose = fopen( dirname( __FILE__ ) . '/curl_log.txt', 'a+' );
				curl_setopt( $curl, CURLOPT_STDERR, $verbose );
			}
			$response = curl_exec( $curl );

			if ( WP_DEBUG ) {
				error_log( print_r( $response, true ) );
			}

			curl_close( $curl );
			return json_decode( $response, true );
		} catch ( Exception $e ) {
				echo $e->getMessage();
		}
	}

}
