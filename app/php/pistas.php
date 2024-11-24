<?php
session_start();
include 'publipistaBD.php';

if (!isset($_SESSION['email'])) {
    header("Location: ../index.php");
    exit();
}

// Consulta para obtener los datos de las pistas
$sql = "SELECT p.id, p.nombre, p.precio_base, fp.url AS imagen 
        FROM pistas p 
        LEFT JOIN fotos_pistas fp ON p.id = fp.pista_id 
        LIMIT 8";
$result = $conn->query($sql);
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
    <link rel="stylesheet" href="../css/miestilo.css" />
</head>

<body class="full-background d-flex flex-column min-vh-100">
    <!-- Header -->
    <header class="bg-dark text-light w-100">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a href="pistas.php" class="navbar-brand d-flex align-items-center">
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
                            <a href="mis_reservas.php" class="nav-link text-light">Mis Reservas</a>
                        </li>
                        <li class="nav-item">
                            <a href="logout.php" class="nav-link text-light">Cerrar Sesión</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Contenido principal con las imágenes de las pistas -->
    <main class="container my-5">
        <div class="row row-cols-1 row-cols-md-4 g-4">
            <?php while ($pista = $result->fetch_assoc()): ?>
                <div class="col">
                    <div class="card h-100 bg-dark text-light pista-card">
                        <div class="pista-img-container">
                            <img src="../<?php echo htmlspecialchars($pista['imagen']); ?>" class="card-img-top"
                                alt="<?php echo htmlspecialchars($pista['nombre']); ?>" style="cursor: pointer;"
                                onclick="abrirModalReserva(<?php echo $pista['id']; ?>, <?php echo $pista['precio_base']; ?>)">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($pista['nombre']); ?></h5>
                            <p class="card-text">Precio: <?php echo htmlspecialchars($pista['precio_base']); ?> €/hora</p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </main>

    <!-- Modal de Reserva -->
    <div class="modal fade" id="reservaModal" tabindex="-1" aria-labelledby="reservaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reservaModalLabel">Reservar Pista</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="formReserva" action="mis_reservas.php" method="post">
                        <!-- Campo oculto para el ID de la pista -->
                        <input type="hidden" name="pista_id" id="pista_id">
                        <input type="hidden" id="precio_base" name="precio_base">
                        <div class="mb-3">
                            <label for="fecha_reserva" class="form-label">Fecha de Reserva:</label>
                            <input type="date" id="fecha_reserva" name="fecha_reserva" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="hora_inicio" class="form-label">Hora de Inicio:</label>
                            <input type="time" id="hora_inicio" name="hora_inicio" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="hora_fin" class="form-label">Hora de Fin:</label>
                            <input type="time" id="hora_fin" name="hora_fin" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Reservar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
    <script src="../miscript.js"></script>
</body>

</html>