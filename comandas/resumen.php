<?php
// Modal para mostrar resumen de comanda
?>
<div class="modal fade" id="modalResumenComanda" tabindex="-1" role="dialog" aria-labelledby="modalResumenComandaLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalResumenComandaLabel">Resumen de Comanda</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="resumenComandaBody">
        <!-- Aquí se puede mostrar el resumen de la comanda seleccionada -->
        <p>Implementar detalle de productos vendidos, si aplica.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
