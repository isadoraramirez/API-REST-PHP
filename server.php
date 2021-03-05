<?php

// el servidor se levanta con este comando :php -S localhost:8000 router.php
//para levantar el otro server el de autenticacion es igual pero puerto 8001:  php -S localhost:8001 auth_server.php
//las peticiones se hacen atravez de curl

//  autenticacion por via http, muy insegura, nada recomendable////////////
// $user = array_key_exists( 'PHP_AUTH_USER', $_SERVER ) ? $_SERVER['PHP_AUTH_USER']: '';
// $pwd = array_key_exists( 'PHP_AUTH_PW', $_SERVER ) ? $_SERVER['PHP_AUTH_PW']: '';
// if ( $user !== 'eli1' || $pwd !== '1234'){
// 	die;
// }

//autenticacio HMAC://////////////////////////
// if (
// 	!array_key_exists('HTTP_X_HASH',$_SERVER)||
// 	!array_key_exists('HTTP_X_TIMESTAMP',$_SERVER)||
// 	!array_key_exists('HTTP_X_UID',$_SERVER)
// ) {
// 	die;
// }

// list( $hash, $uid, $timestamp ) = [
// 	$_SERVER['HTTP_X_HASH'],
// 	$_SERVER['HTTP_X_UID'],
// 	$_SERVER['HTTP_X_TIMESTAMP'],
// ];

// $secret = 'esta es la clave secreta';

// $newHash = sha1($uid.$timestamp.$secret);

// if ( $newHash !== $hash ) {
// 	die;
// }

// Autenticacion via access tokens, segura////////////////

if( !array_key_exists( 'HTTP_X_TOKEN',$_SERVER)){
    die;
}

$url = 'http://localhost:8001';

$ch = curl_init( $url );
curl_setopt(
    $ch,
    CURLOPT_HTTPHEADER,
    [
        "X-Token: {$_SERVER['HTTP_X_TOKEN']}",
    ]);
    curl_setopt(
        $ch,
        CURLOPT_RETURNTRANSFER,
        true
    );
    //llamamos
    $ret = curl_exec($ch);
    if($ret !== 'true'){
        die;
    }

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