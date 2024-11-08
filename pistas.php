<?php
session_start();
include 'publipistaBD.php';

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// Mostrar mensaje de error si hay conflictos en la reserva
if (isset($_GET['reserva'])): ?>
    <div class="alert alert-danger text-center">
        <?php if ($_GET['reserva'] == 'conflict'): ?>
            Ya tienes una reserva para esta pista en el mismo día y hora.
        <?php elseif ($_GET['reserva'] == 'type_conflict'): ?>
            Ya tienes una reserva activa de este tipo de pista en otra fecha.
        <?php endif; ?>
    </div>
<?php endif; ?>

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
                $sql = "SELECT id, nombre FROM pistas";
                $result = $conn->query($sql);
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

<!-- JavaScript para validar fecha, hora y calcular precio -->
<script>
    function abrirModalReserva(pistaId) {
        $('#pista_id').val(pistaId);
        $('#fecha_reserva').val('');
        $('#hora_inicio').val('');
        $('#hora_fin').val('');
        $('#precio_total').val('');
        
        // Configura la fecha mínima en el campo de fecha de reserva
        const today = new Date().toISOString().split('T')[0];
        $('#fecha_reserva').attr('min', today);

        $('#modalReserva').modal('show');
    }

    $('#fecha_reserva').on('change', function() {
        const fechaSeleccionada = new Date($(this).val());
        const hoy = new Date();
        
        // Si la fecha seleccionada es hoy, limita la hora de inicio a la hora actual
        if (fechaSeleccionada.toDateString() === hoy.toDateString()) {
            const horas = hoy.getHours().toString().padStart(2, '0');
            const minutos = hoy.getMinutes().toString().padStart(2, '0');
            $('#hora_inicio').attr('min', `${horas}:${minutos}`);
        } else {
            $('#hora_inicio').removeAttr('min'); // Elimina el límite si no es hoy
        }
    });

    $('#hora_inicio').on('change', function() {
        const horaInicio = $('#hora_inicio').val();
        
        if (horaInicio) {
            // Calcula la hora mínima de fin: una hora después de la hora de inicio
            const horaMinimaFin = calcularHoraFinMinima(horaInicio);
            $('#hora_fin').val(horaMinimaFin); // Establece el valor inicial de hora_fin
            $('#hora_fin').attr('min', horaMinimaFin); // Establece el mínimo para hora_fin
        }
        
        calcularPrecio();
    });

    $('#hora_fin').on('change', calcularPrecio);

    function calcularPrecio() {
    const horaInicio = $('#hora_inicio').val();
    const horaFin = $('#hora_fin').val();
    const pistaId = $('#pista_id').val();

    if (horaInicio && horaFin) {
        const horasReserva = calcularHoras(horaInicio, horaFin);
        
        // Verifica que la duración sea al menos 1 hora y en incrementos de 0.5 horas
        if (horasReserva >= 1 && horasReserva % 0.5 === 0) {
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
            alert("La reserva debe ser de al menos una hora y en incrementos de media hora.");
        }
    }
}


    // Función para calcular la diferencia en horas entre hora_inicio y hora_fin
    function calcularHoras(horaInicio, horaFin) {
        const inicio = new Date(`1970-01-01T${horaInicio}:00`);
        const fin = new Date(`1970-01-01T${horaFin}:00`);
        const diferencia = (fin - inicio) / (1000 * 3600); // Convertir milisegundos a horas
        return diferencia > 0 ? diferencia : 0;
    }

    // Función para calcular la hora mínima de fin (una hora después de la hora de inicio)
    function calcularHoraFinMinima(horaInicio) {
        const [horas, minutos] = horaInicio.split(':').map(Number);
        let finHoras = horas + 1;
        let finMinutos = minutos;

        if (finMinutos === 30) {
            finHoras += 1;
            finMinutos = 0;
        }

        // Ajustar el formato a HH:MM
        return `${String(finHoras).padStart(2, '0')}:${String(finMinutos).padStart(2, '0')}`;
    }
</script>

</body>
</html>


