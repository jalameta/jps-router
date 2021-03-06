<?php

namespace Jalameta\Router;

use Jalameta\Router\Contracts\Bindable;
use Jalameta\Router\Concerns\RouteController;

/**
 * Base router class.
 *
 * @author      veelasky <veelasky@gmail.com>
 */
abstract class BaseRoute implements Bindable
{
    use RouteController;

    /**
     * Route path prefix.
     *
     * @var string
     */
    protected $prefix = '/';

    /**
     * Registered route name.
     *
     * @var string
     */
    protected $name;

    /**
     * Middleware used in route.
     *
     * @var array|string
     */
    protected $middleware;

    /**
     * Route for specific domain.
     *
     * @var string
     */
    protected $domain;

    /**
     * Route for specific regular expression.
     *
     * @var array|string
     */
    protected $regex;

    /**
     * Router Registrar.
     *
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * SelfBindingRoute constructor.
     */
    public function __construct()
    {
        $this->router = app('router');
    }

    /**
     * Bind and register the current route.
     *
     * @return void
     */
    public function bind()
    {
        $this->router->group($this->getRouteGroupOptions(), function () {
            $this->register();
        });

        $this->afterRegister();
    }

    /**
     * Perform after register callback.
     *
     * @return void
     */
    public function afterRegister()
    {
        //
    }

    /**
     * Register routes handled by this class.
     *
     * @return void
     */
    abstract public function register();

    /**
     * Get route prefix.
     *
     * @param string $path
     *
     * @return string
     */
    public function prefix($path = '/')
    {
        return $this->prefix == '/' ? $path : $this->mergePath($path);
    }

    /**
     * Remove slash
     *
     * @param $path
     * @return mixed
     */
    private function removeSlashes($path)
    {
        return ltrim(rtrim($path, '/'), '/');
    }

    /**
     * Merge path from prefix property and path input
     *
     * @param $path
     * @return mixed
     */
    private function mergePath($path)
    {
        $prefix = $this->removeSlashes($this->prefix);
        $path = $this->removeSlashes($path);

        return $prefix . '/' . $path;
    }

    /**
     * Get route name.
     *
     * @param null|string $suffix
     *
     * @return string
     */
    public function name($suffix = null)
    {
        if (empty($suffix))
            return $this->getBaseName(false);

        return empty($this->name) ? $suffix : $this->getBaseName() . $suffix;
    }

    /**
     * Get Base name.
     *
     * @param bool $dotSuffix
     *
     * @return string
     */
    private function getBaseName($dotSuffix = true)
    {
        return $this->name . ($dotSuffix) ? '.' : '';
    }

    /**
     * Get Route Binder Options.
     *
     * @return array
     */
    public function getRouteGroupOptions()
    {
        $options = [];

        if (isset($this->middleware) && !empty($this->middleware)) {
            $options['middleware'] = $this->middleware;
        }

        if (isset($this->domain) && !empty($this->domain)) {
            $options['domain'] = $this->domain;
        }

        if (!empty($this->regex)) {
            $options['regex'] = $this->regex;
        }

        return $options;
    }
}
