<?php
/** @var array $cliente */
?>
<div class="page-title">Editar cliente</div>

<form method="post" action="<?= e(url('/clientes/actualizar')) ?>" enctype="multipart/form-data">
  <input type="hidden" name="id" value="<?= (int) $cliente['id'] ?>" />
  <div class="card" style="max-width:560px;">
    <div class="card-title"><i class="fa-solid fa-user-pen"></i> Datos del cliente</div>

    <label>Nombre</label>
    <input type="text" name="nombre" value="<?= e($cliente['nombre']) ?>" placeholder="Nombre completo" required />

    <label>Teléfono</label>
    <input type="tel" name="telefono" value="<?= e($cliente['telefono']) ?>" placeholder="Ej: 3101234567" required />

    <label>Placa de la moto</label>
    <input type="text" name="placa" value="<?= e($cliente['placa']) ?>" placeholder="Ej: ABC123" oninput="this.value=this.value.toUpperCase()" />

    <label>Tipo de moto</label>
    <input type="text" name="moto" value="<?= e($cliente['moto']) ?>" placeholder="Ej: Honda CB125" />

    <label>Foto de la moto</label>
    <div class="foto-area" onclick="document.getElementById('fotoInput').click()">
      <div id="fotoPreview">
        <?php if (!empty($cliente['foto'])): ?>
          <img src="<?= e(url('uploads/' . $cliente['foto'])) ?>" alt="moto" style="max-width:100%;max-height:160px;border-radius:8px;" />
          <p>Toca para cambiar la foto</p>
        <?php else: ?>
          <i class="fa-solid fa-camera" style="font-size:28px;color:var(--texto3);"></i><p>Toca para tomar foto</p>
        <?php endif; ?>
      </div>
    </div>
    <input type="file" id="fotoInput" name="foto" accept="image/*" capture="environment" style="display:none" onchange="cargarFoto(this)" />

    <div style="display:flex; gap:8px; margin-top:1.2rem;">
      <button type="submit" class="btn btn-success" style="flex:1;"><i class="fa-solid fa-floppy-disk"></i> Guardar cambios</button>
      <a class="btn btn-outline" href="<?= e(url('/clientes')) ?>"><i class="fa-solid fa-xmark"></i> Cancelar</a>
    </div>
  </div>
</form>
