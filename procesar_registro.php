<?php
session_start();
include 'publipistaBD.php';

$error = "";
$response = ["success" => false];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);

    // Verificar si el correo ya está registrado
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "El email ya está registrado.";
        $response["error"] = $error;
    } else {
        // Insertar el nuevo usuario en la base de datos
        $sql = "INSERT INTO usuarios (nombre, apellidos, email, telefono, contrasena) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $nombre, $apellidos, $email, $telefono, $contrasena);

        if ($stmt->execute()) {
            $_SESSION['email'] = $email;

            // Guardar los datos en el archivo usuarios.txt
            $file_path = 'usuarios.txt';
            $user_data = "$nombre,$apellidos,$email,$telefono" . PHP_EOL;

            file_put_contents($file_path, $user_data, FILE_APPEND);

            // Redirigir a pistas.php después de registrarse
            $response["success"] = true;
        } else {
            $error = "Error al registrar el usuario.";
            $response["error"] = $error;
        }
    }

    echo json_encode($response);
}
