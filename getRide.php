<?php
    require 'BD.php';
    include('Token/Token.php');
    $headers = apache_request_headers();
    header('Content-type: application/json');
    $token = new Token();

    $results=[];
    $id = json_decode($_REQUEST['id']);
    $id_alumno = $token->getClaimValue($headers['Authorization'], "id");


    $records = $conn->prepare('select 
                                ride.id, ride.espacios_disponibles, ride.hora_salida, ride.lugar_salida, ride.sentido, ride.id_conductor, ride.espacios, 
                                alumno.nombre, alumno.apellido, 
                                carro.modelo, carro.marca, carro.placas, carro.color 
                                from alumno 
                                join ride on ride.id_conductor=alumno.matricula
                                join carro on carro.id_alumno=alumno.matricula
                                where ride.id='.$id.' group by ride.id;');
    $records->execute();

    foreach($records as $res){
        $rides[]=$res;
    }

    $records = $conn->prepare('select * from paradas where id_ride='.$id.';');
    $records->execute();

    $paradas=[];

    foreach($records as $res){
        $paradas[]=$res;
    }

    $records = $conn->prepare('select solicitud.id
                                from solicitud 
                                join ride on solicitud.id_ride=ride.id 
                                where id_alumno='.$id_alumno.' and id_ride='.$id.' and ride.hora_salida>= NOW() - INTERVAL 2 HOUR;');
    $records->execute();

    $solicitud=[];
    foreach($records as $res){
        $solicitud[]=$res;
    }

    $conductor=false;
    if($rides[0]['id_conductor']==$id_alumno){
        $conductor=true;
    }

    $records = $conn->prepare('select alumno.nombre, alumno.apellido, paradas.lugar
                                from solicitud
                                join alumno on solicitud.id_alumno=alumno.matricula
                                join paradas on solicitud.id_parada=paradas.id
                                where solicitud.id_ride='.$id.';');
    $records->execute();

    $solicitudesConductores=[];
    foreach($records as $res){
        $solicitudesConductores[]=$res;
    }
    
    $response=[5];
    $response[0]=$rides;
    $response[1]=$paradas;
    $response[2]=$solicitud;
    $response[3]=$conductor;
    $response[4]=$solicitudesConductores;

    header('Content-type: application/json');
    echo json_encode($response);
    http_response_code(200);
?>