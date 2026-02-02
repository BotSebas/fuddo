<!-- Modal Nueva Comanda - Productos -->
<div class="modal fade" id="modalNuevaComanda" tabindex="-1" role="dialog" aria-labelledby="modalNuevaComandaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalNuevaComandaLabel">Productos - Comanda</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="mb-3 d-flex justify-content-between">
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarProductoComanda" onclick="inicializarComandaSiNoExiste()">
            <i class="fas fa-plus"></i> Agregar Producto
          </button>
          <button type="button" class="btn btn-success" onclick="abrirModalCerrarCuentaComanda()">
            <i class="fas fa-dollar-sign"></i> Cerrar Cuenta
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
          <tbody id="tablaProductosNuevaComanda">
            <tr>
              <td colspan="3" class="text-center text-muted">Sin productos agregados</td>
            </tr>
          </tbody>
        </table>

        <div class="alert alert-info mt-3">
          <h5>Total: <strong id="totalNuevaComanda">$0.00</strong></h5>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" onclick="cancelarNuevaComanda()">
          <i class="fas fa-times-circle"></i> Cancelar Servicio
        </button>
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="cerrarModalNuevaComanda()">Cerrar</button> -->
      </div>
    </div>
  </div>
</div>

<!-- Modal Agregar Producto a Nueva Comanda -->
<div class="modal fade" id="modalAgregarProductoComanda" tabindex="-1" role="dialog" aria-labelledby="modalAgregarProductoComandaLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalAgregarProductoComandaLabel">Agregar Producto</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="formAgregarProductoComanda" method="POST" onsubmit="agregarProductoNuevaComanda(event)">
        <div class="modal-body">
          <input type="hidden" id="comandaIdNuevo" name="comanda_id">
          
          <div class="form-group">
            <label for="productoComanda">Producto</label>
            <select class="form-control select2" id="productoComanda" name="producto_id" required style="width: 100%;">
              <option value="">Seleccionar producto</option>
              <?php
              include '../includes/conexion.php';
              $sqlProductos = "SELECT id, nombre_producto, valor_con_iva, inventario FROM " . TBL_PRODUCTOS . " WHERE estado = 'activo' AND inventario > 0 ORDER BY nombre_producto ASC";
              $resultProductos = $conexion->query($sqlProductos);
              
              if ($resultProductos && $resultProductos->num_rows > 0) {
                while($prodRow = $resultProductos->fetch_assoc()) {
                  echo "<option value='" . $prodRow['id'] . "' data-precio='" . $prodRow['valor_con_iva'] . "'>" . htmlspecialchars($prodRow['nombre_producto']) . "</option>";
                }
              }
              ?>
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

<!-- Modal Cerrar Cuenta Comanda -->
<div class="modal fade" id="modalCerrarCuentaComanda" tabindex="-1" role="dialog" aria-labelledby="modalCerrarCuentaComandaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCerrarCuentaComandaLabel">Cerrar Cuenta - Comanda</h5>
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
          <tbody id="tablaProductosCierreComanda">
            <!-- Los productos se cargarán aquí -->
          </tbody>
          <tfoot>
            <tr class="font-weight-bold">
              <td colspan="3" class="text-right">TOTAL CUENTA:</td>
              <td><span id="totalCuentaCierreComanda" style="color: #27ae60; font-size: 1.2em;">$0.00</span></td>
            </tr>
          </tfoot>
        </table>

        <div class="row mt-4">
          <div class="col-md-12">
            <div class="form-group">
              <label for="metodoPagoComanda"><strong>Método de Pago</strong></label>
              <select class="form-control" id="metodoPagoComanda" required>
                <option value="">Seleccionar método</option>
                <option value="efectivo">Efectivo</option>
                <option value="llave">Llave</option>
                <option value="nequi">Nequi</option>
                <option value="daviplata">Daviplata</option>
                <option value="tarjeta">Tarjeta</option>
              </select>
            </div>
          </div>
        </div>

        <div class="row mt-3">
          <div class="col-md-6">
            <div class="form-group">
              <label for="montoPagoComanda">¿Con cuánto paga?</label>
              <input type="number" class="form-control" id="montoPagoComanda" placeholder="Ingrese el monto" min="0" step="0.01" oninput="calcularCambioComanda()">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Cambio</label>
              <input type="text" class="form-control" id="montoCambioComanda" readonly style="font-weight: bold; font-size: 1.1em; color: #27ae60;">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" onclick="finalizarCuentaComanda()">Finalizar</button>
      </div>
    </div>
  </div>
