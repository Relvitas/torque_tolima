/* ============================================================
   Torque Tolima — JS de interacción (UI). La persistencia
   ocurre en el servidor; aquí solo hay comportamiento visual.
   ============================================================ */

const MESES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
const DIAS  = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];

/* ===== LAVADA: selección de precio ===== */
function seleccionarPrecio(precio, btn) {
  document.querySelectorAll('.precio-btn').forEach(b => b.classList.remove('selected'));
  btn.classList.add('selected');
  document.getElementById('precio').value = precio;
  document.getElementById('inputPersonalizado').style.display = 'none';
  mostrarPrecio(precio);
}
function seleccionarPersonalizado(btn) {
  document.querySelectorAll('.precio-btn').forEach(b => b.classList.remove('selected'));
  btn.classList.add('selected');
  document.getElementById('precio').value = 0;
  document.getElementById('inputPersonalizado').style.display = 'block';
  document.getElementById('resumen-lavada').style.display = 'none';
  document.getElementById('valorPersonalizado').focus();
}
function mostrarPrecio(p) {
  document.getElementById('resumen-lavada').style.display = 'block';
  document.getElementById('precio-display').textContent = '$' + Number(p).toLocaleString('es-CO');
}

/* ===== LAVADA: editar valor de una lavada del día ===== */
function editarValorLavada(id, actual) {
  const v = prompt('Nuevo valor de la lavada (COP):', actual);
  if (v === null) return;
  const num = parseInt(String(v).replace(/\D/g, ''), 10);
  if (!num || num <= 0) {
    alert('Ingresa un valor válido mayor a 0');
    return;
  }
  document.getElementById('ep-id').value = id;
  document.getElementById('ep-precio').value = num;
  document.getElementById('form-editar-precio').submit();
}

/* ===== LAVADA: avisar por WhatsApp que la moto está lista ===== */
function avisarLavadaLista(tel, nombre) {
  let num = String(tel).replace(/\D/g, '');
  // Celular colombiano de 10 dígitos -> anteponer código de país 57.
  if (num.length === 10 && num.charAt(0) === '3') {
    num = '57' + num;
  }
  const msg = 'Hola ' + (nombre || '') + '! 🏍️\n\n' +
    'Te informamos que la lavada de tu moto en Torque Tolima ya está lista. ✅\n' +
    'Ya puedes pasar a recogerla.\n\n¡Gracias por tu preferencia!';
  window.open('https://wa.me/' + num + '?text=' + encodeURIComponent(msg), '_blank');
}

/* ===== LAVADA: foto preview ===== */
function cargarFoto(input) {
  const file = input.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = e => {
    document.getElementById('fotoPreview').innerHTML =
      '<img src="' + e.target.result + '" alt="moto" style="width:90px;height:90px;object-fit:cover;border-radius:6px;" />';
  };
  reader.readAsDataURL(file);
}

/* ===== LAVADA: autocompletar por teléfono (AJAX) ===== */
function initLavada() {
  const tel = document.getElementById('telefono');
  if (!tel) return;

  document.getElementById('valorPersonalizado')?.addEventListener('input', function () {
    const v = parseInt(this.value) || 0;
    document.getElementById('precio').value = v;
    if (v > 0) mostrarPrecio(v);
  });

  tel.addEventListener('input', function () { buscarClientePorTel(this.value); });

  // Si llega con un teléfono precargado (ej. desde "Clientes"), autocompleta.
  if (tel.value.trim().length >= 7) buscarClientePorTel(tel.value.trim());
}

