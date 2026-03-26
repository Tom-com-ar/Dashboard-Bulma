let eventosPorFecha = {};
let eventosPorId = {};

let fechaSeleccionada = null;
let eventoEditandoId = null;

const calendar = document.getElementById("calendar");
const monthYear = document.getElementById("monthYear");

let date = new Date();

function escapeHtml(str) {
  return String(str ?? "")
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

function horaToMinutes(hora) {
  if (!hora) return 0;
  // Espera "HH:MM:SS" o "HH:MM"
  const parts = String(hora).split(":");
  const h = parseInt(parts[0] || "0", 10);
  const m = parseInt(parts[1] || "0", 10);
  return h * 60 + m;
}

function getMaxVisibleEvents() {
  const w = window.innerWidth || 1024;
  if (w <= 480) return 2;
  if (w <= 768) return 3;
  return 4;
}

async function cargarEventos() {
  try {
    const res = await fetch("./php/calendario/eventos.php");
    if (res.status === 401) {
      window.location.href = "./login.php";
      return;
    }
    if (!res.ok) throw new Error("Error de conexión");

    const data = await res.json();
    eventosPorFecha = {};
    eventosPorId = {};

    if (data && Array.isArray(data)) {
      data.forEach(ev => {
        const fecha = ev.fecha;
        if (!eventosPorFecha[fecha]) eventosPorFecha[fecha] = [];
        eventosPorFecha[fecha].push(ev);
        eventosPorId[String(ev.id)] = ev;
      });
    }

    // Ordenar por hora dentro de cada fecha
    Object.keys(eventosPorFecha).forEach(fecha => {
      eventosPorFecha[fecha].sort((a, b) => {
        const diff = horaToMinutes(a.hora) - horaToMinutes(b.hora);
        if (diff !== 0) return diff;
        return (a.id || 0) - (b.id || 0);
      });
    });
  } catch (e) {
    console.error(e);
  } finally {
    renderCalendar();
  }
}

function renderCalendar() {
  if (!calendar || !monthYear) return;

  calendar.innerHTML = "";

  const year = date.getFullYear();
  const month = date.getMonth();

  const monthNames = [
    "Enero","Febrero","Marzo","Abril","Mayo","Junio",
    "Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"
  ];
  monthYear.innerText = monthNames[month] + " " + year;

  const firstDay = new Date(year, month, 1).getDay();
  const daysInMonth = new Date(year, month + 1, 0).getDate();

  // Lunes como primer día
  const startDay = firstDay === 0 ? 6 : firstDay - 1;

  for (let i = 0; i < startDay; i++) {
    calendar.innerHTML += `<div></div>`;
  }

  for (let i = 1; i <= daysInMonth; i++) {
    const fecha = `${year}-${String(month + 1).padStart(2, "0")}-${String(i).padStart(2, "0")}`;

    let eventsHtml = "";
    const list = eventosPorFecha[fecha] || [];

    if (list.length > 0) {
      const stackItems = list.map(ev => {
        const colorClass = ev.color ? String(ev.color) : "event-green";
        const titulo = escapeHtml(ev.titulo);
        const time = ev.hora ? ` ${String(ev.hora).substring(0, 5)}` : "";

        return `
          <div class="event-item ${colorClass}" 
               data-event-id="${escapeHtml(ev.id)}" 
               title="${escapeHtml(ev.descripcion)}">
               
            <div class="event-time">${escapeHtml(time.trim())}</div>
            <div class="event-title">${titulo}</div>
          </div>
        `;
      }).join("");

      eventsHtml = `<div class="event-stack">${stackItems}</div>`;
    }

    calendar.innerHTML += `
      <div class="calendar-cell" data-fecha="${fecha}">
        <strong>${i}</strong>
        ${eventsHtml}
      </div>
    `;
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

function syncModalUI(mode) {
  const modalTitulo = document.getElementById("modalTitulo");
  const btnGuardar = document.getElementById("btnGuardarEvento");
  const btnBorrar = document.getElementById("btnBorrarEvento");

  if (mode === "create") {
    if (modalTitulo) modalTitulo.textContent = "Crear Evento";
    if (btnGuardar) btnGuardar.textContent = "Guardar";
    if (btnBorrar) btnBorrar.style.display = "none";
  } else {
    if (modalTitulo) modalTitulo.textContent = "Editar Evento";
    if (btnGuardar) btnGuardar.textContent = "Guardar Cambios";
    if (btnBorrar) btnBorrar.style.display = "inline-flex";
  }
}

function abrirModalCrear(fecha) {
  fechaSeleccionada = fecha;
  eventoEditandoId = null;

  const inputFecha = document.getElementById("eventoFecha");
  const inputId = document.getElementById("eventoId");
  const inputTitulo = document.getElementById("eventoTitulo");
  const inputHora = document.getElementById("eventoHora");
  const inputDescripcion = document.getElementById("eventoDescripcion");

  if (inputFecha) inputFecha.value = fecha;
  if (inputId) inputId.value = "";
  if (inputTitulo) inputTitulo.value = "";
  if (inputHora) inputHora.value = "12:00";
  if (inputDescripcion) inputDescripcion.value = "";

  syncModalUI("create");
  document.getElementById("modalEvento").classList.add("is-active");
}

function abrirModalEditar(eventId) {
  const ev = eventosPorId[String(eventId)];
  if (!ev) return;

  eventoEditandoId = ev.id;
  fechaSeleccionada = ev.fecha;

  const inputFecha = document.getElementById("eventoFecha");
  const inputId = document.getElementById("eventoId");
  const inputTitulo = document.getElementById("eventoTitulo");
  const inputHora = document.getElementById("eventoHora");
  const inputDescripcion = document.getElementById("eventoDescripcion");

  if (inputFecha) inputFecha.value = ev.fecha;
  if (inputId) inputId.value = String(ev.id);
  if (inputTitulo) inputTitulo.value = ev.titulo || "";
  if (inputHora) inputHora.value = ev.hora ? String(ev.hora).substring(0, 5) : "12:00";
  if (inputDescripcion) inputDescripcion.value = ev.descripcion || "";

  syncModalUI("edit");
  document.getElementById("modalEvento").classList.add("is-active");
}

function cerrarModal() {
  document.getElementById("modalEvento").classList.remove("is-active");
  fechaSeleccionada = null;
  eventoEditandoId = null;
}

async function guardarOActualizarEvento() {
  const titulo = document.getElementById("eventoTitulo").value.trim();
  const hora = document.getElementById("eventoHora").value || "00:00";
  const descripcion = document.getElementById("eventoDescripcion").value.trim();

  if (!fechaSeleccionada) {
    alert("Selecciona una fecha.");
    return;
  }
  if (!titulo) {
    alert("Por favor, ingresa un nombre para el evento");
    return;
  }

  const payload = {
    titulo: titulo,
    fecha: fechaSeleccionada,
    hora: hora + ":00",
    color: "event-green",
    descripcion: descripcion
  };

  let url = "./php/calendario/guardar_evento.php";
  if (eventoEditandoId) {
    url = "./php/calendario/actualizar_evento.php";
    payload.id = eventoEditandoId;
  }

  try {
    const res = await fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload)
    });

    if (res.status === 401) {
      window.location.href = "./login.php";
      return;
    }

    const data = await res.json();
    if (!data.success) throw new Error(data.message || "Error al guardar");

    cerrarModal();
    await cargarEventos();
  } catch (e) {
    console.error(e);
    alert("Error al guardar el evento");
  }
}

