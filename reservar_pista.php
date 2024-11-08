<?php
session_start();
include 'publipistaBD.php';

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pista_id = $_POST['pista_id'];
    $fecha_reserva = $_POST['fecha_reserva'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    $email = $_SESSION['email'];

    // Obtener el ID del usuario y el precio base de la pista seleccionada
    $sql = "SELECT u.id AS usuario_id, p.precio_base, p.tipo 
            FROM usuarios u
            JOIN pistas p ON p.id = ?
            WHERE u.email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $pista_id, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();
    $usuario_id = $user_data['usuario_id'];
    $precio_base = $user_data['precio_base'];
    $tipo_pista = $user_data['tipo'];

    // Verificar restricciones de reserva
    // 1. Comprobar si ya existe una reserva el mismo día y en un horario que se solape para esta pista
    $sql = "SELECT * FROM reservas 
    WHERE usuario_id = ? 
    AND pista_id = ? 
    AND fecha_reserva = ? 
    AND ((hora_inicio < ? AND hora_fin > ?) OR (hora_inicio < ? AND hora_fin > ?))";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisssss", $usuario_id, $pista_id, $fecha_reserva, $hora_fin, $hora_inicio, $hora_inicio, $hora_fin);
    $stmt->execute();
    $conflicting_reserva = $stmt->get_result();

    if ($conflicting_reserva->num_rows > 0) {
    // Si ya existe una reserva en conflicto
    header("Location: pistas.php?reserva=conflict");
    exit();
    }



    // 2. Comprobar si ya existe una reserva activa del mismo tipo de pista en otro día
    $sql = "SELECT * FROM reservas r 
            JOIN pistas p ON r.pista_id = p.id 
            WHERE r.usuario_id = ? 
            AND p.tipo = ? 
            AND r.fecha_reserva > CURDATE()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $usuario_id, $tipo_pista);
    $stmt->execute();
    $conflicting_tipo = $stmt->get_result();

    if ($conflicting_tipo->num_rows > 0) {
        // Si ya existe una reserva activa del mismo tipo de pista en otra fecha
        header("Location: pistas.php?reserva=type_conflict");
        exit();
    }

    // Calcula la duración de la reserva en horas y el precio total
    $horas_reserva = (strtotime($hora_fin) - strtotime($hora_inicio)) / 3600;
    $precio_total = $precio_base * $horas_reserva;

    // Inserta la reserva en la base de datos
    $sql = "INSERT INTO reservas (usuario_id, pista_id, fecha_reserva, hora_inicio, hora_fin, precio_total) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisssd", $usuario_id, $pista_id, $fecha_reserva, $hora_inicio, $hora_fin, $precio_total);

    if ($stmt->execute()) {
        header("Location: mis_reservas.php?reserva=success");
    } else {
        header("Location: pistas.php?reserva=error");
    }
}
?>
