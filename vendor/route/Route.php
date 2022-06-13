<?php

class Route
{
    private $functionsPath;
    private $data;
    private bool $authFailed = false;
    private bool $stopSearching = false;
    private bool $show404;

    /**
     * @param $path
     *  Set default folder of functions you will use on loadFunction method.
     */
    public function __construct($path, $show404 = true)
    {
        $this->functionsPath = $path;
        $this->show404 = $show404;
    }

    /**
     * @param $path
     * @param $view
     * @return bool
     *
     * Define your actions and paths using Route.
     */
    public function Route($path, $view): bool
    {
        if ($this->authFailed === false) {
            if (!$this->stopSearching) {
                $params = $_GET['paths'];
                /// When the url is dynamic
                if (strpos($path, '*') !== false) {
                    if ($path === '*') {
                        $this->data = $view();
                        $this->stopSearching = true;
                        return true;
                    } else {
                        $pathParams = explode("/", $path);
                        $paramsExploded = explode('/', "/$params");

                        $ok = true;
                        $data = [];
                        for ($i = 0; $i < count($paramsExploded); $i++) {
                            $p = $paramsExploded[$i];
                            if ($pathParams[$i] == '*') {
                                $data[] = $p;
                            }
                            if (strtolower($pathParams[$i]) != strtolower($p) && $pathParams[$i] != '*') {
                                $ok = false;
                                break;
                            }
                        }
                        if ($ok) {
                            $this->data = $view($data);
                            $this->stopSearching = true;
                            return true;
                        }
                    }
                } else if (strtolower("/$params") === strtolower($path)) {
                    $this->data = $view();
                    $this->stopSearching = true;
                    return true;
                } else {
                    if($this->show404) {
                        $this->data = $this->loadFunction("NotfoundFunction");
                    }
                }
                return false;
            }
            return false;
        } else {
            return false;
        }
    }

    /**
     * @param $view
     * @return $this|false
     * You can check the user authorization before load the screen or action.
     */
    public function checkAuth($view, ...$d)
    {
        $view = $view($d);
        if ($view === true) {
            return $this;
        } else {
            if (is_array($view)) {
                if (!$this->authFailed && !$this->stopSearching) {
                    $this->data = $view;
                    $this->authFailed = true;
                }
                return $this;
            }
        }
        return false;
    }


    /**
     * @param $functionName
     * @param ...$params
     * @return mixed
     * You can use this function to load the functions you want load for user this function will going to the path you set and load the function inside the file
     * Notice: Remember the function name and the file name need to be a same name.
     */
    public function loadFunction($functionName, ...$params)
    {
        if (!function_exists($functionName)) require_once $this->functionsPath . "/" . $functionName . ".php";
        return $functionName($params);
    }


    /**
     * @param $showMethod
     * @return void
     * Pass a function in show method and it will give you a data of the route.
     */
    public function show($showMethod)
    {
        $showMethod($this->data);
    }
}