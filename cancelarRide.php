<?php
    require 'BD.php';
    include('Token/Token.php');
    $_POST = json_decode(file_get_contents('php://input'), true);
    $output = new \stdClass();
    $token = new Token();

    if(isset($_POST)){
        $conn->beginTransaction();
        try {
            $id=$_POST['id'];
            $sql = "DELETE FROM solicitud WHERE id=:id;";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $sql = "UPDATE ride SET espacios_disponibles=espacios_disponibles+1 WHERE id=:id_ride;";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_ride', $_POST['id_ride']);
            $stmt->execute();
            $message = '¡La solicitud ha sido cancelada!';
            $conn->commit();

            $output->message = $message;

            echo json_encode($message);
            http_response_code(200);
        } catch (\Throwable $th) {
            $conn->rollBack();
            $message="Ha ocurrido un error, intentelo de nuevo";
            echo json_encode($_POST['id']);
        }
        http_response_code(200);
    }else{
        echo json_encode("Acceso no autorizado");
        http_response_code(403); 
    }

    header('Content-type: application/json');
    http_response_code(200);
?>