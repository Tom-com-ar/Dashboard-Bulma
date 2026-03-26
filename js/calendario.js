let eventosPorFecha = {};
let eventosPorId = {};

let fechaSeleccionada = null;
let eventoEditandoId = null;

const calendar = document.getElementById("calendar");
const monthYear = document.getElementById("monthYear");

let date = new Date();

let guardando = false;

// =====================
// HELPERS
// =====================
function escapeHtml(str) {
  return String(str ?? "")
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

// =====================
// CARGAR EVENTOS
// =====================
async function cargarEventos() {
  try {
    const res = await fetch("./php/calendario/eventos.php");
    const data = await res.json();

    eventosPorFecha = {};
    eventosPorId = {};

    data.forEach(ev => {
      if (!eventosPorFecha[ev.fecha]) eventosPorFecha[ev.fecha] = [];
      eventosPorFecha[ev.fecha].push(ev);
      eventosPorId[String(ev.id)] = ev;
    });

  } catch (e) {
    console.error(e);
  } finally {
    renderCalendar();
  }
}

// =====================
// RENDER CALENDAR
// =====================
function renderCalendar() {
  if (!calendar || !monthYear) return;

  calendar.innerHTML = "";

  const year = date.getFullYear();
  const month = date.getMonth();

  const monthNames = [
    "Enero","Febrero","Marzo","Abril","Mayo","Junio",
    "Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"
  ];

  monthYear.innerText = `${monthNames[month]} ${year}`;

  const firstDay = new Date(year, month, 1).getDay();
  const daysInMonth = new Date(year, month + 1, 0).getDate();
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
        const colorClass = ev.color || "event-green";
        const isEditable = colorClass === "event-green";
        const titulo = escapeHtml(ev.titulo);
        const time = ev.hora ? ev.hora.substring(0,5) : "";
    
        return `
          <div class="event-item ${colorClass}" 
               data-event-id="${ev.id}"
               data-editable="${isEditable ? '1':'0'}">
    
            <div class="event-content">
              ${time ? `<div class="event-time">${time}</div>` : ""}
              <div class="event-title">${titulo}</div>
            </div>
    
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

// =====================
// NAVEGACION
// =====================
function prevMonth() {
  date.setMonth(date.getMonth() - 1);
  renderCalendar();
}

function nextMonth() {
  date.setMonth(date.getMonth() + 1);
  renderCalendar();
}

// =====================
// MODAL
// =====================
function resetModal() {
  ["eventoTitulo","eventoHora","eventoDescripcion"].forEach(id=>{
    const el = document.getElementById(id);
    if (el) {
      el.disabled = false;
      el.value = "";
    }
  });

  document.getElementById("btnGuardarEvento").style.display = "inline-flex";
  document.getElementById("btnBorrarEvento").style.display = "none";
}

function abrirModalCrear(fecha) {
  resetModal();

  fechaSeleccionada = fecha;
  eventoEditandoId = null;

  document.getElementById("eventoFecha").value = fecha;
  document.getElementById("modalTitulo").textContent = "Crear Evento";

  document.getElementById("modalEvento").classList.add("is-active");
}

function abrirModalEditar(id) {
  resetModal();

  const ev = eventosPorId[id];
  if (!ev) return;

  eventoEditandoId = id;
  fechaSeleccionada = ev.fecha;

  document.getElementById("eventoFecha").value = ev.fecha;
  document.getElementById("eventoTitulo").value = ev.titulo;
  document.getElementById("eventoHora").value = ev.hora?.substring(0,5);
  document.getElementById("eventoDescripcion").value = ev.descripcion;

  document.getElementById("btnBorrarEvento").style.display = "inline-flex";
  document.getElementById("modalTitulo").textContent = "Editar Evento";

  document.getElementById("modalEvento").classList.add("is-active");
}

function abrirModalVer(id) {
  const ev = eventosPorId[id];
  if (!ev) return;

  document.getElementById("eventoFecha").value = ev.fecha;
  document.getElementById("eventoTitulo").value = ev.titulo;
  document.getElementById("eventoHora").value = ev.hora?.substring(0,5);
  document.getElementById("eventoDescripcion").value = ev.descripcion;

  ["eventoTitulo","eventoHora","eventoDescripcion"].forEach(id=>{
    document.getElementById(id).disabled = true;
  });

  document.getElementById("btnGuardarEvento").style.display = "none";
  document.getElementById("btnBorrarEvento").style.display = "none";
  document.getElementById("modalTitulo").textContent = "Ver Evento";

  document.getElementById("modalEvento").classList.add("is-active");
}

function cerrarModal() {
  document.getElementById("modalEvento").classList.remove("is-active");
}

// =====================
// GUARDAR / EDITAR
// =====================
async function guardarOActualizarEvento() {
  if (guardando) return;
  guardando = true;

  try {
    const titulo = document.getElementById("eventoTitulo").value.trim();
    const hora = document.getElementById("eventoHora").value;
    const descripcion = document.getElementById("eventoDescripcion").value;

    if (!titulo) return alert("Falta título");

    const payload = {
      titulo,
      fecha: fechaSeleccionada,
      hora: hora + ":00",
      color: "event-green",
      descripcion
    };

    let url = "./php/calendario/guardar_evento.php";

    if (eventoEditandoId) {
      payload.id = eventoEditandoId;
      url = "./php/calendario/actualizar_evento.php";
    }

    await fetch(url, {
      method: "POST",
      headers: {"Content-Type":"application/json"},
      body: JSON.stringify(payload)
    });

    cerrarModal();
    cargarEventos();

  } catch (e) {
    console.error(e);
  } finally {
    guardando = false;
  }
}

// =====================
// BORRAR
// =====================
async function borrarEvento() {
  if (!eventoEditandoId) return;

  const ok = confirm("¿Seguro que querés borrar?");
  if (!ok) return;

  await fetch("./php/calendario/eliminar_evento.php", {
    method: "POST",
    headers: {"Content-Type":"application/json"},
    body: JSON.stringify({ id: eventoEditandoId })
  });

  cerrarModal();
  cargarEventos();
}

// =====================
// INIT (ANTI DUPLICADOS)
// =====================
if (!window._calendarInitialized) {
  window._calendarInitialized = true;

  document.getElementById("btnGuardarEvento")
    ?.addEventListener("click", guardarOActualizarEvento);

  document.getElementById("btnBorrarEvento")
    ?.addEventListener("click", borrarEvento);

  calendar?.addEventListener("click", (e) => {
    const item = e.target.closest(".event-item");

    if (item) {
      const id = item.dataset.eventId;
      const editable = item.dataset.editable === "1";

      editable ? abrirModalEditar(id) : abrirModalVer(id);
      return;
    }

    const cell = e.target.closest(".calendar-cell");
    if (cell) abrirModalCrear(cell.dataset.fecha);
  });
}

// =====================
cargarEventos();