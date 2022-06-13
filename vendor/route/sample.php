<?php
error_reporting(0);
include "vendor/route/Route.php";

$route = new Route(__DIR__ . "/app/functions");



/// Routing to notfound or the usual route.
$route->Route("*", function () {
    global $route;
    return $route->loadFunction("NotfoundFunction");
});


/// Routing the main path.
$route->Route("/", function () {
    global $route;
    return $route->loadFunction("MainFunction");
});

/// You can use checkAuth before load function to check user authorization.
$route->checkAuth(function () {
    // check authorize. return true is user really authorized.
    return true;
})->Route("/checkLogin", function () {
    global $route;
    return $route->loadFunction("MainFunction");
});

/// Routing to test. and run TestFunction()
$route->Route("/test", function () {
    global $route;
    return $route->loadFunction("TestFunction");
});

/// Routing to test/{ANY}/a/{ANY} and pass the data to view function
$route->Route("/test/*/a/*", function ($data) {
    global $route;
    return $route->loadFunction("TestFunction");
});

/// You can customize how display the data comes from the Functions you load.
$route->show(function ($data) {
    if(isset($data['html'])) {
        echo $data['html'];
    } else {
        header("Content-type: application/json", 128);
        echo json_encode($data);
    }
});