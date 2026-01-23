<?php
/**
 * Script para crear un nuevo restaurante con su BD completa
 * Solo puede ser ejecutado por super-admin
 */

include 'includes/auth.php';

// Verificar que sea super-admin
if (!isset($_SESSION['rol_master']) || $_SESSION['rol_master'] !== 'super-admin') {
    header("Location: home.php");
    exit();
}

include 'includes/url.php';
include_once 'lang/idiomas.php';
include 'includes/conexion_master.php';

// Procesar creación de restaurante
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $nombre = trim($_POST['nombre']);
    $identificador = trim($_POST['identificador']);
    $contacto = trim($_POST['contacto']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);

    // Validaciones
    if (empty($nombre) || empty($identificador)) {
        echo json_encode(['success' => false, 'message' => 'Nombre e identificador son obligatorios']);
        exit();
    }

    // Nombre de la BD del restaurante
    // NOTA: En Cloudways usamos PREFIJO de tabla, en local se mantiene BD separada
    $nombre_bd = 'fuddo_' . preg_replace('/[^a-z0-9_]/', '', strtolower($identificador));
    $table_prefix = 'fuddo_' . $identificador . '_';

    try {
        // 1. Verificar que el identificador no exista
        $stmt = $conexion_master->prepare("SELECT id FROM restaurantes WHERE identificador = ?");
        $stmt->bind_param("s", $identificador);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'El identificador ya existe']);
            exit();
        }

        // 2. Detectar si estamos en Cloudways
        $is_cloudways = (strpos($_SERVER['HTTP_HOST'], 'cloudwaysapps.com') !== false);

        if (!$is_cloudways) {
            // MODO LOCAL: Verificar que la BD no exista
            $check_db = $conexion_master->query("SHOW DATABASES LIKE '$nombre_bd'");
            if ($check_db->num_rows > 0) {
                echo json_encode(['success' => false, 'message' => 'La base de datos ya existe']);
                exit();
            }
        }

        // 3. Insertar restaurante en BD maestra
        // IMPORTANTE: nombre_bd ahora guarda el prefijo en Cloudways, el nombre de BD en local
        $valor_nombre_bd = $is_cloudways ? $table_prefix : $nombre_bd;
        
        $stmt = $conexion_master->prepare("
            INSERT INTO restaurantes (nombre, identificador, nombre_bd, contacto, email, telefono, estado) 
            VALUES (?, ?, ?, ?, ?, ?, 'activo')
        ");
        $stmt->bind_param("ssssss", $nombre, $identificador, $valor_nombre_bd, $contacto, $email, $telefono);
        $stmt->execute();
        $id_restaurante = $conexion_master->insert_id;

        if ($is_cloudways) {
            // MODO CLOUDWAYS: Crear tablas con prefijo en la misma BD
            // Leer template y reemplazar {PREFIX}
            $sql_template = file_get_contents('sql/template_restaurante.sql');
            $sql_schema = str_replace('{PREFIX}', $table_prefix, $sql_template);
            
            // Ejecutar en la BD actual (fwedexhvyx)
            $conexion_master->multi_query($sql_schema);
            
            // Esperar a que terminen todas las consultas
            do {
                if ($result = $conexion_master->store_result()) {
                    $result->free();
                }
            } while ($conexion_master->more_results() && $conexion_master->next_result());

        } else {
            // MODO LOCAL: Crear BD separada (comportamiento original)
            // 4. Crear la base de datos del restaurante
            $conexion_master->query("CREATE DATABASE `$nombre_bd` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            // 5. Obtener esquema de BD plantilla (leer de archivo SQL)
            $sql_schema = file_get_contents('sql/schema_restaurante.sql');
            
            // Conectar a la nueva BD
            $conexion_nueva = new mysqli('localhost', 'root', '', $nombre_bd);
            
            if ($conexion_nueva->connect_error) {
                throw new Exception("Error al conectar a la nueva BD");
            }

            // 6. Ejecutar el esquema SQL
            $conexion_nueva->multi_query($sql_schema);
            
            // Esperar a que terminen todas las consultas
            do {
                if ($result = $conexion_nueva->store_result()) {
                    $result->free();
                }
            } while ($conexion_nueva->more_results() && $conexion_nueva->next_result());

            $conexion_nueva->close();
        }

        echo json_encode([
            'success' => true,
            'message' => 'Restaurante creado exitosamente. Ahora puedes crear usuarios desde el módulo Usuarios.',
            'id_restaurante' => $id_restaurante,
            'nombre_bd' => $valor_nombre_bd
        ]);

    } catch (Exception $e) {
        // Rollback: eliminar restaurante y BD/tablas si algo falla
        if (isset($id_restaurante)) {
            $conexion_master->query("DELETE FROM usuarios_master WHERE id_restaurante = $id_restaurante");
            $conexion_master->query("DELETE FROM restaurantes WHERE id = $id_restaurante");
        }
        
        if (!$is_cloudways && isset($nombre_bd)) {
            // Local: eliminar BD
            $conexion_master->query("DROP DATABASE IF EXISTS `$nombre_bd`");
        }
        // Cloudways: las tablas con prefijo se eliminan automáticamente con el DELETE CASCADE
        // o se pueden eliminar manualmente si es necesario
        
        if (!$is_cloudways && isset($nombre_bd)) {
            // Local: eliminar BD
            $conexion_master->query("DROP DATABASE IF EXISTS `$nombre_bd`");
        }
        // Cloudways: las tablas con prefijo se eliminan automáticamente con el DELETE CASCADE
        // o se pueden eliminar manualmente si es necesario

        echo json_encode([
            'success' => false,
            'message' => 'Error al crear restaurante: ' . $e->getMessage()
        ]);
    }

} else {
    // Mostrar formulario con layout AdminLTE
    include 'includes/menu.php';
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
  <!-- Content Header -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0"><i class="fas fa-store mr-2"></i>Crear Nuevo Restaurante</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active">Nuevo Restaurante</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      
      <div class="card">
        <div class="card-header" style="background-color: #27ae60; color: white;">
          <h3 class="card-title"><i class="fas fa-plus mr-2"></i>Formulario de Registro</h3>
        </div>
        <div class="card-body">
          <form method="post" id="formRestaurante">
            <h4 class="text-success"><i class="fas fa-building mr-2"></i>Datos del Restaurante</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nombre *</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Identificador * (sin espacios, solo letras y números)</label>
                            <input type="text" name="identificador" class="form-control" required pattern="[a-z0-9]+">
                            <small class="text-muted">
                                Código único del restaurante. Se usará para generar el nombre de la BD: <strong>fuddo_<span id="preview-identificador">identificador</span></strong><br>
                                Ejemplo: rest001, pizzeria, burger
                            </small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Contacto</label>
                            <input type="text" name="contacto" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Teléfono</label>
                            <input type="tel" name="telefono" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Importante:</strong> Después de crear el restaurante, debes crear usuarios desde el módulo <strong>Usuarios</strong> y asignarlos a este restaurante.
                </div>

                <div class="mt-4">
                  <button type="submit" class="btn btn-fuddo btn-lg">
                    <i class="fas fa-save mr-2"></i>Crear Restaurante
                  </button>
                  <a href="home.php" class="btn btn-secondary btn-lg">
                    <i class="fas fa-times mr-2"></i>Cancelar
                  </a>
                </div>
            </form>
        </div>
      </div>
    </div>
  </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$('#formRestaurante').on('submit', function(e) {
    e.preventDefault();
    
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Se creará un nuevo restaurante con su propia base de datos",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#27ae60',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, crear',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Creando restaurante...',
                html: 'Por favor espera',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: 'crear_restaurante.php',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Restaurante creado!',
                            html: '<strong>Base de datos:</strong> ' + response.nombre_bd,
                            confirmButtonColor: '#27ae60'
                        }).then(() => {
                            window.location.href = 'home.php';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                            confirmButtonColor: '#27ae60'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al procesar la solicitud',
                        confirmButtonColor: '#27ae60'
                    });
                }
            });
        }
    });
});
</script>

<?php 
include 'includes/footer.php'; 
}
?>
