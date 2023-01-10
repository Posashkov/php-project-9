<?php

require __DIR__ . '/../vendor/autoload.php';

use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

// Create container
$container = new Container();
AppFactory::setContainer($container);

// Set view in Container
$container->set('view', function () {
    return Twig::create('../templates'/*, ['cache' => '../cache']*/);
});

// Create App
$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

// Add Twig-View Middleware
$app->add(TwigMiddleware::createFromContainer($app));

// Routes
$app->get('/', function ($request, $response) {
    return $this->get('view')->render($response, 'index.twig', []);
});

$app->run();
