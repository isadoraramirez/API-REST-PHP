<?php

// Definimos los recursos disponibles
$allowedResourceType = [
    'books',
    'authors',
    'genres',
];

// Validamos que el recurso este disponible
$resourceType = $_GET['resource_type'];

if ( !in_array($resourceType, $allowedResourceType)) {
    die;
}

// Defino los recursos
$books = [
    1 => [
        'titulo' => 'Lo que el viento se llevo',
        'id_autor' => 2,
        'id_genero' => 2,
    ],
    2 => [
        'titulo' => 'La Iliada',
        'id_autor' => 1,
        'id_genero' => 1,
    ],
    3 => [
        'titulo' => 'La Odisea',
        'id_autor' => 1,
        'id_genero' => 1,
    ],
];

// Se indica al cliente que lo que recibirá es un json
header('Content-Type: application/json');

// Levantamos el id del recurso buscado
$resourceId = array_key_exists('resource_id', $_GET) ? $_GET['resource_id']:'';

// Generamos la respuesta asumiendo que el pedido es correcto
switch( strtoupper($_SERVER['REQUEST_METHOD']) ) {
    case 'GET':
         if ( empty( $resourceId ) ){
            echo json_encode( $books );
         }else{
            if( array_key_exists( $resourceId, $books) ){
                echo json_encode( $books[ $resourceId ] );
            }
        }
        break;
    case 'POST':
		// Tomamos la entrada "cruda"
		$json = file_get_contents('php://input');
		// Transformamos el json recibido a un nuevo elemento del array
		$books[] = json_decode($json, true);
		// emitimos hacia la salida la ultima clave del array
		// echo array_keys( $books )[ count($books) -1];
        echo json_encode( $books );
		break;
    case 'PUT':
		//validamos que el recuso buscado exista
		if (!empty($resourceId) && array_key_exists( $resourceId, $books) ){
		//tomamos la entrada cruda
		  $json = file_get_contents('php://input');
		 //transformamos el json recibido en un nuevo elemento
		 $books[ $resourceId]= json_decode($json, true);
        //retornamos la coleccion modificada en formato json
		 echo json_encode( $books );
		 }
        break;
    case 'DELETE':
		//validamos que el recuso buscado exista
		if (!empty($resourceId) && array_key_exists( $resourceId, $books) ){
			unset( $books [$resourceId]);
			 }
			  echo json_encode ( $books );
        break;
}