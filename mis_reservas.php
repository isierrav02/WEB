<?php
session_start();
include 'publipistaBD.php';

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// Consulta para obtener todas las reservas del usuario conectado
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
<body class="full-background d-flex align-items-center justify-content-center vh-100">
    <div class="container text-center">
        <!-- Logo y descripción -->
        <div class="logo-section mb-4">
            <h1 class="display-5 text-light">PUBLIPISTA</h1>
            <p class="lead text-light">MIS RESERVAS DE PISTAS DEPORTIVAS</p>
        </div>

        <!-- Tabla de reservas del usuario -->
        <div class="card mx-auto p-4" style="max-width: 800px;">
            <h2 class="card-title text-center mb-4">Mis Reservas</h2>
            <?php if ($result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
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
            <div class="mt-4">
                <a href="pistas.php" class="btn btn-primary">Volver a Reservas</a>
                <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
            </div>
        </div>
    </div>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
