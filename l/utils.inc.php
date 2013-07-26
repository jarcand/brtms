<?php

/**
 * This library file contains generate utility functions.
 */

// If the http_response_code() function isn't defined, make our own
if (!function_exists('http_response_code')) {
	
	/**
	 * Output the headers for the specified the HTTP response code.
	 * @param $code - The HTTP response code to output.
	 */
	function http_response_code($code) {
		$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
		header($protocol . ' ' . $code . ' ');
	}
}

