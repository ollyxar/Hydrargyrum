<?php namespace Hg\Routes;

class Route
{
    private $uri;
    private $requestMethod;
    private $action;
    private $parameters;
    private $prefix = '';

    public function __construct($uri, $requestMethod)
    {
        $this->uri = trim(strtok($uri, '?'), "/ \t\n\r\0\x0B");
        $this->requestMethod = $requestMethod;
    }

    private function method(string $methodName, string $urlPattern, $action)
    {
        $urlPattern = trim($this->prefix . $urlPattern, "/ \t\n\r\0\x0B");

        if ($this->requestMethod == $methodName) {
            $res = preg_replace('/\{(\w+?)\}/', '([\w\-]+)', $urlPattern);
            $res = preg_replace('/\//', '\/', $res);
            preg_match('/^' . $res . '$/', $this->uri, $matches);

            if ($matches) {
                $this->action = $action;
                array_shift($matches);
                $this->parameters = $matches;
            }
        }

        return $this;
    }

    public function group(string $prefix, callable $closure)
    {
        $prefix = $this->prefix . trim($prefix, "/ \t\n\r\0\x0B");

        if (strpos($this->uri, $prefix) === 0) {
            $this->prefix = $prefix . '/';
            call_user_func($closure, $this->prefix);
        }

        return $this;
    }

    public function get(string $urlPattern, $controllerMethod)
    {
        return $this->method('GET', $urlPattern, $controllerMethod);
    }

    public function post(string $urlPattern, $controllerMethod)
    {
        return $this->method('POST', $urlPattern, $controllerMethod);
    }

    public function action()
    {
        if ($this->action) {
            if (is_callable($this->action)) {
                return call_user_func($this->action);
            } else {
                $controllerMethod = explode('@', $this->action);
                $controller = $controllerMethod[0];
                $method = $controllerMethod[1];
            }

            return (new $controller())->$method(...$this->parameters);
        } else {
            return false;
        }
    }
}