</div>

<input type="hidden" id="comandaIdNuevoHidden">

<script>
// Función para formatear números con separador de miles
function formatCurrency(amount) {
  return amount.toLocaleString('es-CO', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
}

// Cuando se selecciona un producto en la nueva comanda con Select2, actualizar precio
$('#productoComanda').on('select2:select', function(e) {
  const option = e.params.data.element;
  const precio = option.getAttribute('data-precio');
  document.getElementById('precioUnitarioComanda').value = precio ? '$' + formatCurrency(parseFloat(precio)) : '';
  calcularTotalComanda();
});

// También mantener compatibilidad con evento change normal
document.getElementById('productoComanda').addEventListener('change', function() {
  const option = this.options[this.selectedIndex];
  const precio = option.getAttribute('data-precio');
  document.getElementById('precioUnitarioComanda').value = precio ? '$' + formatCurrency(parseFloat(precio)) : '';
  calcularTotalComanda();
});

// Cuando cambia la cantidad, recalcular total
document.getElementById('cantidadComanda').addEventListener('change', calcularTotalComanda);
document.getElementById('cantidadComanda').addEventListener('input', calcularTotalComanda);

function calcularTotalComanda() {
  const cantidad = parseInt(document.getElementById('cantidadComanda').value) || 0;
  const option = document.getElementById('productoComanda').options[document.getElementById('productoComanda').selectedIndex];
  const precio = parseFloat(option.getAttribute('data-precio')) || 0;
  const total = cantidad * precio;
  document.getElementById('precioTotalComanda').value = '$' + formatCurrency(total);
}

function inicializarComandaSiNoExiste() {
  // Generar un ID único secuencial si no existe
  if (!document.getElementById('comandaIdNuevoHidden').value) {
    // Obtener el siguiente ID desde la BD
    fetch('obtener_siguiente_id.php')
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const comandaId = data.siguiente_id;
          document.getElementById('comandaIdNuevoHidden').value = comandaId;
          document.getElementById('comandaIdNuevo').value = comandaId;
        } else {
          // Si falla, usar timestamp como fallback
          const comandaId = Date.now();
          document.getElementById('comandaIdNuevoHidden').value = comandaId;
          document.getElementById('comandaIdNuevo').value = comandaId;
        }
      })
      .catch(error => {
        console.error('Error:', error);
        // Fallback a timestamp
        const comandaId = Date.now();
        document.getElementById('comandaIdNuevoHidden').value = comandaId;
        document.getElementById('comandaIdNuevo').value = comandaId;
      });
  } else {
    document.getElementById('comandaIdNuevo').value = document.getElementById('comandaIdNuevoHidden').value;
  }
}

function agregarProductoNuevaComanda(event) {
  event.preventDefault();
  
  const producto = document.getElementById('productoComanda').value;
  if (!producto) {
    Swal.fire({
      icon: 'warning',
      title: 'Advertencia',
      text: 'Debes seleccionar un producto'
    });
    return false;
  }
  
  const formData = new FormData(document.getElementById('formAgregarProductoComanda'));
  
  fetch('agregar_producto.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Cerrar modal de agregar producto
      $('#modalAgregarProductoComanda').modal('hide');
      
      // Limpiar formulario
      document.getElementById('formAgregarProductoComanda').reset();
      document.getElementById('precioUnitarioComanda').value = '';
      document.getElementById('precioTotalComanda').value = '';
      
      // Recargar productos de la comanda
      const comandaId = document.getElementById('comandaIdNuevoHidden').value;
      cargarProductosNuevaComanda(comandaId);
      
      // Mostrar mensaje de éxito
      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
      });
      Toast.fire({
        icon: 'success',
        title: data.message || 'Producto agregado'
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: data.message
      });
    }
  })
  .catch(error => {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Error al procesar la solicitud'
    });
    console.error('Error:', error);
  });
  
  return false;
}

