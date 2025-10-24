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

// GET /connections

$app->get('/connections', function ($request, $response, $args) use ($pdo) {
    $consulta = $pdo->query("
        SELECT 
            ao.nombre AS aeropuerto_origen,
            co.nombre AS ciudad_origen,
            ad.nombre AS aeropuerto_destino,
            cd.nombre AS ciudad_destino
        FROM conexionesSinEscalas cs
        JOIN aeropuertos ao ON cs.id_aeropuertoOrigen = ao.id_aeropuerto
        JOIN ciudad co ON ao.ciudadId = co.id_ciudad
        JOIN aeropuertos ad ON cs.id_aeropuertoDestino = ad.id_aeropuerto
        JOIN ciudad cd ON ad.ciudadId = cd.id_ciudad
    ");

    $conexiones = $consulta->fetchAll(PDO::FETCH_ASSOC);
    
    $datos = [
        'total_aeropuetos' => count($conexiones),
        'aeropuertos' => $conexiones
    ];

    $response->getBody()->write(json_encode($datos, JSON_PRETTY_PRINT));

    return $response->withHeader('Content-Type', 'application/json');
});

// GET /connections/:from/:to

$app->get('/connections/{from}/{to}', function ($request, $response, $args) use ($pdo) {
    $idOrigen = (int) $args['from'];
    $idDestino = (int) $args['to'];

    $consulta = $pdo->prepare("
        SELECT 
            ao.nombre AS aeropuerto_origen,
            co.nombre AS ciudad_origen,
            ad.nombre AS aeropuerto_destino,
            cd.nombre AS ciudad_destino
        FROM conexionesSinEscalas cs
        JOIN aeropuertos ao ON cs.id_aeropuertoOrigen = ao.id_aeropuerto
        JOIN ciudad co ON ao.ciudadId = co.id_ciudad
        JOIN aeropuertos ad ON cs.id_aeropuertoDestino = ad.id_aeropuerto
        JOIN ciudad cd ON ad.ciudadId = cd.id_ciudad
        WHERE ao.id_aeropuerto = ?
        AND ad.id_aeropuerto = ?
    ");
    $consulta->execute([$idOrigen, $idDestino]);
    
    $conexion = $consulta->fetch(PDO::FETCH_ASSOC);

    if(!$conexion) {
        echo ("<p>No hay conexiones entre estos aeropuertos</p>");
    } else {
        $datos = [
            "conexion" => $conexion
        ];

        $response->getBody()->write(json_encode($datos, JSON_PRETTY_PRINT));
    }

    return $response->withHeader('Content-Type', 'application/json');
});

// GET /airport/:id/connections

$app->get('/airport/{id}/connections', function ($request, $response, $args) use ($pdo) {
    $idAeropuerto = (int) $args['id'];

    $consulta = $pdo->prepare("
        SELECT 
            ao.nombre AS aeropuerto_origen,
            co.nombre AS ciudad_origen,
            ad.nombre AS aeropuerto_destino,
            cd.nombre AS ciudad_destino
        FROM conexionesSinEscalas cs
        JOIN aeropuertos ao ON cs.id_aeropuertoOrigen = ao.id_aeropuerto
        JOIN ciudad co ON ao.ciudadId = co.id_ciudad
        JOIN aeropuertos ad ON cs.id_aeropuertoDestino = ad.id_aeropuerto
        JOIN ciudad cd ON ad.ciudadId = cd.id_ciudad
        WHERE ao.id_aeropuerto = ? OR ad.id_aeropuerto = ?
    ");
    $consulta->execute([$idAeropuerto, $idAeropuerto]);
    
    $conexion = $consulta->fetchAll(PDO::FETCH_ASSOC);

    if(!$conexion) {
        echo ("<p>Este aeropuerto no tiene conexiones</p>");
    } else {
        $datos = [
            "conexion" => $conexion
        ];

        $response->getBody()->write(json_encode($datos, JSON_PRETTY_PRINT));
    }

    return $response->withHeader('Content-Type', 'application/json');
});