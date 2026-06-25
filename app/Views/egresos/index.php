<?php
/** @var array $egresos @var int $totalMes @var int $totalHoy @var array $porMes
 *  @var string $mes @var array $categorias */
?>
<div class="page-title">Egresos del negocio</div>

<div class="stats-grid">
  <div class="stat-card"><div class="stat-num" style="font-size:22px;"><?= e(cop($totalHoy)) ?></div><div class="stat-lbl">Egresos de hoy</div></div>
  <div class="stat-card"><div class="stat-num" style="font-size:22px;"><?= e(cop($totalMes)) ?></div><div class="stat-lbl">Egresos de <?= e(mes_label($mes)) ?></div></div>
  <div class="stat-card"><div class="stat-num"><?= count($egresos) ?></div><div class="stat-lbl">Movimientos del mes</div></div>
</div>

<div class="two-col">
  <div>
    <div class="card">
      <div class="card-title"><i class="fa-solid fa-circle-minus"></i> Registrar egreso</div>
      <form method="post" action="<?= e(url('/egresos/registrar')) ?>">
        <label>Concepto</label>
        <input type="text" name="concepto" placeholder="Ej: Jabón, shampoo, ceras..." required />
        <label>Categoría</label>
        <select name="categoria">
          <?php foreach ($categorias as $cat): ?>
            <option value="<?= e($cat) ?>"><?= e($cat) ?></option>
          <?php endforeach; ?>
        </select>
        <label>Monto (COP)</label>
        <input type="number" name="monto" min="1" step="1" placeholder="Ej: 25000" required />
        <label>Nota (opcional)</label>
        <input type="text" name="nota" placeholder="Detalle adicional" />
        <button type="submit" class="btn btn-primary" style="width:100%; margin-top:1rem;">
          <i class="fa-solid fa-plus"></i> Registrar egreso
        </button>
      </form>
    </div>
  </div>

  <div>
    <div class="card">
      <div class="card-title"><i class="fa-solid fa-calendar-day"></i> Total por mes</div>
      <?php if (empty($porMes)): ?>
        <p style="color:var(--gris);font-size:13px;">Sin egresos registrados</p>
      <?php else: foreach ($porMes as $m): ?>
        <a class="cita-item" style="text-decoration:none;color:inherit;<?= $m['mes'] === $mes ? 'background:var(--brand-claro);' : '' ?>"
           href="<?= e(url('/egresos?mes=' . urlencode($m['mes']))) ?>">
          <div>
            <strong style="font-size:13px;"><?= e(mes_label($m['mes'])) ?></strong><br>
            <small style="color:var(--gris);"><?= (int) $m['cantidad'] ?> movimiento(s)</small>
          </div>
          <strong style="color:var(--azul);"><?= e(cop($m['total'])) ?></strong>
        </a>
      <?php endforeach; endif; ?>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-title"><i class="fa-solid fa-list"></i> Egresos de <?= e(mes_label($mes)) ?></div>
  <div class="tabla-wrap">
    <table>
      <thead>
        <tr>
          <th>Fecha</th><th>Concepto</th><th>Categoría</th><th>Monto</th><th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($egresos as $eg): ?>
          <tr>
            <td style="font-size:12px;color:var(--gris);"><?= e($eg['creado_en']) ?></td>
            <td>
              <strong><?= e($eg['concepto']) ?></strong>
              <?php if (!empty($eg['nota'])): ?><br><small style="color:var(--gris);"><?= e($eg['nota']) ?></small><?php endif; ?>
            </td>
            <td><span class="badge badge-pagada"><?= e($eg['categoria']) ?></span></td>
            <td style="font-weight:600;"><?= e(cop($eg['monto'])) ?></td>
            <td>
              <form method="post" action="<?= e(url('/egresos/eliminar')) ?>"
                    onsubmit="return confirm('¿Eliminar este egreso? Esta acción no se puede deshacer.');" style="display:inline;">
                <input type="hidden" name="id" value="<?= (int) $eg['id'] ?>" />
                <button type="submit" class="btn btn-danger" style="padding:4px 10px;font-size:12px;" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php if (empty($egresos)): ?>
    <div style="text-align:center;padding:2rem;color:var(--texto2);">No hay egresos en <?= e(mes_label($mes)) ?></div>
  <?php endif; ?>
</div>
