<?php
session_start();
include '../includes/conexion_master.php';

header('Content-Type: application/json');

// Verificar permisos
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'super-admin') {
    echo json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción']);
    exit();
}

try {
    $id = $_POST['id'] ?? null;
    $nombre = trim($_POST['nombre'] ?? '');
    $identificador = trim($_POST['identificador'] ?? '');
    $contacto = trim($_POST['contacto'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $plan = $_POST['plan'] ?? 'basico';
    $fecha_expiracion = !empty($_POST['fecha_expiracion']) ? $_POST['fecha_expiracion'] : null;
    
    // Validaciones básicas
    if (empty($nombre) || empty($identificador)) {
        throw new Exception('Nombre e identificador son obligatorios');
    }
    
    // Validar identificador (solo letras, números y guiones bajos)
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $identificador)) {
        throw new Exception('El identificador solo puede contener letras, números y guiones bajos');
    }
    
    if (empty($id)) {
        // CREAR NUEVO RESTAURANTE
        
        // Verificar que el identificador no exista
        $stmt = $conexion_master->prepare("SELECT id FROM restaurantes WHERE identificador = ?");
        $stmt->bind_param("s", $identificador);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception('El identificador ya existe');
        }
        
        // Detectar modo: Cloudways (una BD con prefijos) vs Local (múltiples BDs)
        $is_cloudways = false;
        
        // Detectar si estamos en producción por el dominio
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        if (strpos($host, 'fuddo.co') !== false || strpos($host, 'phpstack-1316371-6163825.cloudwaysapps.com') !== false) {
            $is_cloudways = true;
        } else {
            // En local, verificar si existe la BD mgacgdnjkg
            $test_cloud = @new mysqli('localhost', 'root', '', 'mgacgdnjkg');
            if (!$test_cloud->connect_error) {
                $is_cloudways = true;
                $test_cloud->close();
            }
        }
        
        if ($is_cloudways) {
            // MODO CLOUDWAYS: Crear tablas con prefijo en mgacgdnjkg
            $table_prefix = 'fuddo_' . $identificador . '_';
            
            // Insertar restaurante (nombre_bd guarda el prefijo)
            $sql = "INSERT INTO restaurantes (nombre, identificador, nombre_bd, contacto, email, telefono, plan, fecha_expiracion, estado) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'activo')";
            $stmt = $conexion_master->prepare($sql);
            $stmt->bind_param("ssssssss", $nombre, $identificador, $table_prefix, $contacto, $email, $telefono, $plan, $fecha_expiracion);
            $stmt->execute();
            $id_restaurante = $conexion_master->insert_id;
            
            // Leer template y reemplazar {PREFIX}
            $sql_template = file_get_contents('../sql/template_restaurante.sql');
            if ($sql_template === false) {
                throw new Exception('No se pudo leer el template SQL');
            }
            
            $sql_schema = str_replace('{PREFIX}', $table_prefix, $sql_template);
            
            // Ejecutar en la BD actual (mgacgdnjkg) - usar conexion_master que ya está conectada
            if (!$conexion_master->multi_query($sql_schema)) {
                throw new Exception('Error al crear tablas: ' . $conexion_master->error);
            }
            
            // Esperar a que terminen todas las consultas
            do {
                if ($result = $conexion_master->store_result()) {
                    $result->free();
                }
            } while ($conexion_master->more_results() && $conexion_master->next_result());
            
            echo json_encode([
                'success' => true,
                'message' => 'Restaurante creado exitosamente con tablas prefijadas'
            ]);
            
        } else {
            // MODO LOCAL: Crear BD separada (comportamiento legacy)
            $nombre_bd = 'fuddo_' . strtolower($identificador);
            
            // Verificar que la BD no exista
            $check_db = $conexion_master->query("SHOW DATABASES LIKE '$nombre_bd'");
            if ($check_db->num_rows > 0) {
                throw new Exception('La base de datos ya existe');
            }
            
            // Insertar restaurante en BD maestra
            $sql = "INSERT INTO restaurantes (nombre, identificador, nombre_bd, contacto, email, telefono, plan, fecha_expiracion, estado) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'activo')";
            $stmt = $conexion_master->prepare($sql);
            $stmt->bind_param("ssssssss", $nombre, $identificador, $nombre_bd, $contacto, $email, $telefono, $plan, $fecha_expiracion);
            $stmt->execute();
            $id_restaurante = $conexion_master->insert_id;
            
            // Crear la base de datos del restaurante
            $conexion_master->query("CREATE DATABASE `$nombre_bd` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Obtener esquema de BD plantilla
            $sql_schema = file_get_contents('../sql/schema_restaurante.sql');
            if ($sql_schema === false) {
                throw new Exception('No se pudo leer el esquema SQL');
            }
            
            // Conectar a la nueva BD
            $conexion_nueva = new mysqli('localhost', 'root', '', $nombre_bd);
            
            if ($conexion_nueva->connect_error) {
                throw new Exception("Error al conectar a la nueva BD");
            }
            
            // Ejecutar el esquema SQL
            $conexion_nueva->multi_query($sql_schema);
            
            // Esperar a que terminen todas las consultas
            do {
                if ($result = $conexion_nueva->store_result()) {
                    $result->free();
                }
            } while ($conexion_nueva->more_results() && $conexion_nueva->next_result());
            
            $conexion_nueva->close();
            
            echo json_encode([
                'success' => true,
                'message' => 'Restaurante creado exitosamente'
            ]);
        }
        
    } else {
        // ACTUALIZAR RESTAURANTE EXISTENTE
        
        $sql = "UPDATE restaurantes SET nombre = ?, contacto = ?, email = ?, telefono = ?, plan = ?, fecha_expiracion = ? WHERE id = ?";
        $stmt = $conexion_master->prepare($sql);
        
        if (!$stmt) {
            throw new Exception('Error al preparar consulta: ' . $conexion_master->error);
        }
        
        $stmt->bind_param("ssssssi", $nombre, $contacto, $email, $telefono, $plan, $fecha_expiracion, $id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Restaurante actualizado exitosamente'
            ]);
        } else {
            throw new Exception('Error al actualizar el restaurante: ' . $stmt->error);
        }
    }
    
} catch (Exception $e) {
    // Rollback si es creación
    if (isset($id_restaurante) && empty($id)) {
        // Eliminar tablas con prefijo si existen (modo Cloudways)
        if (isset($table_prefix)) {
            $tables_to_drop = [
                $table_prefix . 'mesas',
                $table_prefix . 'productos', 
                $table_prefix . 'servicios',
                $table_prefix . 'servicios_total',
                $table_prefix . 'usuarios'
            ];
            foreach ($tables_to_drop as $table) {
                @$conexion_master->query("DROP TABLE IF EXISTS `$table`");
            }
        }
        
        // Eliminar BD si existe (modo local)
        if (isset($nombre_bd)) {
            @$conexion_master->query("DROP DATABASE IF EXISTS `$nombre_bd`");
        }
        
        // Eliminar registro del restaurante
        @$conexion_master->query("DELETE FROM restaurantes WHERE id = $id_restaurante");
    }
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
