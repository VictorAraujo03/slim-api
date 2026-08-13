<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\App;
use Slim\Psr7\Factory\ResponseFactory;

//Application middleware
return function (App $app): void {
    $container = $app->getContainer();

    // Verificação de token JWT para rotas sob /api (exceto /api/token)
    $app->add(function (Request $request, RequestHandler $handler) use ($container): Response {
        $path = $request->getUri()->getPath();

        if ($request->getMethod() === 'OPTIONS' || !str_starts_with($path, '/api') || $path === '/api/token') {
            return $handler->handle($request);
        }

        $token = $request->getHeaderLine('X-Token');
        $secretKey = $container->get(SettingsInterface::class)->get('secretKey');

        try {
            if ($token === '') {
                throw new \UnexpectedValueException('Token não informado.');
            }
            JWT::decode($token, new Key($secretKey, 'HS256'));
        } catch (\Throwable) {
            $response = (new ResponseFactory())->createResponse(401);
            $response->getBody()->write(json_encode(['status' => 'erro', 'mensagem' => 'Token inválido ou ausente.']));
            return $response->withHeader('Content-Type', 'application/json');
        }

        return $handler->handle($request);
    });

    // CORS
    $app->add(function (Request $request, RequestHandler $handler): Response {
        $response = $handler->handle($request);
        return $response
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization, X-Token')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
    });
};
