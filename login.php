<?php
    include('Token/Token.php');
    $_POST = json_decode(file_get_contents('php://input'), true);
    $output = new \stdClass();
    $token = new Token();
    require ("BD.php");

    if(isset($_POST) && !empty($_POST)) {
        $username = $_POST['matricula'];
        $password = $_POST['contrasena'];

        $stmt = $conn->prepare("SELECT * FROM alumno WHERE matricula = ? AND contrasena = ?");

        if ($stmt->execute(array($username, md5($password)))) {
            foreach ($stmt as $row) {
                $array = array(
                    'id' => $row['matricula'],
                    'nombre' => $row['nombre']
                );
            }
            if (!empty($array)){
                $output->state = true;
                $output->username = $array['nombre'];
                $output->token = $token->create( $array['id']);
            }else{
                $output->state = false;
                $output->message = "Credenciales inválidas.";
            }
        }else {
            $output->state = false;
            $output->message = "Consulta fallida.";
        }
    }else {
        $output->state = false;
        $output->message = "Sólo se permite acceso por POST.";
    }

    $myJSON = json_encode($output);

    header('Content-type: application/json');
    echo $myJSON;
    http_response_code(200);
?>