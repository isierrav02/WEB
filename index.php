<?php
session_start();
include 'publipistaBD.php';

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

// Consulta para obtener las URLs y descripciones de las imágenes de las 9 pistas
$sql = "SELECT url, descripcion FROM fotos_pistas WHERE pista_id IN (1, 2, 3, 4, 5, 6, 7, 8, 9)";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio | Publipista</title>
    <link rel="icon" href="img/favicon_Publipista.webp" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/miestilo.css">
</head>

<body>
    <!-- Header -->
    <header class="bg-dark text-light w-100">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a href="index.php" class="navbar-brand d-flex align-items-center">
                    <img src="img/Publipista.webp" alt="Logo" width="40" height="40" class="me-2">
                    <h1 class="h5 m-0">Publipista</h1>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a href="#" class="nav-link text-light">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a href="pista.php" class="nav-link text-light">Pistas</a>
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

    <!-- Carrusel de Imágenes -->
    <section id="carouselPistas" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php
            $active = true; // Variable para definir el primer elemento como "active"
            while ($foto = $result->fetch_assoc()): ?>
                <div class="carousel-item <?php echo $active ? 'active' : ''; ?>">
                    <img src="<?php echo htmlspecialchars($foto['url']); ?>" class="d-block w-100" alt="Pista">
                    <div class="carousel-caption d-none d-md-block">
                        <p><?php echo htmlspecialchars($foto['descripcion']); ?></p>
                    </div>
                </div>
                <?php
                $active = false; // Solo el primer elemento será "active"
            endwhile; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselPistas" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselPistas" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>

        <!-- Botón para Reservas centrado en el carrusel -->
        <div class="reserva-container position-absolute top-50 start-50 translate-middle">
            <a href="#" class="btn-reserva" data-bs-toggle="modal" data-bs-target="#loginModal">
                <i class="bi bi-calendar-check me-2"></i> Reservar Pistas
            </a>
        </div>
    </section>

    <!-- ¿Por qué elegir Publipista? -->
    <section class="features py-5 bg-light">
        <div class="container text-center">
            <h2 class="mb-4">¿Por qué elegir Publipista?</h2>
            <div class="row">
                <div class="col-md-4">
                    <i class="bi bi-calendar-check fs-1 text-primary"></i>
                    <h4 class="mt-3">Reserva Rápida y Fácil</h4>
                    <p>Realiza tus reservas en cuestión de segundos, sin complicaciones.</p>
                </div>
                <div class="col-md-4">
                    <i class="bi bi-geo-alt fs-1 text-primary"></i>
                    <h4 class="mt-3">Ubicación Conveniente</h4>
                    <p>Encuentra pistas en las mejores ubicaciones cerca de ti.</p>
                </div>
                <div class="col-md-4">
                    <i class="bi bi-trophy fs-1 text-primary"></i>
                    <h4 class="mt-3">Calidad de Instalaciones</h4>
                    <p>Pistas de calidad para que disfrutes cada momento en la cancha.</p>
                </div>
            </div>
        </div>
    </section>

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
                    <form action="index.php" method="post">
                        <div class="mb-3">
                            <label for="emailLogin" class="form-label">Correo Electrónico:</label>
                            <input type="email" id="emailLogin" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="contrasenaLogin" class="form-label">Contraseña:</label>
                            <input type="password" id="contrasenaLogin" name="contrasena" class="form-control" required>
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
                        <input type="hidden" name="source" value="index"> <!-- Identificador de la página -->
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
                    <img src="img/Publipista.webp" alt="Logo" width="40" height="40"> Reservas de pistas deportivas
                </div>
                <div class="col-lg-4 mb-3 mb-lg-0 h5 m-0">
                    <img src="img/escudo_puebla-del-prior.jpg" alt="Imagen central" width="100" height="40">
                    Ayuntamiento de Puebla del Prior
                </div>
                <div class="col-lg-4 h5 m-0">
                    <a href="politica_privacidad.html" class="text-light d-block mb-3">Política de Privacidad</a>
                    <a href="politica_cookies.html" class="text-light d-block mb-3">Política de Cookies</a>
                    <a href="terminos_condiciones.html" class="text-light d-block">Términos y Condiciones</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>