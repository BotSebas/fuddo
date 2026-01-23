<!-- Modal Productos de Mesa -->
<div class="modal fade" id="modalProductosMesa" tabindex="-1" role="dialog" aria-labelledby="modalProductosMesaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalProductosMesaLabel"><?php echo $mesas_productos_titulo; ?> <span id="nombreMesa"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="mb-3 d-flex justify-content-between">
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarProducto" onclick="setMesaId(document.getElementById('mesaIdHidden').value)">
            <i class="fas fa-plus"></i> <?php echo $mesas_agregar_producto; ?>
          </button>
          <button type="button" class="btn btn-success" onclick="abrirModalCerrarCuenta()">
            <i class="fas fa-dollar-sign"></i> <?php echo $mesas_cerrar_cuenta; ?>
          </button>
        </div>

        <table class="table table-striped table-hover">
          <thead class="bg-light">
            <tr>
              <th><?php echo $mesas_producto; ?></th>
              <th><?php echo $mesas_valor; ?></th>
              <th><?php echo $mesas_acciones; ?></th>
            </tr>
          </thead>
          <tbody id="tablaProductos">
            <!-- Los productos se cargarán aquí dinámicamente -->
          </tbody>
        </table>

        <div class="alert alert-info mt-3">
          <h5>Total: <strong id="totalProductos">$0.00</strong></h5>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" onclick="cancelarServicio()">
          <i class="fas fa-times-circle"></i> <?php echo $mesas_btn_cancelar_servicio; ?>
        </button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="cerrarModalYActualizar()"><?php echo $mesas_btn_cerrar; ?></button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Agregar Producto -->
<div class="modal fade" id="modalAgregarProducto" tabindex="-1" role="dialog" aria-labelledby="modalAgregarProductoLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalAgregarProductoLabel"><?php echo $mesas_modal_agregar; ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="formAgregarProducto" method="POST" onsubmit="agregarProductoMesa(event)">
        <div class="modal-body">
          <input type="hidden" id="mesaId" name="mesa_id">
          
          <div class="form-group">
            <label for="producto"><?php echo $mesas_producto; ?></label>
            <select class="form-control" id="producto" name="producto_id" required>
              <option value=""><?php echo $mesas_seleccionar_producto; ?></option>
              <?php
              include '../includes/conexion.php';
              // Obtener productos activos de la base de datos con stock disponible
              $sqlProductos = "SELECT id, nombre_producto, valor_con_iva, inventario FROM " . TBL_PRODUCTOS . " WHERE estado = 'activo' AND inventario > 0 ORDER BY nombre_producto ASC";
              $resultProductos = $conexion->query($sqlProductos);
              
              if ($resultProductos && $resultProductos->num_rows > 0) {
                while($prodRow = $resultProductos->fetch_assoc()) {
                  echo "<option value='" . $prodRow['id'] . "' data-precio='" . $prodRow['valor_con_iva'] . "'>" . htmlspecialchars($prodRow['nombre_producto']) . " - $" . number_format($prodRow['valor_con_iva'], 2) . "</option>";
                }
              }
              ?>
            </select>
          </div>

          <div class="form-group">
            <label for="cantidad"><?php echo $mesas_cantidad; ?></label>
            <input type="number" class="form-control" id="cantidad" name="cantidad" value="1" min="1" required>
          </div>

          <div class="form-group">
            <label for="precioUnitario"><?php echo $mesas_precio_unitario; ?></label>
            <input type="text" class="form-control" id="precioUnitario" readonly>
          </div>

          <div class="form-group">
            <label for="precioTotal"><?php echo $mesas_precio_total; ?></label>
            <input type="text" class="form-control" id="precioTotal" readonly>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $btn_cancelar; ?></button>
          <button type="submit" class="btn btn-primary"><?php echo $mesas_agregar_producto; ?></button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Cerrar Cuenta -->
