<?php
    require 'BD.php';
    include('Token/Token.php');
    $_POST = json_decode(file_get_contents('php://input'), true);
    $output = new \stdClass();
    $token = new Token();

    if(isset($_POST)){
        if (!empty($_POST['matricula']) && !empty($_POST['contrasena'])) {
            if($_POST['contrasena']==$_POST['confirm_contrasena']){
                $conn->beginTransaction();
                try {
                    $sql = "INSERT INTO alumno VALUES (:matricula, :contrasena, :nombre, :apellido, :correo)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':matricula', $_POST['matricula']);
                    $password = md5($_POST['contrasena']);
                    $stmt->bindParam(':contrasena', $password);
                    $stmt->bindParam(':nombre', $_POST['nombre']);
                    $stmt->bindParam(':apellido', $_POST['apellido']);
                    $stmt->bindParam(':correo', $_POST['correo']);
                    $stmt->execute();
                    $message = '¡La cuenta ha sido creada!';
                    $conn->commit();

                    $output->state = true;
                    $output->username = $_POST['nombre'];
                    $output->token = $token->create( $_POST['matricula']);

                    echo json_encode($output);
                    http_response_code(200);
                } catch (\Throwable $th) {
                    $conn->rollBack();
                    $message="El usuario ya existe, intente otro nombre";
                    echo json_encode($message);
                }
            }else{
                $message = 'Las contraseñas no coinciden';
                echo json_encode($message);
            }
        }else{
            $message = 'Formulario incompleto';
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