<?php
session_start();
include 'conexion_master.php';

header('Content-Type: application/json');

// Verificar que sea super-admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'super-admin') {
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_restaurante = $_POST['id_restaurante'] ?? '';
    $nombre_bd = $_POST['nombre_bd'] ?? '';

    if (empty($id_restaurante) || empty($nombre_bd)) {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
        exit();
    }

    // Obtener información del restaurante
    $stmt = $conexion_master->prepare("SELECT id, nombre, identificador, nombre_bd FROM restaurantes WHERE id = ? AND estado = 'activo'");
    $stmt->bind_param("i", $id_restaurante);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $restaurante = $resultado->fetch_assoc();
        
        // Verificar que las tablas del restaurante existan
        // Si nombre_bd termina en _ es un prefijo (modo Cloudways), si no es nombre de BD (modo legacy)
        if (substr($restaurante['nombre_bd'], -1) === '_') {
            // Modo Cloudways: verificar que exista al menos una tabla con el prefijo
            $tabla_test = $restaurante['nombre_bd'] . 'mesas';
            $check_table = $conexion_master->query("SHOW TABLES LIKE '$tabla_test'");
            if ($check_table->num_rows === 0) {
                echo json_encode(['success' => false, 'message' => 'Las tablas del restaurante no existen']);
                exit();
            }
        } else {
            // Modo legacy: verificar que la BD exista
            $check_db = $conexion_master->query("SHOW DATABASES LIKE '{$restaurante['nombre_bd']}'");
            if ($check_db->num_rows === 0) {
                echo json_encode(['success' => false, 'message' => 'La base de datos del restaurante no existe']);
                exit();
            }
        }

        // Actualizar sesión para conectarse al restaurante
        $_SESSION['id_restaurante'] = $restaurante['id'];
        $_SESSION['identificador'] = $restaurante['identificador']; // CRÍTICO: Para generar TABLE_PREFIX
        $_SESSION['nombre_bd'] = $restaurante['nombre_bd'];
        $_SESSION['nombre_restaurante'] = $restaurante['nombre'];
        $_SESSION['modo_soporte'] = true; // Flag para indicar que está en modo soporte

        echo json_encode([
            'success' => true, 
            'message' => 'Conectado al restaurante: ' . $restaurante['nombre']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Restaurante no encontrado o inactivo']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}

$conexion_master->close();
?>
