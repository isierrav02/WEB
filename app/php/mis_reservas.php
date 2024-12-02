<?php
session_start();
include 'publipistaBD.php';
require '../PHPMailer/Exception.php';
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Asegurarse de que el usuario está logueado
if (!isset($_SESSION['email'])) {
    header("Location: ../index.php");
    exit();
}

// Obtener el ID del usuario logueado a partir del email
$email = $conn->real_escape_string($_SESSION['email']);
$sql_usuario = "SELECT id, nombre FROM usuarios WHERE email = '$email'";
$result = $conn->query($sql_usuario);
$usuario = $result->fetch_assoc();
$usuario_id = $usuario['id'];
$nombre_usuario = $usuario['nombre'];

// Limpiar automáticamente las reservas pasadas
$fecha_hora_actual = date("Y-m-d H:i:s");
$sql_limpieza = "DELETE FROM reservas WHERE CONCAT(fecha_reserva, ' ', hora_fin) <= '$fecha_hora_actual'";
$conn->query($sql_limpieza);

// Procesar formulario de reserva
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pista_id'], $_POST['fecha_reserva'], $_POST['hora_inicio'], $_POST['hora_fin'], $_POST['precio_base'])) {
    $pista_id = (int) $_POST['pista_id'];
    $fecha_reserva = $conn->real_escape_string($_POST['fecha_reserva']);
    $hora_inicio = $conn->real_escape_string($_POST['hora_inicio']);
    $hora_fin = $conn->real_escape_string($_POST['hora_fin']);
    $precio_base = (float) $_POST['precio_base'];

    // Verificar si ya existe una reserva idéntica en la fecha y pista para el usuario
    $sql_verificar_existente = "SELECT * FROM reservas 
                                WHERE usuario_id = $usuario_id 
                                AND pista_id = $pista_id 
                                AND fecha_reserva = '$fecha_reserva'";
    $result_existente = $conn->query($sql_verificar_existente);

    if ($result_existente->num_rows > 0) {
        echo "<script>alert('Ya tienes una reserva para esta pista en la fecha seleccionada.'); window.history.back();</script>";
        exit();
    }

    // Verificar si el usuario tiene otra reserva en el mismo horario en cualquier pista
    $sql_verificar_usuario = "SELECT * FROM reservas 
                              WHERE usuario_id = $usuario_id 
                              AND fecha_reserva = '$fecha_reserva' 
                              AND (
                                  (hora_inicio < '$hora_fin' AND hora_fin > '$hora_inicio') OR 
                                  (hora_inicio < '$hora_inicio' AND hora_fin > '$hora_fin')
                              )";
    $result_usuario = $conn->query($sql_verificar_usuario);

    if ($result_usuario->num_rows > 0) {
        echo "<script>alert('Ya tienes una reserva en el mismo horario.'); window.history.back();</script>";
        exit();
    }

    // Verificar si la pista ya está reservada en el mismo horario por otro usuario
    $sql_verificar_pista = "SELECT * FROM reservas 
                            WHERE pista_id = $pista_id 
                            AND fecha_reserva = '$fecha_reserva' 
                            AND (
                                (hora_inicio < '$hora_fin' AND hora_fin > '$hora_inicio') OR 
                                (hora_inicio < '$hora_inicio' AND hora_fin > '$hora_fin')
                            )";
    $result_pista = $conn->query($sql_verificar_pista);

    if ($result_pista->num_rows > 0) {
        echo "<script>alert('Esta pista ya está reservada en el horario seleccionado.'); window.history.back();</script>";
        exit();
    }

    // Obtener el nombre de la pista
    $sql_pista = "SELECT nombre FROM pistas WHERE id = $pista_id";
    $result_pista = $conn->query($sql_pista);
    $pista = $result_pista->fetch_assoc();
    $nombre_pista = $pista['nombre'];

    // Calcular el precio total en base a la duración de la reserva
    $inicio = new DateTime($hora_inicio);
    $fin = new DateTime($hora_fin);
    $duracion = $inicio->diff($fin);
    $horas = $duracion->h;
    $minutos = $duracion->i;
    $precio_total = $precio_base * ($horas + $minutos / 60);

    // Insertar la reserva si no hay conflictos
    $sql_insertar = "INSERT INTO reservas (usuario_id, pista_id, fecha_reserva, hora_inicio, hora_fin, precio_total, fecha_creacion) 
                     VALUES ($usuario_id, $pista_id, '$fecha_reserva', '$hora_inicio', '$hora_fin', $precio_total, NOW())";

    if ($conn->query($sql_insertar) === TRUE) {
        // Configurar y enviar el correo de confirmación usando PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Configuración del servidor SMTP de Gmail
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ismaelsierravega2003@gmail.com'; // La dirección de correo de Gmail
            $mail->Password = 'gtnr lwci mtaf vmza';        // La contraseña desde Gmail
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Configuración del correo
            $mail->setFrom('no-reply@publipista.com', 'Publipista');
            $mail->addAddress($email, $nombre_usuario);
            $mail->Subject = 'Confirmación de Reserva - Publipista';

            // Establecer la codificación y el cuerpo en HTML para soportar caracteres especiales
            $mail->CharSet = 'UTF-8';
            $mail->isHTML(true);
            $mail->Body = "Hola $nombre_usuario<br><br>Tu reserva en la <strong>$nombre_pista</strong> se ha realizado correctamente.<br><br>"
                . "Detalles de la reserva:<br>"
                . "<strong>Pista:</strong> $nombre_pista<br>"
                . "<strong>Fecha:</strong> $fecha_reserva<br>"
                . "<strong>Hora de inicio:</strong> $hora_inicio h<br>"
                . "<strong>Hora de fin:</strong> $hora_fin h<br>"
                . "<strong>Precio total:</strong> " . number_format($precio_total, 2) . " €<br><br>"
                . "Gracias por usar Publipista.<br><br>Saludos,<br>El equipo de Publipista<br><br>"
                . "<small><em>Este correo ha sido generado automáticamente. Por favor, no responda a este mensaje.</em></small>";

            // Enviar el correo
            $mail->send();

        } catch (Exception $e) {
            echo "Error al enviar el correo de confirmación: {$mail->ErrorInfo}";
        }

        // Redirigir a mis_reservas.php
        header("Location: mis_reservas.php");
        exit();
    } else {
        echo "Error al realizar la reserva: " . $conn->error;
    }
}

