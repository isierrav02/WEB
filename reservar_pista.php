<?php
session_start();
include 'publipistaBD.php';

// Asegurarse de que el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

// Obtener el ID del usuario logueado
$usuario_id = $_SESSION['usuario_id'];

// Obtener la fecha y hora actuales combinadas
$fecha_hora_actual = date("Y-m-d H:i:s");
// Eliminar todas las reservas cuyo tiempo ya ha pasado
$sql_limpieza = "DELETE FROM reservas 
                 WHERE CONCAT(fecha_reserva, ' ', hora_fin) <= ?";
$stmt_limpieza = $conn->prepare($sql_limpieza);
$stmt_limpieza->bind_param("s", $fecha_hora_actual);
$stmt_limpieza->execute();

// Procesar la reserva
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pista_id = $_POST['pista_id'];
    $fecha_reserva = $_POST['fecha_reserva'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];

    // Verificar si el usuario ya tiene una reserva en el mismo horario
    $sql_verificar_usuario = "SELECT * FROM reservas 
                              WHERE usuario_id = ? 
                              AND fecha_reserva = ? 
                              AND (
                                  (hora_inicio <= ? AND hora_fin > ?) OR 
                                  (hora_inicio < ? AND hora_fin >= ?)
                              )";
    $stmt_usuario = $conn->prepare($sql_verificar_usuario);
    $stmt_usuario->bind_param("issss", $usuario_id, $fecha_reserva, $hora_fin, $hora_inicio, $hora_inicio, $hora_fin);
    $stmt_usuario->execute();
    $result_usuario = $stmt_usuario->get_result();

    if ($result_usuario->num_rows > 0) {
        // Si el usuario ya tiene una reserva que se solape
        echo "<script>alert('Ya tienes una reserva en el mismo horario.'); window.history.back();</script>";
        exit();
    }

    // Verificar si la pista ya está reservada en el mismo horario
    $sql_verificar_pista = "SELECT * FROM reservas 
                            WHERE pista_id = ? 
                            AND fecha_reserva = ? 
                            AND (
                                (hora_inicio <= ? AND hora_fin > ?) OR 
                                (hora_inicio < ? AND hora_fin >= ?)
                            )";
    $stmt_pista = $conn->prepare($sql_verificar_pista);
    $stmt_pista->bind_param("issss", $pista_id, $fecha_reserva, $hora_fin, $hora_inicio, $hora_inicio, $hora_fin);
    $stmt_pista->execute();
    $result_pista = $stmt_pista->get_result();

    if ($result_pista->num_rows > 0) {
        // Si la pista ya está reservada en el horario seleccionado
        echo "<script>alert('Esta pista ya está reservada en el horario seleccionado.'); window.history.back();</script>";
        exit();
    }

    // Si no hay conflictos, insertar la reserva
    $sql_insertar = "INSERT INTO reservas (usuario_id, pista_id, fecha_reserva, hora_inicio, hora_fin) 
                     VALUES (?, ?, ?, ?, ?)";
    $stmt_insertar = $conn->prepare($sql_insertar);
    $stmt_insertar->bind_param("iisss", $usuario_id, $pista_id, $fecha_reserva, $hora_inicio, $hora_fin);

    if ($stmt_insertar->execute()) {
        echo "<script>alert('Reserva realizada con éxito.'); window.location.href = 'mis_reservas.php';</script>";
    } else {
        echo "<script>alert('Hubo un error al realizar la reserva. Inténtalo de nuevo.'); window.history.back();</script>";
    }
}
