<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app): void {
    $app->options('/{routes:.+}', function (Request $request, Response $response) {
        return $response;
    });

    //Routes
    $autenticacaoRoutes = require __DIR__ . '/routes/autenticacao.php';
    $autenticacaoRoutes($app);

    $produtosRoutes = require __DIR__ . '/routes/produtos.php';
    $produtosRoutes($app);
};