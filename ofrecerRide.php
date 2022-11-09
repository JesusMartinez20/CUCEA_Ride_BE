<?php
    require 'BD.php';
    include('Token/Token.php');
    $_POST = json_decode(file_get_contents('php://input'), true);
    $output = new \stdClass();
    $token = new Token();

    if(isset($_POST)){
        $paradas=$_POST['paradas'];
        $conn->beginTransaction();
        try {
            $id=$token->getClaimValue($_POST['token'], "id");
            $sql = "INSERT INTO ride VALUES ('', str_to_date(:hora_salida, '%d/%m/%Y, %H:%i:%S'), :sentido, :lugar_salida, :espacios, :espacios, :id)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':hora_salida', $_POST['hora_salida']);
            $stmt->bindParam(':sentido', $_POST['sentido']);
            $stmt->bindParam(':lugar_salida', $_POST['lugar_salida']);
            $stmt->bindParam(':espacios', $_POST['espacios']);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $last = $conn->lastInsertId();
            
            foreach($paradas as $i=>$p){
                $sql = "INSERT INTO paradas VALUES ('', :lugar, :orden, :id_ride)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':lugar', $p);
                $stmt->bindParam(':orden', $i);
                $stmt->bindParam(':id_ride', $last);
                $stmt->execute();
            }

            $conn->commit();

            $message = '¡El ride ha sido agregado!';
            $output->message = $message;

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