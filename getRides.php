<?php
    require 'BD.php';
    include('Token/Token.php');
    $headers = apache_request_headers();
    header('Content-type: application/json');
    $token = new Token();

    $rides=[];
    $records = $conn->prepare('select 
                                ride.id, ride.espacios_disponibles, ride.hora_salida, ride.lugar_salida, ride.sentido, 
                                alumno.nombre, alumno.apellido,
                                carro.modelo, carro.marca
                                from ride 
                                join alumno on ride.id_conductor=alumno.matricula
                                join carro on carro.id_alumno=alumno.matricula
                                where ride.hora_salida >= NOW() 
                                and ride.espacios_disponibles>0
                                order by ride.hora_salida DESC;');

    $records->execute();

    foreach($records as $res){
        $rides[]=$res;
    }

    header('Content-type: application/json');
    echo json_encode($rides);
    http_response_code(200);
?>