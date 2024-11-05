<?php
session_start();
include 'publipistaBD.php';

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
}

$email = $_SESSION['email'];

// Consultar ID de usuario para realizar reservas
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$usuario_id = $user['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pista_id = $_POST['pista_id'];
    $fecha_reserva = $_POST['fecha_reserva'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];

    $sql = "SELECT * FROM reservas WHERE usuario_id = ? AND pista_id = ? AND fecha_reserva = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $usuario_id, $pista_id, $fecha_reserva);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Ya tienes una reserva para esta pista en esa fecha.";
    } else {
        $sql = "INSERT INTO reservas (usuario_id, pista_id, fecha_reserva, hora_inicio, hora_fin) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisss", $usuario_id, $pista_id, $fecha_reserva, $hora_inicio, $hora_fin);
        if ($stmt->execute()) {
            $success = "Reserva realizada con éxito.";
        } else {
            $error = "Error al realizar la reserva.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pistas Deportivas | Publipista</title>
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    />
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="bootstrap/css/miestilo.css" />
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Pistas Disponibles</h1>
        <?php if (isset($success)) { echo "<div class='alert alert-success'>$success</div>"; } ?>
        <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="tipo-pista" class="form-label">Elige el tipo de pista:</label>
                <select id="tipo-pista" name="tipo-pista" class="form-select">
                    <option value="tenis">Pista de Tenis</option>
                    <option value="padel">Pista de Pádel</option>
                    <option value="futbol7">Fútbol 7</option>
                    <option value="futbol11">Fútbol 11</option>
                    <option value="baloncesto">Cancha de Baloncesto</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="fecha" class="form-label">Fecha de la reserva:</label>
                <input type="date" id="fecha" name="fecha" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="hora" class="form-label">Hora de la reserva:</label>
                <input type="time" id="hora" name="hora" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Reservar</button>
        </form>

        <h2 class="mt-5">Mis Reservas</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Pista</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM reservas WHERE usuario_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $usuario_id);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($reserva = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$reserva['tipo_pista']}</td>
                        <td>{$reserva['fecha']}</td>
                        <td>{$reserva['hora']}</td>
                        <td>
                            <form method='POST' action='eliminar.php' style='display:inline;'>
                                <input type='hidden' name='id' value='{$reserva['id']}'>
                                <button type='submit' class='btn btn-danger btn-sm'>Eliminar</button>
                            </form>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="./bootstrap/js/bootstrap.bundle.min.js"></script>

</body>
</html>
