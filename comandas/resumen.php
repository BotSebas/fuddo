<?php
// Modal para mostrar resumen de comanda
?>
<div class="modal fade" id="modalResumenComanda" tabindex="-1" role="dialog" aria-labelledby="modalResumenComandaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalResumenComandaLabel">Detalle de Comanda</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <h6 class="mb-3">Productos Consumidos:</h6>
        <table class="table table-bordered">
          <thead class="thead-light">
            <tr>
              <th>Producto</th>
              <th>Cantidad</th>
              <th>Valor Unitario</th>
              <th>Valor Total</th>
            </tr>
          </thead>
          <tbody id="tablaResumenProductos">
            <tr>
              <td colspan="4" class="text-center text-muted">Cargando...</td>
            </tr>
          </tbody>
          <tfoot>
            <tr class="font-weight-bold">
              <td colspan="2" class="text-left">MÉTODO DE PAGO: <span id="resumenMetodoPago" style="color: #2980b9;"></span></td>
              <td class="text-right">TOTAL CUENTA:</td>
              <td><span id="totalResumenComanda" style="color: #27ae60; font-size: 1.2em;">$0.00</span></td>
            </tr>
          </tfoot>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
// Función para formatear números con separador de miles
function formatCurrency(amount) {
  return amount.toLocaleString('es-CO', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
}

function verResumenComanda(idComanda) {
  // Abrir el modal primero
  $('#modalResumenComanda').modal('show');
  
  // Cargar productos de la comanda
  fetch('obtener_detalle.php?id_comanda=' + encodeURIComponent(idComanda))
    .then(response => response.json())
    .then(data => {
      console.log('Datos recibidos:', data);
      const tablaBody = document.getElementById('tablaResumenProductos');
      tablaBody.innerHTML = '';
      let total = 0;
      
      if (data.success && data.productos && data.productos.length > 0) {
        data.productos.forEach(producto => {
          const fila = document.createElement('tr');
          fila.innerHTML = `
            <td>${producto.nombre}</td>
            <td>${producto.cantidad}</td>
            <td>$${formatCurrency(parseFloat(producto.valor_unitario))}</td>
            <td>$${formatCurrency(parseFloat(producto.valor_total))}</td>
          `;
          tablaBody.appendChild(fila);
          total += parseFloat(producto.valor_total);
        });
        
        document.getElementById('totalResumenComanda').textContent = '$' + formatCurrency(total);
      } else {
        const fila = document.createElement('tr');
        fila.innerHTML = '<td colspan="4" class="text-center text-muted">No hay productos en esta comanda</td>';
        tablaBody.appendChild(fila);
        document.getElementById('totalResumenComanda').textContent = '$0.00';
      }
      
      // Actualizar método de pago siempre (fuera del condicional)
      const metodoPagoElement = document.getElementById('resumenMetodoPago');
      if (metodoPagoElement) {
        metodoPagoElement.textContent = data.metodo_pago || 'N/A';
      }
    })
    .catch(error => {
      console.error('Error:', error);
      const tablaBody = document.getElementById('tablaResumenProductos');
      tablaBody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Error al cargar los productos</td></tr>';
    });
}
</script>
