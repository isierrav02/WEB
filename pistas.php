<?php
session_start();
include 'publipistaBD.php';

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// Consulta para obtener todas las pistas deportivas
$sql = "SELECT id, nombre FROM pistas";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas de Pistas | Publipista</title>
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

        <!-- Contenido principal de la página de pistas -->
        <div class="card mx-auto p-4" style="max-width: 600px;">
            <h2 class="card-title text-center mb-4">Bienvenido a las Reservas de Pistas</h2>
            <p class="text-center">Aquí puedes ver y reservar las pistas deportivas disponibles.</p>

            <!-- Listado de pistas deportivas -->
            <div class="list-group">
                <?php
                if ($result->num_rows > 0) {
                    // Recorrer todas las pistas y mostrarlas como enlaces
                    while ($row = $result->fetch_assoc()) {
                        echo '<a href="#" class="list-group-item list-group-item-action" onclick="abrirModalReserva(' . $row['id'] . ')">' . htmlspecialchars($row['nombre']) . '</a>';
                    }
                } else {
                    echo '<p class="text-center">No hay pistas disponibles en este momento.</p>';
                }
                ?>
            </div>

            <!-- Botón de cierre de sesión -->
            <div class="mt-4">
                <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
            </div>
        </div>
    </div>

    <!-- Modal para Reservar Pista -->
<div class="modal fade" id="modalReserva" tabindex="-1" aria-labelledby="modalReservaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalReservaLabel">Reservar Pista</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formReserva" action="reservar_pista.php" method="post">
                    <input type="hidden" id="pista_id" name="pista_id">
                    
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
                    <div class="mb-3">
                        <label for="precio_total" class="form-label">Precio Total:</label>
                        <input type="text" id="precio_total" class="form-control" readonly>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Reservar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- JavaScript para calcular el precio total y gestionar el modal -->
<script>
    function abrirModalReserva(pistaId) {
        $('#pista_id').val(pistaId);
        $('#fecha_reserva').val('');
        $('#hora_inicio').val('');
        $('#hora_fin').val('');
        $('#precio_total').val('');
        $('#modalReserva').modal('show');
    }

    $('#hora_inicio, #hora_fin').on('change', function() {
        var horaInicio = $('#hora_inicio').val();
        var horaFin = $('#hora_fin').val();
        var pistaId = $('#pista_id').val();

        if (horaInicio && horaFin && pistaId) {
            var horasReserva = calcularHoras(horaInicio, horaFin);
            if (horasReserva > 0) {
                $.ajax({
                    url: 'get_precio_base.php',
                    method: 'POST',
                    data: { pista_id: pistaId, horas: horasReserva },
                    success: function(response) {
                        $('#precio_total').val(response);
                    }
                });
            } else {
                $('#precio_total').val('0.00');
            }
        }
    });

    function calcularHoras(horaInicio, horaFin) {
        var inicio = new Date("1970-01-01 " + horaInicio);
        var fin = new Date("1970-01-01 " + horaFin);
        var diferencia = (fin - inicio) / 1000 / 3600; // Convertir milisegundos a horas
        return diferencia > 0 ? diferencia : 0;
    }
</script>
</body>
</html>