async function buscarClientePorTel(valor) {
  const div = document.getElementById('cliente-ok');
  const cardP = document.getElementById('card-puntos');
  if (valor.length < 7) { div.style.display = 'none'; cardP.style.display = 'none'; return; }

  try {
    const res = await fetch('lavada/buscar?tel=' + encodeURIComponent(valor));
    const data = await res.json();
    if (!data.encontrado) { div.style.display = 'none'; cardP.style.display = 'none'; return; }

    const c = data.cliente;
    document.getElementById('nombre').value = c.nombre || '';
    document.getElementById('tipoMoto').value = c.moto || '';
    document.getElementById('placa').value = c.placa || '';
    if (c.foto) document.getElementById('fotoPreview').innerHTML = '<img src="' + c.foto + '" alt="moto" />';

    div.textContent = '✓ Cliente encontrado: ' + c.nombre + ' | Lavadas este ciclo: ' + c.ciclo + '/5';
    div.style.display = 'block';
    actualizarPuntos(c);
  } catch (e) { /* silencio: sin conexión, sigue como cliente nuevo */ }
}

function actualizarPuntos(c) {
  const card = document.getElementById('card-puntos');
  const bar  = document.getElementById('puntos-preview');
  const txt  = document.getElementById('puntos-texto');
  card.style.display = 'block';
  const ciclo = c.ciclo;
  let html = '';
  for (let i = 1; i <= 5; i++) html += '<div class="punto' + (i <= ciclo ? ' lleno' : '') + '">' + (i <= ciclo ? '<i class="fa-solid fa-check"></i>' : i) + '</div>';
  html += '<div class="punto estrella"><i class="fa-solid fa-star"></i></div>';
  bar.innerHTML = html;
  if (ciclo === 0 && c.lavadas > 0) txt.textContent = '🎉 ¡Próxima lavada es GRATIS!';
  else txt.textContent = 'Faltan ' + (5 - ciclo) + ' lavada(s) para la gratis';
}

/* ============================================================
   CITAS: calendario
   ============================================================ */
let calYear, calMonth, fechaSeleccionada = null, horaSeleccionada = null;

