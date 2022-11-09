<?php
    require 'BD.php';
    include('Token/Token.php');
    $_POST = json_decode(file_get_contents('php://input'), true);
    $output = new \stdClass();
    $token = new Token();

    if(isset($_POST)){
        $conn->beginTransaction();
        try {
            $id_ride=$_POST['id_ride'];
            $id=$token->getClaimValue($_POST['token'], "id");

            $records = $conn->prepare('select * from ride where id_conductor='.$id.' and hora_salida >= NOW() - INTERVAL 8 HOUR order by hora_salida limit 1;');
            $records->execute();

            foreach($records as $res){
                $rides[]=$res;
            }

            $records = $conn->prepare('select * from solicitud join ride on solicitud.id_ride=ride.id where id_alumno='.$id.' and ride.hora_salida >= NOW() - INTERVAL 8 HOUR order by hora_salida limit 1;');
            $records->execute();

            foreach($records as $res){
                $solicitudes[]=$res;
            }

            if(!empty($solicitudes)){
                $message="Ya has solicitado un ride que esta en progreso o por empezar, por el momento no puedes solicitar más rides";
            }else if(!empty($rides)){
                $message="Tienes un ride en progreso o que esta por empezar, por el momento no puedes solicitar rides";
            }else{
                $records = $conn->prepare('select * from ride where id='.$id_ride.';');
                $records->execute();

                foreach($records as $res){
                    $asientos[]=$res;
                }

                if($asientos[0]['espacios_disponibles']>0){
                    $sql="UPDATE ride SET espacios_disponibles=espacios_disponibles-1 WHERE id=:id_ride;";
                    $records=$conn->prepare($sql);
                    $records->bindParam(':id_ride',$id_ride);
                    $records->execute();

                    $sql="INSERT INTO solicitud VALUES ('',:id_alumno,:id_ride,:id_parada)";
                    $records=$conn->prepare($sql);
                    $records->bindParam(':id_alumno',$id);
                    $records->bindParam(':id_ride',$id_ride);
                    $records->bindParam(':id_parada',$_POST['id_parada']);
                    $records->execute();
                    $message = '¡El ride ha sido agregado!';
                }else{
                    $message = 'Lo sentimos, no hay mas asientos dispobibles';
                }

                $output->message = $message;
                $conn->commit();
            }
            
            echo json_encode($message);
            http_response_code(200);
        } catch (\Throwable $th) {
            $conn->rollBack();
            $message="Ha ocurrido un error, intentelo de nuevo";
            echo json_encode($th->getMessage());
        }
        http_response_code(200);
    }else{
        echo json_encode("Acceso no autorizado");
        http_response_code(403); 
    }

    header('Content-type: application/json');
    http_response_code(200);
?>