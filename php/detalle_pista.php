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

<body class="bg-dark text-white">
    <div class="container mt-5 p-4 bg-secondary rounded shadow">
        <?php if ($pista): ?>
            <!-- Título e Imagen de la Pista -->
            <div class="text-center mb-4">
                <h1 class="display-5 fw-bold"><?php echo htmlspecialchars($pista['nombre']); ?></h1>
                <img src="../<?php echo htmlspecialchars($pista['imagen']); ?>" class="img-fluid rounded mt-3 shadow"
                    alt="Imagen de <?php echo htmlspecialchars($pista['nombre']); ?>" style="max-width: 80%; height: auto;">
            </div>

            <!-- Detalles de la Pista -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h4 class="fw-bold">Tipo</h4>
                    <p><?php echo htmlspecialchars($pista['tipo']); ?></p>
                </div>
                <div class="col-md-6">
                    <h4 class="fw-bold">Ubicación</h4>
                    <p><?php echo htmlspecialchars($pista['ubicacion']); ?></p>
                </div>
                <div class="col-md-6">
                    <h4 class="fw-bold">Precio Base</h4>
                    <p><?php echo htmlspecialchars($pista['precio_base']); ?> €/hora</p>
                </div>
                <div class="col-md-12">
                    <h4 class="fw-bold">Descripción</h4>
                    <p><?php echo htmlspecialchars($pista['descripcion']); ?></p>
                </div>
            </div>

            <!-- Botón Volver -->
            <div class="text-center">
                <a href="pista.php" class="btn btn-outline-light rounded-pill">
                    <i class="bi bi-arrow-left-circle"></i> Volver a Pistas
                </a>
            </div>
        <?php else: ?>
            <p class="text-center">No se encontraron detalles para esta pista.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>