async function borrarEvento() {
  if (!eventoEditandoId) return;

  const ok = confirm("¿Seguro que quieres borrar este evento?");
  if (!ok) return;

  try {
    const res = await fetch("./php/calendario/eliminar_evento.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id: eventoEditandoId })
    });

    if (res.status === 401) {
      window.location.href = "./login.php";
      return;
    }

    const data = await res.json();
    if (!data.success) throw new Error(data.message || "Error al borrar");

    cerrarModal();
    await cargarEventos();
  } catch (e) {
    console.error(e);
    alert("Error al borrar el evento");
  }
}

// Click sobre eventos (editar) o sobre la celda (crear)
calendar?.addEventListener("click", (e) => {
  const item = e.target.closest(".event-item");
  if (item) {
    if (!item.classList.contains("event-green")) {
      return;
    }
    const eventId = item.getAttribute("data-event-id");
    abrirModalEditar(eventId);
    return;
  }

  if (e.target.closest(".event-more")) {
    return;
  }

  const cell = e.target.closest(".calendar-cell");
  if (cell) {
    const fecha = cell.getAttribute("data-fecha");
    if (fecha) abrirModalCrear(fecha);
  }
});

// Cerrar modal al hacer clic fuera
document.addEventListener("click", function (event) {
  const modal = document.getElementById("modalEvento");
  if (event.target === modal) cerrarModal();
});

// Re-render al cambiar tamaño (para ajustar cantidad visible)
let resizeTimer = null;
window.addEventListener("resize", () => {
  clearTimeout(resizeTimer);
  resizeTimer = setTimeout(() => renderCalendar(), 120);
});

cargarEventos();

// Aseguramos que las funciones llamadas desde `onclick=""` estén en el scope global.
// (Algunos navegadores/casos de carga pueden hacer que no queden accesibles como se espera.)
window.prevMonth = prevMonth;
window.nextMonth = nextMonth;
window.abrirModalCrear = abrirModalCrear;
window.abrirModalEditar = abrirModalEditar;
window.cerrarModal = cerrarModal;
window.guardarOActualizarEvento = guardarOActualizarEvento;
window.borrarEvento = borrarEvento;