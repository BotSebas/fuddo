<?php
include '../includes/auth.php';

// Verificar que sea super-admin
if ($_SESSION['rol'] !== 'super-admin') {
    header("Location: ../home.php");
    exit();
}

include '../includes/url.php';
include_once '../lang/idiomas.php';
include '../includes/menu.php';
include '../includes/conexion_master.php';

// Obtener todos los restaurantes
$sql = "SELECT id, nombre, identificador, nombre_bd, contacto, email, telefono, estado, fecha_creacion, plan, fecha_expiracion FROM restaurantes ORDER BY fecha_creacion DESC";
$resultado = $conexion_master->query($sql);
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-store"></i> Gestión de Restaurantes</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../home.php">Home</a></li>
                        <li class="breadcrumb-item active">Restaurantes</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Lista de Restaurantes</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-fuddo" id="btnNuevoRestaurante">
                                    <i class="fas fa-plus"></i> Nuevo Restaurante
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="tablaRestaurantes" class="table table-bordered table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Contacto</th>
                                        <th>Plan</th>
                                        <th>Estado</th>
                                        <th>Fecha Creación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if ($resultado && $resultado->num_rows > 0):
                                        while($row = $resultado->fetch_assoc()):
                                    ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($row['nombre']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($row['contacto'] ?? '-'); ?></td>
                                        <td>
                                            <?php 
                                            $badges = [
                                                'basico' => 'badge-secondary',
                                                'premium' => 'badge-primary',
                                                'enterprise' => 'badge-success'
                                            ];
                                            $badge = $badges[$row['plan']] ?? 'badge-secondary';
                                            ?>
                                            <span class="badge <?php echo $badge; ?>">
                                                <?php echo ucfirst($row['plan']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="custom-control custom-switch">
                                                <input 
                                                    type="checkbox" 
                                                    class="custom-control-input estado-switch" 
                                                    id="estado_<?php echo $row['id']; ?>"
                                                    data-id="<?php echo $row['id']; ?>"
                                                    <?php echo $row['estado'] === 'activo' ? 'checked' : ''; ?>
                                                >
                                                <label class="custom-control-label" for="estado_<?php echo $row['id']; ?>"></label>
                                            </div>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($row['fecha_creacion'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info btn-editar" data-id="<?php echo $row['id']; ?>" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger btn-eliminar" data-id="<?php echo $row['id']; ?>" data-nombre="<?php echo htmlspecialchars($row['nombre']); ?>" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php 
                                        endwhile;
                                    else:
                                    ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No hay restaurantes registrados</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Restaurante -->
<div class="modal fade" id="modalRestaurante" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #27ae60; color: white;">
                <h5 class="modal-title" id="modalRestauranteLabel">Nuevo Restaurante</h5>
                <button type="button" class="close" data-dismiss="modal" style="color: white;">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formRestaurante" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="restaurante_id" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre">Nombre del Restaurante <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="identificador">Identificador <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="identificador" name="identificador" required>
                                <small class="form-text text-muted">Solo letras, números y guiones bajos</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contacto">Contacto</label>
                                <input type="text" class="form-control" id="contacto" name="contacto">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telefono">Teléfono</label>
                                <input type="text" class="form-control" id="telefono" name="telefono">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="plan">Plan</label>
                                <select class="form-control" id="plan" name="plan">
                                    <option value="basico">Básico</option>
                                    <option value="premium">Premium</option>
                                    <option value="enterprise">Enterprise</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_expiracion">Fecha de Expiración</label>
                                <input type="date" class="form-control" id="fecha_expiracion" name="fecha_expiracion">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-fuddo">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- DataTables -->
<link rel="stylesheet" href="<?php echo $BASE_URL; ?>plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<script src="<?php echo $BASE_URL; ?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo $BASE_URL; ?>plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>

<!-- SweetAlert2 -->
<script src="<?php echo $BASE_URL; ?>assets/vendor/sweetalert2/sweetalert2.all.min.js"></script>

<script>
console.log('=== INICIANDO SCRIPT ===');
console.log('jQuery version:', $.fn.jquery);

$(document).ready(function() {
    console.log('✓ DOM Ready');
    console.log('✓ jQuery cargado:', typeof jQuery !== 'undefined');
    console.log('✓ Botón existe:', $('#btnNuevoRestaurante').length);
    console.log('✓ Modal existe:', $('#modalRestaurante').length);
    console.log('✓ Bootstrap modal:', typeof $.fn.modal !== 'undefined');
    
    // DataTable
    try {
        const tabla = $('#tablaRestaurantes').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            },
            "order": [[0, "desc"]]
        });
        console.log('✓ DataTable inicializado');
    } catch(e) {
        console.error('Error en DataTable:', e);
    }

    // Nuevo restaurante - MÉTODO SIMPLE
    $(document).on('click', '#btnNuevoRestaurante', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('🔥 CLICK DETECTADO en btnNuevoRestaurante');
        
        try {
            $('#formRestaurante')[0].reset();
            $('#restaurante_id').val('');
            $('#modalRestauranteLabel').text('Nuevo Restaurante');
            $('#identificador').prop('readonly', false);
            console.log('✓ Formulario reseteado');
            
            $('#modalRestaurante').modal('show');
            console.log('✓ Modal.show() ejecutado');
        } catch(error) {
            console.error('Error al abrir modal:', error);
        }
    });

    // Editar restaurante - Usar delegación de eventos
    $('#tablaRestaurantes').on('click', '.btn-editar', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: 'obtener.php',
            method: 'GET',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const r = response.data;
                    $('#restaurante_id').val(r.id);
                    $('#nombre').val(r.nombre);
                    $('#identificador').val(r.identificador);
                    $('#contacto').val(r.contacto);
                    $('#email').val(r.email);
                    $('#telefono').val(r.telefono);
                    $('#plan').val(r.plan);
                    $('#fecha_expiracion').val(r.fecha_expiracion);
                    
                    $('#modalRestauranteLabel').text('Editar Restaurante');
                    $('#identificador').prop('readonly', true);
                    $('#modalRestaurante').modal('show');
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Error al obtener los datos', 'error');
            }
        });
    });

    // Guardar restaurante
    $('#formRestaurante').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const btnSubmit = $(this).find('button[type="submit"]');
        btnSubmit.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        
        $.ajax({
            url: 'procesar.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                    btnSubmit.prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', xhr.responseText);
                Swal.fire('Error', 'Error al procesar la solicitud', 'error');
                btnSubmit.prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
            }
        });
    });

    // Cambiar estado - Usar delegación de eventos
    $('#tablaRestaurantes').on('change', '.estado-switch', function() {
        const id = $(this).data('id');
        const checkbox = $(this);
        
        $.ajax({
            url: 'cambiar_estado.php',
            method: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Estado actualizado',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                    checkbox.prop('checked', !checkbox.is(':checked'));
                }
            },
            error: function() {
                Swal.fire('Error', 'Error al cambiar el estado', 'error');
                checkbox.prop('checked', !checkbox.is(':checked'));
            }
        });
    });

    // Eliminar restaurante - Usar delegación de eventos
    $('#tablaRestaurantes').on('click', '.btn-eliminar', function() {
        const id = $(this).data('id');
        const nombre = $(this).data('nombre');
        
        Swal.fire({
            title: '¿Estás seguro?',
            html: `Se eliminará el restaurante <strong>${nombre}</strong> y su base de datos.<br><br><span class="text-danger">Esta acción no se puede deshacer.</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'eliminar.php',
                    method: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Error al eliminar el restaurante', 'error');
                    }
                });
            }
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>
