<?php

require __DIR__ . '/../vendor/autoload.php';

use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Hexlet\Code\Connection;

session_start();

// Create container
$container = new Container();
AppFactory::setContainer($container);

// Set view in Container
$container->set('view', function () {
    return Twig::create('../templates'/*, ['cache' => '../cache']*/);
});
// Set flash in Container
$container->set('flash', function () {
    return new \Slim\Flash\Messages();
});

// Create App
$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

// Add Twig-View Middleware
$app->add(TwigMiddleware::createFromContainer($app));

// Routes
$app->get('/', function ($request, $response) {


    try {
        Connection::get()->connect();
     //   $this->get('flash')->addMessage('success',
     // 'A connection to the PostgreSQL database server has been established successfully.');
    } catch (\PDOException $e) {
        $this->get('flash')->addMessage('danger', $e->getMessage());
    }


    $messages = $this->get('flash')->getMessages();

    $params = [
        'flash' => $messages,
    ];
    return $this->get('view')->render($response, 'index.twig', $params);
})->setName('index');

$app->run();
