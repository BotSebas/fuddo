<!-- Modal Productos - Comanda (similar a mesas) -->
<div class="modal fade" id="modalProductosComanda" tabindex="-1" role="dialog" aria-labelledby="modalProductosComandaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalProductosComandaLabel">Productos - Comanda <span id="descripcionComanda"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="mb-3 d-flex justify-content-between">
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarProductoComanda" onclick="setComandaId(document.getElementById('comandaIdHidden').value)">
            <i class="fas fa-plus"></i> Agregar Producto
          </button>
          <button type="button" class="btn btn-success" onclick="abrirModalCerrarComanda()">
            <i class="fas fa-dollar-sign"></i> Cerrar Comanda
          </button>
        </div>

        <table class="table table-striped table-hover">
          <thead class="bg-light">
            <tr>
              <th>Producto</th>
              <th>Valor</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody id="tablaProductosComanda">
            <!-- Los productos se cargarán aquí dinámicamente -->
          </tbody>
        </table>

        <div class="alert alert-info mt-3">
          <h5>Total: <strong id="totalProductosComanda">$0.00</strong></h5>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" onclick="cancelarComanda()">
          <i class="fas fa-times-circle"></i> Cancelar Comanda
        </button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<input type="hidden" id="comandaIdHidden">

<!-- Modal Agregar Producto a Comanda -->
<div class="modal fade" id="modalAgregarProductoComanda" tabindex="-1" role="dialog" aria-labelledby="modalAgregarProductoComandaLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalAgregarProductoComandaLabel">Agregar Producto</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="formAgregarProductoComanda" method="POST" onsubmit="agregarProductoComanda(event)">
        <div class="modal-body">
          <input type="hidden" id="comandaId" name="comanda_id">
          <div class="form-group">
            <label for="productoComanda">Producto</label>
            <select class="form-control" id="productoComanda" name="producto_id" required>
              <option value="">Seleccionar producto</option>
              <!-- Opciones de productos se cargarán dinámicamente -->
            </select>
          </div>
          <div class="form-group">
            <label for="cantidadComanda">Cantidad</label>
            <input type="number" class="form-control" id="cantidadComanda" name="cantidad" value="1" min="1" required>
          </div>
          <div class="form-group">
            <label for="precioUnitarioComanda">Precio Unitario</label>
            <input type="text" class="form-control" id="precioUnitarioComanda" readonly>
          </div>
          <div class="form-group">
            <label for="precioTotalComanda">Precio Total</label>
            <input type="text" class="form-control" id="precioTotalComanda" readonly>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Agregar Producto</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Aquí irá la lógica JS para cargar productos, calcular totales y gestionar la comanda
// Similar a mesas, pero usando IDs y endpoints de comandas
</script>
