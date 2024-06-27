<?php
namespace App\Core;

class Request {
	/* Function to convert the URI into a more workable string. */
    public static function uri() {
        return trim( parse_url( $_SERVER["REQUEST_URI"], PHP_URL_PATH ), "/" );
    }

	/* Function to get the request method (GET/POST/Etc). */
    public static function method() {
        return $_SERVER["REQUEST_METHOD"];
    }
}
?>