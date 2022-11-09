<?php
    date_default_timezone_set('America/Mexico_City');
    require 'BD.php';
    $id=json_decode($_REQUEST['id']);

    $results=[];
    $records = $conn->prepare('select * from sensordata where sensorID="'.$id.'" and reading_time in (SELECT max(reading_time) FROM sensordata where sensorID="'.$id.'" ) order by reading_time;');
    $records->execute();

    foreach($records as $res){
        $sensors[]=$res;
    }

    header('Content-type: application/json');
    echo json_encode($sensors);
    http_response_code(200);
?>