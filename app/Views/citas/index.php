<?php
/** @var array $citas @var array $proximas @var array $horarios @var string $waNum */
$MESES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
?>
<div class="page-title">Calendario de citas</div>
<div class="two-col">
  <div>
    <div class="card">
      <div class="cal-nav">
        <button type="button" onclick="cambiarMes(-1)"><i class="fa-solid fa-chevron-left"></i></button>
        <span id="cal-titulo"></span>
        <button type="button" onclick="cambiarMes(1)"><i class="fa-solid fa-chevron-right"></i></button>
      </div>
      <div class="cal-grid" id="cal-grid"></div>
    </div>

    <div class="card" id="panel-nueva-cita" style="display:none;">
      <div class="card-title" id="label-fecha-cita"></div>
      <form method="post" action="<?= e(url('/citas/agendar')) ?>">
        <input type="hidden" name="fecha" id="cita-fecha" />
        <input type="hidden" name="hora" id="cita-hora" />
        <label>Nombre del cliente</label>
        <input type="text" name="nombre" id="cita-nombre" placeholder="Nombre" />
        <label>Teléfono</label>
        <input type="tel" name="telefono" id="cita-tel" placeholder="Teléfono" />
        <label>Placa / moto</label>
        <input type="text" name="placa" id="cita-placa" placeholder="Ej: ABC123" oninput="this.value=this.value.toUpperCase()" />
        <label>Hora</label>
        <div class="horarios-grid" id="horarios-grid"></div>
        <label>Nota (opcional)</label>
        <input type="text" name="nota" id="cita-nota" placeholder="Ej: lavada completa" />
        <div style="display:flex; gap:8px; margin-top:1rem;">
          <button type="submit" class="btn btn-primary" style="flex:1"><i class="fa-solid fa-calendar-check"></i> Agendar</button>
          <button type="button" class="btn btn-wa" onclick="agendarWhatsapp()">
            <i class="fa-brands fa-whatsapp" style="font-size:18px;"></i>
            WhatsApp
          </button>
        </div>
      </form>
    </div>
  </div>

  <div>
    <div id="citas-del-dia-card" style="display:none;" class="card">
      <div class="card-title" id="citas-dia-titulo"></div>
      <div id="lista-citas-dia"></div>
    </div>
    <div class="card">
      <div class="card-title"><i class="fa-solid fa-thumbtack"></i> Próximas citas</div>
      <div id="proximas-citas">
        <?php if (empty($proximas)): ?>
          <p style="color:var(--gris);font-size:13px;">No hay citas próximas</p>
        <?php else: foreach ($proximas as $c):
          [$y, $m, $d] = explode('-', $c['fecha']); ?>
          <div class="cita-item">
            <div>
              <strong style="font-size:13px;"><?= e($c['nombre']) ?></strong><br>
              <small style="color:var(--gris);">
                <?= (int) $d ?> <?= e(substr($MESES[(int) $m - 1], 0, 3)) ?> ·
                <?= e($c['placa'] ?: $c['telefono']) ?>
              </small>
            </div>
            <strong style="color:var(--azul);"><?= e($c['hora']) ?></strong>
          </div>
        <?php endforeach; endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Formulario oculto para eliminar citas -->
<form method="post" action="<?= e(url('/citas/eliminar')) ?>" id="form-eliminar-cita" style="display:none;">
  <input type="hidden" name="id" id="eliminar-cita-id" />
</form>

<script>
  window.TQ_CITAS    = <?= json_encode($citas, JSON_UNESCAPED_UNICODE) ?>;
  window.TQ_HORARIOS = <?= json_encode($horarios, JSON_UNESCAPED_UNICODE) ?>;
  window.TQ_WA_NUM   = <?= json_encode($waNum) ?>;
</script>
