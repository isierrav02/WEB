<?php
session_start();
include 'publipistaBD.php';

// Consulta a la base de datos para obtener los datos necesarios de las pistas y sus imágenes
$sql = "SELECT p.id, p.nombre AS titulo, p.ubicacion, p.precio_base, fp.url AS imagen, fp.descripcion 
        FROM pistas p 
        LEFT JOIN fotos_pistas fp ON p.id = fp.pista_id";
$result = $conn->query(query: $sql);

if (isset($_SESSION['email'])) {
    header("Location: pistas.php");
    exit();
}

// Mostrar mensaje de error si existe
if (isset($_GET['error'])) {
    $error = htmlspecialchars($_GET['error']);
    echo "<script>
        alert('$error');
        // Limpiar el parámetro de error de la URL
        if (history.replaceState) {
            const newUrl = window.location.protocol + '//' + window.location.host + window.location.pathname;
            history.replaceState(null, '', newUrl);
        }
    </script>";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pistas | Publipista</title>
    <link rel="icon" href="../img/favicon_Publipista.webp" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/pista.css" />
    <script src="../jquery/jquery-3.7.1.min.js"></script>
    <script src="../miscript.js"></script>
</head>

<body class="bg-dark text-white">
    <!-- Header -->
    <header class="bg-dark text-light w-100">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a href="../index.php" class="navbar-brand d-flex align-items-center">
                    <img src="../img/Publipista.webp" alt="Logo" width="40" height="40" class="me-2">
                    <h1 class="h5 m-0">Publipista</h1>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a href="../index.php" class="nav-link text-light">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a href="https://www.facebook.com/ayuntamiento2023" class="nav-link text-light"><i
                                    class="bi bi-facebook"></i></a>
                        </li>
                        <li class="nav-item">
                            <a href="" class="nav-link text-light"><i class="bi bi-twitter"></i></a>
                        </li>
                        <li class="nav-item">
                            <a href="" class="nav-link text-light"><i class="bi bi-instagram"></i></a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link text-light" data-bs-toggle="modal"
                                data-bs-target="#loginModal"><i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <!-- Contenido principal -->
    <div class="container p-2">
        <?php while ($pista = $result->fetch_assoc()): ?>
            <div class="row position-relative mb-4">
                <!-- Fondo difuminado de la imagen -->
                <div class="imagenDifuminada" style="background-image: url('../<?php echo $pista['imagen']; ?>');"></div>

                <!-- Imagen circular y contenido de la pista -->
                <div class="col-3 position-relative">
                    <img class="imagenDescubre" src="../<?php echo $pista['imagen']; ?>"
                        alt="Imagen de <?php echo htmlspecialchars($pista['titulo']); ?>">
                </div>
                <div class="col-6 text-white">
                    <h4 class="fw-bold"><?php echo htmlspecialchars($pista['titulo']); ?></h4>
                    <p>Ubicación: <?php echo htmlspecialchars($pista['ubicacion']); ?></p>
                    <p>Precio Base: <?php echo htmlspecialchars($pista['precio_base']); ?> €/hora</p>
                    <p><?php echo htmlspecialchars($pista['descripcion']); ?></p>
                </div>

                <!-- Botón "Más información" -->
                <div class="col-3 position-relative">
                    <a href="detalle_pista.php?id=<?php echo $pista['id']; ?>">
                        <button
                            class="btn border-outline-rojo rounded-pill position-absolute top-50 translate-middle-y border-0">
                            Más información
                        </button>
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    <!-- Modal de Inicio de Sesión -->
    <section class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Iniciar Sesión</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <!-- Mostrar mensaje de error si existe -->
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form action="../index.php" method="post">
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
                        ¿Aún no tienes cuenta? <a href="#" class="text-primary" data-bs-toggle="modal"
                            data-bs-target="#registroModal" data-bs-dismiss="modal">Regístrate aquí</a>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal de Registro -->
    <section class="modal fade" id="registroModal" tabindex="-1" aria-labelledby="registroModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registroModalLabel">Registro de Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="formRegistro" method="POST" action="procesar_registro.php">
                        <input type="hidden" name="source" value="pista"> <!-- Identificador de la página -->
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
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light p-3 mt-auto w-100">
        <div class="container">
            <div class="row text-center text-lg-center">
                <div class="col-lg-4 mb-3 mb-lg-0 h5 m-0">
                    <img src="../img/Publipista.webp" alt="Logo" width="40" height="40"> Reservas de pistas deportivas
                </div>
                <div class="col-lg-4 mb-3 mb-lg-0 h5 m-0">
                    <img src="../img/escudo_puebla-del-prior.jpg" alt="Imagen central" width="100" height="40">
                    Ayuntamiento de Puebla del Prior
                </div>
                <div class="col-lg-4 h5 m-0">
                    <a href="../html/politica_privacidad.html" class="text-light d-block mb-3">Política de
                        Privacidad</a>
                    <a href="../html/politica_cookies.html" class="text-light d-block mb-3">Política de Cookies</a>
                    <a href="../html/terminos_condiciones.html" class="text-light d-block">Términos y Condiciones</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>