<?php

/* This is a left over file from the tutorial, this might still be moved to App later on, but might also remain here as is. */

function dd($value) {
    echo "<pre>";
    var_dump($value);
    echo "</pre>";

    die();
}

function base_path($path) {
    return __DIR__ . '/../' . $path;
}

function inpFilt($string) {
    return htmlspecialchars($string);
}