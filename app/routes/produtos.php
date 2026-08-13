<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use App\Models\Produto;


/*
    ORM -> Object Relational Mapping
    Illuminate Database -> Eloquent ORM
*/

//Rotas para produtos
return function (App $app): void {
    $app->group('/api/v1', function (Group $group) {
        //Lista produtos
        $group->get('/produtos/lista', function (Request $request, Response $response) {
            $produtos = Produto::get();
            $response->getBody()->write($produtos->toJson());
            return $response->withHeader('Content-Type', 'application/json');
        });

        //Adiciona produto
        $group->post('/produtos/adiciona', function (Request $request, Response $response) {
            $dados = $request->getParsedBody();
            if (!is_array($dados)) {
                $response->getBody()->write(json_encode(['message' => 'Envie o corpo da requisição como JSON (Content-Type: application/json).']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
            $produto = Produto::create($dados);
            $response->getBody()->write($produto->toJson());
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        });

        //Recupera produto
        $group->get('/produtos/lista/{id}', function (Request $request, Response $response, array $args) {
            $produto = Produto::findOrFail($args['id']);
            $response->getBody()->write($produto->toJson());
            return $response->withHeader('Content-Type', 'application/json');
        });
        //Atualiza
        $group->put('/produtos/atualiza/{id}', function (Request $request, Response $response, array $args) {
            $produto = Produto::findOrFail($args['id']);
            $dados = $request->getParsedBody();
            if (!is_array($dados)) {
                $response->getBody()->write(json_encode(['message' => 'Envie o corpo da requisição como JSON (Content-Type: application/json).']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
            $produto->update($dados);
            $response->getBody()->write($produto->toJson());
            return $response->withHeader('Content-Type', 'application/json');
        });
        //Deleta
        $group->delete('/produtos/deleta/{id}', function (Request $request, Response $response, array $args) {
            $produto = Produto::findOrFail($args['id']);
            $produto->delete();
            $response->getBody()->write(json_encode(['message' => 'Produto deletado com sucesso']));
            return $response->withHeader('Content-Type', 'application/json');
        });
        //Remove produto para um determinado ID
        $group->delete('/produtos/remove/{id}', function (Request $request, Response $response, array $args) {
            $produto = Produto::findOrFail($args['id']);
            $produto->delete();
            $response->getBody()->write(json_encode(['message' => 'Produto removido com sucesso']));
            return $response->withHeader('Content-Type', 'application/json');
        });
    });
};
