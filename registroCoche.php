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
            $sql = "INSERT INTO carro VALUES (:placas, :marca, :modelo, :color, :id)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':placas', $_POST['placas']);
            $stmt->bindParam(':marca', $_POST['marca']);
            $stmt->bindParam(':modelo', $_POST['modelo']);
            $stmt->bindParam(':color', $_POST['color']);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $message = '¡El carro ha sido agregado!';
            $conn->commit();

            $output->message = $message;

            echo json_encode($message);
            http_response_code(200);
        } catch (\Throwable $th) {
            $conn->rollBack();
            $message="Ha ocurrido un error, intentelo de nuevo";
            echo json_encode($message);
        }
        http_response_code(200);
    }else{
        echo json_encode("Acceso no autorizado");
        http_response_code(403); 
    }

    header('Content-type: application/json');
    http_response_code(200);
?>