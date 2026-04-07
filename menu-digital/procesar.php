<?php
session_start();
require_once '../includes/conexion.php';

// Manejador AJAX para toggle de visibilidad
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'toggle_visible') {
    header('Content-Type: application/json');
    
    try {
        // Verificar sesión
        if (!isset($_SESSION['id_restaurante'])) {
            throw new Exception('Sesión no válida');
        }
        
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) {
            throw new Exception('ID inválido');
        }
        
        $visible = intval($_POST['visible'] ?? 0);
        $visible = ($visible === 1) ? 1 : 0;
        
        // Actualizar visibilidad
        $sql = "UPDATE " . TBL_MENU_DIGITAL . " SET visible = $visible WHERE id = $id";
        if (!$conexion->query($sql)) {
            throw new Exception('Error al actualizar: ' . $conexion->error);
        }
        
        echo json_encode([
            'exito' => true,
            'mensaje' => $visible ? 'Sección mostrada' : 'Sección ocultada'
        ]);
        exit();
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'exito' => false,
            'error' => $e->getMessage()
        ]);
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Verificar sesión
        if (!isset($_SESSION['id_restaurante'])) {
            header("Location: index.php?error=sin_sesion");
            exit();
        }
        
        $bloques = $_POST['bloques'] ?? [];
        $colorTema = $_POST['color_tema'] ?? 'verde';
        $modoOscuro = isset($_POST['modo_oscuro']) ? intval($_POST['modo_oscuro']) : 0;
        $facebook = trim($_POST['facebook'] ?? '');
        $instagram = trim($_POST['instagram'] ?? '');
        $tiktok = trim($_POST['tiktok'] ?? '');
        $youtube = trim($_POST['youtube'] ?? '');
        $whatsapp = trim($_POST['whatsapp'] ?? '');
        
        // Validar tema
        $temasPermitidos = ['verde', 'azul', 'rojo', 'amarillo', 'naranja', 'violeta', 'purpura', 'gris', 'negro', 'turquesa', 'cafe', 'vino', 'menta', 'petroleo', 'lavanda', 'arena'];
        if (!in_array($colorTema, $temasPermitidos)) {
            $colorTema = 'verde';
        }
        
        // Validar modo oscuro
        $modoOscuro = ($modoOscuro === 1) ? 1 : 0;
        
        // Procesar logo del menú
        $logoMenu = null;
        if (isset($_FILES['logo_menu']) && $_FILES['logo_menu']['error'] === UPLOAD_ERR_OK) {
            $archivoTmp = $_FILES['logo_menu']['tmp_name'];
            $nombreArchivo = $_FILES['logo_menu']['name'];
            $tamañoArchivo = $_FILES['logo_menu']['size'];
            $tipoArchivo = $_FILES['logo_menu']['type'];
            
            // Validar tipo de archivo
            $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($tipoArchivo, $tiposPermitidos)) {
                header("Location: index.php?error=" . urlencode("Formato de imagen no permitido"));
                exit();
            }
            
            // Validar tamaño (2MB máximo)
            if ($tamañoArchivo > 2 * 1024 * 1024) {
                header("Location: index.php?error=" . urlencode("La imagen no debe superar 2MB"));
                exit();
            }
            
            // Generar nombre único
            $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
            $identificador = $_SESSION['identificador'] ?? 'default';
            $nombreNuevo = 'logo_' . $identificador . '_' . time() . '.' . $extension;
            $rutaDestino = '../assets/img/menu/' . $nombreNuevo;
            
            // Mover archivo
            if (move_uploaded_file($archivoTmp, $rutaDestino)) {
                $logoMenu = $nombreNuevo;
                
                // Eliminar logo anterior si existe
                $sqlLogoAnterior = "SELECT logo_menu FROM " . TBL_MENU_DIGITAL . " WHERE logo_menu IS NOT NULL LIMIT 1";
                $resultLogoAnterior = $conexion->query($sqlLogoAnterior);
                if ($resultLogoAnterior && $resultLogoAnterior->num_rows > 0) {
                    $rowLogo = $resultLogoAnterior->fetch_assoc();
                    $logoAnterior = $rowLogo['logo_menu'];
                    if (!empty($logoAnterior) && file_exists('../assets/img/menu/' . $logoAnterior)) {
                        unlink('../assets/img/menu/' . $logoAnterior);
                    }
                }
            }
        } else {
            // Mantener logo existente
            $sqlLogoExistente = "SELECT logo_menu FROM " . TBL_MENU_DIGITAL . " WHERE logo_menu IS NOT NULL LIMIT 1";
            $resultLogoExistente = $conexion->query($sqlLogoExistente);
            if ($resultLogoExistente && $resultLogoExistente->num_rows > 0) {
                $rowLogo = $resultLogoExistente->fetch_assoc();
                $logoMenu = $rowLogo['logo_menu'];
            }
        }
        
        if (empty($bloques)) {
            header("Location: index.php?error=sin_bloques");
            exit();
        }
        
        // Escapar valores
        $colorTema = $conexion->real_escape_string($colorTema);
        $logoMenuEscapado = $logoMenu ? "'" . $conexion->real_escape_string($logoMenu) . "'" : "NULL";
        $facebookEscapado = !empty($facebook) ? "'" . $conexion->real_escape_string($facebook) . "'" : "NULL";
        $instagramEscapado = !empty($instagram) ? "'" . $conexion->real_escape_string($instagram) . "'" : "NULL";
        $tiktokEscapado = !empty($tiktok) ? "'" . $conexion->real_escape_string($tiktok) . "'" : "NULL";
        $youtubeEscapado = !empty($youtube) ? "'" . $conexion->real_escape_string($youtube) . "'" : "NULL";
        $whatsappEscapado = !empty($whatsapp) ? "'" . $conexion->real_escape_string($whatsapp) . "'" : "NULL";
        
        // Obtener estados de visibilidad de bloques existentes antes de actualizar
        $estadosVisibilidad = [];
        $sqlEstados = "SELECT id, visible FROM " . TBL_MENU_DIGITAL;
        $resultEstados = $conexion->query($sqlEstados);
        if ($resultEstados && $resultEstados->num_rows > 0) {
            while ($row = $resultEstados->fetch_assoc()) {
                $estadosVisibilidad[$row['id']] = $row['visible'];
            }
        }
        
        // Eliminar bloques existentes
        $sqlDelete = "DELETE FROM " . TBL_MENU_DIGITAL;
        $conexion->query($sqlDelete);
        
        // Insertar nuevos bloques preservando visibilidad
        $primerBloque = true;
        foreach ($bloques as $bloque) {
            $titulo = $conexion->real_escape_string($bloque['titulo']);
            $productosIds = isset($bloque['productos']) ? implode(',', $bloque['productos']) : '';
            $orden = intval($bloque['orden']);
            $bloqueId = intval($bloque['id'] ?? 0);
            
            // Recuperar visibilidad anterior si existe
            $visible = isset($estadosVisibilidad[$bloqueId]) ? $estadosVisibilidad[$bloqueId] : 1;
            
            if (empty($titulo)) continue;
            
            // Solo agregar redes sociales en el primer bloque
            if ($primerBloque) {
                $sqlInsert = "INSERT INTO " . TBL_MENU_DIGITAL . " 
                             (titulo_seccion, productos_ids, orden, color_tema, modo_oscuro, logo_menu, estado, visible, facebook, instagram, tiktok, youtube, whatsapp) 
                             VALUES ('$titulo', '$productosIds', $orden, '$colorTema', $modoOscuro, $logoMenuEscapado, 'activo', $visible, $facebookEscapado, $instagramEscapado, $tiktokEscapado, $youtubeEscapado, $whatsappEscapado)";
                $primerBloque = false;
            } else {
                $sqlInsert = "INSERT INTO " . TBL_MENU_DIGITAL . " 
                             (titulo_seccion, productos_ids, orden, color_tema, modo_oscuro, logo_menu, estado, visible) 
                             VALUES ('$titulo', '$productosIds', $orden, '$colorTema', $modoOscuro, $logoMenuEscapado, 'activo', $visible)";
            }
            
            $conexion->query($sqlInsert);
        }
        
        header("Location: index.php?exito=guardado");
        
    } catch (Exception $e) {
        header("Location: index.php?error=excepcion&msg=" . urlencode($e->getMessage()));
    }
} else {
    header("Location: index.php");
}
?>
