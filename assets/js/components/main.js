/**
 * WP Hunt
 * imakeplugins.com
 *
 * Licensed under the GPLv2+ license.
 */

window.WPHunt = window.WPHunt || {};

( function( window, document, $, plugin ) {
	let $c = {};

	plugin.init = function() {
		plugin.cache();
		plugin.bindEvents();
	};

	plugin.cache = function() {
		$c.window = $( window );
		$c.body = $( document.body );
	};

	plugin.bindEvents = function() {
		$( '#wp-hunt-api-authenticate' ).on( 'click', function() {
			var data = {
				clientid: $( '#wp-hunt-client-id' ).val(),
				secretkey: $( '#wp-hunt-client-secret' ).val()
			};
			var postData = {
				action: 'wp_hunt_api_authenticate',
				data: data,
				wp_hunt_nonce: wp_hunt_vars.nonce
			};

			if ( 0 == $.trim( data.clientid ).length ) {
				alert( 'Please enter the API Client ID!' );
				return false;
			}

			if ( 0 == $.trim( data.secretkey ).length ) {
				alert( 'Please enter the API Client Secret Key!' );
				return false;
			}

			$.post( wp_hunt_vars.ajaxurl, postData, function( response ) {
				if ( 'fail' == response ) {
					alert( wp_hunt_vars.error_message );
				} else {
					$( '#wp-hunt-api-token' ).text( response );
					$( '#wp-hunt-api-token-div' ).show();
				}
			});
		});
	};

	$( plugin.init );
}( window, document, require( 'jquery' ), window.WPHunt ) );
