<?php
session_start();
include '../includes/conexion_master.php';
include '../includes/enviar_correo.php';

header('Content-Type: application/json');

// Verificar permisos
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'super-admin') {
    echo json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción']);
    exit();
}

try {
    // DEBUGGING: Log todos los datos recibidos
    error_log("=== NUEVA SOLICITUD ===");
    error_log("POST data recibido: " . json_encode($_POST));
    
    $id = $_POST['id'] ?? null;
    $usuario = trim($_POST['usuario'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $rol = $_POST['rol'] ?? '';
    $password = $_POST['password'] ?? '';
    $id_restaurante = $_POST['id_restaurante'] ?? null;
    
    error_log("Variables parseadas:");
    error_log("  id: '$id'");
    error_log("  usuario: '$usuario'");
    error_log("  rol: '$rol'");
    error_log("  esEdicion: " . (!empty($id) ? 'true' : 'false'));
    
    // Determinar si es edición o creación
    $esEdicion = !empty($id);
    $rolFueProvidoPorUsuario = !empty($rol); // Guardar si el usuario explícitamente especificó rol
    
    // Validaciones básicas
    if (empty($usuario) || empty($nombre)) {
        throw new Exception('Por favor completa todos los campos obligatorios');
    }
    
    // Si es NUEVO usuario, rol es obligatorio
    if (!$esEdicion && empty($rol)) {
        throw new Exception('Debes seleccionar un rol para el nuevo usuario');
    }
    
    // Si es EDICIÓN y el rol está vacío, obtener el rol actual (no cambiar)
    if ($esEdicion && empty($rol)) {
        error_log("DEBUG: Entrando a obtener rol actual para edición...");
        
        // Obtener rol actual del usuario
        $sqlGet = "SELECT rol FROM usuarios_master WHERE id = " . intval($id);
        error_log("DEBUG: Query: $sqlGet");
        
        $queryRolActual = $conexion_master->query($sqlGet);
        
        if (!$queryRolActual) {
            $error = $conexion_master->error;
            error_log("ERROR: Query SQL falló: $error");
            throw new Exception('Error de base de datos: ' . $error);
        }
        
        error_log("DEBUG: Query ejecutada. Num rows: " . $queryRolActual->num_rows);
        
        if ($queryRolActual->num_rows > 0) {
            $rowRol = $queryRolActual->fetch_assoc();
            $rol = $rowRol['rol'];
            error_log("DEBUG: Rol obtenido: '$rol'");
        } else {
            error_log("ERROR: No existe usuario con ID: $id");
            throw new Exception('No se encontró el usuario a editar (ID: ' . $id . ')');
        }
    }
    
    // Obtener lista de roles válidos desde la tabla roles_master
    $rolesValidos = [];
    $sqlRoles = "SELECT rol FROM roles_master ORDER BY rol";
    $resultRoles = $conexion_master->query($sqlRoles);
    if ($resultRoles && $resultRoles->num_rows > 0) {
        while ($rowRole = $resultRoles->fetch_assoc()) {
            $rolesValidos[] = $rowRole['rol'];
        }
    }
    
    error_log("DEBUG: Roles válidos en BD: " . json_encode($rolesValidos));
    
    // Validar rol después de obtener el actual si es edición
    if (empty($rol)) {
        throw new Exception('Rol no puede estar vacío');
    }
    
    if (!in_array($rol, $rolesValidos)) {
        error_log("ERROR: Rol encontrado pero inválido: '$rol'");
        throw new Exception('Rol no válido: "' . htmlspecialchars($rol) . '". Roles válidos: ' . implode(', ', $rolesValidos));
    }
    
    // Validar restaurante basado en el rol
    // Solo super-admin puede NO tener restaurante asignado
    if ($rol !== 'super-admin' && empty($id_restaurante)) {
        // Si es nuevo usuario: requerir restaurante
        if (!$esEdicion) {
            throw new Exception('Debes seleccionar un restaurante para el rol "' . $rol . '"');
        }
        
        // Si es edición: verificar que al menos mantenga el restaurante actual
        $sqlRestActual = "SELECT id_restaurante FROM usuarios_master WHERE id = " . intval($id);
        $queryRestActual = $conexion_master->query($sqlRestActual);
        if ($queryRestActual && $queryRestActual->num_rows > 0) {
            $rowRest = $queryRestActual->fetch_assoc();
            if (empty($rowRest['id_restaurante'])) {
                // No tiene restaurante y no está seleccionando uno
                throw new Exception('Este rol requiere tener asignado un restaurante');
            }
            // Si tiene restaurante actual, permitir (mantendrá el actual)
        }
    }
    
    // Si es nuevo usuario, password es obligatorio
    if (!$esEdicion && empty($password)) {
        throw new Exception('La contraseña es obligatoria para usuarios nuevos');
    }
    
    if (!empty($password) && strlen($password) < 6) {
        throw new Exception('La contraseña debe tener al menos 6 caracteres');
    }
    
    // Manejo de foto
    $rutaFoto = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $directorioDestino = '../assets/img/users/';
        
        // Crear directorio si no existe
        if (!file_exists($directorioDestino)) {
            mkdir($directorioDestino, 0755, true);
        }
        
        // Validar tipo de archivo
        $tipoArchivo = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $tiposPermitidos = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (!in_array($tipoArchivo, $tiposPermitidos)) {
            throw new Exception('Solo se permiten archivos JPG, PNG o GIF');
        }
        
        // Validar tamaño (2MB máximo)
        if ($_FILES['foto']['size'] > 2097152) {
            throw new Exception('La imagen no debe superar los 2MB');
        }
        
        // Generar nombre único basado en usuario
        $nombreArchivo = preg_replace('/[^a-zA-Z0-9]/', '_', $usuario) . '_' . time() . '.' . $tipoArchivo;
        $rutaCompleta = $directorioDestino . $nombreArchivo;
        
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaCompleta)) {
            $rutaFoto = 'assets/img/users/' . $nombreArchivo;
        } else {
            throw new Exception('Error al subir la foto');
        }
    }
    
    if (empty($id)) {
        // CREAR NUEVO USUARIO EN USUARIOS_MASTER
        
        // Verificar que el usuario no exista
        $stmt = $conexion_master->prepare("SELECT id FROM usuarios_master WHERE usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception('El usuario ya existe');
        }
        
        // Hashear contraseña
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insertar en usuarios_master
        $sql = "INSERT INTO usuarios_master (usuario, password, nombre, email, id_restaurante, rol, estado, foto) VALUES (?, ?, ?, ?, ?, ?, 'activo', ?)";
        $stmt = $conexion_master->prepare($sql);
        $stmt->bind_param("sssssss", $usuario, $passwordHash, $nombre, $email, $id_restaurante, $rol, $rutaFoto);
        
        if ($stmt->execute()) {
            // Enviar correo de bienvenida con credenciales
            if (!empty($email)) {
                // Construir URL de login dinámicamente
                $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $loginUrl = $scheme . $host . '/login.php';
                
                $asunto = 'FUDDO - Bienvenido al Sistema POS';
                $cuerpoHTML = generarHTMLBienvenidaUsuario($nombre, $usuario, $email, $password, $rol, $loginUrl);
                
                enviarCorreo($email, $asunto, $cuerpoHTML, $nombre);
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Usuario creado exitosamente'
            ]);
        } else {
            throw new Exception('Error al crear el usuario');
        }
        
    } else {
        // ACTUALIZAR USUARIO EXISTENTE EN USUARIOS_MASTER
        
        // Verificar que el usuario no esté duplicado (excepto el mismo)
        $stmt = $conexion_master->prepare("SELECT id FROM usuarios_master WHERE usuario = ? AND id != ?");
        $stmt->bind_param("si", $usuario, $id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception('El usuario ya existe');
        }
        
        // Construir query dinámicamente
        $campos = [];
        $tipos = "";
        $valores = [];
        
        $campos[] = "usuario = ?";
        $tipos .= "s";
        $valores[] = $usuario;
        
        $campos[] = "nombre = ?";
        $tipos .= "s";
        $valores[] = $nombre;
        
        $campos[] = "email = ?";
        $tipos .= "s";
        $valores[] = $email;
        
        // Solo actualizar rol si el usuario lo especificó explícitamente
        if ($rolFueProvidoPorUsuario) {
            $campos[] = "rol = ?";
            $tipos .= "s";
            $valores[] = $rol;
        }
        
        // Actualizar restaurante solo si es necesario
        // Si es super-admin: siempre NULL
        // Si es admin-restaurante y se especificó restaurante: actualizar
        // Si es admin-restaurante y NO se especificó: mantener actual
        if ($rol === 'super-admin') {
            $campos[] = "id_restaurante = NULL";
        } else if (!empty($id_restaurante)) {
            // Solo actualizar si se especificó un valor
            $campos[] = "id_restaurante = ?";
            $tipos .= "i";
            $valores[] = $id_restaurante;
        }
        // Si id_restaurante está vacío y no es super-admin, no actualizar (mantener actual)
        
        // Solo actualizar password si se proporcionó uno nuevo
        if (!empty($password)) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $campos[] = "password = ?";
            $tipos .= "s";
            $valores[] = $passwordHash;
        }
        
        // Solo actualizar foto si se subió una nueva
        if ($rutaFoto !== null) {
            $campos[] = "foto = ?";
            $tipos .= "s";
            $valores[] = $rutaFoto;
        }
        
        $tipos .= "i";
        $valores[] = $id;
        
        $sql = "UPDATE usuarios_master SET " . implode(", ", $campos) . " WHERE id = ?";
        $stmt = $conexion_master->prepare($sql);
        $stmt->bind_param($tipos, ...$valores);
        
        if ($stmt->execute()) {
            // Si el usuario editado es el mismo que está logueado, actualizar la sesión
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $id) {
                $_SESSION['nombre'] = $nombre;
                $_SESSION['email'] = $email;
                if ($rutaFoto !== null) {
                    $_SESSION['foto'] = $rutaFoto;
                }
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente'
            ]);
        } else {
            throw new Exception('Error al actualizar el usuario');
        }
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
