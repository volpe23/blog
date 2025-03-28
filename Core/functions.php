<?php
// require base_path("routes.php");


$uri = parse_url($_SERVER["REQUEST_URI"])["path"];

function base_path(string $path): string
{
    return str_replace("\\", "/", realpath(BASE_PATH . $path));
}


function route_to_controller(string $uri, array $routes): void
{
    if (array_key_exists($uri, $routes)) {
        require base_path($routes[$uri]["controller"]);
    }
}

function dd(mixed $value)
{
    echo "<pre>" . var_dump($value) . "</pre>";
    die();
}

route_to_controller($uri, $routes);
