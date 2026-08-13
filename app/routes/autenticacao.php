<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use App\Models\Usuario;
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

//Rotas para geração de token
return function (App $app): void {
    $container = $app->getContainer();

    $app->post('/api/token', function (Request $request, Response $response) use ($container) {
        $dados = $request->getParsedBody();

        $email = $dados['email'] ?? null;
        $senha = $dados['senha'] ?? null;

        $usuario = Usuario::where('email', $email)->first();

        if (!is_null($usuario) && (md5($senha) === $usuario->senha)) {
            // Gerar token
            $secretKey = $container->get(SettingsInterface::class)->get('secretKey');
            $chaveAcesso = JWT::encode(['id' => $usuario->id, 'email' => $usuario->email], $secretKey, 'HS256');

            $response->getBody()->write(json_encode(['chave' => $chaveAcesso]));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode(['status' => 'erro']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    });
};
