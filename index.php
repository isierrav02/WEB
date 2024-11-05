<?php
session_start();
include 'publipistaBD.php';

if (isset($_SESSION['email'])) {
    header("Location: pistas.php");
    exit();
}

$error = ""; // Variable para almacenar el mensaje de error

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $contrasena = $_POST['contrasena'];

        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($contrasena, $user['contrasena'])) {
                $_SESSION['email'] = $email;
                header("Location: pistas.php");
                exit();
            } else {
                $error = "Contraseña incorrecta.";
            }
        } else {
            $error = "Usuario no encontrado.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Principal Login / Registro | Publipista</title>
    <link rel="icon" href="img/favicon_Publipista.webp" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="bootstrap/css/miestilo.css" />
</head>
<body class="full-background d-flex align-items-center justify-content-center vh-100">
    <div class="container text-center">
        <div class="logo-section mb-4">
            <h1 class="display-5 text-light">PUBLIPISTA</h1>
            <p class="lead text-light">RESERVAS DE PISTAS DEPORTIVAS</p>
        </div>

        <!-- Cuadro de inicio de sesión -->
        <div class="card mx-auto p-4" style="max-width: 400px;">
            <h2 class="card-title text-center mb-4">Iniciar Sesión</h2>

            <!-- Mostrar mensaje de error si existe -->
            <?php if (!empty($error)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="" method="post">
                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico:</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="contrasena" class="form-label">Contraseña:</label>
                    <input type="password" id="contrasena" name="contrasena" class="form-control" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100">Entrar</button>
            </form>
            <p class="text-center mt-3">
                ¿Aún no tienes cuenta? <a href="registro.php" class="text-primary">Regístrate aquí</a>
            </p>
        </div>
    </div>

    <script src="./bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>

