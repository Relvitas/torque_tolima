<div class="page-title">Registrar lavada</div>
<form method="post" action="<?= e(url('/lavada/registrar')) ?>" enctype="multipart/form-data">
  <div class="two-col">
    <div>
      <div class="card">
        <div class="card-title">Datos del cliente</div>
        <label>Teléfono</label>
        <input type="tel" id="telefono" name="telefono" placeholder="Ej: 3101234567" autocomplete="off" />
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
