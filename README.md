# Restful Base API (PHP 8.1)

Use this code to start your first restful api.

This code will help you to define your paths and validate inputs before get to actions and if the action has problem you
will find out and stop it before get to late.

## Define Route

you can define routes in index.php, i'll explain it for you in the following code:

before that we need to add our function into `app->functions` then we ready. (Remember this the file name should be the
same of function name.)

```php
$route->Route("{PATH}", function () {
    global $route;
    return $route->loadFunction("{FUNCTION_NAME}");
});
```

insert the code after $route = new Route();
after that change the `{PATH}` to the path you want for example you want user have access to your function when
load `https://localhost/login/`. we have to put `/login` instead of `{PATH}`

for run the function that we want to run after url has been launched, we need to replace our function name
instead `{FUNCTION_NAME}`.

our path /login run our function now.

### do you need to check something before the run the function???

I made easy way for that :)

use `checkAuth` after $route like this:

```php 
$route->Route("/login", function () {
    global $route;
    $route->checkAuth('{CHECK_AUTH_FUNCTION}');
    return $route->loadFunction("LoginFunction");
});  
```

You can enter your function name instead `{CHECK_AUTH_FUNCTION}` in the function you create you have to return true or
false if it'll be true the route and login function will called and if it'll be false the project get out from that
route and go to other route for check.

you can use checkAuth as many as you want after each other.

```php
$route->checkAuth('{A}')->checkAuth('{B}')->checkAuth('{C}');
```

## Let's go to work with input validator

I create `ValidateInputs` in index.php and i use this function whenever i need in my routs.
I always have some usual inputs e.g. `fingerprint, username` so i use this function before any routs defined so i always
check this with the pattern i defined in `app->defines->ValidatorPattern.php` you can define your own property like:

```php
'age'=> [
    'type'=> 'regex',
    'regex'=>'/^100|[1-9]?\d$/',
    'require'=>true
]
```

you can create your own validate params like that. you can use `int, string, array, bool, float, enum, double, null` as type.

careful when you use it because you can make a little mistake.


and it's all I guess (sorry for bad grammar)
