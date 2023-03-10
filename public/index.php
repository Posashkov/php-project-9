<?php

require __DIR__ . '/../vendor/autoload.php';

use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Hexlet\Code\Connection;
use Hexlet\Code\PostgreSQLExecutor;
use Hexlet\Code\Url;

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
$router = $app->getRouteCollector()->getRouteParser();

$app->get('/', function ($request, $response) {
    $messages = $this->get('flash')->getMessages();

    $params = [
        'flash' => $messages,
    ];
    return $this->get('view')->render($response, 'index.twig', $params);
})->setName('index');

$app->get('/urls', function ($request, $response) use ($router) {
    try {
        $urls = Url::getAll();
    } catch (\Exception | \PDOException $e) {
        $this->get('flash')->addMessage('danger', $e->getMessage());
        return $response->withRedirect($router->urlFor('index'));
    }

    $params = [
        'route' => $router->urlFor('url.show', ['id' => '']), // TODO: переделать
        'urls' => $urls,
    ];

    return $this->get('view')->render($response, 'urls/index.twig', $params);
})->setName('url.index');

$app->post('/urls', function ($request, $response) use ($router) {
    $parsedUrl = $request->getParsedBodyParam('url');

    $newUrl = htmlspecialchars($parsedUrl['name']);

    $errors = [];
    // TODO: validator, error code 422

    $urlId = 0;
    try {
        $url = Url::byName($newUrl);

        if ($url->getId() > 0) {
            $this->get('flash')->addMessage('success', 'Страница уже существует');
            return $response->withRedirect($router->urlFor('url.show', ['id' => (string)$url->getId()]));
        }

        $urlId = $url->setName($newUrl)->store()->getId();
    } catch (\Exception | \PDOException $e) {
        $this->get('flash')->addMessage('danger', $e->getMessage());
        return $response->withRedirect($router->urlFor('index'));
    }

    if ($urlId <= 0) {
        $this->get('flash')->addMessage('danger', 'Что-то пошло не так');
        return $response->withRedirect($router->urlFor('index'));
    }

    /*if (count($errors) > 0) {
        $this->get('flash')->addMessage('danger', implode('<br>', $errors));
        return $response->withRedirect($router->urlFor('index'));
    }*/

    $this->get('flash')->addMessage('success', 'Страница успешно добавлена');

    return $response->withRedirect($router->urlFor('url.show', ['id' => (string)$urlId]));
})->setName('url.store');

$app->get('/urls/{id:[0-9]+}', function ($request, $response, $args) use ($router) {
    $id = $args['id'];
    // получить данные из бд по переданому id
    // если записи с таким id нет, то вывод ошибки 404

    try {
        $url = Url::byId($id);

        if (!$url->getId()) {
            // TODO: rediect to 404 page
            $this->get('flash')->addMessage('danger', 'Страница не найдена');
            return $response->withRedirect($router->urlFor('index'));
        }
    } catch (\Exception | \PDOException $e) {
        $this->get('flash')->addMessage('danger', $e->getMessage());
        return $response->withRedirect($router->urlFor('index'));
    }

    $messages = $this->get('flash')->getMessages();

    $params = [
        'flash' => $messages,
        'url' => $url
    ];
    return $this->get('view')->render($response, 'urls/show.twig', $params);
})->setName('url.show');

$app->run();
