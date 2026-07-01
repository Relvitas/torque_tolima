<?php
/** @var string $telPrefill @var array $lavadasHoy @var int $totalHoy */
?>
<div class="page-title">Registrar lavada</div>
<form method="post" action="<?= e(url('/lavada/registrar')) ?>" enctype="multipart/form-data">
  <div class="two-col">
    <div>
      <div class="card">
        <div class="card-title">Datos del cliente</div>
        <label>Teléfono</label>
        <input type="tel" id="telefono" name="telefono" placeholder="Ej: 3101234567" autocomplete="off" value="<?= e($telPrefill ?? '') ?>" />
        <div class="cliente-ok" id="cliente-ok"></div>
        <label>Nombre</label>
        <input type="text" id="nombre" name="nombre" placeholder="Nombre completo" />
        <label>Placa de la moto</label>
        <input type="text" id="placa" name="placa" placeholder="Ej: ABC123" oninput="this.value=this.value.toUpperCase()" />
        <label>Tipo de moto</label>
        <input type="text" id="tipoMoto" name="tipoMoto" placeholder="Ej: Honda CB125" />
        <label>Foto de la moto</label>
        <div class="foto-area" onclick="document.getElementById('fotoInput').click()">
          <div id="fotoPreview"><i class="fa-solid fa-camera" style="font-size:28px;color:var(--texto3);"></i><p>Toca para tomar foto</p></div>
        </div>
        <input type="file" id="fotoInput" name="foto" accept="image/*" capture="environment" style="display:none" onchange="cargarFoto(this)" />
      </div>
    </div>
    <div>
      <div class="card">
        <div class="card-title">Tipo de lavada</div>
        <input type="hidden" id="precio" name="precio" value="0" />
        <div class="precio-grid">
          <button type="button" class="precio-btn" onclick="seleccionarPrecio(12000,this)">$12.000<br><small>Básica</small></button>
          <button type="button" class="precio-btn" onclick="seleccionarPrecio(15000,this)">$15.000<br><small>Estándar</small></button>
          <button type="button" class="precio-btn" onclick="seleccionarPrecio(20000,this)">$20.000<br><small>Completa</small></button>
          <button type="button" class="precio-btn" onclick="seleccionarPersonalizado(this)">Otro valor<br><small>Personalizado</small></button>
        </div>
        <div id="inputPersonalizado" style="display:none; margin-top:10px;">
          <input type="number" id="valorPersonalizado" placeholder="Ingresa el valor en pesos" />
        </div>
        <div id="resumen-lavada" style="margin-top:1rem; padding:12px; background:var(--gris-claro); border-radius:var(--radio-sm); display:none;">
          <div style="font-size:13px; color:var(--texto2);">Precio seleccionado</div>
          <div id="precio-display" style="font-size:22px; font-weight:700; color:var(--verde);"></div>
        </div>
      </div>
      <div class="card" id="card-puntos" style="display:none;">
        <div class="card-title">Progreso de fidelidad</div>
        <div class="puntos-bar" id="puntos-preview"></div>
        <p id="puntos-texto" style="font-size:13px; color:var(--texto2); margin-top:8px;"></p>
      </div>
      <button type="submit" class="btn btn-success btn-block"><i class="fa-solid fa-circle-check"></i> Registrar lavada</button>
    </div>
  </div>
</form>

<div class="card">
  <div class="card-title" style="display:flex; justify-content:space-between; align-items:center;">
    <span><i class="fa-solid fa-clock-rotate-left"></i> Lavadas de hoy (<?= count($lavadasHoy) ?>)</span>
    <span style="font-size:14px; color:var(--verde);">Total: <?= e(cop($totalHoy)) ?></span>
  </div>
  <div class="tabla-wrap">
    <table>
      <thead>
        <tr><th>Hora</th><th>Cliente</th><th>Placa</th><th>Moto</th><th>Valor</th><th>Acciones</th></tr>
      </thead>
      <tbody>
        <?php foreach ($lavadasHoy as $h): ?>
          <tr>
            <td style="font-size:12px;color:var(--gris);"><?= e(date('H:i', strtotime($h['creado_en']))) ?></td>
            <td>
              <strong><?= e($h['nombre']) ?></strong><br>
              <small style="color:var(--gris);"><?= e($h['telefono']) ?></small>
            </td>
            <td><?= e($h['placa'] ?: '—') ?></td>
            <td style="font-size:12px;"><?= e($h['moto'] ?: '—') ?></td>
            <td style="font-weight:600; white-space:nowrap;">
              <?php if ($h['gratis']): ?>
                $0
              <?php else: ?>
                <?= e(cop($h['precio'])) ?>
                <button type="button" class="btn btn-outline" style="padding:2px 8px;font-size:11px;margin-left:4px;"
                        title="Editar valor" onclick="editarValorLavada(<?= (int) $h['id'] ?>, <?= (int) $h['precio'] ?>)">
                  <i class="fa-solid fa-pen"></i>
                </button>
              <?php endif; ?>
            </td>
            <td>
              <div style="display:flex; gap:6px; align-items:center; flex-wrap:wrap;">
                <?php if ($h['gratis']): ?>
                  <span class="badge badge-gratis">GRATIS</span>
                <?php else: ?>
                  <form method="post" action="<?= e(url('/lavada/pago')) ?>" style="display:inline;"
                        title="Clic para cambiar el estado de pago">
                    <input type="hidden" name="id" value="<?= (int) $h['id'] ?>" />
                    <?php if ($h['pagado'] ?? 1): ?>
                      <button type="submit" class="pill pill--pagada">
                        <i class="fa-solid fa-circle-check"></i> Pagada
                      </button>
                    <?php else: ?>
                      <button type="submit" class="pill pill--debe">
                        <i class="fa-solid fa-circle-exclamation"></i> Debe
                      </button>
                    <?php endif; ?>
                  </form>
                  <?php if ($h['pagado'] ?? 1):
                    $metodo = ($h['metodo_pago'] ?? 'efectivo') === 'nequi' ? 'nequi' : 'efectivo'; ?>
                    <form method="post" action="<?= e(url('/lavada/metodo')) ?>" style="display:inline;"
                          title="Clic para cambiar el método de pago">
                      <input type="hidden" name="id" value="<?= (int) $h['id'] ?>" />
                      <?php if ($metodo === 'nequi'): ?>
                        <button type="submit" class="pill pill--nequi">
                          <i class="fa-solid fa-mobile-screen-button"></i> Nequi
                        </button>
                      <?php else: ?>
                        <button type="submit" class="pill pill--efectivo">
                          <i class="fa-solid fa-money-bill-wave"></i> Efectivo
                        </button>
                      <?php endif; ?>
                    </form>
                  <?php endif; ?>
                <?php endif; ?>
                <button type="button" class="btn btn-wa" style="padding:4px 10px;font-size:12px;"
                        onclick="avisarLavadaLista(<?= e(json_encode($h['telefono'])) ?>, <?= e(json_encode($h['nombre'])) ?>)"
                        title="Avisar por WhatsApp que la moto está lista">
                  <i class="fa-brands fa-whatsapp"></i> Lista
                </button>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php if (empty($lavadasHoy)): ?>
    <div style="text-align:center;padding:2rem;color:var(--texto2);">Aún no hay lavadas registradas hoy</div>
  <?php endif; ?>
</div>

<!-- Formulario oculto para editar el valor de una lavada -->
<form method="post" action="<?= e(url('/lavada/precio')) ?>" id="form-editar-precio" style="display:none;">
  <input type="hidden" name="id" id="ep-id" />
  <input type="hidden" name="precio" id="ep-precio" />
</form>
