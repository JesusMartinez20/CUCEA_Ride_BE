<?php
    require 'BD.php';
    include('Token/Token.php');
    $headers = apache_request_headers();
    header('Content-type: application/json');
    $token = new Token();

    $id = $token->getClaimValue($headers['Authorization'], "id");

    $results=[];
    $records = $conn->prepare('select * from carro where id_alumno='.$id.';');
    $records->execute();

    foreach($records as $res){
        $carros[]=$res;
    }

    header('Content-type: application/json');
    echo json_encode($carros);
    http_response_code(200);
?>