function initCal() {
  if (!document.getElementById('cal-grid')) return;
  const hoy = new Date();
  calYear = hoy.getFullYear();
  calMonth = hoy.getMonth();
  renderCal();
}
function cambiarMes(dir) {
  calMonth += dir;
  if (calMonth > 11) { calMonth = 0; calYear++; }
  if (calMonth < 0) { calMonth = 11; calYear--; }
  renderCal();
}
function renderCal() {
  const citas = window.TQ_CITAS || [];
  document.getElementById('cal-titulo').textContent = MESES[calMonth] + ' ' + calYear;
  const hoy = new Date(); hoy.setHours(0, 0, 0, 0);
  const primerDia = new Date(calYear, calMonth, 1).getDay();
  const diasMes = new Date(calYear, calMonth + 1, 0).getDate();
  const diasAntes = new Date(calYear, calMonth, 0).getDate();

  let html = DIAS.map(d => '<div class="cal-dia-nombre">' + d + '</div>').join('');
  for (let i = 0; i < primerDia; i++) html += '<div class="cal-dia otro-mes pasado">' + (diasAntes - primerDia + i + 1) + '</div>';

  for (let d = 1; d <= diasMes; d++) {
    const fecha = new Date(calYear, calMonth, d); fecha.setHours(0, 0, 0, 0);
    const fs = calYear + '-' + String(calMonth + 1).padStart(2, '0') + '-' + String(d).padStart(2, '0');
    const esPasado = fecha < hoy, esHoy = fecha.getTime() === hoy.getTime(), esSel = fs === fechaSeleccionada;
    const tieneCita = citas.some(c => c.fecha === fs);
    let cls = 'cal-dia' + (esPasado ? ' pasado' : '') + (esHoy ? ' hoy' : '') + (esSel ? ' seleccionado' : '') + (tieneCita ? ' tiene-cita' : '');
    html += '<div class="' + cls + '"' + (esPasado ? '' : ' onclick="seleccionarFecha(\'' + fs + '\')"') + '>' + d + '</div>';
  }
  const rest = 42 - primerDia - diasMes;
  for (let i = 1; i <= rest; i++) html += '<div class="cal-dia otro-mes pasado">' + i + '</div>';
  document.getElementById('cal-grid').innerHTML = html;
}
function seleccionarFecha(fs) {
  fechaSeleccionada = fs; horaSeleccionada = null;
  renderCal();
  const [y, m, d] = fs.split('-');
  const label = parseInt(d) + ' de ' + MESES[parseInt(m) - 1] + ' de ' + y;
  document.getElementById('label-fecha-cita').textContent = '📅 Agendar para el ' + label;
  document.getElementById('panel-nueva-cita').style.display = 'block';
  document.getElementById('cita-fecha').value = fs;
  document.getElementById('cita-hora').value = '';
  renderHorarios(fs);
  renderCitasDia(fs, label);
}
function renderHorarios(fs) {
  const citas = window.TQ_CITAS || [];
  const horarios = window.TQ_HORARIOS || [];
  const ocupados = citas.filter(c => c.fecha === fs).map(c => c.hora);
  document.getElementById('horarios-grid').innerHTML = horarios.map(h => {
    const ocup = ocupados.includes(h), sel = h === horaSeleccionada;
    return '<button type="button" class="hora-btn' + (ocup ? ' ocupado' : sel ? ' selected' : '') + '"' +
      (ocup ? ' disabled' : '') + ' onclick="selHora(\'' + h + '\',this)">' + h + '</button>';
  }).join('');
}
function selHora(hora, btn) {
  horaSeleccionada = hora;
  document.getElementById('cita-hora').value = hora;
  document.querySelectorAll('.hora-btn').forEach(b => b.classList.remove('selected'));
  btn.classList.add('selected');
}
function renderCitasDia(fs, label) {
  const citas = (window.TQ_CITAS || []).filter(c => c.fecha === fs);
  const cont = document.getElementById('citas-del-dia-card');
  if (!citas.length) { cont.style.display = 'none'; return; }
  cont.style.display = 'block';
  document.getElementById('citas-dia-titulo').textContent = 'Citas del ' + label;
  document.getElementById('lista-citas-dia').innerHTML = citas.map(c => {
    const extra = [c.placa, c.nota].filter(Boolean).join(' · ');
    return '<div class="cita-item"><div><strong>' + esc(c.nombre) + '</strong><br>' +
      '<small style="color:var(--gris);">' + esc(c.telefono) + (extra ? ' · ' + esc(extra) : '') + '</small></div>' +
      '<div style="display:flex;align-items:center;gap:10px;"><strong style="color:var(--azul);">' + esc(c.hora) + '</strong>' +
      '<button class="btn btn-danger" style="padding:4px 10px;font-size:12px;" onclick="eliminarCita(' + c.id + ')"><i class="fa-solid fa-xmark"></i></button></div></div>';
  }).join('');
}
function eliminarCita(id) {
  if (!confirm('¿Eliminar esta cita?')) return;
  document.getElementById('eliminar-cita-id').value = id;
  document.getElementById('form-eliminar-cita').submit();
}
function agendarWhatsapp() {
  const nombre = document.getElementById('cita-nombre').value.trim() || '...';
  const placa  = document.getElementById('cita-placa').value.trim() || '...';
  const hora   = horaSeleccionada || '(hora por confirmar)';
  let fechaLabel = '(fecha por confirmar)';
  if (fechaSeleccionada) {
    const [y, m, d] = fechaSeleccionada.split('-');
    fechaLabel = parseInt(d) + ' de ' + MESES[parseInt(m) - 1] + ' de ' + y;
  }
  const msg = 'Hola! Quiero confirmar tu cita en Torque Tolima 🏍️\n\nNombre: ' + nombre +
    '\nPlaca: ' + placa + '\nFecha: ' + fechaLabel + '\nHora: ' + hora +
    '\n\nQuedo pendiente de confirmación. Gracias!';
  window.open('https://wa.me/' + window.TQ_WA_NUM + '?text=' + encodeURIComponent(msg), '_blank');
}

/* ===== util ===== */
function esc(s) {
  return String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
}

/* ===== arranque ===== */
document.addEventListener('DOMContentLoaded', function () {
  initLavada();
  initCal();
});
