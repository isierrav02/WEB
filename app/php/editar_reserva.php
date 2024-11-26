<?php
include 'publipistaBD.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reserva_id'], $_POST['fecha_reserva'], $_POST['hora_inicio'], $_POST['hora_fin'])) {
    $reserva_id = $_POST['reserva_id'];
    $fecha_reserva = $_POST['fecha_reserva'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];

    // Obtener el ID de la pista asociada a la reserva
    $sql = "SELECT pista_id FROM reservas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reserva_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $reserva = $result->fetch_assoc();
    $pista_id = $reserva['pista_id'];

    // Obtener el precio base de la pista
    $sql_precio = "SELECT precio_base FROM pistas WHERE id = ?";
    $stmt_precio = $conn->prepare($sql_precio);
    $stmt_precio->bind_param("i", $pista_id);
    $stmt_precio->execute();
    $result_precio = $stmt_precio->get_result();
    $pista = $result_precio->fetch_assoc();
    $precio_base = $pista['precio_base'];

    // Calcular el nuevo precio total según la duración de la reserva
    $inicio = new DateTime($hora_inicio);
    $fin = new DateTime($hora_fin);
    $duracion = $inicio->diff($fin);
    $horas = $duracion->h;
    $minutos = $duracion->i;
    $precio_total = $precio_base * ($horas + $minutos / 60);

    // Actualizar la reserva en la base de datos con el nuevo precio total
    $sql_update = "UPDATE reservas SET fecha_reserva = ?, hora_inicio = ?, hora_fin = ?, precio_total = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sssdi", $fecha_reserva, $hora_inicio, $hora_fin, $precio_total, $reserva_id);

    if ($stmt_update->execute()) {
        header("Location: mis_reservas.php");
        exit();
    } else {
        echo "Error al actualizar la reserva: " . $stmt_update->error;
    }
}
