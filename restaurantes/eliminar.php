<?php
session_start();
include '../includes/conexion_master.php';

header('Content-Type: application/json');

// Verificar permisos
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'super-admin') {
    echo json_encode(['success' => false, 'message' => 'No tienes permisos']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    
    try {
        // Obtener información del restaurante
        $sql = "SELECT nombre, nombre_bd, identificador FROM restaurantes WHERE id = ?";
        $stmt = $conexion_master->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Restaurante no encontrado');
        }
        
        $restaurante = $result->fetch_assoc();
        $nombre_bd = $restaurante['nombre_bd'];
        $nombre_restaurante = $restaurante['nombre'];
        
        error_log("[ELIMINAR] ID: $id | Nombre: $nombre_restaurante | Prefijo/BD: $nombre_bd");

        // ============================================
        // DESACTIVAR RESTRICCIONES DE LLAVES FORÁNEAS
        // ============================================
        $conexion_master->query("SET FOREIGN_KEY_CHECKS=0");
        error_log("[ELIMINAR] Restricciones de claves foráneas desactivadas");

        // ============================================
        // ELIMINAR USUARIOS DEL RESTAURANTE
        // ============================================
        $sqlUsuarios = "SELECT COUNT(*) as total FROM usuarios_master WHERE id_restaurante = ?";
        $stmtUsuarios = $conexion_master->prepare($sqlUsuarios);
        $stmtUsuarios->bind_param("i", $id);
        $stmtUsuarios->execute();
        $resultUsuarios = $stmtUsuarios->get_result();
        $totalUsuarios = $resultUsuarios->fetch_assoc()['total'];
        
        if ($totalUsuarios > 0) {
            $conexion_master->query("DELETE FROM usuarios_master WHERE id_restaurante = $id");
            error_log("[ELIMINAR] Eliminados $totalUsuarios usuarios del restaurante");
        }

        // ============================================
        // ELIMINAR PERMISOS
        // ============================================
        $conexion_master->query("DELETE FROM restaurante_aplicaciones WHERE id_restaurante = $id");

        // ============================================
        // ELIMINAR TABLAS EN mgacgdnjkg (MODO CLOUDWAYS)
        // ============================================
        // Si nombre_bd contiene "__" es prefijo (fuddo_demo_* o fuddo_*)
        if (strpos($nombre_bd, '_') !== false && strpos($nombre_bd, '_', strpos($nombre_bd, '_') + 1)) {
            // Es un prefijo: fuddo_restaurant_
            $query = "SHOW TABLES FROM mgacgdnjkg LIKE '" . $nombre_bd . "%'";
            $result_tables = $conexion_master->query($query);
            
            $tablasEliminadas = 0;
            while ($row = $result_tables->fetch_row()) {
                $tabla = $row[0];
                $conexion_master->query("DROP TABLE IF EXISTS `mgacgdnjkg`.`$tabla`");
                $tablasEliminadas++;
                error_log("[ELIMINAR TABLA] Eliminada: $tabla");
            }
            error_log("[ELIMINAR] Se eliminaron $tablasEliminadas tablas con prefijo: $nombre_bd");
        } else {
            // Es una BD separada (modo legacy)
            $conexion_master->query("DROP DATABASE IF EXISTS `$nombre_bd`");
            error_log("[ELIMINAR] Eliminada BD: $nombre_bd");
        }

        // ============================================
        // REACTIVAR RESTRICCIONES DE LLAVES FORÁNEAS
        // ============================================
        $conexion_master->query("SET FOREIGN_KEY_CHECKS=1");
        error_log("[ELIMINAR] Restricciones de claves foráneas reactivadas");

        // ============================================
        // ELIMINAR RESTAURANTE
        // ============================================
        $sql_delete = "DELETE FROM restaurantes WHERE id = ?";
        $stmt_delete = $conexion_master->prepare($sql_delete);
        $stmt_delete->bind_param("i", $id);
        
        if ($stmt_delete->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Restaurante eliminado exitosamente'
            ]);
            error_log("[ELIMINAR EXITOSO] Restaurante ID: $id | Nombre: $nombre_restaurante");
        } else {
            throw new Exception('Error al eliminar el restaurante');
        }
        
    } catch (Exception $e) {
        error_log("[ELIMINAR ERROR] " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}
?>