<div class="modal fade" id="modalCerrarCuenta" tabindex="-1" role="dialog" aria-labelledby="modalCerrarCuentaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCerrarCuentaLabel"><?php echo $mesas_cerrar_cuenta_modal; ?> <span id="nombreMesaCierre"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <h6 class="mb-3"><?php echo $mesas_productos_consumidos; ?></h6>
        <table class="table table-bordered">
          <thead class="thead-light">
            <tr>
              <th><?php echo $mesas_producto; ?></th>
              <th><?php echo $mesas_cantidad; ?></th>
              <th><?php echo $mesas_valor_unitario; ?></th>
              <th><?php echo $mesas_valor_total; ?></th>
            </tr>
          </thead>
          <tbody id="tablaProductosCierre">
            <!-- Los productos se cargarán aquí -->
          </tbody>
          <tfoot>
            <tr class="font-weight-bold">
              <td colspan="3" class="text-right"><?php echo $mesas_total_cuenta_final; ?></td>
              <td><span id="totalCuentaCierre" style="color: #27ae60; font-size: 1.2em;">$0.00</span></td>
            </tr>
          </tfoot>
        </table>

        <div class="row mt-4">
          <div class="col-md-12">
            <div class="form-group">
              <label for="metodoPago"><strong><?php echo $mesas_metodo_pago; ?></strong></label>
              <select class="form-control" id="metodoPago" required>
                <option value=""><?php echo $mesas_seleccionar_metodo; ?></option>
                <option value="efectivo"><?php echo $mesas_efectivo; ?></option>
                <option value="llave"><?php echo $mesas_llave; ?></option>
                <option value="nequi"><?php echo $mesas_nequi; ?></option>
                <option value="daviplata"><?php echo $mesas_daviplata; ?></option>
                <option value="tarjeta"><?php echo $mesas_tarjeta; ?></option>
              </select>
            </div>
          </div>
        </div>

        <div class="row mt-3">
          <div class="col-md-6">
            <div class="form-group">
              <label for="montoPago"><?php echo $mesas_con_cuanto_paga; ?></label>
              <input type="number" class="form-control" id="montoPago" placeholder="<?php echo $mesas_ingrese_monto; ?>" min="0" step="0.01" oninput="calcularCambio()">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label><?php echo $mesas_cambio; ?></label>
              <input type="text" class="form-control" id="montoCambio" readonly style="font-weight: bold; font-size: 1.1em; color: #27ae60;">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $btn_cancelar; ?></button>
        <button type="button" class="btn btn-success" onclick="finalizarCuenta()"><?php echo $mesas_finalizar; ?></button>
      </div>
    </div>
  </div>
</div>

<input type="hidden" id="mesaIdHidden">

<script>
// Cuando se selecciona un producto, actualizar precio
document.getElementById('producto').addEventListener('change', function() {
  const option = this.options[this.selectedIndex];
  const precio = option.getAttribute('data-precio');
  document.getElementById('precioUnitario').value = precio ? '$' + parseFloat(precio).toFixed(2) : '';
  calcularTotal();
});

// Cuando cambia la cantidad, recalcular total
document.getElementById('cantidad').addEventListener('change', calcularTotal);
document.getElementById('cantidad').addEventListener('input', calcularTotal);

function calcularTotal() {
  const cantidad = parseInt(document.getElementById('cantidad').value) || 0;
  const option = document.getElementById('producto').options[document.getElementById('producto').selectedIndex];
  const precio = parseFloat(option.getAttribute('data-precio')) || 0;
  const total = cantidad * precio;
  document.getElementById('precioTotal').value = '$' + total.toFixed(2);
}

function setMesaId(mesaId) {
  document.getElementById('mesaId').value = mesaId;
}

