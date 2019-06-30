/**
 * WP Hunt - v0.0.1 - 2019-06-30
 * imakeplugins.com
 *
 * Copyright (c) 2019;
 * Licensed GPLv2+
 */

(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
(function (global){
'use strict';

/**
 * WP Hunt
 * imakeplugins.com
 *
 * Licensed under the GPLv2+ license.
 */

window.WPHunt = window.WPHunt || {};

(function (window, document, $, plugin) {
	var $c = {};

	plugin.init = function () {
		plugin.cache();
		plugin.bindEvents();
	};

	plugin.cache = function () {
		$c.window = $(window);
		$c.body = $(document.body);
	};

	plugin.bindEvents = function () {
		$('#wp-hunt-api-authenticate').on('click', function () {
			var data = {
				clientid: $('#wp-hunt-client-id').val(),
				secretkey: $('#wp-hunt-client-secret').val()
			};
			var postData = {
				action: 'wp_hunt_api_authenticate',
				data: data,
				wp_hunt_nonce: wp_hunt_vars.nonce
			};

			if (0 == $.trim(data.clientid).length) {
				alert('Please enter the API Client ID!');
				return false;
			}

			if (0 == $.trim(data.secretkey).length) {
				alert('Please enter the API Client Secret Key!');
				return false;
			}

			$.post(wp_hunt_vars.ajaxurl, postData, function (response) {
				if ('fail' == response) {
					alert(wp_hunt_vars.error_message);
				} else {
					$('#wp-hunt-api-token').text(response);
					$('#wp-hunt-api-token-div').show();
				}
			});
		});
	};

	$(plugin.init);
})(window, document, (typeof window !== "undefined" ? window['jQuery'] : typeof global !== "undefined" ? global['jQuery'] : null), window.WPHunt);

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
},{}]},{},[1]);
