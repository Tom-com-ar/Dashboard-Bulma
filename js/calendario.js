let eventos = {};
let fechaSeleccionada = null;

// Fetch de eventos con manejo de rutas
fetch("./php/eventos.php")
  .then(res => {
    if (!res.ok) {
      console.error('Error al cargar eventos:', res.status);
      throw new Error('Error de conexión');
    }
    return res.json();
  })
  .then(data => {
    if (data && data.length) {
      data.forEach(ev => {
        eventos[ev.fecha] = ev;
      });
    }
    renderCalendar();
  })
  .catch(error => {
    console.error('Error:', error);
    renderCalendar();
  });

const calendar = document.getElementById("calendar");
const monthYear = document.getElementById("monthYear");

let date = new Date();

function renderCalendar() {
  calendar.innerHTML = "";

  const year = date.getFullYear();
  const month = date.getMonth();

  // Nombre del mes
  const monthNames = [
    "Enero","Febrero","Marzo","Abril","Mayo","Junio",
    "Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"
  ];
  monthYear.innerText = monthNames[month] + " " + year;

  // Primer día del mes
  const firstDay = new Date(year, month, 1).getDay();

  // Cantidad de días del mes
  const daysInMonth = new Date(year, month + 1, 0).getDate();

  // Ajuste para que empiece en lunes
  const startDay = firstDay === 0 ? 6 : firstDay - 1;

  // Espacios vacíos
  for (let i = 0; i < startDay; i++) {
    calendar.innerHTML += `<div></div>`;
  }

  // Días
  for (let i = 1; i <= daysInMonth; i++) {
    const fecha = `${year}-${String(month+1).padStart(2,'0')}-${String(i).padStart(2,'0')}`;

    let contenido = `<div class="calendar-cell" onclick="abrirModal('${fecha}')"><strong>${i}</strong>`;

    if (eventos[fecha]) {
      const hora = eventos[fecha].hora ? ` ${eventos[fecha].hora.substring(0, 5)}` : '';
      contenido += `<div class="event ${eventos[fecha].color}">${eventos[fecha].titulo}${hora}</div>`;
    }

    contenido += `</div>`;
    calendar.innerHTML += contenido;
  }
}

function prevMonth() {
  date.setMonth(date.getMonth() - 1);
  renderCalendar();
}

function nextMonth() {
  date.setMonth(date.getMonth() + 1);
  renderCalendar();
}

function abrirModal(fecha) {
  fechaSeleccionada = fecha;
  document.getElementById('eventoFecha').value = fecha;
  document.getElementById('eventoTitulo').value = '';
  document.getElementById('eventoHora').value = '12:00';
  document.getElementById('modalEvento').classList.add('is-active');
  document.getElementById('eventoDescripcion').value = '';
}

function cerrarModal() {
  document.getElementById('modalEvento').classList.remove('is-active');
  fechaSeleccionada = null;
}

function guardarEvento() {
  const titulo = document.getElementById('eventoTitulo').value.trim();
  const hora = document.getElementById('eventoHora').value || '00:00';
  const descripcion = document.getElementById('eventoDescripcion').value.trim();
  
  if (!titulo) {
    alert('Por favor, ingresa un nombre para el evento');
    return;
  }
  
  const eventoData = {
    titulo: titulo,
    fecha: fechaSeleccionada,
    hora: hora + ':00',
    color: 'event-green',
    descripcion: descripcion,
  };
  
  // Guardar en la base de datos
  fetch('./php/guardar_evento.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(eventoData)
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      // Agregar el evento a la variable local
      eventos[fechaSeleccionada] = {
        titulo: titulo,
        fecha: fechaSeleccionada,
        hora: hora + ':00',
        color: 'event-green',
        descripcion: descripcion,
      };
      cerrarModal();
      renderCalendar();
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Error al guardar el evento');
  });
}

// Cerrar modal al hacer clic fuera
document.addEventListener('click', function(event) {
  const modal = document.getElementById('modalEvento');
  if (event.target === modal) {
    cerrarModal();
  }
});

renderCalendar();