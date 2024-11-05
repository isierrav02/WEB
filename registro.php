<?php
session_start();
include 'publipistaBD.php';

if (isset($_SESSION['email'])) {
    header("Location: pistas.php");
    exit();
}

$error = ""; // Variable para almacenar el mensaje de error

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['register'])) {
        // Recoger los datos del formulario de registro
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
        } else {
            // Insertar el nuevo usuario en la base de datos
            $sql = "INSERT INTO usuarios (nombre, apellidos, email, telefono, contrasena) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $nombre, $apellidos, $email, $telefono, $contrasena);
            
            if ($stmt->execute()) {
                $_SESSION['email'] = $email;

                // Guardar datos en el archivo de texto
                $file_path = 'usuarios.txt';
                $user_data = "$nombre,$apellidos,$email,$telefono" . PHP_EOL;

                // Añadir datos al archivo solo si existe o crearlo la primera vez
                if (file_exists($file_path)) {
                    file_put_contents($file_path, $user_data, FILE_APPEND);
                } else {
                    file_put_contents($file_path, $user_data);
                }

                // Redireccionar a pistas.php después de registrarse
                header("Location: pistas.php");
                exit();
            } else {
                $error = "Error al registrar el usuario.";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario | Publipista</title>
    <link rel="icon" href="img/favicon_Publipista.webp" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="bootstrap/css/miestilo.css" />
</head>
<body class="full-background d-flex align-items-center justify-content-center vh-100">
    <div class="container text-center">
        <!-- Logo y descripción -->
        <div class="logo-section mb-4">
            <h1 class="display-5 text-light">PUBLIPISTA</h1>
            <p class="lead text-light">RESERVAS DE PISTAS DEPORTIVAS</p>
        </div>

        <!-- Cuadro de registro -->
        <div class="card mx-auto p-4" style="max-width: 400px;">
            <h2 class="card-title text-center mb-4">Registro de Usuario</h2>

            <!-- Mostrar mensaje de error si existe -->
            <?php if (!empty($error)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="" method="post">
                <input type="hidden" name="register" value="1">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="apellidos" class="form-label">Apellidos:</label>
                    <input type="text" id="apellidos" name="apellidos" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico:</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="telefono" class="form-label">Teléfono:</label>
                    <input type="text" id="telefono" name="telefono" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="contrasena" class="form-label">Contraseña:</label>
                    <input type="password" id="contrasena" name="contrasena" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Registrarse</button>
            </form>
            <p class="text-center mt-3">
                ¿Ya tienes cuenta? <a href="index.php" class="text-primary">Inicia sesión aquí</a>
            </p>
        </div>
    </div>

    <script src="./bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>

