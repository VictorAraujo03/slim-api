<?php

declare(strict_types=1);

use DI\ContainerBuilder;

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$containerBuilder = new ContainerBuilder();
$settings = require __DIR__ . '/app/settings.php';
$settings($containerBuilder);
$dependencies = require __DIR__ . '/app/dependencies.php';
$dependencies($containerBuilder);

$db = $containerBuilder->build()->get('db');
$schema = $db->schema();
$tabela = 'produtos';

if (!$schema->hasTable($tabela)) {
    $schema->create($tabela, function ($table) {
        $table->increments('id');
        $table->string('titulo', 100);
        $table->text('descricao');
        $table->decimal('preco', 11, 2);
        $table->string('fabricante', 60);
        $table->timestamps();
    });
}

$produtos = [
    [
        'titulo' => 'Smartphone Motorola Moto G6 32GB Dual Chip',
        'descricao' => 'Android Oreo 8.0, tela 5.7, octa-core 1.8 GHz, 4G, câmera 12 + 5MP.',
        'preco' => 899.00,
        'fabricante' => 'Motorola',
        'created_at' => '2019-10-22',
        'updated_at' => '2019-10-22',
    ],
    [
        'titulo' => 'iPhone X Cinza Espacial 64GB',
        'descricao' => 'Tela 5.8, iOS 12, 4G, Wi-Fi, câmera 12MP.',
        'preco' => 4999.00,
        'fabricante' => 'Apple',
        'created_at' => '2020-01-10',
        'updated_at' => '2020-01-10',
    ],
];

foreach ($produtos as $produto) {
    $db->table($tabela)->updateOrInsert(['titulo' => $produto['titulo']], $produto);
}

echo "Tabela produtos pronta.\n";
