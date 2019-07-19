<?php
namespace PHPSTORM_META {
    override( \core\Container::get(0), map([
        'router'				=> \core\Router::class,
        'crouter'			=> \core\lib\router\CRouter::class,
        'config'             => \core\Conf::class,
        'validate'           => \core\Validate::class,
        'db'					=> \core\Db::class,
        'view'				=> \core\View::class,
        'tree'                =>\core\Tree::class,
        'cache'                =>\core\Cache::class,
        'session'                =>\core\Session::class,
        EXAMPLE_B                      =>ExampleB::class,
    ]));
    override(\app(0),map([
            'router'				=> \core\Router::class,
            'crouter'			=> \core\lib\router\CRouter::class,
            'config'             => \core\Conf::class,
            'validate'           => \core\Validate::class,
            'db'					=> \core\Db::class,
            'view'				=> \core\View::class,
            'tree'                =>\core\Tree::class,
            'cache'                =>\core\Cache::class,
            'session'                =>\core\Session::class,
            EXAMPLE_B                      =>static::class,
        ])
    );
}