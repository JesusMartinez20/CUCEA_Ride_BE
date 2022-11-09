<?php
    date_default_timezone_set('America/Mexico_City');
    require 'BD.php';

    $results=[];
    $records = $conn->prepare('select * from sensordata where reading_time in (SELECT max(reading_time) FROM sensordata GROUP BY sensorID ) order by reading_time;');
    $records->execute();

    foreach($records as $res){
        $sensors[]=$res;
    }

    $colors=['#A8DFED','#CBAACB','#FFFFB5','#FFCCB6','#F3B0C4','#B6CFB6','#FF968A','#F6EAC2','#ECEAE4','#FEE1E8','#CCE2CB','#DEA5A4','#5D9B9B','#E7FFAC','#C4FAF8','#F7BD56'];

    $sensors=json_decode(json_encode($sensors), FALSE);

    $date_check  = date("Y-m-d H:i:s",mktime(date("H"), date("i")-5, 0, date("m"), date("d"),   date("Y")));

    $error=FALSE;

    foreach ($sensors as $i => $sensor) {
    $sensor->color='#ddd';
    }

    foreach ($sensors as $i => $sensor) {
    for ($i=0; $i < 5; $i++) { 
        $sensor->message[$i]=null;
    }

    /*if($sensor->value2>20 || $sensor->value2<0){
        $sensor->message[0]="• La temperatura ha sobrepasado el límite.";
        $sensor->color='#FF6961';
    }
    if($sensor->value1>20 || $sensor->value1<0){
        $sensor->message[1]="• La húmedad ha sobrepasado el límite.";
        $sensor->color='#FF6961';
    }
    if($sensor->value3>20 || $sensor->value3<0){
        $sensor->message[2]="• El flujo ha sobrepasado el límite.";
        $sensor->color='#FF6961';
    }
    if($sensor->value4>20 || $sensor->value4<0){
        $sensor->message[3]="• El PH ha sobrepasado el límite.";
        $sensor->color='#FF6961';
    }
    if($sensor->value5>20 || $sensor->value5<0){
        $sensor->message[4]="• La conductividad electrica ha sobrepasado el límite.";
        $sensor->color='#FF6961';
    }*/

    if($sensor->color!='#FF6961'){
        if($sensor->reading_time<$date_check){
        $sensor->color='#ddd';
        }else{
        $sensor->color='#CCE2BC';
        }
    }
    }

    $sensors = json_decode(json_encode($sensors), true);
    $myJSON = json_encode($sensors);
    header('Content-type: application/json');
    echo $myJSON;
    http_response_code(200);
?>