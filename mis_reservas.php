<?php
session_start();
include 'publipistaBD.php';

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// Procesar formulario de reserva
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pista_id'], $_POST['fecha_reserva'], $_POST['hora_inicio'], $_POST['hora_fin'], $_POST['precio_base'])) {
    $email = $_SESSION['email'];
    $pista_id = $_POST['pista_id'];
    $fecha_reserva = $_POST['fecha_reserva'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    $precio_base = (float) $_POST['precio_base'];

    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
    $usuario_id = $usuario['id'];

    $inicio = new DateTime($hora_inicio);
    $fin = new DateTime($hora_fin);
    $duracion = $inicio->diff($fin);
    $horas = $duracion->h;
    $minutos = $duracion->i;
    $precio_total = $precio_base * ($horas + $minutos / 60);

    $stmt = $conn->prepare("INSERT INTO reservas (usuario_id, pista_id, fecha_reserva, hora_inicio, hora_fin, precio_total) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssd", $usuario_id, $pista_id, $fecha_reserva, $hora_inicio, $hora_fin, $precio_total);

    if ($stmt->execute()) {
        header("Location: mis_reservas.php");
        exit();
    } else {
        echo "Error al realizar la reserva: " . $stmt->error;
    }
}

// Consulta de reservas del usuario
$email = $_SESSION['email'];
$sql = "SELECT p.nombre AS pista, r.fecha_reserva, r.hora_inicio, r.hora_fin, r.precio_total 
        FROM reservas r
        JOIN pistas p ON r.pista_id = p.id
        JOIN usuarios u ON r.usuario_id = u.id
        WHERE u.email = ?
        ORDER BY r.fecha_reserva DESC, r.hora_inicio DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reservas | Publipista</title>
    <link rel="icon" href="img/favicon_Publipista.webp" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="bootstrap/css/miestilo.css" />
</head>

<body class="full-background d-flex flex-column min-vh-100">
    <!-- Header -->
    <header class="bg-dark text-light w-100">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a href="mis_reservas.php" class="navbar-brand d-flex align-items-center">
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
                            <a href="index.php" class="nav-link text-light">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a href="pistas.php" class="nav-link text-light">Reservar Pistas</a>
                        </li>
                        <li class="nav-item">
                            <a href="logout.php" class="nav-link text-light">Cerrar Sesión</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Contenido principal -->
    <main class="container my-5">
        <div class="text-center mb-4 text-light">
            <h1 class="display-5 fw-bold">Mis Reservas</h1>
            <p class="lead">Aquí puedes revisar tus reservas de pistas deportivas</p>
        </div>

        <!-- Tabla de reservas del usuario -->
        <div class="card bg-secondary text-light mx-auto p-4 shadow-lg" style="max-width: 900px;">
            <h2 class="card-title text-center mb-4">Historial de Reservas</h2>
            <?php if ($result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-dark table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Pista</th>
                                <th>Fecha</th>
                                <th>Hora de Inicio</th>
                                <th>Hora de Fin</th>
                                <th>Precio (€)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['pista']); ?></td>
                                    <td><?php echo htmlspecialchars($row['fecha_reserva']); ?></td>
                                    <td><?php echo htmlspecialchars($row['hora_inicio']); ?></td>
                                    <td><?php echo htmlspecialchars($row['hora_fin']); ?></td>
                                    <td><?php echo number_format($row['precio_total'], 2); ?> €</td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center">No tienes reservas registradas.</p>
            <?php endif; ?>
            <div class="mt-4 text-center">
                <a href="pistas.php" class="btn btn-outline-light">Volver a Reservas</a>
                <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light p-3 mt-auto w-100">
        <div class="container">
            <div class="row text-center text-lg-start">
                <div class="col-lg-4 mb-3 mb-lg-0 h5 m-0" >
                    <img src="img/Publipista.webp" alt="Logo" width="40" height="40"> Reservas de pistas deportivas
                </div>
                <div class="col-lg-4 mb-3 mb-lg-0 h5 m-0">
                    <img src="img/escudo_puebla-del-prior.jpg" alt="Imagen central" width="100" height="40"> Ayuntamiento de Puebla del Prior
                </div>
                <div class="col-lg-4 h5 m-0">
                    <a href="politica_privacidad.html" class="text-light d-block mb-3">Política de Privacidad</a>
                    <a href="politica_cookies.html" class="text-light d-block mb-3">Política de Cookies</a>
                    <a href="terminos_condiciones.html" class="text-light d-block">Términos y Condiciones</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>