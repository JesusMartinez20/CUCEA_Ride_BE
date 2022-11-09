<?php
    require 'BD.php';
    include('Token/Token.php');
    $headers = apache_request_headers();
    header('Content-type: application/json');
    $token = new Token();

    $id = $token->getClaimValue($headers['Authorization'], "id");

    $results=[];
    $records = $conn->prepare('select 
                                ride.id, ride.espacios_disponibles, ride.hora_salida, ride.lugar_salida, ride.sentido, 
                                alumno.nombre, alumno.apellido,
                                carro.modelo, carro.marca
                                from ride 
                                join alumno on ride.id_conductor=alumno.matricula
                                join carro on carro.id_alumno=alumno.matricula
                                where ride.id_conductor='.$id.'
                                order by ride.hora_salida DESC;');
    $records->execute();

    foreach($records as $res){
        $results[]=$res;
    }

    header('Content-type: application/json');
    echo json_encode($results);
    http_response_code(200);
?>