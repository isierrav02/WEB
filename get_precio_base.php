<?php
include 'publipistaBD.php';

if (isset($_POST['pista_id']) && isset($_POST['horas'])) {
    $pista_id = $_POST['pista_id'];
    $horas = (float)$_POST['horas'];  // Convertir a flotante para manejar medias horas

    // Consulta para obtener el precio base de la pista seleccionada
    $sql = "SELECT precio_base FROM pistas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $pista_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $precio_base = $row['precio_base'];
        
        // Calcula el precio total teniendo en cuenta las fracciones de hora
        $precio_total = $precio_base * $horas;
        echo number_format($precio_total, 2); // Devuelve el precio total formateado a dos decimales
    } else {
        echo "0.00";
    }
}
?>
