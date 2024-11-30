<?php
include 'publipistaBD.php';

if (isset($_GET['id'])) {
    $pista_id = $_GET['id'];

    // Consulta para obtener los detalles de la pista
    $sql = "SELECT p.nombre, p.tipo, p.ubicacion, p.precio_base, fp.url AS imagen, fp.descripcion 
            FROM pistas p 
            LEFT JOIN fotos_pistas fp ON p.id = fp.pista_id
            WHERE p.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $pista_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $pista = $result->fetch_assoc();
} else {
    echo "Pista no encontrada.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la Pista | Publipista</title>
    <link rel="icon" href="../img/favicon_Publipista.webp" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/miestilo.css" />
</head>

<body class="bg-dark text-white d-flex flex-column min-vh-100">
    <div class="container mt-5 p-4 bg-secondary rounded shadow">
        <?php if ($pista): ?>
            <!-- Imagen de la Pista -->
            <div class="text-center mb-4">
                <img src="../<?php echo htmlspecialchars($pista['imagen']); ?>" class="imagen-uniforme shadow"
                    alt="Imagen de <?php echo htmlspecialchars($pista['nombre']); ?>">
            </div>

            <!-- Detalles de la Pista -->
            <div class="card bg-dark text-light shadow-sm p-4">
                <div class="card-body">
                    <h1 class="card-title text-center mb-4 fw-bold"><?php echo htmlspecialchars($pista['nombre']); ?></h1>
                    <ul class="list-group list-group-flush bg-dark">
                        <li class="list-group-item bg-dark text-white">
                            <strong>Tipo:</strong> <?php echo htmlspecialchars($pista['tipo']); ?>
                        </li>
                        <li class="list-group-item bg-dark text-white">
                            <strong>Ubicación:</strong> <?php echo htmlspecialchars($pista['ubicacion']); ?>
                        </li>
                        <li class="list-group-item bg-dark text-white">
                            <strong>Precio Base:</strong> <?php echo htmlspecialchars($pista['precio_base']); ?> €/hora
                        </li>
                        <li class="list-group-item bg-dark text-white">
                            <strong>Descripción:</strong> <?php echo htmlspecialchars($pista['descripcion']); ?>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Botón Volver -->
            <div class="text-center mt-4">
                <a href="pista.php" class="btn btn-outline-light rounded-pill">
                    <i class="bi bi-arrow-left-circle"></i> Volver a Pistas
                </a>
            </div>
        <?php else: ?>
            <p class="text-center">No se encontraron detalles para esta pista.</p>
        <?php endif; ?>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>