<?php
/** @var array $historial @var string $q */
?>
<div class="page-title">Historial de lavadas</div>
<form method="get" action="<?= e(url('/historial')) ?>" class="search-row">
  <input type="text" name="q" value="<?= e($q) ?>" placeholder="Buscar por nombre, placa o teléfono..." />
  <button type="submit" class="btn btn-primary">Buscar</button>
</form>

<div class="card">
  <div class="tabla-wrap">
    <table>
      <thead>
        <tr>
          <th>Fecha</th><th>Cliente</th><th>Placa</th><th>Moto</th>
          <th>Valor</th><th>Estado</th><th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($historial as $h): ?>
          <tr>
            <td style="font-size:12px;color:var(--gris);"><?= e($h['creado_en']) ?></td>
            <td>
              <strong><?= e($h['nombre']) ?></strong><br>
              <small style="color:var(--gris);"><?= e($h['telefono']) ?></small>
            </td>
            <td><?= e($h['placa'] ?: '—') ?></td>
            <td style="font-size:12px;"><?= e($h['moto'] ?: '—') ?></td>
            <td style="font-weight:600;"><?= $h['gratis'] ? '$0' : e(cop($h['precio'])) ?></td>
            <td>
              <span class="badge <?= $h['gratis'] ? 'badge-gratis' : 'badge-pagada' ?>">
                <?= $h['gratis'] ? 'GRATIS' : 'Pagada' ?>
              </span>
            </td>
            <td>
              <div style="display:flex; gap:6px;">
                <a class="btn btn-outline" style="padding:4px 10px;font-size:12px;"
                   href="<?= e(url('/factura/' . $h['id'])) ?>" target="_blank"><i class="fa-solid fa-print"></i> Imprimir</a>
                <form method="post" action="<?= e(url('/historial/eliminar')) ?>"
                      onsubmit="return confirm('¿Eliminar este registro de lavada? El contador del cliente se ajustará.');" style="display:inline;">
                  <input type="hidden" name="id" value="<?= (int) $h['id'] ?>" />
                  <button type="submit" class="btn btn-danger" style="padding:4px 10px;font-size:12px;" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php if (empty($historial)): ?>
    <div style="text-align:center;padding:2rem;color:var(--texto2);">No hay registros</div>
  <?php endif; ?>
</div>
