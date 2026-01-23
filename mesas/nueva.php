<!-- Modal Nueva Mesa -->
<div class="modal fade" id="modalNuevaMesa" tabindex="-1" role="dialog" aria-labelledby="modalNuevaMesaLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalNuevaMesaLabel"><?php echo $mesa_nueva; ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" action="procesar.php">
        <div class="modal-body">
          <div class="form-group">
            <label for="nombreMesa"><?php echo $mesa_nombre_campo; ?></label>
            <input type="text" class="form-control" id="nombreMesa" name="nombre" placeholder="<?php echo $producto_ej_mesa1; ?>" required>
          </div>
          <div class="form-group">
            <label for="ubicacion"><?php echo $mesa_ubicacion_campo; ?></label>
            <input type="text" class="form-control" id="ubicacion" name="ubicacion" placeholder="<?php echo $producto_ej_salon; ?>" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $mesa_cerrar; ?></button>
          <button type="submit" class="btn btn-success"><?php echo $mesa_guardar; ?></button>
        </div>
      </form>
    </div>
  </div>
</div>
