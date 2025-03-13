<?php

/* Easy access dump and die function. */
function dd($value) {
    echo "<pre>";
    var_dump($value);
    echo "</pre>";

    die();
}

/* Easy access function to set the projects base folder. */
function base_path($path) {
    return __DIR__ . '/../' . $path;
}

/* Easy access input filter option, to filter teks befor using it in HTML. */
function inpFilt($string) {
    return htmlspecialchars($string);
}