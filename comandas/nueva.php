<?php
// Modal para crear nueva comanda
?>
<div class="modal fade" id="modalNuevaComanda" tabindex="-1" role="dialog" aria-labelledby="modalNuevaComandaLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="post" action="procesar.php">
        <div class="modal-header">
          <h5 class="modal-title" id="modalNuevaComandaLabel">Nueva Comanda</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="descripcion">Descripción</label>
            <input type="text" class="form-control" name="descripcion" id="descripcion" placeholder="Ej: Botella de licor" required>
          </div>
          <div class="form-group">
            <label for="total">Total Venta</label>
            <input type="number" class="form-control" name="total" id="total" placeholder="Valor total" min="0" step="0.01" required>
          </div>
          <button type="button" class="btn btn-primary my-2" id="btnAbrirAgregarProductoComanda" data-toggle="modal" data-target="#modalAgregarProductoComanda">
            <i class="fas fa-plus"></i> Agregar Producto
          </button>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Crear Comanda</button>
        </div>
      </form>
      <script>
      // Igual que en mesas: abrir modal agregar producto sin cerrar el modal principal
      $(function() {
        $('#btnAbrirAgregarProductoComanda').on('click', function(e) {
          // Solo abrir el modal de agregar producto
          $('#modalAgregarProductoComanda').modal('show');
        });
      });
      </script>
    </div>
  </div>
</div>
