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
    
    // Consulta para obtener el ID del usuario y el precio base de la pista
    $sql = "SELECT id, precio_base FROM pistas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $pista_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $pista = $result->fetch_assoc();
    $precio_base = $pista['precio_base'];

    // Calcula la duración de la reserva en horas
    $horas_reserva = (strtotime($hora_fin) - strtotime($hora_inicio)) / 3600;
    $precio_total = $precio_base * $horas_reserva;

    // Inserta la reserva en la base de datos
    $sql = "INSERT INTO reservas (usuario_id, pista_id, fecha_reserva, hora_inicio, hora_fin, precio_total) 
            VALUES ((SELECT id FROM usuarios WHERE email = ?), ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisssd", $email, $pista_id, $fecha_reserva, $hora_inicio, $hora_fin, $precio_total);

    if ($stmt->execute()) {
        header("Location: pistas.php?reserva=success");
    } else {
        header("Location: pistas.php?reserva=error");
    }
}
?>
