<?php
/** Include the needed files.*/
include "autoload.php";

/** @var $result
 * Define errors in here
 */
$result = (new Result)->setErrorsList([
    0 => "Please control inputs.",
    1 => "The User is dead."
]);

/** Routes */
$route = (new Route(__DIR__ . "/app/functions"));
/// 404 notfound screen.
$route->Route("*", function () {
    global $route;
    return $route->loadFunction("NotfoundFunction");
});
/// Check the usual inputs field e.g. Fingerprint.
$route = $route->checkAuth('ValidateInputs', VALIDATOR_PATTERN['USUAL']);
/// Create Action Path You can use CheckAuth after another CheckAuth and if the all checkAuth return true then we get to Route to load page;
$route->checkAuth('ValidateInputs', VALIDATOR_PATTERN['LOGIN'])->Route("/login", function () {
    global $route;
    return $route->loadFunction("LoginFunction");
});
/// Display The results from function in the way you like.
$route->show(function ($data) {
    if (isset($data['html'])) {
        echo $data['html'];
    } else {
        header("Content-type: application/json");
        /// Default Parameters.
        $data['request']['time'] = time();
        print json_encode($data, 128);
    }
});
/** Routes */


/// Validate the inputs and use theme as safe value :)
function ValidateInputs($pattern)
{
    global $result;
    /// Validate inputs
    $validator = new InputValidator($pattern, $_POST);
    if ($validator->validate()) {
        return true;
    } else {
        return $result->error(-1000, $validator->showError());
    }
}