// Consulta para obtener todas las reservas del usuario conectado
$sql_reservas = "SELECT r.id, p.nombre AS pista, r.fecha_reserva, r.hora_inicio, r.hora_fin, r.precio_total 
                 FROM reservas r
                 JOIN pistas p ON r.pista_id = p.id
                 WHERE r.usuario_id = $usuario_id
                 ORDER BY r.fecha_reserva DESC, r.hora_inicio DESC";
$result_reservas = $conn->query($sql_reservas);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reservas | Publipista</title>
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
                <a href="mis_reservas.php" class="navbar-brand d-flex align-items-center">
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
            <?php if ($result_reservas->num_rows > 0): ?>
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
                            <?php while ($row = $result_reservas->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['pista']); ?></td>
                                    <td><?php echo htmlspecialchars($row['fecha_reserva']); ?></td>
                                    <td><?php echo htmlspecialchars($row['hora_inicio']); ?></td>
                                    <td><?php echo htmlspecialchars($row['hora_fin']); ?></td>
                                    <td><?php echo number_format($row['precio_total'], 2); ?> €</td>
                                    <td>
                                        <!-- Botón para abrir el modal de edición -->
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" 
                                        data-bs-target="#editarReservaModal" onclick="cargarReserva(<?php echo $row['id']; ?>)">
                                            Editar
                                        </button>
                                    </td>
                                    <td>
                                        <!-- Formulario para eliminar la reserva -->
                                        <form action="eliminar.php" method="POST"
                                            onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta reserva?');">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                        </form>
                                    </td>
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
        <!-- Modal para Editar Reserva -->
        <div class="modal fade" id="editarReservaModal" tabindex="-1" aria-labelledby="editarReservaLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarReservaLabel">Editar Reserva</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formEditarReserva" action="editar_reserva.php" method="POST">
                            <input type="hidden" name="reserva_id" id="reserva_id">
                            <div class="mb-3">
                                <label for="fecha_reserva" class="form-label">Fecha de Reserva:</label>
                                <input type="date" id="editar_fecha_reserva" name="fecha_reserva" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="hora_inicio" class="form-label">Hora de Inicio:</label>
                                <input type="time" id="editar_hora_inicio" name="hora_inicio" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="hora_fin" class="form-label">Hora de Fin:</label>
                                <input type="time" id="editar_hora_fin" name="hora_fin" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Guardar Cambios</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

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
<script>
function cargarReserva(reservaId) {
    // Realizar una solicitud AJAX para obtener los datos de la reserva
    fetch(`obtener_reserva.php?id=${reservaId}`)
        .then(response => response.json())
        .then(data => {
            // Asignar los valores a los campos del modal
            document.getElementById('reserva_id').value = data.id;
            document.getElementById('editar_fecha_reserva').value = data.fecha_reserva;
            document.getElementById('editar_hora_inicio').value = data.hora_inicio;
            document.getElementById('editar_hora_fin').value = data.hora_fin;
        })
        .catch(error => console.error('Error:', error));
}
</script>

</html>