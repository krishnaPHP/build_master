<?php
/**
 *
 */

namespace Framework\Core;

abstract class Controller
{
    // Base Controller has a property called $loader,
    // it is an instance of Loader class(introduced later)
    protected $loader;

    public function __construct()
    {
        $this->loader = new Loader();
    }

    public function redirect($url)
    {
        header("Location: $url");
        exit;
    }
}