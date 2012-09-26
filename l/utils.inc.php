<?php

if (!function_exists('http_response_code')) {
	function http_response_code($code) {
		$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
		header($protocol . ' ' . $code . ' ');
	}
}

