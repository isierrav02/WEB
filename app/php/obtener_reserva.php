<?php
include 'publipistaBD.php';

if (isset($_GET['id'])) {
    $reserva_id = $_GET['id'];
    $sql = "SELECT * FROM reservas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reserva_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $reserva = $result->fetch_assoc();

    echo json_encode($reserva);
}