function agregarProductoMesa(event) {
  event.preventDefault();
  
  const producto = document.getElementById('producto').value;
  if (!producto) {
    Swal.fire({
      icon: 'warning',
      title: 'Advertencia',
      text: 'Debes seleccionar un producto'
    });
    return false;
  }
  
  const formData = new FormData(document.getElementById('formAgregarProducto'));
  
  fetch('agregar_producto.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Cerrar modal de agregar producto
      $('#modalAgregarProducto').modal('hide');
      
      // Limpiar formulario
      document.getElementById('formAgregarProducto').reset();
      document.getElementById('precioUnitario').value = '';
      document.getElementById('precioTotal').value = '';
      
      // Recargar productos de la mesa
      const mesaId = document.getElementById('mesaIdHidden').value;
      const nombreMesa = document.getElementById('nombreMesa').textContent;
      cargarProductosMesa(mesaId, nombreMesa);
      
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
        title: data.message
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

function validarProducto() {
  const producto = document.getElementById('producto').value;
  if (!producto) {
    Swal.fire({
      icon: 'warning',
      title: 'Advertencia',
      text: 'Debes seleccionar un producto'
    });
    return false;
  }
  return true;
}

// Cargar productos de una mesa
function cargarProductosMesa(mesaId, nombreMesa) {
  document.getElementById('nombreMesa').textContent = nombreMesa;
  document.getElementById('mesaIdHidden').value = mesaId;
  
  // Realizar petición AJAX para obtener productos
  fetch('obtener_productos.php?mesa_id=' + mesaId)
    .then(response => response.json())
    .then(data => {
      const tablaProductos = document.getElementById('tablaProductos');
      tablaProductos.innerHTML = '';
      let total = 0;

      if (data.productos && data.productos.length > 0) {
        data.productos.forEach(producto => {
          const fila = document.createElement('tr');
          fila.innerHTML = `
            <td>${producto.nombre}</td>
            <td>$${parseFloat(producto.valor).toFixed(2)}</td>
            <td>
              <button type="button" class="btn btn-sm btn-danger" onclick="eliminarProductoMesa(${producto.id}, ${mesaId})">
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

      document.getElementById('totalProductos').textContent = '$' + total.toFixed(2);
    })
    .catch(error => console.error('Error:', error));
}

function eliminarProductoMesa(productoMesaId, mesaId) {
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
            // Recargar productos de la mesa
            const nombreMesa = document.getElementById('nombreMesa').textContent;
            cargarProductosMesa(mesaId, nombreMesa);
            
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
              title: data.message
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

function cerrarModalYActualizar() {
  // Recargar la página para actualizar los totales de las mesas
  window.location.reload();
}

function abrirModalCerrarCuenta() {
  const mesaId = document.getElementById('mesaIdHidden').value;
  const nombreMesa = document.getElementById('nombreMesa').textContent;
  
  document.getElementById('nombreMesaCierre').textContent = nombreMesa;
  
  // Obtener productos detallados de la mesa
  fetch('obtener_detalle.php?mesa_id=' + mesaId)
    .then(response => response.json())
    .then(data => {
      const tablaProductosCierre = document.getElementById('tablaProductosCierre');
      tablaProductosCierre.innerHTML = '';
      let total = 0;
      
      if (data.success && data.productos && data.productos.length > 0) {
        data.productos.forEach(producto => {
          const fila = document.createElement('tr');
          fila.innerHTML = `
            <td>${producto.nombre}</td>
            <td>${producto.cantidad}</td>
            <td>$${parseFloat(producto.valor_unitario).toFixed(2)}</td>
            <td>$${parseFloat(producto.valor_total).toFixed(2)}</td>
          `;
          tablaProductosCierre.appendChild(fila);
          total += parseFloat(producto.valor_total);
        });
        
        document.getElementById('totalCuentaCierre').textContent = '$' + total.toFixed(2);
        document.getElementById('totalCuentaCierre').setAttribute('data-total', total);
        
        // Limpiar campos de pago
        document.getElementById('montoPago').value = '';
        document.getElementById('montoCambio').value = '';
        
        // Abrir modal
        $('#modalCerrarCuenta').modal('show');
      } else {
        Swal.fire({
          icon: 'warning',
          title: 'Sin productos',
          text: 'No hay productos en esta mesa para cerrar la cuenta'
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

function calcularCambio() {
  const total = parseFloat(document.getElementById('totalCuentaCierre').getAttribute('data-total')) || 0;
  const montoPago = parseFloat(document.getElementById('montoPago').value) || 0;
  
  if (montoPago > 0) {
    const cambio = montoPago - total;
    if (cambio >= 0) {
      document.getElementById('montoCambio').value = '$' + cambio.toFixed(2);
      document.getElementById('montoCambio').style.color = '#27ae60';
    } else {
      document.getElementById('montoCambio').value = 'Falta: $' + Math.abs(cambio).toFixed(2);
      document.getElementById('montoCambio').style.color = '#dc3545';
    }
  } else {
    document.getElementById('montoCambio').value = '';
  }
}

function finalizarCuenta() {
  const total = parseFloat(document.getElementById('totalCuentaCierre').getAttribute('data-total')) || 0;
  const montoPago = parseFloat(document.getElementById('montoPago').value) || 0;
  const metodoPago = document.getElementById('metodoPago').value;
  
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
    text: 'Esta acción finalizará el servicio de la mesa',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#27ae60',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Sí, finalizar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      const mesaId = document.getElementById('mesaIdHidden').value;
      
      // Enviar datos para finalizar la cuenta
      const formData = new FormData();
      formData.append('mesa_id', mesaId);
      formData.append('total', total);
      formData.append('metodo_pago', metodoPago);
      
      fetch('finalizar_cuenta.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Cerrar modales primero
          $('#modalCerrarCuenta').modal('hide');
          $('#modalProductosMesa').modal('hide');
          
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
            title: 'Cuenta cerrada exitosamente'
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

function cancelarServicio() {
  const mesaId = document.getElementById('mesaIdHidden').value;
  const nombreMesa = document.getElementById('nombreMesa').textContent;
  
  Swal.fire({
    title: '¿Confirmar cancelación del servicio?',
    text: 'Esto marcará todos los productos como cancelados y liberará la mesa',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Sí, cancelar servicio',
    cancelButtonText: 'No, volver'
  }).then((result) => {
    if (result.isConfirmed) {
      const formData = new FormData();
      formData.append('mesa_id', mesaId);
      
      fetch('cancelar_servicio.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          Swal.fire({
            icon: 'success',
            title: 'Servicio cancelado',
            text: data.message
          }).then(() => {
            $('#modalProductosMesa').modal('hide');
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
</script>
