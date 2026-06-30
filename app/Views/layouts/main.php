<?php
/** @var string $content  Contenido de la vista */
/** @var string $seccion  Sección activa (nueva|citas|clientes|historial|resumen) */
$seccion = $seccion ?? 'nueva';
$nav = [
    'nueva'     => ['url' => '/',          'icon' => 'fa-solid fa-circle-plus',    'label' => 'Nueva lavada', 'mlabel' => 'Lavada'],
    'citas'     => ['url' => '/citas',     'icon' => 'fa-solid fa-calendar-days',  'label' => 'Citas',        'mlabel' => 'Citas'],
    'clientes'  => ['url' => '/clientes',  'icon' => 'fa-solid fa-users',          'label' => 'Clientes',     'mlabel' => 'Clientes'],
    'historial' => ['url' => '/historial', 'icon' => 'fa-solid fa-clipboard-list', 'label' => 'Historial',    'mlabel' => 'Historial'],
    'egresos'   => ['url' => '/egresos',   'icon' => 'fa-solid fa-money-bill-trend-up', 'label' => 'Egresos', 'mlabel' => 'Egresos'],
    'resumen'   => ['url' => '/resumen',   'icon' => 'fa-solid fa-chart-column',   'label' => 'Resumen',      'mlabel' => 'Resumen'],
];
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="default" />
<meta name="apple-mobile-web-app-title" content="Torque Tolima" />
<title>Torque Tolima — Autolavado de Motos</title>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="<?= e(url('css/app.css')) ?>" />
</head>
<body>

<div class="app-shell" id="appShell">
  <script>try{if(localStorage.getItem('tq_sidebar')==='1'){document.getElementById('appShell').classList.add('collapsed');}}catch(e){}</script>
  <aside class="sidebar">
    <div class="sidebar-head">
      <div class="sidebar-logo">
        <h1><i class="fa-solid fa-motorcycle"></i> <span class="logo-text">Torque Tolima</span></h1>
        <p class="logo-text">Autolavado de motos</p>
      </div>
      <button class="sidebar-toggle" type="button" onclick="toggleSidebar()" title="Contraer / expandir menú" aria-label="Contraer o expandir menú">
        <i class="fa-solid fa-angles-left"></i>
      </button>
    </div>
    <nav class="sidebar-nav">
      <?php foreach ($nav as $key => $item): ?>
        <a class="nav-item <?= is_active($key, $seccion) ?>" href="<?= e(url($item['url'])) ?>" title="<?= e($item['label']) ?>">
          <span class="nav-icon"><i class="<?= e($item['icon']) ?>"></i></span><span class="nav-label"><?= e($item['label']) ?></span>
        </a>
      <?php endforeach; ?>
    </nav>
  </aside>

  <div class="mobile-header">
    <h1><i class="fa-solid fa-motorcycle"></i> Torque Tolima</h1>
  </div>

  <main class="main-content">
    <?= $content ?>
  </main>
</div>

<nav class="mobile-nav">
  <div class="mobile-nav-inner">
    <?php foreach ($nav as $key => $item): ?>
      <a class="mob-nav-btn <?= is_active($key, $seccion) ?>" href="<?= e(url($item['url'])) ?>">
        <span class="mob-nav-icon"><i class="<?= e($item['icon']) ?>"></i></span><?= e($item['mlabel']) ?>
      </a>
    <?php endforeach; ?>
  </div>
</nav>

<div class="toast" id="toast"></div>

<script>
  // Toast de mensajes flash provenientes del servidor.
  (function () {
    const msg = <?= json_encode($flash, JSON_UNESCAPED_UNICODE) ?>;
    if (msg) {
      const t = document.getElementById('toast');
      t.textContent = msg;
      t.classList.add('show');
      setTimeout(() => t.classList.remove('show'), 3500);
    }
  })();
</script>
<script src="<?= e(url('js/app.js')) ?>"></script>
</body>
</html>
