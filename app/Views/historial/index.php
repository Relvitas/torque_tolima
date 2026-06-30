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
              <div style="display:flex; gap:6px; align-items:center; flex-wrap:wrap;">
                <?php if ($h['gratis']): ?>
                  <span class="badge badge-gratis">GRATIS</span>
                <?php else: ?>
                  <form method="post" action="<?= e(url('/lavada/pago')) ?>" style="display:inline;"
                        title="Clic para cambiar el estado de pago">
                    <input type="hidden" name="id" value="<?= (int) $h['id'] ?>" />
                    <input type="hidden" name="volver" value="historial" />
                    <?php if ($h['pagado'] ?? 1): ?>
                      <button type="submit" class="btn btn-success" style="padding:4px 10px;font-size:12px;">
                        <i class="fa-solid fa-circle-check"></i> Pagada
                      </button>
                    <?php else: ?>
                      <button type="submit" class="btn btn-danger" style="padding:4px 10px;font-size:12px;">
                        <i class="fa-solid fa-circle-exclamation"></i> Debe
                      </button>
                    <?php endif; ?>
                  </form>
                  <?php if ($h['pagado'] ?? 1):
                    $metodo = ($h['metodo_pago'] ?? 'efectivo') === 'nequi' ? 'nequi' : 'efectivo'; ?>
                    <form method="post" action="<?= e(url('/lavada/metodo')) ?>" style="display:inline;"
                          title="Clic para cambiar el método de pago">
                      <input type="hidden" name="id" value="<?= (int) $h['id'] ?>" />
                      <input type="hidden" name="volver" value="historial" />
                      <?php if ($metodo === 'nequi'): ?>
                        <button type="submit" class="btn btn-outline" style="padding:4px 10px;font-size:12px;color:#6d28d9;border-color:#6d28d9;">
                          <i class="fa-solid fa-mobile-screen-button"></i> Nequi
                        </button>
                      <?php else: ?>
                        <button type="submit" class="btn btn-outline" style="padding:4px 10px;font-size:12px;color:#15803d;border-color:#15803d;">
                          <i class="fa-solid fa-money-bill-wave"></i> Efectivo
                        </button>
                      <?php endif; ?>
                    </form>
                  <?php endif; ?>
                <?php endif; ?>
              </div>
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
