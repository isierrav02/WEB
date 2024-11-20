<?php
session_start();
include 'publipistaBD.php';

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
        // Redirigir a index.php con el mensaje de error
        header("Location: index.php?error=" . urlencode("El email ya está registrado."));
        exit();
    } else {
        // Insertar el nuevo usuario en la base de datos
        $sql = "INSERT INTO usuarios (nombre, apellidos, email, telefono, contrasena) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $nombre, $apellidos, $email, $telefono, $contrasena);

        if ($stmt->execute()) {
            $_SESSION['email'] = $email;

            // Guardar datos en el archivo de texto usuarios.txt
            $file_path = 'usuarios.txt';
            $user_data = "$nombre,$apellidos,$email,$telefono" . PHP_EOL;
            file_put_contents($file_path, $user_data, FILE_APPEND);

            // Redirigir a pistas.php si el registro fue exitoso
            header("Location: pistas.php");
            exit();
        } else {
            // Enviar mensaje de error en caso de fallo al registrar
            header("Location: index.php?error=" . urlencode("Error al registrar el usuario."));
            exit();
        }
    }
}
