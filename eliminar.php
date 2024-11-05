<?php
session_start();
include 'publipistaBD.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];

    $sql = "DELETE FROM reservas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: pistas.php");
    } else {
        echo "Error al eliminar la reserva.";
    }
}
?>
