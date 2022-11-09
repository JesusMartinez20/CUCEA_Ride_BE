<?php
    require 'BD.php';
    include('Token/Token.php');
    $_POST = json_decode(file_get_contents('php://input'), true);
    $output = new \stdClass();
    $token = new Token();

    if(isset($_POST)){
        $conn->beginTransaction();
        try {
            $id=$token->getClaimValue($_POST['token'], "id");

            $sql = "DELETE FROM solicitud WHERE id_ride=:id_ride";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_ride', $_POST['id']);
            $stmt->execute();
            
            $sql = "DELETE FROM paradas WHERE id_ride=:id_ride";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_ride', $_POST['id']);
            $stmt->execute();

            $sql = "DELETE FROM ride WHERE id=:id_ride";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_ride', $_POST['id']);
            $stmt->execute();

            $conn->commit();

            $message = '¡El ride ha sido eliminado!';

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