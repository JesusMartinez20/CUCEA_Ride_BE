<?php
    define( 'WP_MAX_MEMORY_LIMIT' , '512M' );
    header('Content-Type: application/json');
    require 'BD.php';
    $inicio=json_decode($_REQUEST['inicio']);
    $fin=json_decode($_REQUEST['fin']);
    $id=json_decode($_REQUEST['id']);

    if ($inicio=="Invalid Date" ||$fin=="Invalid Date"){
        $records = $conn->prepare('select * from sensordata where sensorID="'.$id.'" and reading_time>=(NOW() - INTERVAL 60 DAY) AND FlujoIn!="0.00" order BY reading_time;');
    }else{
        //echo 'select * from sensordata where sensor="'.$id.'" and reading_time>=str_to_date("'.$inicio.'", "%d/%m/%Y") and reading_time<=str_to_date("'.$fin.'", "%d/%m/%Y") order BY reading_time;';
        $records = $conn->prepare('select * from sensordata where sensorID="'.$id.'" and reading_time>=str_to_date("'.$inicio.'", "%d/%m/%Y %H:%i:%S") and reading_time<=str_to_date("'.$fin.'", "%d/%m/%Y %H:%i:%S") AND FlujoIn!="0.00" order BY reading_time;');
        //$records = $conn->prepare('select * from sensordata where sensorID="'.$id.'" and reading_time>=str_to_date("10/11/2021 0:02:14", "%d/%m/%Y %H:%i:%S") and reading_time<=str_to_date("24/11/2021 0:02:24", "%d/%m/%Y %H:%i:%S") order BY reading_time;');
    }

    $results=[];
    $records->execute();

    foreach($records as $res){
        $results[]=$res;
    }

    echo json_encode($results);
    http_response_code(200);
?>