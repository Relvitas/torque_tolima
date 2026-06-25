<?php
/** @var array $l  Lavada  @var string $waNum  @var int $meta */
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<title>Factura — Torque Tolima</title>
<style>
  body { margin: 0; background: #fff; }
  #factura {
    width: 72mm; font-family: 'Courier New', monospace;
    font-size: 12.5px; font-weight: 700; line-height: 1.45;
    color: #000; padding: 4mm;
    -webkit-print-color-adjust: exact; print-color-adjust: exact;
  }
  /* En térmicas, el texto normal sale muy claro: forzamos negrita en todo. */
  #factura, #factura * { font-weight: 700 !important; color: #000 !important; }
  #factura .lg  { font-size: 16px; }
  #factura .total { font-size: 14px; }
  @media print {
    @page { size: 80mm auto; margin: 0; }
    body { width: 80mm; }
  }
</style>
</head>
<body onload="window.print()">
<div id="factura">
  <div style="text-align:center; margin-bottom:4mm;">
    <div class="lg">TORQUE TOLIMA</div>
    <div>Autolavado de Motos</div>
    <div>Tel: <?= e($waNum) ?></div>
    <div style="border-top:1px dashed #000; margin:3mm 0;"></div>
  </div>
  <div><b>FACTURA DE SERVICIO</b></div>
  <div style="border-bottom:1px dashed #000; margin:2mm 0;"></div>
  <table style="width:100%; font-size:12.5px; border-collapse:collapse;">
    <tr><td style="padding:1mm 0;">Fecha:</td><td style="text-align:right;"><?= e($l['creado_en']) ?></td></tr>
    <tr><td style="padding:1mm 0;">Cliente:</td><td style="text-align:right;"><?= e($l['nombre']) ?></td></tr>
    <tr><td style="padding:1mm 0;">Teléfono:</td><td style="text-align:right;"><?= e($l['telefono']) ?></td></tr>
    <tr><td style="padding:1mm 0;">Placa:</td><td style="text-align:right;"><?= e($l['placa'] ?: '—') ?></td></tr>
    <tr><td style="padding:1mm 0;">Moto:</td><td style="text-align:right;"><?= e($l['moto'] ?: '—') ?></td></tr>
    <tr><td style="padding:1mm 0;">Lavada #:</td><td style="text-align:right;"><?= (int) $l['num_lavada'] ?></td></tr>
  </table>
  <div style="border-top:1px dashed #000; margin:2mm 0;"></div>
  <table style="width:100%; font-size:12px; border-collapse:collapse;">
    <tr><td><b>Servicio de lavado</b></td><td style="text-align:right;"><b><?= $l['gratis'] ? 'GRATIS 🎁' : e(cop($l['precio'])) ?></b></td></tr>
    <tr class="total"><td>TOTAL:</td><td style="text-align:right;"><?= $l['gratis'] ? '$0' : e(cop($l['precio'])) ?></td></tr>
  </table>
  <div style="border-top:1px dashed #000; margin:3mm 0;"></div>
  <div style="text-align:center; font-size:11.5px;">
    <?php if ($l['gratis']): ?>🎉 LAVADA GRATIS - Premio por fidelidad!<?php endif; ?>
    <div style="margin-top:2mm;">Programa de fidelidad:</div>
    <div>Cada <?= (int) $meta - 1 ?> lavadas, la <?= (int) $meta ?>.ª es GRATIS</div>
    <div style="margin-top:3mm; font-weight:bold;">¡Gracias por su preferencia!</div>
    <div>Vuelva pronto 🏍️</div>
  </div>
</div>
</body>
</html>
