<?php

namespace App;

use App\Core\Route;

/**
 * Class Bootstrap
 */
final class Bootstrap
{
    /**
     * Bootstrap constructor.
     */
    public function __construct()
    {
        $route = new Route();
        $page = $route->handle();

        echo $page;
    }
}