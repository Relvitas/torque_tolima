<?php
/** @var int $lavadasHoy @var int $totalClientes @var int $ingresos @var int $totalGratis
 *  @var array $topClientes @var array $porTipo */
?>
<div class="page-title">Resumen del negocio</div>

<div class="stats-grid">
  <div class="stat-card"><div class="stat-num"><?= (int) $lavadasHoy ?></div><div class="stat-lbl">Lavadas de hoy</div></div>
  <div class="stat-card"><div class="stat-num"><?= (int) $totalClientes ?></div><div class="stat-lbl">Clientes</div></div>
  <div class="stat-card"><div class="stat-num" style="font-size:22px;"><?= e(cop($ingresos)) ?></div><div class="stat-lbl">Ingresos de hoy</div></div>
  <div class="stat-card"><div class="stat-num"><?= (int) $totalGratis ?></div><div class="stat-lbl">Lavadas gratis dadas</div></div>
</div>

<div class="card">
  <div class="card-title"><i class="fa-solid fa-calendar-day"></i> Total generado por mes</div>
  <div class="tabla-wrap">
    <table>
      <thead>
        <tr>
          <th>Mes</th><th>Lavadas</th><th>Gratis</th>
          <th style="text-align:right;">Total generado</th>
          <th style="text-align:right;">Egresos</th>
          <th style="text-align:right;">Utilidad neta</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($porMes as $m):
          $actual = ($m['mes'] === $mesActual); ?>
          <tr<?= $actual ? ' style="background:var(--brand-claro);"' : '' ?>>
            <td style="font-weight:600;">
              <?= e(mes_label($m['mes'])) ?>
              <?php if ($actual): ?><span class="badge badge-pagada">Mes actual</span><?php endif; ?>
            </td>
            <td><?= (int) $m['cantidad'] ?></td>
            <td><?= (int) $m['gratis'] ?></td>
            <td style="text-align:right;font-weight:700;"><?= e(cop($m['total'])) ?></td>
            <td style="text-align:right;color:var(--rojo, #dc2626);"><?= e(cop($m['egresos'])) ?></td>
            <td style="text-align:right;font-weight:700;color:<?= $m['neto'] < 0 ? 'var(--rojo, #dc2626)' : 'var(--verde, #16a34a)' ?>;"><?= e(cop($m['neto'])) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php if (empty($porMes)): ?>
    <div style="text-align:center;padding:2rem;color:var(--texto2);">Aún no hay lavadas registradas</div>
  <?php endif; ?>
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
