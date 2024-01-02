<?php

/* TODO List:
        - Edit/clean-up comments, to much clutter atm left over from the concept/design stage.
 */

namespace App\Core;

class Request {
    public static function uri() {
		return trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
	}

    public static function method() {
		return $_SERVER['REQUEST_METHOD'];
	}
}

?>