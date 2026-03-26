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

  // Ajuste para que empiece en lunes (opcional)
  const startDay = firstDay === 0 ? 6 : firstDay - 1;

  // Espacios vacíos
  for (let i = 0; i < startDay; i++) {
    calendar.innerHTML += `<div></div>`;
  }

  // Días
  for (let i = 1; i <= daysInMonth; i++) {
    calendar.innerHTML += `
    <div class="calendar-cell">
      ${i}
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

renderCalendar();