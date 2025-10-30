<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request; 
use Slim\Factory\AppFactory;

require __DIR__ . '/../bd/bd.php';
$db = new Database();
$pdo = $db->connect();

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

// GET /connections/with-stops/:from/:to

$app->get('/connections/with-stops/{from}/{to}', function ($request, $response, $args) use ($pdo) {
    $idOrigen = (int) $args['from'];
    $idDestino = (int) $args['to'];

    $consulta1 = $pdo->prepare("
        SELECT 
            ao.nombre AS origen,
            ai.nombre AS escala1,
            ad.nombre AS destino
        FROM conexionesSinEscalas c1
        JOIN aeropuertos ao ON c1.id_aeropuertoOrigen = ao.id_aeropuerto
        JOIN aeropuertos ai ON c1.id_aeropuertoDestino = ai.id_aeropuerto
        JOIN aeropuertos ad ON ad.id_aeropuerto = :to
        WHERE ao.id_aeropuerto = :from
        AND ai.id_aeropuerto != :from
        AND ai.id_aeropuerto != :to
    ");
    $consulta1->execute([':from' => $idOrigen, ':to' => $idDestino]);
    $rutas1 = $consulta1->fetchAll(PDO::FETCH_ASSOC);

    $consulta2 = $pdo->prepare("
        SELECT 
            ao.nombre AS origen,
            ai1.nombre AS escala1,
            ai2.nombre AS escala2,
            ad.nombre AS destino
        FROM conexionesSinEscalas c1
        JOIN conexionesSinEscalas c2 ON c1.id_aeropuertoDestino = c2.id_aeropuertoOrigen
        JOIN conexionesSinEscalas c3 ON c2.id_aeropuertoDestino = c3.id_aeropuertoOrigen
        JOIN aeropuertos ao ON c1.id_aeropuertoOrigen = ao.id_aeropuerto
        JOIN aeropuertos ai1 ON c1.id_aeropuertoDestino = ai1.id_aeropuerto
        JOIN aeropuertos ai2 ON c2.id_aeropuertoDestino = ai2.id_aeropuerto
        JOIN aeropuertos ad  ON c3.id_aeropuertoDestino = ad.id_aeropuerto
        WHERE ao.id_aeropuerto = :from
        AND ad.id_aeropuerto = :to
        AND ai1.id_aeropuerto NOT IN (:from,:to)
        AND ai2.id_aeropuerto NOT IN (:from,:to);
    ");
    $consulta2->execute([':from' => $idOrigen, ':to' => $idDestino]);
    $rutas2 = $consulta2->fetchAll(PDO::FETCH_ASSOC);

    $rutas = array_merge($rutas1, $rutas2);

    $datos = [
        "Conexiones con escalas" => $rutas
    ];

    $response->getBody()->write(json_encode($datos, JSON_PRETTY_PRINT));

    return $response->withHeader('Content-Type', 'application/json');
});

// GET /airport/:id/connections/with-stops

$app->get('/airport/{id}/connections/with-stops', function ($request, $response, $args) use ($pdo) {
    $idOrigen = (int) $args['id'];
    
    $consulta1 = $pdo->prepare("
        SELECT
            ao.nombre AS origen,
            ai.nombre AS escala,
            ad.nombre AS destino
        FROM conexionesSinEscalas c1
        JOIN conexionesSinEscalas c2 ON c1.id_aeropuertoDestino = c2.id_aeropuertoOrigen
        JOIN aeropuertos ao ON c1.id_aeropuertoOrigen = ao.id_aeropuerto
        JOIN aeropuertos ai ON c1.id_aeropuertoDestino = ai.id_aeropuerto
        JOIN aeropuertos ad ON c2.id_aeropuertoDestino = ad.id_aeropuerto
        WHERE ao.id_aeropuerto = :from
        AND ad.id_aeropuerto != :from
        AND ai.id_aeropuerto NOT IN (:from, ad.id_aeropuerto)
    ");

    $consulta1->execute([':from' => $idOrigen]);
    $rutas1 = $consulta1->fetchAll(PDO::FETCH_ASSOC);

    $consulta2 = $pdo->prepare("
        SELECT 
            ao.nombre AS origen,
            ai1.nombre AS escala1,
            ai2.nombre AS escala2,
            ad.nombre AS destino
        FROM conexionesSinEscalas c1
        JOIN conexionesSinEscalas c2 ON c1.id_aeropuertoDestino = c2.id_aeropuertoOrigen
        JOIN conexionesSinEscalas c3 ON c2.id_aeropuertoDestino = c3.id_aeropuertoOrigen
        JOIN aeropuertos ao ON c1.id_aeropuertoOrigen = ao.id_aeropuerto
        JOIN aeropuertos ai1 ON c1.id_aeropuertoDestino = ai1.id_aeropuerto
        JOIN aeropuertos ai2 ON c2.id_aeropuertoDestino = ai2.id_aeropuerto
        JOIN aeropuertos ad  ON c3.id_aeropuertoDestino = ad.id_aeropuerto
        WHERE ao.id_aeropuerto = :from
        AND ai1.id_aeropuerto NOT IN (:from, ad.id_aeropuerto)
        AND ai2.id_aeropuerto NOT IN (:from, ad.id_aeropuerto)
        AND ad.id_aeropuerto != :from
    ");

    $consulta2->execute([':from' => $idOrigen]);
    $rutas2 = $consulta2->fetchAll(PDO::FETCH_ASSOC);

    $rutas = array_merge($rutas1, $rutas2);

    $datos = [
        "Conexiones con escalas desde aeropuerto $idOrigen" => $rutas
    ];

    $response->getBody()->write(json_encode($datos, JSON_PRETTY_PRINT));

    return $response->withHeader('Content-Type', 'application/json');
});

// POST /airports

$app->post('/airports', function ($request, $response, $args) use ($pdo) {
    $data = json_decode($request->getBody()->getContents(), true);
    $nombre = $data['nombre'] ?? '';
    $iata = $data['iata'] ?? '';
    $ciudadId = $data['ciudadId'];
    $tipo = $data['tipo'];
    $latitud = $data['latitud'];
    $longitud = $data['longitud'];
    $elevacion = $data['elevacion'];
    $terminales = $data['terminales'];
    $anyoApertura = $data['anyoApertura'];

    try {
        $consulta = $pdo->prepare("
            INSERT INTO aeropuertos
            (nombre, iata, ciudadId, tipo, latitud, longitud, elevacion, terminales, anyoApertura) VALUES
            (:nombre, :iata, :ciudadId, :tipo, :latitud, :longitud, :elevacion, :terminales, :anyoApertura)
        ");

        $consulta->execute([
            ':nombre' => $nombre,
            ':iata' => $iata,
            ':ciudadId' => $ciudadId,
            ':tipo' => $tipo,
            ':latitud' => $latitud,
            ':longitud' => $longitud,
            ':elevacion' => $elevacion,
            ':terminales' => $terminales,
            ':anyoApertura' => $anyoApertura
        ]);

        $idNuevo = $pdo->lastInsertId();

        $response->getBody()->write(json_encode([
            'mensaje' => 'Aeropuerto introducido correctamente',
            'id_inserstado' => $idNuevo
        ], JSON_PRETTY_PRINT));

        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode([
            'error' => 'Error al insertar el aeropuerto',
            'detalle' => $e->getMessage()
        ], JSON_PRETTY_PRINT));

        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});

// PUT /airport/:id

$app->put('/airport/{id}', function ($request, $response, $args) use ($pdo) {
    $id = (int) $args["id"] ?? '';
    
    $datos= json_decode($request->getBody()->getContents(), true);
    $nombre = $datos['nombre'] ?? '';
    
    try {

        $consulta = $pdo->prepare("
            UPDATE aeropuertos
            SET nombre = :nombre
            WHERE id_aeropuerto = :id
        ");

        $consulta->execute([
            ":nombre" => $nombre,
            ":id" => $id
        ]);

        $response->getBody()->write(json_encode([
            'mensaje' => 'Aeropuerto actualizado correctamente'
        ]));

        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');


    } catch (PDOException $error) {
        $response->getBody()->write(json_encode([
            'error' => 'Error al actualizar el aeropuerto',
            'detalle' => $error->getMessage()
        ], JSON_PRETTY_PRINT));

        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});

// DELETE /airport/:id

$app->delete('/airport/{id}', function($request, $response, $args) use ($pdo) {
    $id = (int) $args["id"];

    $aeropuerto = $pdo->prepare("
        SELECT * FROM aeropuertos WHERE id_aeropuerto = :id
    ");

    $aeropuerto->execute([":id" => $id]);

    try {
        $consulta = $pdo->prepare("
            DELETE FROM aeropuertos WHERE id_aeropuerto = :id
        ");

        $consulta->execute(["id" => $id]);
        
        $response->getBody()->write(json_encode([
            'mensaje' => 'Aeropuerto eliminado correctamente'
        ], JSON_PRETTY_PRINT));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    } catch(PDOException $error) {
        $response->getBody->write(json_encode([
            'mensaje' => "No se ha podido eliminar el aeropuerto",
            'detalle' => $error
        ], JSON_PRETTY_PRINT));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }

});

// POST /connections

$app->post('/connections', function($request, $response, $args) use ($pdo) {
    $datos = json_decode($request->getBody()->getContents(), true);
    $aeropuertoOrigen = $datos["aeropuertoOrigen"] ?? '';
    $aeropuertoDestino = $datos["aeropuertoDestino"] ?? '';

    if ($aeropuertoOrigen != null && $aeropuertoDestino != null) {
        try {
            $consulta = $pdo->prepare("
                INSERT INTO conexionesSinEscalas (id_aeropuertoOrigen, id_aeropuertoDestino) VALUES
                (:id_aeropuertoOrigen, :id_aeropuertoDestino)
            ");

            $consulta->execute([
                ":id_aeropuertoOrigen" => $aeropuertoOrigen,
                ":id_aeropuertoDestino" => $aeropuertoDestino
            ]);

            $response->getBody()->write(json_encode([
                "mensaje" => "Conexión añadida con éxito"
            ], JSON_PRETTY_PRINT));

            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        
        } catch (PDOException $error) {
            $response->getBody()->write(json_encode([
                "error" => "No se ha podido crear la conexión",
                "detalles" => $error
            ], JSON_PRETTY_PRINT));

            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    } else {
        $response->getBody()->write(json_encode([
            "error" => "No se han enviado datos suficientes"
        ], JSON_PRETTY_PRINT));

        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});

// DELETE /connections/:id

$app->delete('/connections/{id}', function($request, $response, $args) use ($pdo) {
    $id = (int) $args["id"];

    try {
        $consulta = $pdo->prepare("
            DELETE FROM conexionesSinEscalas WHERE id = :id
        ");

        $consulta->execute([":id" => $id]);

        $response->getBody()->write(json_encode([
            "mensaje" => "Conexión eliminada con éxito"
        ], JSON_PRETTY_PRINT));

        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    } catch (PDOException $error) {
        $response->getBody()->write(json_encode([
            "error" => "No se ha podido eliminar la conexión",
            "detalles" => $error
        ], JSON_PRETTY_PRINT));

        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});