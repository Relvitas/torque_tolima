<?php
/** @var array $clientes @var string $q @var int $meta */
?>
<div class="page-title">Clientes</div>
<form method="get" action="<?= e(url('/clientes')) ?>" class="search-row">
  <input type="text" name="q" value="<?= e($q) ?>" placeholder="Buscar por nombre, teléfono o placa..." />
  <button type="submit" class="btn btn-primary">Buscar</button>
</form>

<div id="lista-clientes">
  <?php if (empty($clientes)): ?>
    <div style="text-align:center;padding:2rem;color:var(--texto2);">
      <?= $q !== '' ? 'No se encontraron clientes' : 'Aún no hay clientes' ?>
    </div>
  <?php else: foreach ($clientes as $c):
    $ciclo = $c['lavadas'] % $meta;
    $esGratis = ($ciclo === 0 && $c['lavadas'] > 0);
  ?>
    <div class="cliente-card">
      <div class="avatar">
        <?php if (!empty($c['foto'])): ?>
          <img src="<?= e(url('uploads/' . $c['foto'])) ?>" alt="moto" />
        <?php else: ?>
          <?= e(inicial($c['nombre'])) ?>
        <?php endif; ?>
      </div>
      <div style="flex:1">
        <div style="font-weight:600;">
          <?= e($c['nombre']) ?>
          <?php if ($esGratis): ?><span class="badge badge-gratis"><i class="fa-solid fa-gift"></i> GRATIS!</span><?php endif; ?>
        </div>
        <div style="font-size:12px;color:var(--gris);">
          <?= e($c['telefono']) ?> · <?= e($c['placa'] ?: 'Sin placa') ?>
        </div>
        <div class="puntos-bar" style="margin-top:6px;">
          <?php for ($i = 1; $i <= $meta - 1; $i++):
            $lleno = $i <= $ciclo; ?>
            <div class="punto<?= $lleno ? ' lleno' : '' ?>"><?= $lleno ? '<i class="fa-solid fa-check"></i>' : $i ?></div>
          <?php endfor; ?>
          <div class="punto estrella"><i class="fa-solid fa-star"></i></div>
        </div>
        <div style="font-size:11px;color:var(--gris);margin-top:4px;">
          Total: <?= (int) $c['lavadas'] ?> lavadas · Gratis ganadas: <?= (int) $c['total_gratis'] ?>
        </div>
      </div>
      <div style="display:flex; flex-direction:column; gap:6px; align-self:flex-start;">
        <a class="btn btn-success" style="padding:6px 12px;font-size:12px;white-space:nowrap;"
           href="<?= e(url('/?tel=' . urlencode($c['telefono']))) ?>" title="Registrar lavada para este cliente">
          <i class="fa-solid fa-plus"></i> Lavada
        </a>
        <form method="post" action="<?= e(url('/clientes/eliminar')) ?>"
              onsubmit="return confirm('¿Eliminar este cliente? Se borrará también todo su historial de lavadas. Esta acción no se puede deshacer.');">
          <input type="hidden" name="id" value="<?= (int) $c['id'] ?>" />
          <button type="submit" class="btn btn-danger" style="padding:6px 12px;font-size:12px;width:100%;" title="Eliminar cliente"><i class="fa-solid fa-trash"></i></button>
        </form>
      </div>
    </div>
  <?php endforeach; endif; ?>
</div>
