<?php

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

return function (RouteBuilder $routes): void {
  
    $routes->setRouteClass(DashedRoute::class);

    $routes->scope('/', function (RouteBuilder $builder): void {

        $builder->connect('/', ['prefix' => 'Admin', 'controller' => 'users', 'action' => 'login']);
        //$builder->connect('/', ['controller' => 'Pages', 'action' => 'view','inicial']);
        $builder->connect('/blog/*', ['controller' => 'Posts', 'action' => 'index']);
        $builder->connect('/busca/*', ['controller' => 'Posts', 'action' => 'search']);

        $builder->connect('/*', ['controller' => 'Pages', 'action' => 'view']);
        $builder->connect('/categoria/*', ['controller' => 'Categories', 'action' => 'view']);

        $builder->connect('/maintenance', ['prefix' => 'Admin', 'controller' => 'users', 'action' => 'maintenance']);
        $builder->connect('/login', ['prefix' => 'Admin', 'controller' => 'users', 'action' => 'login']);
        $builder->connect('/painel', ['prefix' => 'Admin', 'controller' => 'users', 'action' => 'login']);
        $builder->connect('/recupera', ['prefix' => 'Admin', 'controller' => 'users', 'action' => 'recover']);
        $builder->connect('/envia_contato', ['controller' => 'pages', 'action' => 'sendContact']);


        $builder->fallbacks();
    });

   $routes->prefix('Admin', function ($routes) {
        /* $routes->registerMiddleware('csrf', new CsrfProtectionMiddleware([
            'httpOnly' => true,
        ]));*/

        $routes->connect('/', ['controller' => 'Welcome']);
        $routes->connect('/login', ['controller' => 'users', 'action' => 'view']);
        $routes->connect('/admin', ['controller' => 'users', 'action' => 'view']);
        $routes->connect('/logout', ['controller' => 'users', 'action' => 'logout']);
        $routes->connect('/courses/courses-students', ['controller' => 'courses', 'action' => 'indexStudents']);
        $routes->connect('/courses/view-students/*', ['controller' => 'courses', 'action' => 'viewStudents']);
        $routes->connect('/courses/watch-students/*', ['controller' => 'courses', 'action' => 'watchStudents']);
        $routes->connect('/courses/purchase-students/*', ['controller' => 'courses', 'action' => 'purchaseStudents']);
        $routes->connect('/courses/update-progress', ['controller' => 'courses', 'action' => 'updateProgress']);

        //$routes->applyMiddleware('csrf');
        $routes->setExtensions(['json', 'xml']);

        $routes->fallbacks('InflectedRoute');
    });

     // API routes
    $routes->prefix("Api", function (RouteBuilder $builder) {

        $builder->setExtensions(["json"]);

        $builder->connect("/authorization", ["controller" => "Access", "action" => "authorization"]);
        $builder->connect("/validation", ["controller" => "Access", "action" => "validation"]);

    });
};
