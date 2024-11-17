// Función para abrir el modal de reserva y asignar el ID y precio de la pista seleccionada
function abrirModalReserva(pistaId, precioBase) {
    document.getElementById('pista_id').value = pistaId;
    document.getElementById('precio_base').value = precioBase;

    // Configurar la fecha mínima en el input de fecha de reserva
    const fechaReservaInput = document.getElementById('fecha_reserva');
    const hoy = new Date();
    const fechaMinima = hoy.toISOString().split('T')[0]; // Formato 'YYYY-MM-DD'
    fechaReservaInput.setAttribute('min', fechaMinima);

    const reservaModal = new bootstrap.Modal(document.getElementById('reservaModal'));
    reservaModal.show();
}

// Función para calcular el precio en función de la duración de la reserva
function calcularPrecio(precioBase, horas, minutos) {
    let totalHoras = horas + (minutos / 60); // Convierte horas y minutos a horas decimales
    return precioBase * totalHoras;
}

// Restricciones para el formulario de reserva y cálculo de precio
document.getElementById('formReserva').addEventListener('submit', function(event) {
    const fechaReserva = document.getElementById('fecha_reserva').value;
    const horaInicio = document.getElementById('hora_inicio').value;
    const horaFin = document.getElementById('hora_fin').value;
    const precioBase = parseFloat(document.getElementById('precio_base').value);

    // Validar que la hora de fin sea mayor que la hora de inicio
    if (horaInicio >= horaFin) {
        alert("La hora de fin debe ser posterior a la hora de inicio.");
        event.preventDefault();
        return;
    }

    // Calcular la duración de la reserva en horas y minutos
    const horaInicioDate = new Date(`1970-01-01T${horaInicio}:00`);
    const horaFinDate = new Date(`1970-01-01T${horaFin}:00`);
    const duracionReserva = (horaFinDate - horaInicioDate) / (1000 * 60); // Duración en minutos

    // Verificar que la duración sea al menos 1 hora y en intervalos de media hora
    if (duracionReserva < 60 || duracionReserva % 30 !== 0) {
        alert("La reserva debe ser de al menos 1 hora y en intervalos de 30 minutos.");
        event.preventDefault();
        return;
    }

    // Calcular el precio total de la reserva
    const horas = Math.floor(duracionReserva / 60); // Horas completas
    const minutos = duracionReserva % 60;           // Minutos restantes (0 o 30)
    const precioTotal = calcularPrecio(precioBase, horas, minutos);

    // Mostrar el precio total en el formulario o en una alerta antes de confirmar la reserva
    if (!confirm(`El precio total de la reserva es de ${precioTotal.toFixed(2)} €. ¿Deseas continuar?`)) {
        event.preventDefault();
    }
});