function cargarProductosNuevaComanda(comandaId) {
  if (!comandaId) return;
  
  // Usar el formato CMD-X para las comandas
  const id_comanda = 'CMD-' + comandaId;
  
  fetch('obtener_productos.php?mesa_id=' + encodeURIComponent(id_comanda))
    .then(response => response.json())
    .then(data => {
      const tablaProductos = document.getElementById('tablaProductosNuevaComanda');
      tablaProductos.innerHTML = '';
      let total = 0;

      if (data.productos && data.productos.length > 0) {
        data.productos.forEach(producto => {
          const fila = document.createElement('tr');
          fila.innerHTML = `
            <td>${producto.nombre}</td>
            <td>$${formatCurrency(parseFloat(producto.valor))}</td>
            <td>
              <button type="button" class="btn btn-sm btn-danger" onclick="eliminarProductoNuevaComanda(${producto.id}, ${comandaId})">
                <i class="fas fa-trash"></i>
              </button>
            </td>
          `;
          tablaProductos.appendChild(fila);
          total += parseFloat(producto.valor);
        });
      } else {
        const fila = document.createElement('tr');
        fila.innerHTML = '<td colspan="3" class="text-center text-muted">Sin productos agregados</td>';
        tablaProductos.appendChild(fila);
      }

      document.getElementById('totalNuevaComanda').textContent = '$' + formatCurrency(total);
    })
    .catch(error => console.error('Error:', error));
}

function eliminarProductoNuevaComanda(productoMesaId, comandaId) {
  Swal.fire({
    title: '¿Estás seguro?',
    text: 'Esta acción no se puede deshacer',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      fetch('eliminar_producto.php?id=' + productoMesaId)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Recargar productos de la comanda
            cargarProductosNuevaComanda(comandaId);
            
            // Mostrar mensaje de éxito
            const Toast = Swal.mixin({
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true
            });
            Toast.fire({
              icon: 'success',
              title: data.message || 'Producto eliminado'
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: data.message
            });
          }
        })
        .catch(error => {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al procesar la solicitud'
          });
          console.error('Error:', error);
        });
    }
  });
}

function cancelarNuevaComanda() {
  const comandaId = document.getElementById('comandaIdNuevoHidden').value;
  
  if (!comandaId) {
    $('#modalNuevaComanda').modal('hide');
    return;
  }
  
  Swal.fire({
    title: '¿Confirmar cancelación?',
    text: 'Esto eliminará todos los productos agregados',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Sí, cancelar',
    cancelButtonText: 'No, volver'
  }).then((result) => {
    if (result.isConfirmed) {
      const id_comanda = 'CMD-' + comandaId;
      const formData = new FormData();
      formData.append('comanda_id', id_comanda);
      
      fetch('cancelar_comanda.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          Swal.fire({
            icon: 'success',
            title: 'Comanda cancelada',
            text: data.message
          }).then(() => {
            $('#modalNuevaComanda').modal('hide');
            window.location.reload();
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.message
          });
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Error al procesar la solicitud'
        });
      });
    }
  });
}

// Función para abrir modal de cerrar cuenta
function abrirModalCerrarCuentaComanda() {
  const comandaId = document.getElementById('comandaIdNuevoHidden').value;
  
  if (!comandaId) {
    Swal.fire({
      icon: 'warning',
      title: 'Sin productos',
      text: 'Agrega productos antes de cerrar la cuenta'
    });
    return;
  }
  
  const id_comanda = 'CMD-' + comandaId;
  
  // Obtener productos detallados de la comanda
  fetch('obtener_detalle.php?id_comanda=' + encodeURIComponent(id_comanda))
    .then(response => response.json())
    .then(data => {
      const tablaProductosCierre = document.getElementById('tablaProductosCierreComanda');
      tablaProductosCierre.innerHTML = '';
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
          tablaProductosCierre.appendChild(fila);
          total += parseFloat(producto.valor_total);
        });
        
        document.getElementById('totalCuentaCierreComanda').textContent = '$' + formatCurrency(total);
        document.getElementById('totalCuentaCierreComanda').setAttribute('data-total', total);
        
        // Limpiar campos de pago
        document.getElementById('metodoPagoComanda').value = '';
        document.getElementById('montoPagoComanda').value = '';
        document.getElementById('montoCambioComanda').value = '';
        
        // Abrir modal
        $('#modalCerrarCuentaComanda').modal('show');
      } else {
        Swal.fire({
          icon: 'warning',
          title: 'Sin productos',
          text: 'No hay productos en esta comanda para cerrar la cuenta'
        });
      }
    })
    .catch(error => {
      console.error('Error:', error);
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Error al cargar los productos'
      });
    });
}

