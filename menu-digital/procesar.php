<?php
session_start();
require_once '../includes/conexion.php';

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
        
        // Eliminar bloques existentes
        $sqlDelete = "DELETE FROM " . TBL_MENU_DIGITAL;
        $conexion->query($sqlDelete);
        
        // Insertar nuevos bloques
        foreach ($bloques as $bloque) {
            $titulo = $conexion->real_escape_string($bloque['titulo']);
            $productosIds = isset($bloque['productos']) ? implode(',', $bloque['productos']) : '';
            $orden = intval($bloque['orden']);
            
            if (empty($titulo)) continue;
            
            $sqlInsert = "INSERT INTO " . TBL_MENU_DIGITAL . " 
                         (titulo_seccion, productos_ids, orden, color_tema, modo_oscuro, logo_menu, estado) 
                         VALUES ('$titulo', '$productosIds', $orden, '$colorTema', $modoOscuro, $logoMenuEscapado, 'activo')";
            
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
