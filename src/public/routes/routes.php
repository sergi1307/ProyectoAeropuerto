<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request; 
use Slim\Factory\AppFactory;

// GET CIUDADES GENÉRICAS

$app->get('/cities', function ($request, $response, $args) use ($pdo) {
    $consulta = $pdo->query("SELECT * FROM ciudad");
    $ciudades = $consulta->fetchAll(PDO::FETCH_ASSOC);

    $datos = [
        'total_ciudades' => count($ciudades),
        'ciudades' => $ciudades
    ];

    $response->getBody()->write(json_encode($datos, JSON_PRETTY_PRINT));

    return $response->withHeader('Content-Type', 'application/json');
});

// GET CIUDADES ESPECÍFICAS

$app->get('/city/{id}', function ($request, $response, $args) use ($pdo) {
    $id = (int) $args['id'];

    $consulta = $pdo->prepare("SELECT * FROM ciudad WHERE id_ciudad = :id");
    $consulta->execute([':id' => $id]);

    $ciudad = $consulta->fetch(PDO::FETCH_ASSOC);

    if(!$ciudad) {
        echo("<p>Ciudad no encontrada</p>");
    } else {
        $datos = [
            'ciudades' => $ciudad
        ];

        $response->getBody()->write(json_encode($datos, JSON_PRETTY_PRINT));
    }
    
    return $response->withHeader('Content-Type', 'application/json');
});

// GET AEROPUERTOS GENÉRICOS

$app->get('/airports', function ($request, $response, $args) use ($pdo) {
    $consulta = $pdo->query("SELECT * FROM aeropuertos");
    $aeropuertos = $consulta->fetchAll(PDO::FETCH_ASSOC);

    $datos = [
        'total_aeropuetos' => count($aeropuertos),
        'aeropuertos' => $aeropuertos
    ];

    $response->getBody()->write(json_encode($datos, JSON_PRETTY_PRINT));

    return $response->withHeader('Content-Type', 'application/json');
});

// GET AEROPUERTOS ESPECÍFICOS

$app->get('/airport/{id}', function ($request, $response, $args) use ($pdo) {
    $id = (int) $args['id'];

    $consulta = $pdo->prepare("SELECT * FROM aeropuertos WHERE id_aeropuerto = :id");
    $consulta->execute([':id' => $id]);

    $aeropuerto = $consulta->fetch(PDO::FETCH_ASSOC);

    if(!$aeropuerto) {
        echo("<p>Aeropuerto no encontrado</p>");
    } else {
        $datos = [
            'aeropuerto' => $aeropuerto
        ];

        $response->getBody()->write(json_encode($datos, JSON_PRETTY_PRINT));
    }
    
    return $response->withHeader('Content-Type', 'application/json');
});