function calcularCambioComanda() {
  const total = parseFloat(document.getElementById('totalCuentaCierreComanda').getAttribute('data-total')) || 0;
  const montoPago = parseFloat(document.getElementById('montoPagoComanda').value) || 0;
  
  if (montoPago > 0) {
    const cambio = montoPago - total;
    if (cambio >= 0) {
      document.getElementById('montoCambioComanda').value = '$' + formatCurrency(cambio);
      document.getElementById('montoCambioComanda').style.color = '#27ae60';
    } else {
      document.getElementById('montoCambioComanda').value = 'Falta: $' + formatCurrency(Math.abs(cambio));
      document.getElementById('montoCambioComanda').style.color = '#dc3545';
    }
  } else {
    document.getElementById('montoCambioComanda').value = '';
  }
}

function finalizarCuentaComanda() {
  const total = parseFloat(document.getElementById('totalCuentaCierreComanda').getAttribute('data-total')) || 0;
  const montoPago = parseFloat(document.getElementById('montoPagoComanda').value) || 0;
  const metodoPago = document.getElementById('metodoPagoComanda').value;
  const comandaId = document.getElementById('comandaIdNuevoHidden').value;
  
  // Validar método de pago
  if (!metodoPago) {
    Swal.fire({
      icon: 'warning',
      title: 'Método de pago requerido',
      text: 'Por favor seleccione un método de pago'
    });
    return;
  }
  
  if (montoPago < total) {
    Swal.fire({
      icon: 'warning',
      title: 'Pago insuficiente',
      text: 'El monto ingresado no cubre el total de la cuenta'
    });
    return;
  }
  
  Swal.fire({
    title: '¿Confirmar cierre de cuenta?',
    text: 'Esta acción finalizará la comanda',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#27ae60',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Sí, finalizar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      const formData = new FormData();
      formData.append('comanda_id', comandaId);
      formData.append('total', total);
      formData.append('metodo_pago', metodoPago);
      
      fetch('finalizar_comanda.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Cerrar modales
          $('#modalCerrarCuentaComanda').modal('hide');
          $('#modalNuevaComanda').modal('hide');
          
          // Mostrar notificación toast
          const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
          });
          
          Toast.fire({
            icon: 'success',
            title: 'Comanda cerrada exitosamente'
          });
          
          // Recargar después de que se cierre la notificación
          setTimeout(() => {
            window.location.reload();
          }, 2200);
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.message
          });
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Error al procesar la solicitud'
        });
      });
    }
  });
}

function cerrarModalNuevaComanda() {
  // Limpiar el ID de comanda temporal
  document.getElementById('comandaIdNuevoHidden').value = '';
  // Recargar la página para actualizar
  window.location.reload();
}

// Al abrir el modal, limpiar estado
$('#modalNuevaComanda').on('show.bs.modal', function () {
  document.getElementById('comandaIdNuevoHidden').value = '';
  document.getElementById('tablaProductosNuevaComanda').innerHTML = '<tr><td colspan="3" class="text-center text-muted">Sin productos agregados</td></tr>';
  document.getElementById('totalNuevaComanda').textContent = '$0.00';
});

// Inicializar Select2 cuando se abre el modal de productos
$('#modalAgregarProductoComanda').on('shown.bs.modal', function () {
  $('#productoComanda').select2({
    theme: 'bootstrap4',
    dropdownParent: $('#modalAgregarProductoComanda'),
    placeholder: 'Buscar producto...',
    allowClear: true,
    language: {
      noResults: function() {
        return "No se encontraron productos";
      },
      searching: function() {
        return "Buscando...";
      }
    }
  });
});

// Destruir Select2 al cerrar el modal para evitar duplicados
$('#modalAgregarProductoComanda').on('hidden.bs.modal', function () {
  if ($('#productoComanda').hasClass('select2-hidden-accessible')) {
    $('#productoComanda').select2('destroy');
  }
});
</script>
