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
        
        error_log("[BACKUP] Generando backup para: $nombre_restaurante | Prefijo: $nombre_bd");

        // ============================================
        // GENERAR DUMP SQL DE TODAS LAS TABLAS
        // ============================================
        
        // Obtener todas las tablas con el prefijo
        $query = "SHOW TABLES FROM mgacgdnjkg LIKE '" . $nombre_bd . "%'";
        $result_tables = $conexion_master->query($query);
        
        if (!$result_tables) {
            throw new Exception('Error al consultar tablas: ' . $conexion_master->error);
        }
        
        $tables = [];
        while ($row = $result_tables->fetch_row()) {
            $tables[] = $row[0];
        }
        
        if (empty($tables)) {
            throw new Exception('No se encontraron tablas para este restaurante');
        }

        // Generar SQL completo
        $sqlDump = "-- ============================================\n";
        $sqlDump .= "-- Backup de Restaurante: $nombre_restaurante\n";
        $sqlDump .= "-- Identificador: " . $restaurante['identificador'] . "\n";
        $sqlDump .= "-- Fecha: " . date('Y-m-d H:i:s') . "\n";
        $sqlDump .= "-- ============================================\n\n";
        $sqlDump .= "-- Tablas: " . count($tables) . "\n";
        $sqlDump .= "-- Prefijo: $nombre_bd\n\n";

        foreach ($tables as $tabla) {
            // CREATE TABLE
            $createResult = $conexion_master->query("SHOW CREATE TABLE `mgacgdnjkg`.`$tabla`");
            if ($createResult) {
                $createRow = $createResult->fetch_row();
                $createSQL = $createRow[1];
                $sqlDump .= "\n-- ============================================\n";
                $sqlDump .= "-- Tabla: $tabla\n";
                $sqlDump .= "-- ============================================\n";
                $sqlDump .= "DROP TABLE IF EXISTS `$tabla`;\n";
                $sqlDump .= $createSQL . ";\n\n";
                
                // Obtener datos
                $dataResult = $conexion_master->query("SELECT * FROM `mgacgdnjkg`.`$tabla`");
                if ($dataResult && $dataResult->num_rows > 0) {
                    $fieldsResult = $conexion_master->query("SHOW COLUMNS FROM `mgacgdnjkg`.`$tabla`");
                    $fields = [];
                    while ($field = $fieldsResult->fetch_assoc()) {
                        $fields[] = "`" . $field['Field'] . "`";
                    }
                    $fieldStr = implode(", ", $fields);
                    
                    while ($row = $dataResult->fetch_assoc()) {
                        $values = [];
                        foreach ($row as $value) {
                            if ($value === null) {
                                $values[] = "NULL";
                            } else {
                                $values[] = "'" . $conexion_master->real_escape_string($value) . "'";
                            }
                        }
                        $valueStr = implode(", ", $values);
                        $sqlDump .= "INSERT INTO `$tabla` ($fieldStr) VALUES ($valueStr);\n";
                    }
                }
            }
        }

        $sqlDump .= "\n-- ============================================\n";
        $sqlDump .= "-- Fin del Backup\n";
        $sqlDump .= "-- ============================================\n";

        // Guardar en archivo temporal
        $filename = 'backup_' . $nombre_restaurante . '_' . date('YmdHis') . '.sql';
        $filepath = sys_get_temp_dir() . '/' . $filename;
        
        if (file_put_contents($filepath, $sqlDump) === false) {
            throw new Exception('Error al crear archivo de backup');
        }

        error_log("[BACKUP] Archivo creado: $filepath | Tamaño: " . filesize($filepath) . " bytes");

        // Enviar archivo al cliente
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        
        readfile($filepath);
        
        // Limpiar archivo temporal después de descargarlo
        unlink($filepath);
        
        exit();
        
    } catch (Exception $e) {
        error_log("[BACKUP ERROR] " . $e->getMessage());
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
