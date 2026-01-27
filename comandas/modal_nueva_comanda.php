<?php
// Modal para crear y gestionar una nueva comanda con productos (similar a mesas)
?>
<div class="modal fade" id="modalNuevaComanda" tabindex="-1" role="dialog" aria-labelledby="modalNuevaComandaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalNuevaComandaLabel">Nueva Comanda</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="comanda-productos">
          <!-- Aquí se cargará la tabla de productos agregados a la comanda -->
        </div>
        <button type="button" class="btn btn-primary my-2" id="btnAgregarProductoComanda">
          <i class="fas fa-plus"></i> Agregar Producto
        </button>
        <div class="text-right mt-3">
          <button type="button" class="btn btn-success" id="btnCerrarComanda">
            <i class="fas fa-cash-register"></i> Cerrar Comanda
          </button>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" id="btnCancelarComanda">Cancelar Comanda</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
// Al hacer clic en el botón, abrir el modal de productos de comanda
document.addEventListener('DOMContentLoaded', function() {
  var btn = document.getElementById('btnAgregarProductoComanda');
  if (btn) {
    btn.addEventListener('click', function() {
      $('#modalProductosComanda').modal('show');
    });
  }
});
</script>
