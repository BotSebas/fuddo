<?php
include 'includes/auth.php';
include 'includes/url.php';
include_once 'lang/idiomas.php';
include 'includes/menu.php';
include 'includes/conexion_master.php';

// Verificar que sea super-admin
if ($_SESSION['rol'] !== 'super-admin') {
    header("Location: home.php");
    exit();
}

// Obtener todos los restaurantes
$sqlRestaurantes = "SELECT id, nombre, identificador, estado FROM restaurantes ORDER BY nombre ASC";
$resultRestaurantes = $conexion_master->query($sqlRestaurantes);

// Obtener todas las aplicaciones
$sqlAplicaciones = "SELECT id, clave, nombre, descripcion, icono FROM aplicaciones WHERE estado = 'activo' ORDER BY orden ASC";
$resultAplicaciones = $conexion_master->query($sqlAplicaciones);
$aplicaciones = [];
if ($resultAplicaciones && $resultAplicaciones->num_rows > 0) {
    while ($row = $resultAplicaciones->fetch_assoc()) {
        $aplicaciones[] = $row;
    }
}

// Obtener permisos actuales
$sqlPermisos = "SELECT id_restaurante, id_aplicacion FROM restaurante_aplicaciones";
$resultPermisos = $conexion_master->query($sqlPermisos);
$permisos = [];
if ($resultPermisos && $resultPermisos->num_rows > 0) {
    while ($row = $resultPermisos->fetch_assoc()) {
        $permisos[$row['id_restaurante']][] = $row['id_aplicacion'];
    }
}
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-shield-alt"></i> Permisos de Aplicaciones</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                        <li class="breadcrumb-item active">Permisos</li>
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
                            <h3 class="card-title">Gestionar Acceso a Módulos por Restaurante</h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                Selecciona qué módulos puede ver cada restaurante. Los cambios se aplican automáticamente.
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 250px;">Restaurante</th>
                                            <?php foreach ($aplicaciones as $app): ?>
                                                <th class="text-center" style="min-width: 100px;">
                                                    <i class="<?php echo $app['icono']; ?>"></i><br>
                                                    <small><?php echo $app['nombre']; ?></small>
                                                </th>
                                            <?php endforeach; ?>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if ($resultRestaurantes && $resultRestaurantes->num_rows > 0):
                                            while ($restaurante = $resultRestaurantes->fetch_assoc()):
                                                $id_restaurante = $restaurante['id'];
                                                $permisos_rest = $permisos[$id_restaurante] ?? [];
                                        ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($restaurante['nombre']); ?></strong><br>
                                                <small class="text-muted"><?php echo $restaurante['identificador']; ?></small>
                                                <?php if ($restaurante['estado'] !== 'activo'): ?>
                                                    <br><span class="badge badge-warning">Inactivo</span>
                                                <?php endif; ?>
                                            </td>
                                            <?php foreach ($aplicaciones as $app): ?>
                                                <td class="text-center">
                                                    <div class="custom-control custom-switch">
                                                        <input 
                                                            type="checkbox" 
                                                            class="custom-control-input permiso-check" 
                                                            id="permiso_<?php echo $id_restaurante; ?>_<?php echo $app['id']; ?>"
                                                            data-restaurante="<?php echo $id_restaurante; ?>"
                                                            data-aplicacion="<?php echo $app['id']; ?>"
                                                            <?php echo in_array($app['id'], $permisos_rest) ? 'checked' : ''; ?>
                                                        >
                                                        <label 
                                                            class="custom-control-label" 
                                                            for="permiso_<?php echo $id_restaurante; ?>_<?php echo $app['id']; ?>"
                                                        ></label>
                                                    </div>
                                                </td>
                                            <?php endforeach; ?>
                                            <td class="text-center">
                                                <button 
                                                    class="btn btn-sm btn-success btn-activar-todos" 
                                                    data-restaurante="<?php echo $id_restaurante; ?>"
                                                    title="Activar todos"
                                                >
                                                    <i class="fas fa-check-double"></i>
                                                </button>
                                                <button 
                                                    class="btn btn-sm btn-danger btn-desactivar-todos" 
                                                    data-restaurante="<?php echo $id_restaurante; ?>"
                                                    title="Desactivar todos"
                                                >
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php 
                                            endwhile;
                                        else:
                                        ?>
                                        <tr>
                                            <td colspan="<?php echo count($aplicaciones) + 2; ?>" class="text-center">
                                                No hay restaurantes registrados
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>

<script>
$(document).ready(function() {
    // Manejar cambio de permisos individuales
    $('.permiso-check').on('change', function() {
        const checkbox = $(this);
        const restaurante = checkbox.data('restaurante');
        const aplicacion = checkbox.data('aplicacion');
        const accion = checkbox.is(':checked') ? 'asignar' : 'revocar';
        
        $.ajax({
            url: 'permisos/procesar.php',
            method: 'POST',
            data: {
                restaurante: restaurante,
                aplicacion: aplicacion,
                accion: accion
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Permiso actualizado',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                    // Revertir el checkbox
                    checkbox.prop('checked', !checkbox.is(':checked'));
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al actualizar el permiso'
                });
                // Revertir el checkbox
                checkbox.prop('checked', !checkbox.is(':checked'));
            }
        });
    });

    // Activar todos los permisos de un restaurante
    $('.btn-activar-todos').on('click', function() {
        const restaurante = $(this).data('restaurante');
        const checkboxes = $(`input[data-restaurante="${restaurante}"]`);
        
        checkboxes.each(function() {
            if (!$(this).is(':checked')) {
                $(this).prop('checked', true).trigger('change');
            }
        });
    });

    // Desactivar todos los permisos de un restaurante
    $('.btn-desactivar-todos').on('click', function() {
        const restaurante = $(this).data('restaurante');
        const checkboxes = $(`input[data-restaurante="${restaurante}"]`);
        
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Se revocarán todos los permisos de este restaurante",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, revocar todos',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                checkboxes.each(function() {
                    if ($(this).is(':checked')) {
                        $(this).prop('checked', false).trigger('change');
                    }
                });
            }
        });
    });
});
</script>
