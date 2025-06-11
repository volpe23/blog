<?php

use Core\Facades\App;
use Core\Session;

/**
 * Returns path from project base path
 * @param string $path
 * 
 * @return string
 */
function base_path(string $path): string
{
    return str_replace("\\", "/", realpath(BASE_PATH . $path));
}

/**
 * Formated dump and die
 * @param mixed $value
 * @param mixed[] $args
 */
function dd(mixed $value, mixed ...$args)
{
    echo "<pre>";
    var_dump($value, $args);
    echo "</pre>";
    die();
}

/**
 * Returns a view with attributes
 * @param string $viewPath
 * @param array $attributes
 * 
 * @return void
 */
function view(string $viewPath, $attributes = []): void
{
    extract($attributes);
    require base_path("views/" . $viewPath . ".view.php");
}

/**
 * Redirects to a url
 * @param string $url
 * 
 * @return void
 */
function redirect(string $url, array $attributes = []): void
{
    extract($attributes);
    header("Location: $url");
    exit();
}

/**
 * Returns time diff from current time
 * @param string $time
 * 
 * @return string
 */
function timestamp(string $time, ?string $from = null): string
{
    $now = $from ? strtotime($from) : time();
    $timestamp = strtotime($time);

    $timeDiff = $now - $timestamp;
    // Time units in seconds
    $units = [
        "year" => 365 * 24 * 60 * 60,
        "month" => 30 * 24 * 60 * 60,
        "day" => 24 * 60 * 60,
        "hour" => 60 * 60,
        "minute" => 60,
    ];

    foreach ($units as $unit => $dur) {
        $quotient = floor($timeDiff / $dur);
        if ($quotient >= 1) {
            return "{$quotient} $unit" . ($unit > 1 ? "s" : "") . " ago";
        }
    }

    return "just now";
}

function session(): Session
{
    return App::get("session");
}


function array_find(array $array, callable $callback): mixed
{
    foreach ($array as $key => $value) {
        if ($callback($value, $key)) return $value;
    }

    return false;
}
