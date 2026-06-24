<?php
/** @var int $totalLavadas @var int $totalClientes @var int $ingresos @var int $totalGratis
 *  @var array $topClientes @var array $porTipo */
?>
<div class="page-title">Resumen del negocio</div>

<div class="stats-grid">
  <div class="stat-card"><div class="stat-num"><?= (int) $totalLavadas ?></div><div class="stat-lbl">Total lavadas</div></div>
  <div class="stat-card"><div class="stat-num"><?= (int) $totalClientes ?></div><div class="stat-lbl">Clientes</div></div>
  <div class="stat-card"><div class="stat-num">$<?= number_format($ingresos / 1000, 0, ',', '.') ?>k</div><div class="stat-lbl">Ingresos COP</div></div>
  <div class="stat-card"><div class="stat-num"><?= (int) $totalGratis ?></div><div class="stat-lbl">Lavadas gratis dadas</div></div>
</div>

<div class="two-col">
  <div class="card">
    <div class="card-title"><i class="fa-solid fa-trophy"></i> Top clientes</div>
    <?php if (empty($topClientes)): ?>
      <p style="color:var(--gris);font-size:13px;">Sin datos</p>
    <?php else: foreach ($topClientes as $c): ?>
      <div class="cita-item">
        <div>
          <strong style="font-size:13px;"><?= e($c['nombre']) ?></strong><br>
          <small style="color:var(--gris);"><?= e($c['telefono']) ?> · <?= e($c['placa'] ?: 'sin placa') ?></small>
        </div>
        <span style="font-weight:600;color:var(--azul);"><?= (int) $c['lavadas'] ?> <i class="fa-solid fa-motorcycle"></i></span>
      </div>
    <?php endforeach; endif; ?>
  </div>

  <div class="card">
    <div class="card-title"><i class="fa-solid fa-money-bill-wave"></i> Ingresos por tipo de lavada</div>
    <?php if (empty($porTipo)): ?>
      <p style="color:var(--gris);font-size:13px;">Sin datos</p>
    <?php else: foreach ($porTipo as $t): ?>
      <div class="cita-item">
        <span><?= e(cop($t['precio'])) ?></span>
        <span style="font-weight:600;"><?= (int) $t['cantidad'] ?> lavada(s)</span>
      </div>
    <?php endforeach; endif; ?>
  </div>
</div>
