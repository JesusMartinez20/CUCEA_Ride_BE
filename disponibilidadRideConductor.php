<?php
    require 'BD.php';
    include('Token/Token.php');
    $headers = apache_request_headers();
    header('Content-type: application/json');
    $token = new Token();
    $message="";

    $id = $token->getClaimValue($headers['Authorization'], "id");

    $records = $conn->prepare('select * from ride where id_conductor='.$id.' and hora_salida >= NOW() - INTERVAL 8 HOUR order by hora_salida limit 1;');
    $records->execute();

    foreach($records as $res){
        $rides[]=$res;
    }
    
    $records = $conn->prepare('select * from carro where id_alumno='.$id.';');
    $records->execute();

    foreach($records as $res){
        $carros[]=$res;
    }

    $records = $conn->prepare('select solicitud.id, solicitud.id_alumno, ride.hora_salida from solicitud join ride on solicitud.id_ride=ride.id where solicitud.id_alumno='.$id.' and ride.hora_salida>= NOW() - INTERVAL 2 HOUR;');
    $records->execute();

    foreach($records as $res){
        $solicitudes[]=$res;
    }

    if(empty($carros)){
        $message="Debes tener un carro registrado para poder ofrecer rides";
    }else if(!empty($rides)){
        $message="Tienes un ride en progreso o que esta por empezar, por el momento no puedes ofrecer rides";
    }else if(!empty($solicitudes)){
        $message="Has solicitado un ride, no puedes ofrecer uno por el momento";
    }else{
        $message="OK";
    }

    header('Content-type: application/json');
    echo json_encode($message);
    http_response_code(200);
?>